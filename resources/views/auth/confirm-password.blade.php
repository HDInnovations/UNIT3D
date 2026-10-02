@extends('layout.with-main')

@section('title')
    <title>{{ __('auth.password-confirmation') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('auth.password-confirmation') }} - {{ config('other.title') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('auth.password-confirmation') }}
    </li>
@endsection

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('auth.password-confirmation') }}</h2>
        <div class="panel__body">
            <form class="form" action="{{ route('password.confirm.store') }}" method="POST">
                @csrf
                <p>{{ __('auth.password-confirm-desc') }}</p>
                <p class="form__group">
                    <input
                        type="password"
                        class="form__text"
                        id="password"
                        name="password"
                        required
                    />
                    <label class="form__label form__label--floating" for="password">
                        {{ __('auth.password') }}
                    </label>
                    @error('error')
                        <span class="form__hint">{{ $error }}</span>
                    @enderror
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
