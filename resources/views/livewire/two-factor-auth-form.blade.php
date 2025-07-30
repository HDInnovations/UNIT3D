<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('Two Factor Authentication') }}</h2>
    </header>
    <div class="panel__body">
        @if ($this->enabled)
            @if ($showingConfirmation)
                <span class="text-warning">
                    {{ __('Finish enabling two factor authentication.') }}
                </span>
            @else
                <span class="text-success">
                    {{ __('You have enabled two factor authentication.') }}
                </span>
            @endif
        @else
            <span class="text-danger">
                {{ __('You have not enabled two factor authentication.') }}
            </span>
        @endif

        <div>
            <span class="text-muted">
                {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from a synchronized 2fa app such as Google Authenticator, Authy, BitWarden, etc.') }}
            </span>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div>
                    <p class="text-info">
                        @if ($showingConfirmation)
                            {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                        @else
                            {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                        @endif
                    </p>
                </div>

                <div class="twoStep__qrCode">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div>
                    <p>{{ __('Setup Key') }}: {{ decrypt($this->user->two_factor_secret) }}</p>
                </div>

                @if ($showingConfirmation)
                    <div>
                        <label for="code" value="{{ __('Code') }}"></label>

                        <input
                            id="code"
                            name="code"
                            class="form__text"
                            type="text"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            wire:model.live="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication"
                        />

                        @error('code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="panel__body">
                    <span class="text-danger">
                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                    </span>
                    {{-- format-ignore-start --}}
                    <pre>
                        @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </pre>
                    {{-- format-ignore-end --}}
                </div>
            @endif
        @endif

        <div>
            @if (! $this->enabled)
                <button
                    class="form__button form__button--filled"
                    wire:click="enableTwoFactorAuthentication"
                    wire:loading.attr="disabled"
                >
                    {{ __('Enable') }}
                </button>
            @else
                @if ($showingRecoveryCodes)
                    <button
                        class="form__button form__button--filled"
                        wire:click="regenerateRecoveryCodes"
                    >
                        {{ __('Regenerate Recovery Codes') }}
                    </button>
                    <button
                        class="form__button form__button--filled"
                        x-data="recovery-codes"
                        x-on:click.stop="copy"
                    >
                        {{ __('Copy Recovery Codes') }}
                    </button>
                    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
                        Alpine.data('recovery-codes', () => ({
                            copy() {
                                let text = document.createElement('textarea');
                                text.innerHTML = JSON.parse(atob('{{ base64_encode(decrypt($this->user->two_factor_recovery_codes)) }}')).join("\n");
                                navigator.clipboard.writeText(text.value);
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    icon: 'success',
                                    title: 'Copied to clipboard!',
                                });
                            },
                        }));
                    </script>
                @elseif ($showingConfirmation)
                    <button
                        class="form__button form__button--filled"
                        type="button"
                        wire:click="confirmTwoFactorAuthentication"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Confirm') }}
                    </button>
                @else
                    <button
                        class="form__button form__button--filled"
                        wire:click="showRecoveryCodes"
                    >
                        {{ __('Show Recovery Codes') }}
                    </button>
                @endif

                @if ($showingConfirmation)
                    <button
                        class="form__button form__button--filled"
                        wire:click="disableTwoFactorAuthentication"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Cancel') }}
                    </button>
                @else
                    <button
                        class="form__button form__button--filled"
                        wire:click="disableTwoFactorAuthentication"
                        wire:loading.attr="disabled"
                    >
                        {{ __('Disable') }}
                    </button>
                @endif
            @endif
        </div>
    </div>
</section>
