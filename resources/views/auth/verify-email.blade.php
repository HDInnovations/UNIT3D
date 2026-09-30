<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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
        <meta property="og:description" content="{{ config('other.meta_description') }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:locale" content="{{ app()->getLocale() }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @show
        <link rel="icon" href="{{ url('/img/vltava-mark.svg') }}" type="image/svg+xml" />
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
                        <img src="{{ url('/img/vltava-mark.svg') }}" alt="" style="height: 42px" />
                        <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
                    </a>
                    <ul class="auth-form__important-infos">
                        <li class="auth-form__important-info">{{ __('interface.verify-email-almost-done') }}</li>
                        <li class="auth-form__important-info">
                            {{ __('interface.verify-email-instructions') }}
                        </li>
                        @if (Session::has('warning'))
                            <li class="auth-form__important-info">
                                {{ __('interface.flash-warning', ['message' => Session::get('warning')]) }}
                            </li>
                        @endif

                        @if (Session::has('info'))
                            <li class="auth-form__important-info">
                                {{ __('interface.flash-info', ['message' => Session::get('info')]) }}
                            </li>
                        @endif

                        @if (Session::has('success'))
                            <li class="auth-form__important-info">
                                {{ __('interface.flash-success', ['message' => Session::get('success')]) }}
                            </li>
                        @endif
                    </ul>
                    @if (config('captcha.enabled'))
                        @hiddencaptcha
                    @endif

                    <details class="auth-form__dropdown">
                        <summary class="auth-form__dropdown-text">{{ __('interface.verify-email-having-issues') }}</summary>
                        <button class="auth-form__primary-button">{{ __('interface.verify-email-resend') }}</button>
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
