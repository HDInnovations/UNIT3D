@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('common.contact') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('common.contact') }} {{ config('other.title') }}." />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('about') }}" class="breadcrumb__link">
            {{ __('common.about') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.contact') }}
    </li>
@endsection

@section('page', 'page__contact')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.contact') }}</h2>
        <div class="panel__body">
            <form class="form" action="{{ route('contact.store') }}" method="POST">
                @csrf
                <p class="form__group">
                    <input
                        id="contact-name"
                        class="form__text"
                        name="contact-name"
                        placeholder=" "
                        required
                        type="text"
                        value="{{ auth()->user()->username }}"
                    />
                    <label class="form__label form__label--floating" for="contact-name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="email"
                        class="form__text"
                        name="email"
                        placeholder=" "
                        required
                        type="email"
                        value="{{ auth()->user()->email }}"
                    />
                    <label class="form__label form__label--floating" for="email">
                        {{ __('common.email') }}
                    </label>
                </p>
                <p class="form__group">
                    <textarea
                        id="message"
                        class="form__textarea"
                        name="message"
                        placeholder=" "
                        required
                    ></textarea>
                    <label class="form__label form__label--floating" for="message">
                        {{ __('common.message') }}
                    </label>
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

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.contact-header') }}</h2>
        <div class="panel__body">{{ __('common.contact-desc') }}</div>
    </section>
@endsection
