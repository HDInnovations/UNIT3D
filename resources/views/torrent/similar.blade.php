@extends('layout.with-main')

@section('title')
    <title>
        {{ __('common.similar') }} - {{ $meta->title ?? $meta->name }}
        ({{ substr($meta->release_date ?? $meta->first_air_date, 0, 4) }}) -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('common.similar') }} - {{ $meta->title ?? $meta->name }} ({{ substr($meta->release_date ?? $meta->first_air_date, 0, 4) }})"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.index') }}" class="breadcrumb__link">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.similar') }} - {{ $meta->title ?? $meta->name }}
        ({{ \substr($meta->release_date ?? $meta->first_air_date, 0, 4) }})
    </li>
@endsection

@section('page', 'page__torrent-similar--index')

@section('main')
    @switch(true)
        @case($category->movie_meta)
            @include('torrent.partials.movie-meta')

            @break
        @case($category->tv_meta)
            @include('torrent.partials.tv-meta')

            @break
        @case($category->game_meta)
            @include('torrent.partials.game-meta')

            @break
        @default
            @include('torrent.partials.no-meta')

            @break
    @endswitch
    @livewire('similar-torrent', ['category' => $category, 'tmdbId' => $tmdb, 'igdbId' => $igdb, 'work' => $meta])
    <livewire:comments :model="$meta" :category="$category" />
@endsection
