<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('livewire-interface.two-factor-authentication') }}</h2>
    </header>
    <div class="panel__body">
        @if ($this->enabled)
            @if ($showingConfirmation)
                <span class="text-warning">
                    {{ __('livewire-interface.finish-enabling-2fa') }}
                </span>
            @else
                <span class="text-success">
                    {{ __('livewire-interface.2fa-enabled') }}
                </span>
            @endif
        @else
            <span class="text-danger">
                {{ __('livewire-interface.2fa-not-enabled') }}
            </span>
        @endif

        <div>
            <span class="text-muted">
                {{ __('livewire-interface.2fa-explanation') }}
            </span>
        </div>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div>
                    <p class="text-info">
                        @if ($showingConfirmation)
                            {{ __('livewire-interface.2fa-finish-scan-qr') }}
                        @else
                            {{ __('livewire-interface.2fa-enabled-scan-qr') }}
                        @endif
                    </p>
                </div>

                <div class="twoStep__qrCode">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div>
                    <p>
                        {{ __('livewire-interface.setup-key') }}:
                        {{ decrypt($this->user->two_factor_secret) }}
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div>
                        <label for="code" value="{{ __('common.code') }}"></label>

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
                        {{ __('livewire-interface.recovery-codes-explanation') }}
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
                    {{ __('common.enable') }}
                </button>
            @else
                @if ($showingRecoveryCodes)
                    <button
                        class="form__button form__button--filled"
                        wire:click="regenerateRecoveryCodes"
                    >
                        {{ __('livewire-interface.regenerate-recovery-codes') }}
                    </button>
                @elseif ($showingConfirmation)
                    <button
                        class="form__button form__button--filled"
                        type="button"
                        wire:click="confirmTwoFactorAuthentication"
                        wire:loading.attr="disabled"
                    >
                        {{ __('livewire-interface.confirm') }}
                    </button>
                @else
                    <button
                        class="form__button form__button--filled"
                        wire:click="showRecoveryCodes"
                    >
                        {{ __('livewire-interface.show-recovery-codes') }}
                    </button>
                @endif

                @if ($showingConfirmation)
                    <button
                        class="form__button form__button--filled"
                        wire:click="disableTwoFactorAuthentication"
                        wire:loading.attr="disabled"
                    >
                        {{ __('common.cancel') }}
                    </button>
                @else
                    <button
                        class="form__button form__button--filled"
                        wire:click="disableTwoFactorAuthentication"
                        wire:loading.attr="disabled"
                    >
                        {{ __('common.disable') }}
                    </button>
                @endif
            @endif
        </div>
    </div>
</section>
