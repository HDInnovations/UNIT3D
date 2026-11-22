@if (config('other.freeleech') == true || config('other.invite-only') == false || config('other.doubleup') == true)
    <section class="alert special-event-alert" x-data="timer()" x-init="start()">
        <div class="alert__content">
            <strong>
                @if (config('other.freeleech') == true)
                        🌐 {{ __('common.freeleech_activated') }} 🌐
                @endif

                @if (config('other.invite-only') == false)
                    🌐 {{ __('common.openreg_activated') }} 🌐
                @endif

                @if (config('other.doubleup') == true)
                        🌐 {{ __('common.doubleup_activated') }} 🌐
                @endif
            </strong>
            <div>
                <span x-text="days">00</span>
                <span>{{ __('common.day') }}</span>
                <span x-text="hours">00</span>
                <span>{{ __('common.hour') }}</span>
                <span x-text="minutes">00</span>
                <span>{{ __('common.minute') }}</span>
                <span>{{ __('common.and') }}</span>
                <span x-text="seconds">00</span>
                <span>{{ __('common.second') }}</span>
            </div>
        </div>
    </section>
@endif
