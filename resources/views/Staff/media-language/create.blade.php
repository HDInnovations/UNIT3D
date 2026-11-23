@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.media_languages.index') }}" class="breadcrumb__link">
            {{ __('common.media-languages') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-media-language--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.add') }}
        </h2>
        <div class="panel__body">
            <form class="form" method="POST" action="{{ route('staff.media_languages.store') }}">
                @csrf
                <p class="form__group">
                    <input id="name" class="form__text" name="name" type="text" required />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input id="code" class="form__text" name="code" type="text" required />
                    <label class="form__label form__label--floating" for="code">
                        {{ __('common.code') }}
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
