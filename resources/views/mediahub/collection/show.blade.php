@extends('layout.with-main')

@section('title')
    <title>{{ $collection->name }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ $collection->name }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.collections.index') }}" class="breadcrumb__link">
            {{ __('mediahub.collections') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $collection->name }}
    </li>
@endsection

@section('page', 'page__collection--show')

@section('main')
    <section class="meta">
        @if ($collection?->backdrop)
            <img
                class="meta__backdrop"
                src="{{ tmdb_image('back_big', $collection->backdrop) }}"
                alt=""
            />
        @endif

        <a class="meta__title-link" href="#">
            <h1 class="meta__title">{{ $collection->name }}</h1>
        </a>
        <a class="meta__poster-link" href="#">
            <img
                src="{{ $collection?->poster ? tmdb_image('poster_big', $collection->poster) : url('img/poster-placeholder.svg') }}"
                class="meta__poster"
            />
        </a>
        <div class="meta__actions"></div>
        <ul class="meta__ids"></ul>
        <p class="meta__description">{{ $collection->overview }}</p>
        <div class="meta__chips"></div>
    </section>
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-film"></i>
                Movies
            </h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        href="{{ route('torrents.index', ['collectionId' => $collection->id]) }}"
                        role="button"
                        class="form__button form__button--text"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-eye"></i>
                        Collection torrents list
                    </a>
                </div>
            </div>
        </header>
        <div class="panel__body torrent-search--poster__results">
            @foreach ($collection->movies->sortBy('release_date') as $movie)
                <x-movie.poster
                    :movie="$movie"
                    :category-id="$movie->torrents()->first()->category_id"
                />
            @endforeach
        </div>
    </section>
    <livewire:comments :model="$collection" />
@endsection
