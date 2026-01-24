<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use LaravelWebauthn\Facades\Webauthn;
use Exception;

class WebAuthnTwoFactorController extends Controller
{
    /**
     * Get WebAuthn authentication options for the pending 2FA user.
     */
    public function options(Request $request): JsonResponse
    {
        $userId = Session::get('login.id');

        if ($userId === null) {
            return response()->json(['error' => __('No pending authentication')], 401);
        }

        /** @var User|null $user */
        $user = User::find($userId);

        if ($user === null) {
            return response()->json(['error' => __('User not found')], 404);
        }

        if (! $user->webauthnKeys()->exists()) {
            return response()->json(['error' => __('No security keys registered')], 400);
        }

        $publicKeyOptions = Webauthn::prepareAssertion($user);

        Session::put('webauthn.assertion_options', serialize($publicKeyOptions->data));

        return response()->json($publicKeyOptions->jsonSerialize());
    }

    /**
     * Verify the WebAuthn assertion and complete 2FA login.
     */
    public function verify(Request $request): JsonResponse
    {
        $userId = Session::get('login.id');

        if ($userId === null) {
            return response()->json(['error' => __('No pending authentication')], 401);
        }

        /** @var User|null $user */
        $user = User::find($userId);

        if ($user === null) {
            return response()->json(['error' => __('User not found')], 404);
        }

        if (! Session::has('webauthn.assertion_options')) {
            return response()->json(['error' => __('No pending assertion. Please try again.')], 400);
        }

        try {
            $valid = Webauthn::validateAssertion($user, $request->all());

            if (! $valid) {
                return response()->json(['error' => __('Invalid security key response')], 401);
            }

            Session::forget('webauthn.assertion_options');

            $remember = Session::pull('login.remember', false);

            Session::forget('login.id');

            Auth::login($user, $remember);

            $request->session()->regenerate();

            return response()->json([
                'success'  => true,
                'callback' => route('home.index'),
            ]);
        } catch (Exception $e) {
            Log::error('WebAuthn verification error', [
                'message' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            return response()->json([
                'error' => __('Authentication failed'),
            ], 401);
        }
    }
}
