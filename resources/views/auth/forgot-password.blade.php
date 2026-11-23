@extends('layout.guest')

@section('title')
    <title>{{ __('auth.lost-password') }} - {{ config('other.title') }}</title>
@endsection

@section('content')
    <section class="auth-form">
        <form class="auth-form__form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <a class="auth-form__branding" href="{{ route('home.index') }}">
                <i class="fal fa-tv-retro"></i>
                <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
            </a>
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
            @if (Session::has('status'))
                <ul class="auth-form__errors">
                    @if (Session::has('status'))
                        <li class="auth-form__error">{{ Session::get('status') }}</li>
                    @endif
                </ul>
            @endif
        </form>
    </section>
@endsection
