@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.playlist_categories.index') }}" class="breadcrumb__link">
            Playlist categories
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-playlist-category--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Add playlist category</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.playlist_categories.store') }}"
                enctype="multipart/form-data"
            >
                @csrf
                <p class="form__group">
                    <input id="name" class="form__text" type="text" name="name" placeholder=" " />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        type="text"
                        name="position"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <textarea
                        id="description"
                        name="description"
                        class="form__textarea"
                        placeholder=" "
                    ></textarea>
                    <label class="form__label form__label--floating" for="description">
                        {{ __('common.description') }}
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
