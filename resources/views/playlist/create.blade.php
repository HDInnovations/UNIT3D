@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('playlist.title') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Create playlist" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('playlists.index') }}" class="breadcrumb__link">
            {{ __('playlist.playlists') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__playlist--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('playlist.create') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('playlists.store') }}"
                enctype="multipart/form-data"
            >
                @csrf
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="name"
                        placeholder=" "
                        required
                        value="{{ old('name') }}"
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
                        required
                    >
                        <option hidden selected disabled value=""></option>
                        @foreach ($playlistCategories as $playlistCategory)
                            <option class="form__option" value="{{ $playlistCategory->id }}">
                                {{ $playlistCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="playlist_category_id">
                        {{ __('torrent.category') }}
                    </label>
                </p>
                <p class="form__group">
                    @livewire('bbcode-input', ['name' => 'description', 'label' => __('common.description'), 'required' => true])
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

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('torrent.categories') }}</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('torrent.category') }}</th>
                    <th>{{ __('torrent.description') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($playlistCategories as $playlistCategory)
                    <tr>
                        <td>{{ $playlistCategory->name }}</td>
                        <td>{{ $playlistCategory->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
@endsection
