@extends('layout.default')

@section('breadcrumb')
    <li>
        <a href="{{ route('torrents') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.torrents')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('cards') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.cards-view')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="block">
            @include('torrent.buttons')
            <div class="header gradient blue">
                <div class="inner_content">
                    <h1>
                        @lang('torrent.torrents')
                    </h1>
                </div>
            </div>
            <div id="facetedDefault" style="{{ $user->torrent_filters ? 'display: none;' : '' }}">
                <div class="box">
                    <div class="container mt-5">
                        <div class="mx-0 mt-5 form-group fatten-me">
                            <div>
                                <label for="query"></label>
                                <input type="text" class="form-control facetedSearch"
                                       trigger="keyup" id="query" placeholder="@lang('torrent.search')">
                            </div>
                        </div>
                    </div>
                    <hr style="padding: 0; margin: 0;">
                </div>
            </div>
            <div id="facetedFilters" style="{{ $user->torrent_filters ? '' : 'display: none;' }}">
                <div class="box">
                    <div class="container well search mt-5">
                        <form role="form" method="GET" action="TorrentController@torrents"
                              class="form-horizontal form-condensed form-torrent-search form-bordered">
                            @csrf
                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="name"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.name')</label>
                                <div class="col-sm-9">
                                    <label for="search"></label><input type="text" class="form-control facetedSearch"
                                                                       trigger="keyup" id="search" placeholder="@lang('torrent.name')">
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="name"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.description')</label>
                                <div class="col-sm-9">
                                    <label for="description"></label><input type="text" class="form-control facetedSearch"
                                                                            trigger="keyup" id="description" placeholder="@lang('torrent.description')">
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="keywords"
                                       class="mt-5 col-sm-1 label label-default fatten-me">Keywords</label>
                                <div class="col-sm-9 fatten-me">
                                    <label for="keywaords"></label>
                                    <input type="text" class="form-control facetedSearch"
                                           trigger="keyup" id="keywords" placeholder="Keywords">
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="uploader"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.uploader')</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control facetedSearch" trigger="keyup" id="uploader"
                                           placeholder="@lang('torrent.uploader')">
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="imdb" class="mt-5 col-sm-1 label label-default fatten-me">ID</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control facetedSearch" trigger="keyup" id="imdb"
                                           placeholder="IMDB #">
                                </div>
                                <div class="col-sm-2">
                                    <label for="tvdb"></label><input type="text" class="form-control facetedSearch"
                                                                     trigger="keyup" id="tvdb" placeholder="TVDB #">
                                </div>
                                <div class="col-sm-2">
                                    <label for="tmdb"></label><input type="text" class="form-control facetedSearch"
                                                                     trigger="keyup" id="tmdb" placeholder="TMDB #">
                                </div>
                                <div class="col-sm-2">
                                    <label for="mal"></label><input type="text" class="form-control facetedSearch"
                                                                    trigger="keyup" id="mal" placeholder="MAL #">
                                </div>
                                <div class="col-sm-2">
                                    <label for="igdb"></label><input type="text" class="form-control facetedSearch"
                                                                     trigger="keyup" id="igdb" placeholder="IGDB #">
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="release_year" class="mt-5 col-sm-1 label label-default fatten-me">Year
                                    Range</label>
                                <div class="col-sm-2">
                                    <label for="start_year"></label><input type="text" class="form-control facetedSearch"
                                                                           trigger="keyup" id="start_year" placeholder="Start Year">
                                </div>
                                <div class="col-sm-2">
                                    <label for="end_year"></label><input type="text" class="form-control facetedSearch"
                                                                         trigger="keyup" id="end_year" placeholder="End Year">
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="category"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.category')</label>
                                <div class="col-sm-10">
                                    @foreach ($repository->categories() as $id => $category)
                                        <span class="badge-user">
                                            <label class="inline">
                                                <input type="checkbox" trigger="click" value="{{ $id }}"
                                                       class="category facetedSearch"> {{ $category }}
                                            </label>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="type"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.type')</label>
                                <div class="col-sm-10">
                                    @foreach ($repository->types() as $id => $type)
                                        <span class="badge-user">
                                            <label class="inline">
                                                <input type="checkbox" trigger="click" value="{{ $id }}"
                                                       class="type facetedSearch"> {{ $type }}
                                            </label>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="resolution"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.resolution')</label>
                                <div class="col-sm-10">
                                    @foreach ($repository->resolutions() as $id => $resolution)
                                        <span class="badge-user">
                                            <label class="inline">
                                                <input type="checkbox" value="{{ $id }}"
                                                       class="resolution facetedSearch" trigger="click"> {{ $resolution }}
                                            </label>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="genre"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.genre')</label>
                                <div class="col-sm-10">
                                    @foreach ($repository->genres() as $id => $genre)
                                        <span class="badge-user">
                                            <label class="inline">
                                                <input type="checkbox" trigger="click" value="{{ $id }}"
                                                       class="genre facetedSearch"> {{ $genre }}
                                            </label>
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="type"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.discounts')</label>
                                <div class="col-sm-10">
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="freeleech" trigger="click" value="1"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-star text-gold"></span>
                                            @lang('torrent.freeleech')
                                        </label>
                                    </span>
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="doubleupload" trigger="click" value="1"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-gem text-green"></span>
                                            @lang('torrent.double-upload')
                                        </label>
                                    </span>
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="featured" trigger="click" value="1"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-certificate text-pink"></span>
                                            @lang('torrent.featured')
                                        </label>
                                    </span>
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="type"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.special')</label>
                                <div class="col-sm-10">
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="stream" trigger="click" value="1"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-play text-red"></span>
                                            @lang('torrent.stream-optimized')
                                        </label>
                                    </span>
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="highspeed" trigger="click" value="1"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-tachometer text-red"></span>
                                            @lang('common.high-speeds')
                                        </label>
                                    </span>
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="sd" trigger="click" value="1" class="facetedSearch">
                                            <span class="{{ config('other.font-awesome') }} fa-ticket text-orange"></span>
                                            @lang('torrent.sd-content')
                                        </label>
                                    </span>
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="internal" trigger="click" value="1"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-magic"
                                                    style="color: #baaf92;"></span> @lang('torrent.internal')
                                        </label>
                                    </span>
                                </div>
                            </div>

                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="type"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.health')</label>
                                <div class="col-sm-10">
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="alive" trigger="click" value="1"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-smile text-green"></span>
                                            @lang('torrent.alive')
                                        </label>
                                    </span>
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="dying" trigger="click" value="1"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-meh text-orange"></span>
                                            @lang('torrent.dying-torrent')
                                        </label>
                                    </span>
                                    <span class="badge-user">
                                        <label class="inline">
                                            <input type="checkbox" id="dead" trigger="click" value="0"
                                                   class="facetedSearch"> <span
                                                    class="{{ config('other.font-awesome') }} fa-frown text-red"></span>
                                            @lang('torrent.dead-torrent')
                                        </label>
                                    </span>
                                </div>
                            </div>
                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="sort"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.sort')</label>
                                <div class="col-sm-2">
                                    <label for="sorting"></label><select id="sorting" trigger="change" name="sorting"
                                                                         class="form-control facetedSearch">
                                        @foreach ($repository->sorting() as $value => $sort)
                                            <option value="{{ $value }}">{{ $sort }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mx-0 mt-5 form-group fatten-me">
                                <label for="sort"
                                       class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.direction')</label>
                                <div class="col-sm-2">
                                    <label for="direction"></label><select id="direction" trigger="change" name="direction"
                                                                           class="form-control facetedSearch">
                                        @foreach ($repository->direction() as $value => $dir)
                                            <option value="{{ $value }}">{{ $dir }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <span id="facetedHeader"></span>
            <div id="facetedSearch" type="card" font-awesome="{{ config('other.font-awesome') }}">
                <div style="width: 100% !important; display: table !important;">
                    <div class="align-center" style="width: 100% !important; display: table-cell !important;">
                        {{ $torrents->links() }}</div>
                </div>
                <div style="width: 100% !important; display: table !important;">
                    <div class="mb-5" style="width: 100% !important; display: table-cell !important;">
                        @foreach ($torrents as $torrent)
                            <div class="col-md-4">
                                <div class="card is-torrent">
                                    <div class="card_head">
                                        <span class="badge-user text-bold" style="float:right;">
                                            <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-up text-green"></i>
                                            {{ $torrent->seeders }} /
                                            <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-down text-red"></i>
                                            {{ $torrent->leechers }} /
                                            <i class="{{ config('other.font-awesome') }} fa-fw fa-check text-orange"></i>
                                            {{ $torrent->times_completed }}
                                        </span>&nbsp;
                                        <span class="badge-user text-bold text-blue"
                                              style="float:right;">{{ $torrent->getSize() }}</span>&nbsp;
                                        <span class="badge-user text-bold text-blue"
                                              style="float:right;">{{ $torrent->resolution->name ?? 'No Res' }}</span>
                                        <span class="badge-user text-bold text-blue"
                                              style="float:right;">{{ $torrent->type->name }}</span>&nbsp;
                                        <span class="badge-user text-bold text-blue"
                                              style="float:right;">{{ $torrent->category->name }}</span>&nbsp;
                                    </div>
                                    <div class="card_body">
                                        <div class="body_poster">
                                            @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                                                <img src="{{ isset($torrent->meta->poster) ? \tmdb_image('poster_mid', $torrent->meta->poster) : 'https://via.placeholder.com/200x300' }}"
                                                     class="show-poster" alt="@lang('torrent.poster')">
                                            @endif

                                            @if ($torrent->category->game_meta && isset($torrent->meta) && $torrent->meta->cover->image_id &&
                                                $torrent->meta->name)
                                                <img src="https://images.igdb.com/igdb/image/upload/t_cover_big/{{ $torrent->meta->cover->image_id }}.jpg"
                                                     class="show-poster"
                                                     data-name='<i style="color: #a5a5a5;">{{ $torrent->meta->name ?? 'N/A' }}</i>'
                                                     data-image='<img src="https://images.igdb.com/igdb/image/upload/t_original/{{ $torrent->meta->cover->image_id }}.jpg" alt="@lang('
                                                    torrent.poster')" style="height: 1000px;">'
                                                     class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                                            @endif

                                            @if ($torrent->category->music_meta)
                                                <img src="https://via.placeholder.com/200x300" class="show-poster"
                                                     data-name='<i style="color: #a5a5a5;">N/A</i>'
                                                     data-image='<img src="https://via.placeholder.com/200x300" alt="@lang('
                                                    torrent.poster')" style="height: 1000px;">'
                                                     class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                                            @endif
                                            
                                            @if ($torrent->category->no_meta)
                                                @if(file_exists(public_path().'/files/img/torrent-cover_'.$torrent->id.'.jpg')) 
                                                    <img src="{{ url('files/img/torrent-cover_' . $torrent->id . '.jpg') }}" class="show-poster" alt="@lang('torrent.poster')">
                                                @else
                                                        <img src="https://via.placeholder.com/200x300" class="show-poster" data-name='<i style="color: #a5a5a5;">N/A</i>'
                                                        data-image='<img src="https://via.placeholder.com/200x300" alt="@lang('torrent.poster')" style="height: 1000px;">'
                                                        class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                                                @endif
                                            @endif
                                        </div>
                                        <div class="body_description">
                                            <h3 class="description_title">
                                                <a href="{{ route('torrent', ['id' => $torrent->id]) }}">{{ $torrent->name }}
                                                    @if($torrent->category->movie_meta || $torrent->category->tv_meta)
                                                        <span class="text-bold text-pink"> {{ $torrent->meta->releaseYear ?? ''}}</span>
                                                    @endif
                                                    @if($torrent->category->game_meta && isset($torrent->meta) &&
                                                        $torrent->meta->first_release_date)
                                                        <span class="text-bold text-pink">
                                                            {{ date('Y', strtotime($torrent->meta->first_release_date)) }}</span>
                                                    @endif
                                                </a>
                                            </h3>
                                            @if ($torrent->category->movie_meta && isset($torrent->meta) && $torrent->meta->genres)
                                                @foreach ($torrent->meta->genres as $genre)
                                                    <span class="genre-label">{{ $genre->name }}</span>
                                                @endforeach
                                            @endif
                                            @if ($torrent->category->tv_meta && isset($torrent->meta) && $torrent->meta->genres)
                                                @foreach ($torrent->meta->genres as $genre)
                                                    <span class="genre-label">{{ $genre->name }}</span>
                                                @endforeach
                                            @endif
                                            @if ($torrent->category->game_meta && isset($torrent->meta) && $torrent->meta->genres)
                                                @foreach ($torrent->meta->genres as $genre)
                                                    <span class="genre-label">{{ $genre->name }}</span>
                                                @endforeach
                                            @endif
                                            <p class="description_plot">
                                                {{ $torrent->meta->overview ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card_footer">
                                        <div style="float: left;">
                                            @if ($torrent->anon == 1)
                                                <span
                                                        class="badge-user text-orange text-bold">{{ strtoupper(trans('common.anonymous')) }}
                                                    @if (auth()->user()->id == $torrent->user->id || auth()->user()->hasPrivilegeTo('users_view_private'))
                                                        <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                                            ({{ $torrent->user->username }})
                                                        </a>
                                                    @endif
                                                </span>
                                            @else
                                                <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                                    <span class="badge-user text-bold"
                                                          style="color:{{ $torrent->user->primaryRole->color }}; background-image:{{ $torrent->user->primaryRole->effect }};">
                                                        <i class="{{ $torrent->user->primaryRole->icon }}" data-toggle="tooltip" title=""
                                                           data-original-title="{{ $torrent->user->primaryRole->name }}"></i>
                                                        {{ $torrent->user->username }}
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                        <span class="badge-user text-bold" style="float: right;">
                                            <i class="{{ config('other.font-awesome') }} fa-thumbs-up text-gold"></i>
                                            {{ $torrent->meta->vote_average ?? 0 }}/10 ({{ $torrent->meta->vote_count ?? 0 }} @lang('torrent.votes'))
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div style="width: 100% !important; display: table !important;">
                    <div class="align-center" style="width: 100% !important; display: table-cell !important;">
                        {{ $torrents->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection