@extends('layout.with-main')

@section('title')
    <title>Articles - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.articles.index') }}" class="breadcrumb__link">
            {{ __('staff.articles') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-article--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.add') }} {{ __('common.article') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('staff.articles.store') }}"
            >
                @csrf
                <p class="form__group">
                    <input type="text" name="title" id="title" class="form__text" required />
                    <label class="form__label form__label--floating" for="title">
                        {{ __('common.title') }}
                    </label>
                </p>
                <p class="form__group">
                    <label for="image" class="form__label">{{ __('common.image') }}</label>
                    <input class="form__file" type="file" name="image" id="image" />
                </p>
                @livewire('bbcode-input', ['name' => 'content', 'label' => __('content'), 'required' => true ])
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
