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
    <li class="breadcrumbV2">
        {{ $article->title }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-article--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }} {{ __('common.article') }}: {{ $article->title }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('staff.articles.update', ['article' => $article]) }}"
            >
                @csrf
                <p class="form__group">
                    <input
                        type="text"
                        name="title"
                        id="title"
                        class="form__text"
                        value="{{ $article->title }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="title">
                        {{ __('common.title') }}
                    </label>
                </p>
                <p class="form__group">
                    <label for="image" class="form__label">{{ __('common.image') }}</label>
                    <input class="form__file" type="file" name="image" id="image" />
                </p>
                @livewire('bbcode-input', ['name' => 'content', 'label' => __('content'), 'required' => true, 'content' => $article->content ])
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.save') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
