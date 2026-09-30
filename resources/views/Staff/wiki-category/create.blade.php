@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.wiki_categories.index') }}" class="breadcrumb__link">
            {{ __('staff-interface.wiki-categories') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-wiki-category--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff-interface.add-new-wiki-heading') }}</h2>
        <div class="panel__body">
            <form class="form" method="POST" action="{{ route('staff.wiki_categories.store') }}">
                @csrf
                <p class="form__group">
                    <input id="name" class="form__text" type="text" name="name" required />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input id="position" class="form__text" type="text" name="position" required />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <input id="icon" class="form__text" type="text" name="icon" required />
                    <label class="form__label form__label--floating" for="icon">
                        {{ __('common.icon') }} ({{ __('staff-interface.icon-example-hint') }})
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
