@extends('layout.guest')

@section('title')
    <title>{{ __('Two Factor Authentication') }} - {{ config('other.title') }}</title>
@endsection

@section('content')
    <section class="auth-form" x-data="twoFactor">
        <header class="auth-form__header">
            <button class="auth-form__header-item" x-bind="totpTab">
                {{ __('auth.totp-code') }}
            </button>
            <button class="auth-form__header-item" x-bind="recoveryTab">
                {{ __('auth.recovery-code') }}
            </button>
        </header>
        <form class="auth-form__form" method="POST" action="{{ route('two-factor.login.store') }}">
            @csrf
            <a class="auth-form__branding" href="{{ route('home.index') }}">
                <i class="fal fa-tv-retro"></i>
                <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
            </a>
            <ul class="auth-form__important-infos">
                <li class="auth-form__important-info" x-show="tab === 'totp'">
                    {{ __('auth.enter-totp') }}
                </li>
                <li class="auth-form__important-info" x-cloak x-show="tab === 'recovery'">
                    {{ __('auth.enter-recovery') }}
                </li>
                @if (Session::has('warning'))
                    <li class="auth-form__important-info">
                        Warning: {{ Session::get('warning') }}
                    </li>
                @endif

                @if (Session::has('info'))
                    <li class="auth-form__important-info">Info: {{ Session::get('info') }}</li>
                @endif

                @if (Session::has('success'))
                    <li class="auth-form__important-info">
                        Success: {{ Session::get('success') }}
                    </li>
                @endif
            </ul>
            <p class="auth-form__text-input-group" x-show="tab === 'totp'">
                <label class="auth-form__label" for="code">
                    {{ __('auth.code') }}
                </label>
                <input
                    id="code"
                    class="auth-form__text-input"
                    autocomplete="one-time-code"
                    autofocus
                    inputmode="numeric"
                    name="code"
                    autocapitalize="off"
                    autocorrect="off"
                    spellcheck="false"
                    type="tel"
                    value="{{ old('code') }}"
                    x-bind="totpCode"
                />
            </p>
            <p class="auth-form__text-input-group" x-cloak x-show="tab === 'recovery'">
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
                    type="text"
                    x-bind="recoveryCode"
                />
            </p>
            @if (config('captcha.enabled'))
                @hiddencaptcha
            @endif

            <button class="auth-form__primary-button" x-bind="submitButton">
                {{ __('auth.verify') }}
            </button>
        </form>
    </section>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('twoFactor', () => ({
                tab: 'totp',
                entered: false,
                totpTab: {
                    ['x-on:click']() {
                        this.tab = 'totp';
                        this.$nextTick(() => {
                            this.$refs.totpCode.focus();
                        });
                    },
                },
                recoveryTab: {
                    ['x-on:click']() {
                        this.tab = 'recovery';
                        this.$nextTick(() => {
                            this.$refs.recoveryCode.focus();
                        });
                    },
                },
                totpCode: {
                    ['x-ref']: 'totpCode',
                    ['x-bind:required']() {
                        return this.tab === 'totp';
                    },
                    ['x-on:input']() {
                        if (this.$el.value.length === 6) {
                            this.$el.form.submit();
                            this.entered = true;
                        }
                    },
                },
                recoveryCode: {
                    ['x-ref']: 'recoveryCode',
                    ['x-bind:required']() {
                        return this.tab === 'recovery';
                    },
                },
                submitButton: {
                    ['x-text']() {
                        return this.entered
                            ? {{ Js::from(__('auth.verifying')) }}
                            : {{ Js::from(__('auth.verify')) }};
                    },
                    ['x-bind:disabled']() {
                        return this.entered;
                    },
                },
            }));
        });
    </script>
@endsection
