<!DOCTYPE html>
<html class="no-js" lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8" />
        <title>Application - {{ config('other.title') }}</title>
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="Application" />
        <meta property="og:title" content="{{ __('auth.login') }}" />
        <meta property="og:site_name" content="{{ config('other.title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ url('/img/og.png') }}" />
        <meta property="og:description" content="{{ config('unit3d.powered-by') }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:locale" content="{{ config('app.locale') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        @vite('resources/sass/pages/_auth.scss')
    </head>
    <body>
        <main x-data="{ proofs: 2 }">
            <section class="auth-form">
                <form
                    class="auth-form__form"
                    method="POST"
                    action="{{ route('application.store') }}"
                >
                    @csrf
                    <a class="auth-form__branding" href="{{ route('home.index') }}">
                        <i class="fal fa-tv-retro"></i>
                        <span class="auth-form__site-logo">{{ \config('other.title') }}</span>
                    </a>
                    @if (config('other.application_signups'))
                        <ul class="auth-form__important-infos">
                            <li class="auth-form__important-info">
                                {{ config('other.title') }} {{ __('auth.appl-intro') }}
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
                        <p class="auth-form__select-group">
                            <label for="type" class="auth-form__label">
                                {{ __('auth.are-you') }}
                            </label>
                            <select
                                id="type"
                                class="auth-form__select"
                                name="application[type]"
                                required
                            >
                                <option class="auth-form__option" value="New To The Game" selected>
                                    {{ __('auth.newbie') }}
                                </option>
                                <option
                                    class="auth-form__option"
                                    value="Experienced With Private Trackers"
                                >
                                    {{ __('auth.veteran') }}
                                </option>
                            </select>
                        </p>
                        <p class="auth-form__text-input-group">
                            <label for="email" class="auth-form__label">
                                {{ __('auth.email') }}
                            </label>
                            <input
                                id="email"
                                type="email"
                                class="auth-form__text-input"
                                name="application[email]"
                                required
                            />
                        </p>
                        <p class="auth-form__textarea-group">
                            <label for="referrer" class="auth-form__label">
                                {{ __('auth.appl-reason', ['sitename' => config('other.title')]) }}
                            </label>
                            <textarea
                                id="referrer"
                                type="referrer"
                                class="auth-form__textarea"
                                name="application[referrer]"
                                required
                            ></textarea>
                        </p>
                        <label class="auth-form__label">Proofs</label>
                        <template x-for="proof in proofs">
                            <fieldset class="auth-form__fieldset">
                                <legend
                                    class="auth-form__legend"
                                    x-text="'Proof ' + proof"
                                ></legend>
                                <p class="auth-form__text-input-group">
                                    <label
                                        class="auth-form__label"
                                        x-bind:for="'image' + (proof - 1)"
                                    >
                                        {{ __('auth.proof-image') }}
                                    </label>
                                    <input
                                        x-bind:id="'image' + (proof - 1)"
                                        class="auth-form__text-input"
                                        x-bind:name="'images[' + (proof - 1) + '][image]'"
                                        type="url"
                                        placeholder=" "
                                        required
                                    />
                                </p>
                                <p class="auth-form__text-input-group">
                                    <label
                                        class="auth-form__label"
                                        x-bind:for="'profile' + (proof - 1)"
                                    >
                                        {{ __('auth.proof-profile') }}
                                    </label>
                                    <input
                                        x-bind:id="'profile' + (proof - 1)"
                                        class="auth-form__text-input"
                                        x-bind:name="'links[' + (proof - 1) + '][url]'"
                                        type="url"
                                        placeholder=" "
                                    />
                                </p>
                            </fieldset>
                        </template>
                        <p class="auth-form__button-container">
                            <button
                                x-on:click.prevent="proofs++"
                                class="auth-form__button--text"
                                type="button"
                            >
                                {{ __('common.add') }}
                            </button>
                            <button
                                class="auth-form__button--text"
                                x-on:click.prevent="proofs = Math.max(2, proofs - 1)"
                                type="button"
                            >
                                {{ __('common.delete') }}
                            </button>
                        </p>
                        @if (config('captcha.enabled'))
                            @captcha
                        @endif

                        <button class="auth-form__primary-button">{{ __('auth.apply') }}</button>
                        @if (Session::has('errors'))
                            <ul class="auth-form__errors">
                                @foreach ($errors->all() as $error)
                                    <li class="auth-form__error">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @else
                        <ul class="auth-form__important-infos">
                            <li class="auth-form__important-info">{{ __('auth.appl-closed') }}</li>
                            <li class="auth-form__important-info">{{ __('auth.check-later') }}</li>
                        </ul>
                    @endif
                </form>
            </section>
        </main>
        @vite('resources/js/app.js')
        @livewireScriptConfig(['nonce' => HDVinnie\SecureHeaders\SecureHeaders::nonce()])
    </body>
</html>
