@extends('layout.with-main')

@section('title')
    <title>{{ __('playlist.edit-playlist') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('playlist.edit-playlist') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('playlists.index') }}" class="breadcrumb__link">
            {{ __('playlist.playlists') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a
            href="{{ route('playlists.edit', ['playlist' => $playlist]) }}"
            class="breadcrumb__link"
        >
            {{ $playlist->name }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__playlist--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('playlist.edit-playlist') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('playlists.update', ['playlist' => $playlist]) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="name"
                        placeholder=" "
                        required
                        value="{{ $playlist->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('playlist.title') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="playlist_category_id"
                        class="form__select"
                        name="playlist_category_id"
                    >
                        @foreach ($playlistCategories as $playlistCategory)
                            @if ($playlist->playlist_category_id === $playlistCategory->id)
                                <option
                                    class="form__option"
                                    value="{{ $playlistCategory->id }}"
                                    selected
                                >
                                    {{ $playlistCategory->name }} ({{ __('torrent.current') }})
                                </option>
                            @else
                                <option class="form__option" value="{{ $playlistCategory->id }}">
                                    {{ $playlistCategory->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="playlist_category_id">
                        {{ __('torrent.category') }}
                    </label>
                </p>
                <p class="form__group">
                    @livewire('bbcode-input', [
                        'name'     => 'description',
                        'label'    => __('common.description'),
                        'required' => true,
                        'content'  => $playlist->description
                    ])
                </p>
                <p class="form__group">
                    <label for="cover_image" class="form__label">
                        {{ __('playlist.cover') }}
                    </label>
                    <input id="cover_image" class="form__file" type="file" name="cover_image" />
                </p>
                <p class="form__group">
                    <input type="hidden" name="is_private" value="0" />
                    <input
                        id="is_private"
                        class="form__checkbox"
                        name="is_private"
                        type="checkbox"
                        value="1"
                        @checked($playlist->is_private)
                    />
                    <label class="form__label" for="is_private">
                        {{ __('playlist.is-private') }}
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
