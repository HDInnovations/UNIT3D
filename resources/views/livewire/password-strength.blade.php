<div style="display: contents; background-color: inherit">
    <p class="form__group">
        <input
            id="new_password"
            class="form__text"
            autocomplete="new-password"
            minlength="12"
            name="new_password"
            placeholder=" "
            required
            type="password"
            value="{{ old('new_password') }}"
            wire:model.live="password"
        />
        <label class="form__label form__label--floating" for="new_password">
            {{ __('livewire-interface.new-password') }}
        </label>
    </p>
    <p class="form__group">
        <input
            id="new_password_confirmation"
            class="form__text"
            autocomplete="new-password"
            minlength="12"
            name="new_password_confirmation"
            placeholder=" "
            required
            type="password"
            value="{{ old('new_password') }}"
        />
        <label class="form__label form__label--floating" for="new_password_confirmation">
            {{ __('livewire-interface.repeat-password') }}
        </label>
    </p>
    @php
        $strengthKey = match (true) {
            $strengthScore >= 4 => 'livewire-interface.password-strength-strong',
            $strengthScore === 3 => 'livewire-interface.password-strength-good',
            $strengthScore === 2 => 'livewire-interface.password-strength-fair',
            default => 'livewire-interface.password-strength-weak',
        };
    @endphp
    <p class="form__group">
        <label class="form__label" for="password_strength">
            {{ __('livewire-interface.password-strength-label') }}
            <b>{{ __($strengthKey) }}</b>
        </label>
        <meter
            id="password_strength"
            class="form__meter"
            min="0"
            max="4"
            value="{{ $strengthScore }}"
        >
            {{ __($strengthKey) }}
        </meter>
    </p>
</div>
