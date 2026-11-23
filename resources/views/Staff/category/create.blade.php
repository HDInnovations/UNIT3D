@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.categories.index') }}" class="breadcrumb__link">
            {{ __('staff.torrent-categories') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-category--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.add') }}
            {{ trans_choice('common.a-an-art', false) }}
            {{ __('torrent.category') }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.categories.store') }}"
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
                    <input id="icon" class="form__text" type="text" name="icon" placeholder=" " />
                    <label class="form__label form__label--floating" for="icon">
                        {{ __('common.icon') }} (FontAwesome)
                    </label>
                </p>
                <p class="form__group">
                    <label for="image">
                        {{ __('common.select') }}
                        {{ trans_choice('common.a-an-art', false) }}
                        {{ __('common.image') }}
                        (If not using a FontAwesome icon)
                    </label>
                    <input id="file" class="form__file" type="file" name="image" />
                </p>
                <p class="form__group">
                    <select name="meta" id="meta" class="form__select" required>
                        <option hidden selected disabled value=""></option>
                        <option class="form__option" value="movie">Movie metadata</option>
                        <option class="form__option" value="tv">TV metadata</option>
                        <option class="form__option" value="game">Game metadata</option>
                        <option class="form__option" value="music">Music metadata</option>
                        <option class="form__option" value="no">No metadata</option>
                    </select>
                    <label class="form__label form__label--floating" for="meta">Meta</label>
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
