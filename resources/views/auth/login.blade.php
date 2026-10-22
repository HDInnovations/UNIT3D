@extends('layout.guest')

@section('title')
    <title>{{ __('auth.login') }} - {{ config('other.title') }}</title>
@endsection

@section('content')
    <!-- Do NOT change! For Jackett support -->
    <div class="Jackett" style="display: none">{{ config('unit3d.powered-by') }}</div>
    <!-- Do NOT change! For Jackett support -->
    <section class="auth-form">
        <form class="auth-form__form" method="POST" action="{{ route('login') }}">
            @csrf
            <a class="auth-form__branding" href="{{ route('home.index') }}">
                <i class="fal fa-tv-retro"></i>
                <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
            </a>
            <p class="auth-form__text-input-group">
                <label class="auth-form__label" for="username">
                    {{ __('auth.username') }}
                </label>
                <input
                    id="username"
                    class="auth-form__text-input"
                    autocomplete="username"
                    autofocus
                    name="username"
                    pattern="[^@]*"
                    required
                    type="text"
                    value="{{ old('username') }}"
                />
            </p>
            <p class="auth-form__text-input-group">
                <label class="auth-form__label" for="password">
                    {{ __('auth.password') }}
                </label>
                <input
                    id="password"
                    class="auth-form__text-input"
                    autocomplete="current-password"
                    name="password"
                    required
                    type="password"
                />
            </p>
            <p class="auth-form__checkbox-input-group">
                <input
                    id="remember"
                    class="auth-form__checkbox-input"
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                    type="checkbox"
                />
                <label class="auth-form__label" for="remember">
                    {{ __('auth.remember-me') }}
                </label>
            </p>
            @if (config('captcha.enabled'))
                @hiddencaptcha
            @endif

            <button class="auth-form__primary-button">{{ __('auth.login') }}</button>
        </form>
        <footer class="auth-form__footer">
            @if (! config('other.invite-only'))
                <a class="auth-form__footer-item" href="{{ route('registration.create') }}">
                    {{ __('auth.signup') }}
                </a>
            @elseif (config('other.application_signups'))
                <a class="auth-form__footer-item" href="{{ route('application.create') }}">
                    {{ __('auth.apply') }}
                </a>
            @endif
            <a class="auth-form__footer-item" href="{{ route('password.request') }}">
                {{ __('auth.lost-password') }}
            </a>
        </footer>
    </section>
@endsection
