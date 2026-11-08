@extends('layout.guest')

@section('title')
    <title>{{ __('auth.verify-email') }} - {{ config('other.title') }}</title>
@endsection

@section('content')
    <section class="auth-form">
        <div class="auth-form__form">
            <a class="auth-form__branding" href="{{ route('home.index') }}">
                <i class="fal fa-tv-retro"></i>
                <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
            </a>
            <ul class="auth-form__important-infos">
                <li class="auth-form__important-info">Almost done...</li>
                <li class="auth-form__important-info">
                    We sent an email to {{ auth()->user()->email }}.
                </li>
                <li class="auth-form__important-info">
                    Please click on the verification button in order to activate your account.
                </li>
            </ul>
            @if (config('captcha.enabled'))
                @hiddencaptcha
            @endif

            <details class="auth-form__dropdown">
                <summary class="auth-form__dropdown-text">Didn't receive an email?</summary>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <p>If you didn't receive an email, you can request one every five minutes.</p>
                    <button
                        class="auth-form__primary-button"
                        style="margin-top: 16px; margin-bottom: 36px"
                    >
                        Resend verification email
                    </button>
                </form>

                <details class="auth-form__dropdown">
                    <summary class="auth-form__dropdown-text">
                        Wrong address or mailbox bouncing?
                    </summary>
                    <form
                        method="GET"
                        action="{{ route('users.email.edit', ['user' => auth()->user()]) }}"
                    >
                        <p>
                            If you no longer have access to this address or made a typo, you can
                            update it below.
                        </p>
                        <button class="auth-form__primary-button" style="margin-top: 16px">
                            Change email
                        </button>
                    </form>
                </details>
            </details>
            @if (Session::has('status'))
                <ul class="auth-form__errors">
                    @if (session('status') == 'verification-link-sent')
                        <li class="auth-form__error">
                            {{ __('auth.email-verification-link') }}
                        </li>
                    @endif
                </ul>
            @endif
        </div>
    </section>
@endsection
