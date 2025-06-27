<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8" />
        <title>{{ __('auth.lost-password') }} - {{ config('other.title') }}</title>
        @section('meta')
        <meta
            name="description"
            content="{{ __('auth.login-now-on') }} {{ config('other.title') }} . {{ __('auth.not-a-member') }}"
        />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:title" content="{{ __('auth.login') }}" />
        <meta property="og:site_name" content="{{ config('other.title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ url('/img/og.png') }}" />
        <meta property="og:description" content="{{ config('unit3d.powered-by') }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:locale" content="{{ config('app.locale') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @show
        <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        @vite('resources/sass/pages/_auth.scss')
    </head>
    <body>
        <main>
            <section class="auth-form">
                <form
                    class="auth-form__form"
                    method="POST"
                    action="{{ route('password.update') }}"
                >
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}" />
                    <a class="auth-form__branding" href="{{ route('home.index') }}">
                        <i class="fal fa-tv-retro"></i>
                        <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
                    </a>
                    @if (Session::has('warning') || Session::has('success') || Session::has('info'))
                        <ul class="auth-form__important-infos">
                            @if (Session::has('warning'))
                                <li>Warning: {{ Session::get('warning') }}</li>
                            @endif

                            @if (Session::has('info'))
                                <li>Info: {{ Session::get('info') }}</li>
                            @endif

                            @if (Session::has('success'))
                                <li>Success: {{ Session::get('success') }}</li>
                            @endif
                        </ul>
                    @endif

                    <p class="auth-form__text-input-group">
                        <label class="auth-form__label" for="email">
                            {{ __('auth.email') }}
                        </label>
                        <input
                            id="email"
                            class="auth-form__text-input"
                            autofocus
                            name="email"
                            required
                            type="email"
                            value="{{ old('email') }}"
                        />
                    </p>
                    <p class="auth-form__text-input-group">
                        <label class="auth-form__label" for="password">
                            {{ __('auth.new-password') }}
                        </label>
                        <input
                            id="password"
                            class="auth-form__text-input"
                            autocomplete="new-password"
                            name="password"
                            required
                            type="password"
                            value="{{ old('password') }}"
                        />
                    </p>
                    <p class="auth-form__text-input-group">
                        <label class="auth-form__label" for="password_confirmation">
                            {{ __('auth.confirm-new-password') }}
                        </label>
                        <input
                            id="password_confirmation"
                            class="auth-form__text-input"
                            autocomplete="new-password"
                            name="password_confirmation"
                            required
                            type="password"
                            value="{{ old('password_confirmation') }}"
                        />
                    </p>
                    @if (config('captcha.enabled'))
                        @hiddencaptcha
                    @endif

                    <button class="auth-form__primary-button">
                        {{ __('auth.password-reset') }}
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
    </body>
</html>
