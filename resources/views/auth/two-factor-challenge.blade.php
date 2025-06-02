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
        <main x-data="{ recovery: false, entered: false }">
            <section class="auth-form">
                <header class="auth-form__header">
                    <button
                        class="auth-form__header-item"
                        x-on:click="
                            recovery = false;
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
                            $nextTick(() => {
                                $refs.recovery_code.focus();
                            })
                        "
                    >
                        {{ __('auth.recovery-code') }}
                    </button>
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
                        <li class="auth-form__important-info" x-show="!recovery">
                            {{ __('auth.enter-totp') }}
                        </li>
                        <li class="auth-form__important-info" x-cloak x-show="recovery">
                            {{ __('auth.enter-recovery') }}
                        </li>
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
                    <p class="auth-form__text-input-group" x-show="! recovery">
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
                            x-bind:required="!recovery"
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
                            x-bind:required="recovery"
                            type="text"
                            x-ref="recovery_code"
                        />
                    </p>
                    @if (config('captcha.enabled'))
                        @captcha
                    @endif

                    <button
                        class="auth-form__primary-button"
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
    </body>
</html>
