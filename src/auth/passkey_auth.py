import base64
import os
import secrets
import time
from typing import List, Optional, Dict, Any

from fastapi import APIRouter, HTTPException, status, Request
from pydantic import BaseModel, Field

# Sử dụng thư viện fido2 để triển khai WebAuthn/Passkey,
# đồng thời xử lý triệt để các lỗi và tối ưu hóa từ bản nháp,
# bám sát cấu trúc và nguyên tắc bảo mật của file gốc.
from fido2 import cbor
from fido2.server import Fido2Server, RelyingParty
from fido2.webauthn import AttestedCredentialData, AuthenticatorData # AuthenticatorData được trả về bởi các phương thức complete

# --- Configuration (Các giá trị này nên được lấy từ biến môi trường hoặc hệ thống cấu hình an toàn) ---
# RP_ID: Tên host của ứng dụng (ví dụ: "your-app.com" hoặc "localhost" cho phát triển).
# Giá trị này phải khớp với miền nơi frontend của bạn được phục vụ.
RP_ID = os.getenv("WEBAUTHN_RP_ID", "localhost")
# RP_NAME: Tên thân thiện với người dùng cho ứng dụng của bạn.
RP_NAME = os.getenv("WEBAUTHN_RP_NAME", "UNIT3D App")
# RP_ORIGINS: Danh sách các origin (URL frontend) được phép thực hiện các yêu cầu WebAuthn.
# Đối với phát triển cục bộ, hãy bao gồm URL frontend cục bộ của bạn.
# Giá trị này phải là một chuỗi phân tách bằng dấu phẩy từ biến môi trường, sau đó được chia thành một danh sách.
RP_ORIGINS_RAW = os.getenv("WEBAUTHN_ORIGINS", "http://localhost:8000")
RP_ORIGINS = [o.strip() for o in RP_ORIGINS_RAW.split(',') if o.strip()] # Lọc các chuỗi rỗng

# CHALLENGE_TTL_SECONDS: Thời gian tồn tại (Time-To-Live) cho các challenge WebAuthn để ngăn chặn tấn công replay và cạn kiệt tài nguyên.
# Trong môi trường production, giá trị này nên ngắn (ví dụ: 5 phút) và được lưu trữ trong một kho phiên an toàn,
# bền vững (ví dụ: Redis).
CHALLENGE_TTL_SECONDS = 300 # 5 phút

# --- Database Mock (Thay thế bằng các mô hình cơ sở dữ liệu thực tế và logic ORM của bạn) ---
# Trong một ứng dụng thực tế, đây sẽ là các mô hình cơ sở dữ liệu phù hợp (ví dụ: SQLAlchemy, Tortoise ORM, Django ORM).

class WebAuthnCredential:
    """
    Đại diện cho một thông tin xác thực WebAuthn (Passkey) được liên kết với người dùng.
    Lưu trữ dữ liệu fido2.AttestedCredentialData thô và sign_count hiện tại.
    Thông tin này phải được lưu trữ an toàn trong cơ sở dữ liệu của bạn.
    """
    def __init__(self,
                 credential_id: bytes, # ID thông tin xác thực thô (bytes) từ AttestedCredentialData.credential_id
                 attested_credential_data: AttestedCredentialData, # Dữ liệu thông tin xác thực cốt lõi từ đăng ký
                 sign_count: int,       # Bộ đếm để ngăn chặn tấn công replay
                 user_id: bytes):       # Liên kết đến Người dùng (ID người dùng thô dạng bytes)
        self.credential_id = credential_id
        self.attested_credential_data = attested_credential_data
        self.sign_count = sign_count
        self.user_id = user_id

class User:
    """
    Đại diện cho một người dùng trong hệ thống.
    Trong một ứng dụng thực tế, điều này sẽ liên kết với mô hình Người dùng hiện có của bạn.
    """
    def __init__(self, user_id: bytes, username: str, display_name: str):
        self.user_id = user_id             # ID người dùng dạng bytes, được sử dụng cho các API WebAuthn
        self.username = username
        self.display_name = display_name
        self.credentials: Dict[bytes, WebAuthnCredential] = {} # Key: credential_id (bytes)

# "Cơ sở dữ liệu" trong bộ nhớ chỉ dành cho mục đích trình diễn.
# KHÔNG SỬ DỤNG trong môi trường production. Thay thế bằng cơ sở dữ liệu bền vững.
users_db: Dict[str, User] = {}             # Key: username (chuỗi)

# Các hàm trợ giúp để mô phỏng tương tác cơ sở dữ liệu
def get_user_by_username(username: str) -> Optional[User]:
    """Truy xuất người dùng bằng tên người dùng của họ."""
    return users_db.get(username)

# --- Session Management Mock (Thay thế bằng quản lý phiên/trạng thái an toàn thực tế) ---
# Các challenge này rất quan trọng đối với bảo mật và phải được lưu trữ an toàn phía máy chủ,
# thường là trong một kho phiên (ví dụ: Redis) với TTL ngắn, liên kết với một phiên người dùng cụ thể.
# KHÔNG SỬ DỤNG trong môi trường production dưới dạng các từ điển đơn giản.
# Key: username (chuỗi), Value: {'challenge_bytes': bytes, 'timestamp': float}
registration_challenges: Dict[str, Dict[str, Any]] = {}
authentication_challenges: Dict[str, Dict[str, Any]] = {}

def store_challenge(challenge_dict: Dict[str, Dict[str, Any]], key: str, challenge_bytes: bytes):
    """Lưu trữ một challenge với một dấu thời gian."""
    challenge_dict[key] = {
        "challenge_bytes": challenge_bytes,
        "timestamp": time.time()
    }

def retrieve_and_validate_challenge(challenge_dict: Dict[str, Dict[str, Any]], key: str) -> bytes:
    """Truy xuất và xác thực một challenge, xóa nó sau khi sử dụng."""
    challenge_data = challenge_dict.pop(key, None) # Xóa challenge ngay lập tức để ngăn chặn replay
    if not challenge_data:
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "CHALLENGE_EXPIRED_OR_MISSING",
            "Challenge đã hết hạn hoặc bị thiếu. Vui lòng khởi động lại quá trình."
        )

    if time.time() - challenge_data["timestamp"] > CHALLENGE_TTL_SECONDS:
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "CHALLENGE_EXPIRED",
            "Challenge đã hết hạn. Vui lòng khởi động lại quá trình."
        )
    return challenge_data["challenge_bytes"]

# --- Helper cho mã hóa/giải mã Base64URL với tính nhất quán về phần đệm ---
# WebAuthn thường sử dụng Base64URL không có phần đệm. base64.urlsafe_b64decode của Python mong đợi phần đệm.
def _base64url_decode_padded(data: str) -> bytes:
    """Giải mã một chuỗi Base64URL, thêm phần đệm nếu cần."""
    padding = '=' * (len(data) % 4)
    return base64.urlsafe_b64decode(data + padding)

def _base64url_encode_unpadded(data: bytes) -> str:
    """Mã hóa bytes thành chuỗi Base64URL không có phần đệm."""
    return base64.urlsafe_b64encode(data).decode('ascii').rstrip('=')

# --- Helper cho phản hồi lỗi API chuẩn hóa (Tuân thủ CHƯƠNG VI) ---
class APIErrorResponse(BaseModel):
    error: str = Field(..., description="Một định danh lỗi viết hoa, phân tách bằng dấu gạch dưới.")
    code: int = Field(..., description="Mã trạng thái HTTP hoặc mã lỗi nội bộ dạng số.")
    message: str = Field(..., description="Mô tả lỗi ngắn gọn, thân thiện với người dùng.")

def raise_api_error(
    http_status: int,
    error_code: str,
    message: str
) -> HTTPException:
    """
    Ném ra một FastAPI HTTPException với định dạng phản hồi lỗi chuẩn hóa.
    Tuân thủ CHƯƠNG VI: QUY CHUẨN PHẢN HỒI LỖI (API ERROR SPECIFICATION)
    bằng cách cung cấp một đối tượng lỗi có cấu trúc và tránh để lộ stack trace.
    """
    return HTTPException(
        status_code=http_status,
        detail=APIErrorResponse(
            error=error_code,
            code=http_status,
            message=message
        ).dict()
    )

# --- Cấu hình máy chủ FIDO2 ---
rp = RelyingParty(RP_ID, RP_NAME, RP_ORIGINS)
fido2_server = Fido2Server(rp)

# --- FastAPI Router ---
router = APIRouter(prefix="/passkey", tags=["Passkey Authentication"])

# --- Pydantic Models cho Payload Yêu cầu/Phản hồi API ---
class RegisterStartRequest(BaseModel):
    """Mô hình yêu cầu để bắt đầu đăng ký passkey."""
    username: str
    display_name: str # Tên hiển thị của người dùng, được sử dụng trong lời nhắc passkey

class RegisterStartResponse(BaseModel):
    """Mô hình phản hồi để bắt đầu đăng ký passkey."""
    challenge: str = Field(..., description="Challenge được mã hóa Base64URL cho máy khách.")
    options: Dict = Field(..., description="Các tùy chọn WebAuthn (JSON được giải mã CBOR) cho navigator.credentials.create().")

class RegisterCompleteRequest(BaseModel):
    """Mô hình yêu cầu để hoàn tất đăng ký passkey."""
    username: str
    credential: Dict = Field(..., description="Đối tượng thông tin xác thực WebAuthn (JSON được giải mã CBOR) từ máy khách.")

class LoginStartRequest(BaseModel):
    """Mô hình yêu cầu để bắt đầu đăng nhập passkey."""
    username: str

class LoginStartResponse(RegisterStartResponse):
    """Mô hình phản hồi để bắt đầu đăng nhập passkey (chia sẻ cấu trúc với phản hồi bắt đầu đăng ký)."""
    pass

class LoginCompleteRequest(BaseModel):
    """Mô hình yêu cầu để hoàn tất đăng nhập passkey."""
    username: str
    credential: Dict # Phản hồi xác thực WebAuthn thô từ máy khách

class AuthSuccessResponse(BaseModel):
    """Phản hồi thành công chung cho xác thực/đăng ký."""
    message: str = Field("Xác thực thành công", description="Một thông báo thành công.")
    username: str = Field(..., description="Tên người dùng đã xác thực/đăng ký thành công.")
    user_id: str = Field(..., description="ID người dùng được mã hóa Base64URL của người dùng đã xác thực/đăng ký.")

# --- API Endpoints ---

@router.post("/register/start", response_model=RegisterStartResponse,
             summary="Bắt đầu đăng ký Passkey")
async def register_start(request_data: RegisterStartRequest):
    """
    Tạo các tùy chọn đăng ký WebAuthn (một challenge) cho máy khách.
    Máy khách sử dụng các tùy chọn này để tạo một passkey mới.
    """
    username = request_data.username
    display_name = request_data.display_name

    if get_user_by_username(username):
        raise raise_api_error(
            status.HTTP_409_CONFLICT,
            "USERNAME_TAKEN",
            f"Tên người dùng '{username}' này đã được sử dụng. Vui lòng chọn tên khác hoặc đăng nhập."
        )

    # Tạo ID người dùng duy nhất dưới dạng bytes.
    user_id_bytes = secrets.token_bytes(16) # Sử dụng secrets để có tính ngẫu nhiên tốt hơn
    user = User(user_id=user_id_bytes, username=username, display_name=display_name)
    users_db[username] = user # Lưu người dùng vào DB giả lập bằng tên người dùng làm khóa

    try:
        # Tạo các tùy chọn đăng ký và trạng thái (chứa challenge)
        # Đối số credentials cho fido2.server.register_begin mong đợi các giá trị (AttestedCredentialData)
        # của các thông tin xác thực đã đăng ký cho người dùng này để loại trừ chúng.
        # Đối với đăng ký mới, đó là một danh sách rỗng.
        registration_options_cbor, state = fido2_server.register_begin(
            user_id={"id": user.user_id, "name": user.username, "displayName": user.display_name},
            credentials=list(user.credentials.values()), # Truyền các thông tin xác thực hiện có nếu có
            user_verification="preferred" # Ưu tiên xác minh người dùng (mã PIN/sinh trắc học)
        )
        # Lưu challenge an toàn phía máy chủ với TTL, liên kết với tên người dùng của người dùng
        store_challenge(registration_challenges, username, state.challenge)

        # Trả về các tùy chọn dưới dạng JSON (từ điển được giải mã CBOR) cho API WebAuthn phía máy khách
        return RegisterStartResponse(
            challenge=_base64url_encode_unpadded(state.challenge),
            options=cbor.decode(registration_options_cbor)
        )
    except Exception as e:
        # Ghi nhật ký lỗi cụ thể để gỡ lỗi phía máy chủ
        print(f"Lỗi khi tạo tùy chọn đăng ký cho {username}: {e}")
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "REGISTRATION_INIT_FAILED",
            "Không thể bắt đầu đăng ký passkey. Vui lòng thử lại."
        )

@router.post("/register/complete", response_model=AuthSuccessResponse,
             summary="Hoàn tất đăng ký Passkey")
async def register_complete(request_data: RegisterCompleteRequest, request: Request):
    """
    Xác minh phản hồi đăng ký WebAuthn được gửi bởi máy khách sau khi tạo passkey.
    Nếu thành công, dữ liệu thông tin xác thực passkey mới sẽ được lưu trữ.
    """
    username = request_data.username
    attestation_response = request_data.credential # Phản hồi xác thực WebAuthn thô của máy khách

    user = get_user_by_username(username)
    if not user:
        raise raise_api_error(
            status.HTTP_404_NOT_FOUND,
            "USER_NOT_FOUND",
            f"Không tìm thấy người dùng '{username}' để xác minh đăng ký."
        )

    # Truy xuất và xóa challenge đã lưu trữ để ngăn chặn sử dụng lại (challenge dùng một lần)
    expected_challenge_bytes = retrieve_and_validate_challenge(registration_challenges, username)

    # Lấy origin từ tiêu đề yêu cầu để xác minh với RP_ORIGINS
    request_origin = request.headers.get("Origin")
    if not request_origin or request_origin not in RP_ORIGINS:
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "INVALID_ORIGIN",
            "Origin yêu cầu không khớp với các origin của Relying Party đã cấu hình."
        )

    try:
        # Xác minh phản hồi đăng ký của máy khách bằng thư viện fido2
        # auth_data sẽ là một đối tượng AttestedCredentialData
        auth_data = fido2_server.register_complete(
            expected_challenge_bytes,
            attestation_response, # Đây là đối tượng thông tin xác thực được giải mã của máy khách (dict)
            origin=request_origin,
            rp_id=RP_ID
        )

        # Lưu trữ chi tiết thông tin xác thực mới vào cơ sở dữ liệu
        # Đối với đăng ký, AttestedCredentialData không chứa sign_count.
        # sign_count ban đầu thường là 0.
        new_credential = WebAuthnCredential(
            credential_id=auth_data.credential_id,
            attested_credential_data=auth_data,
            sign_count=0, # sign_count ban đầu cho thông tin xác thực mới
            user_id=user.user_id
        )
        user.credentials[new_credential.credential_id] = new_credential

        return AuthSuccessResponse(
            message="Passkey đã đăng ký thành công!",
            username=username,
            user_id=_base64url_encode_unpadded(user.user_id)
        )
    except Exception as e:
        # CHƯƠNG VI: Cung cấp thông báo lỗi có cấu trúc, ẩn stack trace thô.
        print(f"Đăng ký WebAuthn thất bại cho {username}: {e}") # Ghi nhật ký lỗi chi tiết phía máy chủ
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "REGISTRATION_VERIFICATION_FAILED",
            "Đăng ký passkey thất bại. Phản hồi hoặc dữ liệu không hợp lệ. Vui lòng thử lại."
        )

@router.post("/login/start", response_model=LoginStartResponse,
             summary="Bắt đầu đăng nhập Passkey")
async def login_start(request_data: LoginStartRequest):
    """
    Tạo các tùy chọn xác thực WebAuthn (một challenge và các thông tin xác thực được phép) cho máy khách.
    Máy khách sử dụng các tùy chọn này để đăng nhập bằng một passkey hiện có.
    """
    username = request_data.username

    user = get_user_by_username(username)
    if not user:
        raise raise_api_error(
            status.HTTP_404_NOT_FOUND,
            "USER_NOT_FOUND",
            f"Không tìm thấy người dùng '{username}'. Vui lòng đăng ký tài khoản trước."
        )

    if not user.credentials:
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "NO_PASSKEYS_REGISTERED",
            f"Không có passkey nào được đăng ký cho người dùng '{username}'. Vui lòng đăng ký một passkey trước."
        )

    try:
        # Tạo các tùy chọn xác thực và trạng thái (chứa challenge)
        # fido2.server.authenticate_begin mong đợi một danh sách các đối tượng AttestedCredentialData
        authentication_options_cbor, state = fido2_server.authenticate_begin(
            list(user.credentials.values()), # Cung cấp tất cả các thông tin xác thực đã lưu trữ cho người dùng này
            user_verification="preferred" # Ưu tiên xác minh người dùng (mã PIN/sinh trắc học)
        )
        # Lưu challenge an toàn phía máy chủ với TTL
        store_challenge(authentication_challenges, username, state.challenge)

        # Trả về các tùy chọn dưới dạng JSON (từ điển được giải mã CBOR) cho API WebAuthn phía máy khách
        return LoginStartResponse(
            challenge=_base64url_encode_unpadded(state.challenge),
            options=cbor.decode(authentication_options_cbor)
        )
    except Exception as e:
        print(f"Lỗi khi tạo tùy chọn xác thực cho {username}: {e}")
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "AUTHENTICATION_INIT_FAILED",
            "Không thể bắt đầu đăng nhập passkey. Vui lòng thử lại."
        )

@router.post("/login/complete", response_model=AuthSuccessResponse,
             summary="Hoàn tất đăng nhập Passkey")
async def login_complete(request_data: LoginCompleteRequest, request: Request):
    """
    Xác minh phản hồi xác thực WebAuthn được gửi bởi máy khách sau khi đăng nhập passkey.
    Nếu thành công, người dùng sẽ được xác thực.
    """
    username = request_data.username
    assertion_response = request_data.credential # Phản hồi xác thực WebAuthn thô của máy khách

    user = get_user_by_username(username)
    if not user:
        raise raise_api_error(
            status.HTTP_404_NOT_FOUND,
            "USER_NOT_FOUND",
            f"Không tìm thấy người dùng '{username}' hoặc quá trình xác thực chưa được bắt đầu."
        )

    # Truy xuất và xóa challenge đã lưu trữ
    expected_challenge_bytes = retrieve_and_validate_challenge(authentication_challenges, username)

    # Lấy origin từ tiêu đề yêu cầu để xác minh với RP_ORIGINS
    request_origin = request.headers.get("Origin")
    if not request_origin or request_origin not in RP_ORIGINS:
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "INVALID_ORIGIN",
            "Origin yêu cầu không khớp với các origin của Relying Party đã cấu hình."
        )

    try:
        # Trích xuất ID thông tin xác thực từ phản hồi xác thực của máy khách
        # Trường 'id' trong phản hồi thông tin xác thực là chuỗi được mã hóa Base64URL
        credential_id_from_response_str = assertion_response["id"]
        credential_id_from_response_bytes = _base64url_decode_padded(credential_id_from_response_str)

        # Truy xuất thông tin xác thực đã lưu trữ *cụ thể* bằng ID từ phản hồi của máy khách.
        # Đây là cách đúng đắn, tránh vòng lặp kém hiệu quả trong bản nháp.
        stored_credential = user.credentials.get(credential_id_from_response_bytes)

        # Kiểm tra bảo mật quan trọng: Đảm bảo thông tin xác thực được tìm thấy thuộc về người dùng đang xác thực.
        if not stored_credential or stored_credential.user_id != user.user_id:
            raise raise_api_error(
                status.HTTP_400_BAD_REQUEST,
                "CREDENTIAL_NOT_FOUND_FOR_USER",
                "Không tìm thấy thông tin xác thực hoặc không thuộc về người dùng này."
            )

        # Xác minh phản hồi xác thực bằng thư viện fido2
        # auth_data ở đây sẽ là một đối tượng AuthenticatorData chứa sign_count mới
        auth_data: AuthenticatorData = fido2_server.authenticate_complete(
            expected_challenge_bytes,
            assertion_response,
            stored_credential.attested_credential_data, # Đây là AttestedCredentialData đã lưu trữ từ đăng ký
            stored_credential.sign_count,              # Quan trọng: truyền sign_count hiện tại để ngăn chặn tấn công replay
            origin=request_origin,
            rp_id=RP_ID
        )

        # QUAN TRỌNG: Cập nhật sign_count trong cơ sở dữ liệu để ngăn chặn tấn công replay.
        # Kiểm tra này được thực hiện bởi `authenticate_complete`, nhưng sign_count mới PHẢI được lưu trữ
        # trong một hoạt động nguyên tử để đảm bảo bảo mật.
        stored_credential.sign_count = auth_data.sign_count
        # Trong một DB thực tế, bạn sẽ gọi `db.session.commit()` hoặc tương tự để lưu cập nhật.

        # Nếu xác minh thành công, người dùng được xác thực.
        # Trong một ứng dụng thực tế, bạn sẽ tạo và trả về một mã thông báo phiên an toàn (ví dụ: JWT)
        # cho người dùng, cho phép họ truy cập các tài nguyên được bảo vệ.
        return AuthSuccessResponse(
            message="Đăng nhập thành công qua passkey!",
            username=username,
            user_id=_base64url_encode_unpadded(user.user_id)
        )
    except Exception as e:
        # CHƯƠNG VI: Cung cấp thông báo lỗi có cấu trúc, ẩn stack trace thô.
        print(f"Xác thực WebAuthn thất bại cho {username}: {e}") # Ghi nhật ký lỗi chi tiết phía máy chủ
        raise raise_api_error(
            status.HTTP_400_BAD_REQUEST,
            "AUTHENTICATION_VERIFICATION_FAILED",
            "Xác thực passkey thất bại. Phản hồi, thông tin xác thực hoặc chữ ký không hợp lệ. Vui lòng thử lại."
        )