<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8" />
        <title>{{ __('Two Factor Authentication') }} - {{ config('other.title') }}</title>
        <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        @vite('resources/sass/pages/_auth.scss')
    </head>
    <body>
        @php
            $user = session('login.id') ? \App\Models\User::find(session('login.id')) : null;
            $hasSecurityKeys = $user && $user->webauthnKeys()->exists();
            $hasTotpEnabled = $user && $user->two_factor_secret;
        @endphp
        {{-- Default to security key if available, otherwise TOTP code --}}
        <main x-data="{ 
            recovery: false, 
            entered: false, 
            securityKey: {{ $hasSecurityKeys ? 'true' : 'false' }}, 
            webauthnInProgress: false,
            webauthnError: null
        }">
            <section class="auth-form">
                <header class="auth-form__header">
                    @if ($hasTotpEnabled)
                        <button
                            class="auth-form__header-item"
                            x-on:click="
                                recovery = false;
                                securityKey = false;
                                $nextTick(() => {
                                    $refs.code.focus();
                                })
                            "
                        >
                            {{ __('auth.totp-code') }}
                        </button>
                        <button
                            class="auth-form__header-item"
                            x-on:click="
                                recovery = true;
                                securityKey = false;
                                $nextTick(() => {
                                    $refs.recovery_code.focus();
                                })
                            "
                        >
                            {{ __('auth.recovery-code') }}
                        </button>
                    @endif
                    @if ($hasSecurityKeys)
                        <button
                            class="auth-form__header-item"
                            x-on:click="
                                securityKey = true;
                                recovery = false;
                            "
                        >
                            {{ __('auth.security-key') }}
                        </button>
                    @endif
                </header>
                <form
                    class="auth-form__form"
                    method="POST"
                    action="{{ route('two-factor.login') }}"
                >
                    @csrf
                    <a class="auth-form__branding" href="{{ route('home.index') }}">
                        <i class="fal fa-tv-retro"></i>
                        <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
                    </a>
                    <ul class="auth-form__important-infos">
                        @if ($hasTotpEnabled)
                            <li class="auth-form__important-info" x-show="!recovery && !securityKey">
                                {{ __('auth.enter-totp') }}
                            </li>
                            <li class="auth-form__important-info" x-cloak x-show="recovery">
                                {{ __('auth.enter-recovery') }}
                            </li>
                        @endif
                        <li class="auth-form__important-info" x-cloak x-show="securityKey">
                            {{ __('auth.enter-security-key') }}
                        </li>
                        {{-- WebAuthn error message --}}
                        <li class="auth-form__important-info auth-form__error" x-cloak x-show="webauthnError" x-text="webauthnError"></li>
                        @if (Session::has('warning'))
                            <li class="auth-form__important-info">
                                Warning: {{ Session::get('warning') }}
                            </li>
                        @endif

                        @if (Session::has('info'))
                            <li class="auth-form__important-info">
                                Info: {{ Session::get('info') }}
                            </li>
                        @endif

                        @if (Session::has('success'))
                            <li class="auth-form__important-info">
                                Success: {{ Session::get('success') }}
                            </li>
                        @endif
                    </ul>
                    @if ($hasTotpEnabled)
                        <p class="auth-form__text-input-group" x-show="!recovery && !securityKey">
                            <label class="auth-form__label" for="code">
                                {{ __('auth.code') }}
                            </label>
                            <input
                                id="code"
                                class="auth-form__text-input"
                                autocomplete="one-time-code"
                                inputmode="numeric"
                                name="code"
                                autocapitalize="off"
                                autocorrect="off"
                                spellcheck="false"
                                x-bind:required="!recovery && !securityKey"
                                type="tel"
                                value="{{ old('code') }}"
                                x-on:input="
                                    if ($el.value.length === 6) {
                                        $el.form.submit();
                                        entered = true;
                                    }
                                "
                                x-ref="code"
                            />
                        </p>
                        <p class="auth-form__text-input-group" x-cloak x-show="recovery">
                            <label class="auth-form__label" for="recovery_code">
                                {{ __('Use a recovery code') }}
                            </label>
                            <input
                                id="recovery_code"
                                class="auth-form__text-input"
                                autocomplete="off"
                                name="recovery_code"
                                autocapitalize="off"
                                autocorrect="off"
                                spellcheck="false"
                                x-bind:required="recovery && !securityKey"
                                type="text"
                                x-ref="recovery_code"
                            />
                        </p>
                    @endif

                    {{-- Security Key Authentication --}}
                    @if ($hasSecurityKeys)
                        <div x-cloak x-show="securityKey" class="auth-form__security-key-section">
                            <button
                                type="button"
                                class="auth-form__primary-button"
                                x-bind:disabled="webauthnInProgress"
                                x-on:click="
                                    webauthnError = null;
                                    webauthnInProgress = true;
                                    authenticateWithSecurityKey();
                                "
                                x-text="webauthnInProgress ? @js(__('auth.verifying')) : @js(__('auth.use-security-key'))"
                            >
                                {{ __('auth.use-security-key') }}
                            </button>
                        </div>
                    @endif

                    @if (config('captcha.enabled'))
                        @hiddencaptcha
                    @endif

                    <button
                        class="auth-form__primary-button"
                        x-show="!securityKey"
                        x-text="entered ? @js(__('auth.verifying')) : @js(__('auth.verify'))"
                        x-bind:disabled="entered"
                    >
                        {{ __('auth.verify') }}
                    </button>
                    @if (Session::has('errors'))
                        <ul class="auth-form__errors">
                            @foreach ($errors->all() as $error)
                                <li class="auth-form__error">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </form>
            </section>
        </main>
        @vite('resources/js/app.js')
        @if ($hasSecurityKeys)
            <script>
                async function authenticateWithSecurityKey() {
                    const mainEl = document.querySelector('main');
                    const alpineData = Alpine.$data(mainEl);
                    
                    try {
                        // Fetch the public key options from our custom 2FA controller
                        const optionsResponse = await fetch('{{ route('two-factor.webauthn.options') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        if (!optionsResponse.ok) {
                            const error = await optionsResponse.json();
                            throw new Error(error.error || 'Failed to get authentication options');
                        }

                        const publicKey = await optionsResponse.json();

                        // Decode the challenge
                        publicKey.challenge = base64UrlDecode(publicKey.challenge);

                        // Decode allowCredentials if present
                        if (publicKey.allowCredentials) {
                            publicKey.allowCredentials = publicKey.allowCredentials.map(cred => ({
                                ...cred,
                                id: base64UrlDecode(cred.id)
                            }));
                        }

                        // Request credential from the security key
                        const credential = await navigator.credentials.get({ publicKey });

                        if (!credential) {
                            throw new Error('No credential returned');
                        }

                        // Prepare the credential data to send to the server
                        const credentialData = {
                            id: credential.id,
                            rawId: base64UrlEncode(credential.rawId),
                            type: credential.type,
                            response: {
                                clientDataJSON: base64UrlEncode(credential.response.clientDataJSON),
                                authenticatorData: base64UrlEncode(credential.response.authenticatorData),
                                signature: base64UrlEncode(credential.response.signature),
                                userHandle: credential.response.userHandle ? base64UrlEncode(credential.response.userHandle) : null
                            }
                        };

                        // Send the credential to our custom 2FA verification endpoint
                        const authResponse = await fetch('{{ route('two-factor.webauthn.verify') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(credentialData)
                        });

                        if (authResponse.ok) {
                            const result = await authResponse.json();
                            if (result.callback) {
                                window.location.href = result.callback;
                            } else {
                                window.location.href = '{{ route('home.index') }}';
                            }
                        } else {
                            const error = await authResponse.json();
                            alpineData.webauthnError = error.error || error.message || 'Authentication failed. Please try again.';
                            alpineData.webauthnInProgress = false;
                        }
                    } catch (error) {
                        console.error('WebAuthn authentication error:', error);
                        alpineData.webauthnError = error.message || 'Security key authentication failed. Please try again.';
                        alpineData.webauthnInProgress = false;
                    }
                }

                function base64UrlDecode(input) {
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
                }

                function base64UrlEncode(buffer) {
                    const bytes = new Uint8Array(buffer);
                    let binary = '';
                    for (let i = 0; i < bytes.length; i++) {
                        binary += String.fromCharCode(bytes[i]);
                    }
                    return btoa(binary)
                        .replace(/\+/g, '-')
                        .replace(/\//g, '_')
                        .replace(/=/g, '');
                }
            </script>
        @endif
    </body>
</html>