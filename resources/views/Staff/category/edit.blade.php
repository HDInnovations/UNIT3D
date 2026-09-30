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
    <li class="breadcrumbV2">
        {{ $category->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-category--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }} {{ __('torrent.category') }}: {{ $category->name }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.categories.update', ['category' => $category]) }}"
                enctype="multipart/form-data"
            >
                @method('PATCH')
                @csrf
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="name"
                        value="{{ $category->name }}"
                    />
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
                        value="{{ $category->position }}"
                    />
                    <label class="form__label form__label--floating" for="position" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        type="text"
                        name="icon"
                        value="{{ $category->icon }}"
                    />
                    <label class="form__label form__label--floating" for="icon">
                        {{ __('staff-interface.category-icon-hint') }}
                    </label>
                </p>
                <p class="form__group">
                    <label for="image">
                        {{ __('staff-interface.category-image-hint') }}
                    </label>
                    <input id="file" class="form__file" type="file" name="image" />
                </p>
                <p class="form__group">
                    <select name="meta" id="meta" class="form__select" required>
                        <option
                            class="form__option"
                            value="movie"
                            @selected($category->movie_meta)
                        >
                            {{ __('vltava.staff.movie_metadata') }}
                        </option>
                        <option class="form__option" value="tv" @selected($category->tv_meta)>
                            {{ __('vltava.staff.tv_metadata') }}
                        </option>
                        <option class="form__option" value="game" @selected($category->game_meta)>
                            {{ __('vltava.staff.game_metadata') }}
                        </option>
                        <option
                            class="form__option"
                            value="music"
                            @selected($category->music_meta)
                        >
                            {{ __('vltava.staff.music_metadata') }}
                        </option>
                        <option class="form__option" value="book" @selected($category->book_meta)>
                            {{ __('staff-interface.category-book-metadata') }}
                        </option>
                        <option class="form__option" value="no" @selected($category->no_meta)>
                            {{ __('vltava.staff.no_metadata') }}
                        </option>
                    </select>
                    <label class="form__label form__label--floating" for="meta">{{ __('staff-interface.category-meta-label') }}</label>
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
