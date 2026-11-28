@extends('layout.guest')

@section('title')
    <title>{{ __('auth.signup') }} - {{ config('other.title') }}</title>
@endsection

@section('content')
    <section class="auth-form">
        <form
            class="auth-form__form"
            method="POST"
            action="{{ route('registration.store', ['code' => request()->query('code')]) }}"
        >
            @csrf
            <a class="auth-form__branding" href="{{ route('home.index') }}">
                <i class="fal fa-tv-retro"></i>
                <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
            </a>
            @if (config('other.invite-only') && ! request()->has('code'))
                <ul class="auth-form__important-infos">
                    <li class="auth-form__important-info">
                        {{ __('auth.need-invite') }}
                    </li>
                </ul>
            @else
                <p class="auth-form__text-input-group">
                    <label class="auth-form__label" for="username">
                        {{ __('auth.username') }}
                    </label>
                    <input
                        id="username"
                        class="auth-form__text-input"
                        autofocus
                        name="username"
                        required
                        type="text"
                        value="{{ old('username') }}"
                    />
                </p>
                <p class="auth-form__text-input-group">
                    <label class="auth-form__label" for="email">
                        {{ __('auth.email') }}
                    </label>
                    <input
                        id="email"
                        class="auth-form__text-input"
                        name="email"
                        required
                        type="email"
                        value="{{ old('email') }}"
                    />
                </p>
                <p class="auth-form__text-input-group">
                    <label class="auth-form__label" for="password">
                        {{ __('auth.password') }}
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
                        {{ __('auth.confirm-password') }}
                    </label>
                    <input
                        id="password_confirmation"
                        class="auth-form__text-input"
                        autocomplete="new-password"
                        name="password_confirmation"
                        required
                        type="password"
                        value="{{ old('password') }}"
                    />
                </p>
                @if (config('captcha.enabled'))
                    @hiddencaptcha
                @endif

                <button class="auth-form__primary-button">{{ __('auth.signup') }}</button>
            @endif
        </form>
    </section>
@endsection
