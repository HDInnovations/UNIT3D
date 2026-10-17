@extends('layout.guest')

@section('title')
    <title>{{ __('auth.lost-password') }} - {{ config('other.title') }}</title>
@endsection

@section('content')
    <section class="auth-form">
        <form class="auth-form__form" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}" />
            <input type="hidden" name="email" value="{{ $email }}" />
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
        </form>
    </section>
@endsection
