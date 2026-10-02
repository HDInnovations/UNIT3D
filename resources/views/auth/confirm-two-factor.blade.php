@extends('layout.with-main')

@section('title')
    <title>{{ __('auth.two-factor-confirmation') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('auth.two-factor-confirmation') }} - {{ config('other.title') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('auth.two-factor-confirmation') }}
    </li>
@endsection

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('auth.two-factor-confirmation') }}</h2>
        <div class="panel__body">
            <form class="form" action="{{ route('two-factor.confirm.store') }}" method="POST">
                @csrf
                <p>{{ __('auth.two-factor-confirm-desc') }}</p>
                <p class="form__group">
                    <input
                        id="code"
                        class="form__text"
                        autocomplete="one-time-code"
                        autofocus
                        inputmode="numeric"
                        name="code"
                        autocapitalize="off"
                        autocorrect="off"
                        spellcheck="false"
                        type="tel"
                    />
                    <label class="form__label form__label--floating" for="code">
                        {{ __('auth.code') }}
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
