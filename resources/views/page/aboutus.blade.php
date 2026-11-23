@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('common.about') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('common.about') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('common.about') }}
    </li>
@endsection

@section('page', 'page__about-us--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('page.aboutus-header') }}</h2>
        <div class="panel__body">
            <h3>{{ __('page.aboutus-welcome') }} {{ config('other.title') }}</h3>
            <p>{{ __('page.aboutus-welcome-desc', ['title' => config('other.title')]) }}</p>

            <h4>
                <i class="{{ config('other.font-awesome') }} fa-globe" aria-hidden="true"></i>
                {{ __('page.aboutus-advantage') }} {{ config('other.title') }}
                <i class="{{ config('other.font-awesome') }} fa-globe" aria-hidden="true"></i>
            </h4>
            <ol class="wrapper">
                <li>{{ __('page.aboutus-advantage1') }}</li>
                <li>{{ __('page.aboutus-advantage2') }}</li>
                <li>{{ __('page.aboutus-advantage3') }}</li>
                <li>{{ __('page.aboutus-advantage4', ['title' => config('other.title')]) }}</li>
                <li>{{ __('page.aboutus-advantage5') }}</li>
            </ol>
            <h4>
                <i class="{{ config('other.font-awesome') }} fa-globe" aria-hidden="true"></i>
                {{ __('page.aboutus-rules') }}
                <i class="{{ config('other.font-awesome') }} fa-globe" aria-hidden="true"></i>
            </h4>
            <ol>
                <li>{{ __('page.aboutus-rules1') }}</li>
                <li>{{ __('page.aboutus-rules2') }}</li>
                <li>{{ __('page.aboutus-rules3', ['title' => config('other.title')]) }}</li>
            </ol>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.contact') }}</h2>
        <div class="panel__body">
            <p class="form__group form__group--horizontal">
                <a
                    href="{{ route('contact.index') }}"
                    class="form__button form__button--filled form__button--centered"
                >
                    {{ __('common.contact') }} {{ config('other.title') }}
                </a>
            </p>
        </div>
    </section>
@endsection
