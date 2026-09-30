<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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
                <form class="auth-form__form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <a class="auth-form__branding" href="{{ route('home.index') }}">
                        <img src="{{ url('/img/vltava-mark.svg') }}" alt="" style="height: 42px" />
                        <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
                    </a>
                    @if (Session::has('warning') || Session::has('success') || Session::has('info'))
                        <ul class="auth-form__important-infos">
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
                    @if (config('captcha.enabled'))
                        @hiddencaptcha
                    @endif

                    <button class="auth-form__primary-button">
                        {{ __('auth.password-reset') }}
                    </button>
                    @if (Session::has('errors') || Session::has('status'))
                        <ul class="auth-form__errors">
                            @foreach ($errors->all() as $error)
                                <li class="auth-form__error">{{ $error }}</li>
                            @endforeach

                            @if (Session::has('status'))
                                <li class="auth-form__error">{{ Session::get('status') }}</li>
                            @endif
                        </ul>
                    @endif
                </form>
            </section>
        </main>
    </body>
</html>
