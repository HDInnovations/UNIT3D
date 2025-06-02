<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8" />
        <title>{{ __('auth.verify-email') }} - {{ config('other.title') }}</title>
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
                    action="{{ route('verification.send') }}"
                >
                    @csrf
                    <a class="auth-form__branding" href="{{ route('home.index') }}">
                        <i class="fal fa-tv-retro"></i>
                        <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
                    </a>
                    <ul class="auth-form__important-infos">
                        <li class="auth-form__important-info">Almost done...</li>
                        <li class="auth-form__important-info">
                            Click the verification link sent to your email to activate your account.
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
                    @if (config('captcha.enabled'))
                        @captcha
                    @endif

                    <details class="auth-form__dropdown">
                        <summary class="auth-form__dropdown-text">Having issues?</summary>
                        <button class="auth-form__primary-button">Resend verification email</button>
                    </details>
                    @if (Session::has('errors') || Session::has('status'))
                        <ul class="auth-form__errors">
                            @foreach ($errors->all() as $error)
                                <li class="auth-form__error">{{ $error }}</li>
                            @endforeach

                            @if (session('status') == 'verification-link-sent')
                                <li class="auth-form__error">
                                    {{ __('auth.email-verification-link') }}
                                </li>
                            @endif
                        </ul>
                    @endif
                </form>
            </section>
        </main>
    </body>
</html>
