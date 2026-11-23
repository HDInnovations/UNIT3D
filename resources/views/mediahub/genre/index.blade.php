@extends('layout.with-main')

@section('title')
    <title>{{ __('common.genres') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Genres" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('mediahub.genres') }}
    </li>
@endsection

@section('page', 'page__genre--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.genres') }}</h2>
        <div class="panel__body">
            <ul class="mediahub-card__list">
                @foreach ($genres as $genre)
                    <li class="mediahub-card__list-item">
                        <a
                            href="{{ route('torrents.index', ['view' => 'group', 'genreIds' => [$genre->id]]) }}"
                            class="mediahub-card"
                        >
                            <h2 class="mediahub-card__heading">{{ $genre->name }}</h2>
                            <h3 class="mediahub-card__subheading">
                                <i class="{{ config('other.font-awesome') }} fa-tv-retro"></i>
                                {{ $genre->tv_count }} {{ __('mediahub.shows') }} |
                                <i class="{{ config('other.font-awesome') }} fa-film"></i>
                                {{ $genre->movie_count }} {{ __('mediahub.movies') }}
                            </h3>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endsection
