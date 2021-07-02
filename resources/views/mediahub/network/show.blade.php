@extends('layout.default')

@section('title')
    <title>{{ $network->name }} @lang('mediahub.networks') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ $network->name }} @lang('mediahub.networks')">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('mediahub.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('mediahub.title')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('mediahub.networks.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('mediahub.networks')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('mediahub.networks.show', ['id' => $network->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $network->name }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            <div style="width: 100% !important; display: table !important;">
                <div class="header mediahub" style="width: 100% !important; display: table-cell !important;">
                    <h1 class="text-center" style="font-family: Shrikhand, cursive; font-size: 4em; font-weight: 400; margin: 10px 0 20px;">
                        {{ $network->name }}
                    </h1>
                    <h2 class="text-center" style="margin: 0;">{{ $network->tv_count }} @lang('mediahub.shows')</h2>
                    @foreach($shows as $show)
                        <div class="col-md-12">
                            <div class="card is-torrent">
                                <div class="card_head">
                                    <span class="badge-user text-bold" style="float:right;">
                                        {{ $show->number_of_seasons }} @lang('mediahub.seasons')
                                    </span>
                                    <span class="badge-user text-bold" style="float:right;">
                                        {{ $show->number_of_episodes }} @lang('mediahub.episodes')
                                    </span>
                                </div>
                                <div class="card_body">
                                    <div class="body_poster">
                                        <img src="{{ isset($show->poster) ? \tmdb_image('poster_mid', $show->poster) : 'https://via.placeholder.com/200x300' }}" class="show-poster">
                                    </div>
                                    <div class="body_description">
                                        <h3 class="description_title">
                                            <a href="{{ route('mediahub.shows.show', ['id' => $show->id]) }}">{{ $show->name }}
                                                <span class="text-bold text-pink"> {{ substr($show->first_air_date, 0, 4) }}</span>
                                            </a>
                                        </h3>
                                        @if ($show->genres)
                                            @foreach ($show->genres as $genre)
                                                <span class="genre-label">{{ $genre->name }}</span>
                                            @endforeach
                                        @endif
                                        <p class="description_plot">
                                            {{ $show->overview }}
                                        </p>
                                    </div>
                                </div>
                                <div class="card_footer">
                                    <div style="float: left;">

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center">
                        {{ $shows->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
