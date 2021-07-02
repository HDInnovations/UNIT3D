@extends('layout.default')

@section('title')
    <title>{{ $show->name }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ $show->name }}">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('mediahub.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">MediaHub</span>
        </a>
    </li>
    <li>
        <a href="{{ route('mediahub.shows.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">TV Shows</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('mediahub.shows.show', ['id' => $show->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $show->name }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            <div style="width: 100% !important; display: table !important;">
                <div class="header mediahub" style="width: 100% !important; display: table-cell !important;">
                    <h1 class="text-center" style="font-family: Shrikhand, cursive; font-size: 4em; font-weight: 400; margin: 0;">
                        {{ $show->name }}
                    </h1>
                    <div class="row">
                    <div class="col-md-8">
                    @foreach($show->seasons as $season)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card is-torrent" style=" height: auto; margin-top: 0; margin-bottom: 20px;">
                                <div class="card_head">
                                <span class="badge-user text-bold" style="float:right;">
                                    {{ $season->episodes->count() }} Episodes
                                </span>
                                <span class="badge-user text-bold" style="float:right;">
                                    Season {{ $season->season_number }}
                                </span>
                                </div>
                                <div class="card_body" style="height: 190px;">
                                    <div class="body_poster">
                                        <img src="{{ isset($season->poster) ? \tmdb_image('poster_mid', $season->poster) : 'https://via.placeholder.com/200x300' }}" class="show-poster" style="height: 190px;">
                                    </div>
                                    <div class="body_description" style=" height: 190px;">
                                        <h3 class="description_title">
                                            <a href="{{ route('mediahub.season.show', ['id' => $season->id]) }}">{{ $season->name }}
                                                @if($season->air_date)
                                                    <span class="text-bold text-pink"> ({{ substr($season->air_date, 0, 4) }})</span>
                                                @endif
                                            </a>
                                        </h3>
                                        <p class="description_plot">
                                            {{ $season->overview }}
                                        </p>
                                    </div>
                                </div>
                                <div class="card_footer text-center">
                                    <a data-toggle="collapse" data-target="#{{ $season->season_number }}">
                                        <i class="fas fa-chevron-double-down"></i> <span class="badge-user text-bold"> {{ $season->torrents()->count() }} Torrents Matched</span> <i class="fas fa-chevron-double-down"></i>
                                    </a>
                                </div>
                                <div id="{{ $season->season_number }}" class="collapse">
                                    <div class="card_footer" style="height: auto;">
                                        <div class="table-responsive">
                                            <table class="table table-condensed table-bordered table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('common.name')</th>
                                                        <th>@lang('torrent.size')</th>
                                                        <th>S</th>
                                                        <th>L</th>
                                                        <th>C</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($season->torrents()->get()->sortByDesc('created_at') as $torrent)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('torrent', ['id' => $torrent->id]) }}" style="color: #8fa8e0;">{{ $torrent->name }}</a>
                                                            </td>
                                                            <td>{{ $torrent->getSize() }}</td>
                                                            <td>{{ $torrent->seeders }}</td>
                                                            <td>{{ $torrent->leechers }}</td>
                                                            <td>{{ $torrent->times_completed }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>

                        <div class="info_column col-md-3" style=" background: rgba(0, 0, 0, 0.28); border-radius: 5px; margin-left: 3%;">
                            <div>
                                <h2 class="text-center"><em>Facts</em></h2>
                                <hr>
                                <section class="split_column season">
                                    <div>
                                        <div class="column no_bottom_pad">
                                            <section class="facts left_column">
                                                <h4><bdi>General</bdi></h4>
                                                <div class="seasons">
                                                    <div>
                                                        <span class="badge badge-default">Seasons:</span>
                                                        <span class="text-bold">{{ $show->number_of_seasons }} </span>
                                                    </div>
                                                </div>

                                                <div class="status">
                                                <span class="badge badge-default">Status:</span>
                                                <span class="text-bold">{{ $show->status }} </span>
                                                </div>

                                                <div class="networks">
                                                    <span class="badge badge-default">Networks:</span>
                                                    @if ($show->networks)
                                                        @foreach ($show->networks as $network)
                                                            <span class="text-bold">{{ $network->name }}</span>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="companies">
                                                    <span class="badge badge-default">Companies:</span>
                                                    @if ($show->companies)
                                                        @foreach ($show->companies as $company)
                                                            <span class="text-bold">{{ $company->name }}</span>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="runtime">
                                                    <span class="badge badge-default">Runtime:</span>
                                                    <span class="text-bold">{{ $show->episode_run_time }} </span>
                                                </div>

                                                <div class="torrents">
                                                    <span class="badge badge-default">Torrents:</span>
                                                    <span class="text-bold">{{ $show->torrents_count }} </span>
                                                </div>
                                            </section>

                                            <hr>

                                            <section class="genres right_column">
                                                <h4><bdi>Genres</bdi></h4>
                                                <ul>
                                                    @if ($show->genres)
                                                        @foreach ($show->genres as $genre)
                                                            <li><a class="rounded" href="{{ route('mediahub.genres.show', ['id' => $genre->id]) }}">{{ $genre->name }}</a></li>
                                                        @endforeach
                                                    @endif
                                                </ul>
                                            </section>
                                        </div>
                                    </div>

                                    <div>
                                        {{--EDIT BUTTON --}}
                                    </div>

                                </section>

                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection