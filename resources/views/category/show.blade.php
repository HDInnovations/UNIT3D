@extends('layout.default')

@section('title')
    <title>{{ $category->name }} @lang('torrent.category') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ $category->name }}">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('torrents') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.torrents')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('categories.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.categories')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('categories.show', ['id' => $category->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $category->name }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="block">
            <div class="header gradient green">
                <div class="inner_content">
                    <h1>@lang('torrent.torrents') in {{ $category->name }}</h1>
                </div>
            </div>
            <div class="table-responsive cat-torrents" type="list">
                <div class="text-center">
                    {{ $torrents->links() }}
                </div>
                <table class="table table-condensed table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            @if ($user->show_poster == 1)
                                <th>Poster</th>
                            @else
                                <th></th>
                            @endif
                            <th>@lang('torrent.category')</th>
                            <th>@lang('torrent.type')/@lang('torrent.resolution')</th>
                            <th>@lang('common.name')</th>
                            <th><i class="{{ config('other.font-awesome') }} fa-clock"></i></th>
                            <th><i class="{{ config('other.font-awesome') }} fa-file"></i></th>
                            <th><i class="{{ config('other.font-awesome') }} fa-arrow-circle-up"></i></th>
                            <th><i class="{{ config('other.font-awesome') }} fa-arrow-circle-down"></i></th>
                            <th><i class="{{ config('other.font-awesome') }} fa-check-square"></i></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($torrents as $torrent)
                            @php $meta = null; @endphp
                            @if ($torrent->category->tv_meta)
                                @if ($torrent->tmdb || $torrent->tmdb != 0)
                                    @php $meta = App\Models\Tv::with('genres', 'networks', 'seasons')->where('id', '=', $torrent->tmdb)->first(); @endphp
                                @endif
                            @endif
                            @if ($torrent->category->movie_meta)
                                @if ($torrent->tmdb || $torrent->tmdb != 0)
                                    @php $meta = App\Models\Movie::with('genres', 'cast', 'companies', 'collection')->where('id', '=', $torrent->tmdb)->first(); @endphp
                                @endif
                            @endif
                            @if ($torrent->category->game_meta)
                                @if ($torrent->igdb || $torrent->igdb != 0)
                                    @php $meta = MarcReichel\IGDBLaravel\Models\Game::with(['cover' => ['url','image_id'], 'genres' => ['name']])->find($torrent->igdb); @endphp
                                @endif
                            @endif

                            @if ($torrent->sticky == 1)
                                <tr class="success">
                            @else
                                <tr>
                                    @endif
                                    <td style="width: 1%;">
                                        @if ($user->show_poster == 1)
                                            <div class="torrent-poster pull-left">
                                                @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                                                    <img src="{{ isset($meta->poster) ? \tmdb_image('poster_small', $meta->poster) : 'https://via.placeholder.com/60x90' }}"
                                                         class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                                                @endif

                                                @if ($torrent->category->game_meta && isset($meta) && $meta->cover->image_id && $meta->name)
                                                    <img src="https://images.igdb.com/igdb/image/upload/t_cover_small/{{ $meta->cover->image_id }}.jpg"
                                                         class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                                                @endif

                                                @if ($torrent->category->music_meta)
                                                    <img src="https://via.placeholder.com/60x90" class="torrent-poster-img-small show-poster"
                                                         alt="@lang('torrent.poster')">
                                                @endif
                                                
                                                @if ($torrent->category->no_meta)
                                                    @if(file_exists(public_path().'/files/img/torrent-cover_'.$torrent->id.'.jpg')) 
                                                        <img src="{{ url('files/img/torrent-cover_' . $torrent->id . '.jpg') }}" class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                                                    @else
                                                        <img src="https://via.placeholder.com/60x90" class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
                                                    @endif
                                                @endif
                                            </div>
                                        @else
                                            <div class="torrent-poster pull-left"></div>
                                        @endif
                                    </td>

                                    <td style="width: 1%;">
                                        @if ($torrent->category->image != null)
                                            <a href="{{ route('categories.show', ['id' => $torrent->category->id]) }}">
                                                <div class="text-center">
                                                    <img src="{{ url('files/img/' . $torrent->category->image) }}" data-toggle="tooltip"
                                                         data-original-title="{{ $torrent->category->name }} {{ strtolower(trans('torrent.torrent')) }}"
                                                         style="padding-top: 10px;" alt="{{ $torrent->category->name }}">
                                                </div>
                                            </a>
                                        @else
                                            <a href="{{ route('categories.show', ['id' => $torrent->category->id]) }}">
                                                <div class="text-center">
                                                    <i class="{{ $torrent->category->icon }} torrent-icon" data-toggle="tooltip"
                                                       data-original-title="{{ $torrent->category->name }} {{ strtolower(trans('torrent.torrent')) }}"
                                                       style="padding-top: 10px;"></i>
                                                </div>
                                            </a>
                                        @endif
                                    </td>

                                    <td style="width: 1%;">
                                        <div class="text-center" style="padding-top: 15px;">
                            <span class="label label-success" data-toggle="tooltip"
                                  data-original-title="@lang('torrent.type')">
                                {{ $torrent->type->name }}
                            </span>
                                        </div>
                                        <div class="text-center" style="padding-top: 8px;">
                            <span class="label label-success" data-toggle="tooltip"
                                  data-original-title="@lang('torrent.resolution')">
                                {{ $torrent->resolution->name ?? 'No Res' }}
                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <a class="view-torrent" href="{{ route('torrent', ['id' => $torrent->id]) }}">
                                            {{ $torrent->name }}
                                        </a>

                                        @if ($current = $user->history->where('info_hash', $torrent->info_hash)->first())
                                            @if ($current->seeder == 1 && $current->active == 1)
                                                <button class="btn btn-success btn-circle" type="button" data-toggle="tooltip"
                                                        data-original-title="@lang('torrent.currently-seeding')!">
                                                    <i class="{{ config('other.font-awesome') }} fa-arrow-up"></i>
                                                </button>
                                            @endif

                                            @if ($current->seeder == 0 && $current->active == 1)
                                                <button class="btn btn-warning btn-circle" type="button" data-toggle="tooltip"
                                                        data-original-title="@lang('torrent.currently-leeching')!">
                                                    <i class="{{ config('other.font-awesome') }} fa-arrow-down"></i>
                                                </button>
                                            @endif

                                            @if ($current->seeder == 0 && $current->active == 0 && $current->completed_at == null)
                                                <button class="btn btn-info btn-circle" type="button" data-toggle="tooltip"
                                                        data-original-title="@lang('torrent.not-completed')!">
                                                    <i class="{{ config('other.font-awesome') }} fa-spinner"></i>
                                                </button>
                                            @endif

                                            @if ($current->seeder == 1 && $current->active == 0 && $current->completed_at != null)
                                                <button class="btn btn-danger btn-circle" type="button" data-toggle="tooltip"
                                                        data-original-title="@lang('torrent.completed-not-seeding')!">
                                                    <i class="{{ config('other.font-awesome') }} fa-thumbs-down"></i>
                                                </button>
                                            @endif
                                        @endif

                                        <br>
                                        @if ($torrent->anon == 1)
                                            <span class="badge-extra text-bold">
                                <i class="{{ config('other.font-awesome') }} fa-upload" data-toggle="tooltip"
                                   data-original-title="@lang('torrent.uploader')"></i> @lang('common.anonymous')
                                                @if ($user->id == $torrent->user->id || $user->group->is_modo)
                                                    <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                        ({{ $torrent->user->username }})
                                    </a>
                                                @endif
                            </span>
                                        @else
                                            <span class="badge-extra text-bold">
                                <i class="{{ config('other.font-awesome') }} fa-upload" data-toggle="tooltip"
                                   data-original-title="@lang('torrent.uploader')"></i>
                                <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                    {{ $torrent->user->username }}
                                </a>
                            </span>
                                        @endif

                                        @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                                            <span class="badge-extra text-bold">
                                <span class="text-gold movie-rating-stars">
                                    <i class="{{ config('other.font-awesome') }} fa-thumbs-up" data-toggle="tooltip"
                                       data-original-title="@lang('torrent.rating')"></i>
                                </span>
                                {{ $meta->vote_average ?? 0 }}/10 ({{ $meta->vote_count ?? 0 }} @lang('torrent.votes'))
                            </span>
                                        @endif

                                        @if ($torrent->category->game_meta && isset($meta))
                                    <span class="badge-extra text-bold">@lang('torrent.rating'):
                                        <span class="text-gold movie-rating-stars">
                                            <i class="{{ config('other.font-awesome') }} fa-star"></i>
                                        </span>
                                        {{ $meta->rating ? \round($meta->rating) : '0' }}/100 ({{ $meta->rating_count }} @lang('torrent.votes'))
                                    </span>
                                        @endif

                                        <span class="badge-extra text-bold text-pink">
                                <i class="{{ config('other.font-awesome') }} fa-heart" data-toggle="tooltip"
                                   data-original-title="@lang('torrent.thanks-given')"></i>
                                {{ $torrent->thanks_count }}
                            </span>

                                        <a href="{{ route('torrent', ['id' => $torrent->id, 'hash' => '#comments']) }}">
                                <span class="badge-extra text-bold text-green">
                                    <i class="{{ config('other.font-awesome') }} fa-comment" data-toggle="tooltip"
                                       data-original-title="@lang('common.comments')"></i>
                                    {{ $torrent->comments_count }}
                                </span>
                                        </a>

                                        @if ($torrent->internal == 1)
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-magic' data-toggle='tooltip' title=''
                                       data-original-title='@lang('torrent.internal-release')' style="color: #baaf92;"></i>
                                </span>
                                        @endif

                                        @if ($torrent->stream == 1)
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-play text-red' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.stream-optimized')'></i>
                                </span>
                                        @endif

                                        @if ($torrent->featured == 0)
                                            @if ($torrent->doubleup == 1)
                                                <span class='badge-extra text-bold'>
                                        <i class='{{ config('other.font-awesome') }} fa-gem text-green' data-toggle='tooltip'
                                           title='' data-original-title='@lang('torrent.double-upload')'></i>
                                    </span>
                                            @endif
                                            @if ($torrent->free == 1)
                                                <span class='badge-extra text-bold'>
                                        <i class='{{ config('other.font-awesome') }} fa-star text-gold' data-toggle='tooltip'
                                           title='' data-original-title='@lang('torrent.freeleech')'></i>
                                    </span>
                                            @endif
                                        @endif

                                        @if ($personal_freeleech)
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-id-badge text-orange' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.personal-freeleech')'></i>
                                </span>
                                        @endif

                                        @if ($user->freeleechTokens->where('torrent_id', $torrent->id)->first())
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-star text-bold' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.freeleech-token')'></i>
                                </span>
                                        @endif

                                        @if ($torrent->featured == 1)
                                            <span class='badge-extra text-bold' style='background-image:url(/img/sparkels.gif);'>
                                    <i class='{{ config('other.font-awesome') }} fa-certificate text-pink' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.featured')'></i>
                                </span>
                                        @endif

                                        @if ($user->group->is_freeleech == 1)
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-trophy text-purple' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.special-freeleech')'></i>
                                </span>
                                        @endif

                                        @if (config('other.freeleech') == 1)
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-globe text-blue' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.global-freeleech')'></i>
                                </span>
                                        @endif

                                        @if (config('other.doubleup') == 1)
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-globe text-green' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.global-double-upload')'></i>
                                </span>
                                        @endif

                                        @if ($user->group->is_double_upload == 1)
                                            <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.special-double_upload')'></i>
                                            </span>
                                        @endif

                                        @if ($torrent->leechers >= 5)
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-fire text-orange' data-toggle='tooltip'
                                       title='' data-original-title='@lang('common.hot')'></i>
                                </span>
                                        @endif

                                        @if ($torrent->sticky == 1)
                                            <span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-thumbtack text-black' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.sticky')'></i>
                                </span>
                                        @endif

                                        @if ($user->updated_at->getTimestamp() < $torrent->created_at->getTimestamp())
                                            <span class='badge-extra text-bold'>
                                        <i class='{{ config('other.font-awesome') }} fa-magic text-black' data-toggle='tooltip'
                                           title='' data-original-title='@lang('common.new')'></i>
                                    </span>
                                        @endif

                                        @if ($torrent->highspeed == 1)
                                            <span class='badge-extra text-bold'>
                                        <i class='{{ config('other.font-awesome') }} fa-tachometer text-red' data-toggle='tooltip'
                                           title='' data-original-title='@lang('common.high-speeds')'></i>
                                    </span>
                                        @endif

                                        @if ($torrent->sd == 1)
                                            <span class='badge-extra text-bold'>
                                        <i class='{{ config('other.font-awesome') }} fa-ticket text-orange' data-toggle='tooltip'
                                           title='' data-original-title='@lang('torrent.sd-content')'></i>
                                    </span>
                                        @endif

                                        <br>

                                        @if ($torrent->category->game_meta)
                                            @if (isset($meta) && $meta->genres)
                                                @foreach ($meta->genres as $genre)
                                                    <span class="badge-extra text-bold">
                                            <i class='{{ config('other.font-awesome') }} fa-tag' data-toggle='tooltip' title=''
                                               data-original-title='@lang('torrent.genre')'></i> {{ $genre->name }}
                                        </span>
                                                @endforeach
                                            @endif
                                        @endif

                                        @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                                            @if (isset($meta) && $meta->genres)
                                                @foreach($meta->genres as $genre)
                                                    <span class="badge-extra text-bold">
                                            <i class='{{ config('other.font-awesome') }} fa-tag' data-toggle='tooltip' title=''
                                               data-original-title='@lang('torrent.genre')'></i> {{ $genre->name }}
                                        </span>
                                                @endforeach
                                            @endif
                                        @endif
                                    </td>

                                    <td>
                                        @if (file_exists(public_path().'/files/torrents/'.$torrent->file_name))
                                            @if (config('torrent.download_check_page') == 1)
                                                <a href="{{ route('download_check', ['id' => $torrent->id]) }}">
                                                    <button class="btn btn-primary btn-circle" type="button" data-toggle="tooltip"
                                                            data-original-title="@lang('common.download')">
                                                        <i class="{{ config('other.font-awesome') }} fa-download"></i>
                                                    </button>
                                                </a>
                                            @else
                                                <a href="{{ route('download', ['id' => $torrent->id]) }}">
                                                    <button class="btn btn-primary btn-circle" type="button" data-toggle="tooltip"
                                                            data-original-title="@lang('common.download')">
                                                        <i class="{{ config('other.font-awesome') }} fa-download"></i>
                                                    </button>
                                                </a>
                                            @endif
                                        @else
                                            <a href="magnet:?dn={{ $torrent->name }}&xt=urn:btih:{{ $torrent->info_hash }}&as={{ route('torrent.download.rsskey', ['id' => $torrent->id, 'rsskey' => $user->rsskey ]) }}&tr={{ route('announce', ['passkey' => $user->passkey]) }}&xl={{ $torrent->size }}">
                                                <button class="btn btn-primary btn-circle" type="button" data-toggle="tooltip"
                                                        data-original-title="@lang('common.magnet')">
                                                    <i class="{{ config('other.font-awesome') }} fa-magnet"></i>
                                                </button>
                                            </a>
                                        @endif
                                    </td>

                                    <td>
                            <span data-toggle="tooltip" data-original-title="Bookmark" id="torrentBookmark{{ $torrent->id }}"
                                  torrent="{{ $torrent->id }}"
                                  state="{{ $bookmarks->where('torrent_id', $torrent->id)->first() ? 1 : 0 }}"
                                  class="torrentBookmark"></span>
                                    </td>

                                    <td>
                                        <time>{{ $torrent->created_at->diffForHumans() }}</time>
                                    </td>
                                    <td>
                                        <span class='badge-extra text-blue text-bold'>{{ $torrent->getSize() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('peers', ['id' => $torrent->id]) }}">
                            <span class='badge-extra text-green text-bold'>
                                {{ $torrent->seeders }}
                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('peers', ['id' => $torrent->id]) }}">
                            <span class='badge-extra text-red text-bold'>
                                {{ $torrent->leechers }}
                            </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('history', ['id' => $torrent->id]) }}">
                            <span class='badge-extra text-orange text-bold'>
                                {{ $torrent->times_completed }} @lang('common.times')
                            </span>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                    </tbody>
                </table>
                <div class="text-center">
                    {{ $torrents->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection