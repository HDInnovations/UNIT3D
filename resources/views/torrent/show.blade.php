@extends('layout.with-main')

@section('title')
    <title>
        {{ $torrent->name }} - {{ __('torrent.torrents') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('torrent.meta-desc', ['name' => $torrent->name]) }}!"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.index') }}" class="breadcrumb__link">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $torrent->name }}
    </li>
@endsection

@section('page', 'page__torrent--show')

@section('main')
    @switch(true)
        @case($torrent->category->movie_meta)
            @include('torrent.partials.movie-meta', ['category' => $torrent->category, 'tmdb' => $torrent->tmdb_movie_id])

            @break
        @case($torrent->category->tv_meta)
            @include('torrent.partials.tv-meta', ['category' => $torrent->category, 'tmdb' => $torrent->tmdb_tv_id])

            @break
        @case($torrent->category->game_meta)
            @include('torrent.partials.game-meta', ['category' => $torrent->category, 'igdb' => $torrent->igdb])

            @break
            @case($torrent->category->porn_meta)
                <div class="torrent__porn-meta">
                    <h2>Porn Metadata</h2>
                    @if ($torrent->fansdb_id)
                        <div><strong>FansDB ID:</strong> {{ $torrent->fansdb_id }}</div>
                    @endif
                    @if ($torrent->stashdb_id)
                        <div><strong>StashDB ID:</strong> <a href="{{ route('mediahub.porn.torrentsByStashId', $torrent->stashdb_id) }}">{{ $torrent->stashdb_id }}</a></div>
                        @php
                            $stashScene = (new \App\Services\StashDB\StashDBScraper())->scene($torrent->stashdb_id);
                        @endphp
                        @if ($stashScene)
                            <div><strong>StashDB Scene Title:</strong> {{ $stashScene['title'] ?? 'N/A' }}</div>
                            <div><strong>Release Date:</strong> {{ $stashScene['release_date'] ?? 'N/A' }}</div>
                            <div><strong>Studio:</strong> {{ $stashScene['studio']['name'] ?? 'N/A' }}</div>
                            <div><strong>Performers:</strong>
                                @if (!empty($stashScene['performers']))
                                    {{ collect($stashScene['performers'])->pluck('performer.name')->join(', ') }}
                                @else
                                    N/A
                                @endif
                            </div>
                            <div><strong>Details:</strong> {{ $stashScene['details'] ?? 'N/A' }}</div>
                                @if (!empty($stashScene['images']))
                                    <div><strong>Scene Images:</strong></div>
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                        @foreach ($stashScene['images'] as $image)
                                            <div>
                                                <a href="{{ $image['url'] }}" target="_blank" style="display: flex; justify-content: center; align-items: center;">
                                                    <img src="{{ $image['url'] }}" alt="Scene Image" style="max-width: 1920px; max-height: 1080px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); display: block; margin: 0 auto;">
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                        @endif
                    @endif
                    @if ($torrent->theporndb_id)
                        <div><strong>ThePornDB ID:</strong> {{ $torrent->theporndb_id }}</div>
                    @endif
                </div>
                @break
        @default
            @include('torrent.partials.no-meta', ['category' => $torrent->category])

            @break
    @endswitch
    <h1 class="torrent__name">
        {{ $torrent->name }}
    </h1>
    @include('torrent.partials.general')
    @include('torrent.partials.buttons')

    {{-- Tools Block --}}
    @if (auth()->user()->internals()->exists() ||auth()->user()->group->is_editor ||auth()->user()->group->is_modo ||(auth()->id() === $torrent->user_id && $canEdit))
        @include('torrent.partials.tools')
    @endif

    {{-- Audits, Reports, Downloads Block --}}
    @if (auth()->user()->group->is_modo)
        @include('torrent.partials.audits')
        @include('torrent.partials.reports')
        @include('torrent.partials.downloads')
    @endif

    {{-- MediaInfo Block --}}
    @if ($torrent->mediainfo !== null)
        @include('torrent.partials.mediainfo')
    @endif

    {{-- BDInfo Block --}}
    @if ($torrent->bdinfo !== null)
        @include('torrent.partials.bdinfo')
    @endif

    {{-- Description Block --}}
    @include('torrent.partials.description')

    {{-- Subtitles Block --}}
    @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
        @include('torrent.partials.subtitles')
    @endif

    {{-- Extra Meta Block --}}
    @include('torrent.partials.extra-meta')

    {{-- Comments Block --}}
    @include('torrent.partials.comments')
@endsection
