<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('user.two-step-auth.security-keys.title') }}</h2>
    </header>
    <div class="panel__body">
        <div>
            <span class="text-muted">
                {{ __('user.two-step-auth.security-keys.description') }}
            </span>
        </div>

        @if ($this->hasSecurityKeys())
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('common.created-at') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->securityKeys() as $key)
                            <tr>
                                <td>{{ $key->name }}</td>
                                <td>{{ $key->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <button
                                        class="form__button form__button--text"
                                        wire:click="deleteKey({{ $key->id }})"
                                        wire:confirm="{{ __('user.two-step-auth.security-keys.delete-confirm') }}"
                                    >
                                        {{ __('common.delete') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-warning">
                {{ __('user.two-step-auth.security-keys.no-keys') }}
            </p>
        @endif

        @if ($showRegisterForm)
            <div
                class="security-key-register"
                x-data="webauthnRegister(@entangle('publicKeyOptions'))"
                x-init="register"
            >
                <p class="text-info">
                    {{ __('user.two-step-auth.security-keys.register-prompt') }}
                </p>

                <div class="form__group">
                    <label for="keyName" class="form__label">
                        {{ __('user.two-step-auth.security-keys.key-name') }}
                    </label>
                    <input
                        id="keyName"
                        class="form__text"
                        type="text"
                        wire:model.live="keyName"
                        placeholder="{{ __('user.two-step-auth.security-keys.key-name-placeholder') }}"
                    />
                </div>

                <div class="form__actions">
                    <button
                        class="form__button form__button--filled"
                        type="button"
                        wire:click="cancelRegistration"
                    >
                        {{ __('common.cancel') }}
                    </button>
                </div>
            </div>
        @else
            <div class="form__actions" style="margin-top: 1rem">
                <button
                    class="form__button form__button--filled"
                    type="button"
                    wire:click="startRegistration"
                    wire:loading.attr="disabled"
                >
                    {{ __('user.two-step-auth.security-keys.add-key') }}
                </button>
            </div>
        @endif
    </div>
</section>

@script
    <script>
        Alpine.data('webauthnRegister', (publicKeyOptions) => ({
            publicKeyOptions: publicKeyOptions,

            async register() {
                if (!window.PublicKeyCredential) {
                    alert('{{ __('user.two-step-auth.security-keys.not-supported') }}');
                    $wire.cancelRegistration();
                    return;
                }

                try {
                    const publicKey = JSON.parse(this.publicKeyOptions);

                    // Decode challenge
                    publicKey.challenge = this.base64UrlDecode(publicKey.challenge);

                    // Decode user.id
                    if (publicKey.user && publicKey.user.id) {
                        publicKey.user.id = this.base64UrlDecode(publicKey.user.id);
                    }

                    // Decode excludeCredentials
                    if (publicKey.excludeCredentials) {
                        publicKey.excludeCredentials = publicKey.excludeCredentials.map((cred) => ({
                            ...cred,
                            id: this.base64UrlDecode(cred.id),
                        }));
                    }

                    const credential = await navigator.credentials.create({ publicKey });

                    if (!credential) {
                        throw new Error('No credential returned');
                    }

                    const credentialData = {
                        id: credential.id,
                        rawId: this.base64UrlEncode(credential.rawId),
                        type: credential.type,
                        response: {
                            clientDataJSON: this.base64UrlEncode(
                                credential.response.clientDataJSON,
                            ),
                            attestationObject: this.base64UrlEncode(
                                credential.response.attestationObject,
                            ),
                        },
                    };

                    $wire.registerKey(credentialData);
                } catch (error) {
                    console.error('WebAuthn registration error:', error);
                    $wire.cancelRegistration();
                }
            },

            base64UrlDecode(input) {
                // Replace URL-safe characters with standard Base64 characters
                input = input.replace(/-/g, '+').replace(/_/g, '/');

                // Pad with = if necessary
                const pad = input.length % 4;
                if (pad) {
                    input += '='.repeat(4 - pad);
                }

                // Decode base64 to binary string
                const binaryString = atob(input);

                // Convert binary string to Uint8Array
                const bytes = new Uint8Array(binaryString.length);
                for (let i = 0; i < binaryString.length; i++) {
                    bytes[i] = binaryString.charCodeAt(i);
                }
                return bytes.buffer;
            },

            base64UrlEncode(buffer) {
                const bytes = new Uint8Array(buffer);
                let binary = '';
                for (let i = 0; i < bytes.length; i++) {
                    binary += String.fromCharCode(bytes[i]);
                }
                return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
            },
        }));
    </script>
@endscript
