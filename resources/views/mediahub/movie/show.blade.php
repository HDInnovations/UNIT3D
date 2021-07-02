@extends('layout.default')

@section('title')
    <title>{{ $movie->title }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('mediahub.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('mediahub.title')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('mediahub.movies.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('mediahub.movies')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('mediahub.movies.show', ['id' => $movie->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $movie->title }}</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block single">
            <div class="movie-wrapper">
                <div class="movie-overlay"></div>

                <div class="movie-poster">
                    @php $tmdb_poster = $movie->poster ? \tmdb_image('poster_big', $movie->poster) : 'https://via.placeholder.com/400x600'; @endphp
                    <img src="{{ $tmdb_poster }}" class="img-responsive" id="meta-poster">
                </div>

                <div class="meta-info">
                    <div class="tags">
                        @lang('mediahub.movies')
                    </div>

                    <div class="movie-right">
                        @if(isset($movie->companies) && $movie->companies->isNotEmpty())
                        @php $company = $movie->companies->first(); @endphp
                        <div class="badge-user">
                            <a href="{{ route('mediahub.companies.show', ['id' => $company->id]) }}">
                            @if(isset($company->logo))
                                <img class="img-responsive" src="{{ \tmdb_image('logo_small', $company->logo) }}" title="{{ $company->name }}">
                            @else
                            {{ $company->name }}
                            @endif
                            </a>
                        </div>
                        @endif
                    </div>

                    @php $tmdb_backdrop = $movie->backdrop ? \tmdb_image('back_big', $movie->backdrop) : 'https://via.placeholder.com/960x540'; @endphp
                    <div class="movie-backdrop" style="background-image: url('{{ $tmdb_backdrop }}');"></div>

                    <div class="movie-top">
                        <h1 class="movie-heading">
                            <span class="text-bold">{{ isset($movie->title) ? $movie->title : 'No Meta Found' }}</span>
                            @if(isset($movie->release_date))
                            <span> ({{ substr($movie->release_date, 0, 4) ?? '' }})</span>
                            @endif
                        </h1>

                        <div class="movie-overview">
                            {{ isset($movie->overview) ? Str::limit($movie->overview, $limit = 350, $end = '...') : '' }}
                        </div>
                    </div>

                    <div class="movie-bottom">
                        <div class="movie-details">
                            @if ($movie->imdb_id != 0 && $movie->imdb_id != null)
                            <span class="badge-user text-bold">
                                <a href="https://www.imdb.com/title/{{ $movie->imdb_id }}" title="IMDB" target="_blank">
                                    <i class="{{ config('other.font-awesome') }} fa-film"></i> IMDB: {{ \str_replace('tt', '', $movie->imdb_id) }}
                                </a>
                            </span>
                            @endif

                            <span class="badge-user text-bold">
                                <a href="https://www.themoviedb.org/movie/{{ $movie->id }}" title="The Movie Database"
                                    target="_blank">
                                    <i class="{{ config('other.font-awesome') }} fa-film"></i> TMDB: {{ $movie->id }}
                                </a>
                            </span>

                            @if(isset($movie->crew))
                                @php $director = $movie->crew->where('known_for_department' ,'=', 'Directing')->sortBy('order')->first(); @endphp
                            @if($director)
                                <span class="badge-user text-bold text-purple">
                                    <a href="{{ route('mediahub.persons.show', ['id' => $director->id]) }}">
                                        <i class="{{ config('other.font-awesome') }} fa-camera-movie"></i> Dir. {{ $director->name }}
                                    </a>
                                </span>
                            @endif
                            @endif

                            @if (isset($movie->genres) && $movie->genres->isNotEmpty())
                            @foreach ($movie->genres as $genre)
                            <span class="badge-user text-bold text-green">
                                <a href="{{ route('mediahub.genres.show', ['id' => $genre->id]) }}">
                                    <i class="{{ config('other.font-awesome') }} fa-tag"></i> {{ $genre->name }}
                                </a>
                            </span>
                            @endforeach
                            @endif
                        </div>

                        <div class="movie-details">
                            @if(isset($movie) && !empty(trim($movie->homepage)))
                            <span class="badge-user text-bold">
                                <a href="{{ $movie->homepage }}" title="Homepage" rel="noopener noreferrer" target="_blank">
                                    <i class="{{ config('other.font-awesome') }} fa-external-link-alt"></i> Homepage
                                </a>
                            </span>
                            @endif

                            <span class="badge-user text-bold text-orange">
                                Status: {{ $movie->status ?? 'Unknown' }}
                            </span>

                            @if (isset($movie->runtime))
                            <span class="badge-user text-bold text-orange">
                                @lang('torrent.runtime'): {{ $movie->runtime }}
                                @lang('common.minute')@lang('common.plural-suffix')
                            </span>
                            @endif

                            <span class="badge-user text-bold text-gold">@lang('torrent.rating'):
                                <span class="movie-rating-stars">
                                    <i class="{{ config('other.font-awesome') }} fa-star"></i>
                                </span>
                                {{ $movie->vote_average ?? 0 }}/10 ({{ $movie->vote_count ?? 0 }} @lang('torrent.votes'))
                            </span>
                        </div>

                        <div class="cast-list">
                        @if (isset($movie->cast) && $movie->cast->isNotEmpty())
                            @foreach ($movie->cast->sortBy('order')->take(7) as $cast)
                            <div class="cast-item">
                                <a href="{{ route('mediahub.persons.show', ['id' => $cast->id]) }}" class="badge-user">
                                    @php $tmdb_face = $cast->still ? \tmdb_image('cast_face', $cast->still) : 'https://via.placeholder.com/138x175'; @endphp
                                    <img class="img-responsive" src="{{ $tmdb_face }}" alt="{{ $cast->name }}">
                                    <div class="cast-name">{{ $cast->name }}</div>
                                </a>
                            </div>
                            @endforeach
                        @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-20">
                <table class="table table-condensed table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th>@lang('torrent.category')</th>
                        <th>@lang('torrent.name')</th>
                        <th><i class="{{ config('other.font-awesome') }} fa-clock"></i></th>
                        <th><i class="{{ config('other.font-awesome') }} fa-file"></i></th>
                        <th><i class="{{ config('other.font-awesome') }} fa-arrow-circle-up"></i></th>
                        <th><i class="{{ config('other.font-awesome') }} fa-arrow-circle-down"></i></th>
                        <th><i class="{{ config('other.font-awesome') }} fa-check-square"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($movie->torrents as $torrent)
                        @if ($torrent->sticky == 1)
                            <tr class="success">
                        @else
                            <tr>
                                @endif
                                <td>
                                    <a href="{{ route('categories.show', ['id' => $torrent->category->id]) }}">
                                        <div class="text-center">
                                            <i class="{{ $torrent->category->icon }} torrent-icon" data-toggle="tooltip"
                                               data-original-title="{{ $torrent->category->name }} {{ strtolower(trans('torrent.torrent')) }}"
                                               style="padding-bottom: 6px;"></i>
                                        </div>
                                    </a>
                                    <div class="text-center">
                                        <span class="label label-success" data-toggle="tooltip"
                                              data-original-title="@lang('torrent.type')">
                                            {{ $torrent->type->name }}
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <a class="view-torrent" href="{{ route('torrent', ['id' => $torrent->id]) }}">
                                        {{ $torrent->name }}
                                    </a>
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

                                    @php $history = \App\Models\History::where('user_id', '=', $user->id)->where('info_hash', '=', $torrent->info_hash)->first(); @endphp
                                    @if ($history)
                                        @if ($history->seeder == 1 && $history->active == 1)
                                            <button class="btn btn-success btn-circle" type="button" data-toggle="tooltip"
                                                    data-original-title="@lang('torrent.currently-seeding')!">
                                                <i class="{{ config('other.font-awesome') }} fa-arrow-up"></i>
                                            </button>
                                        @endif

                                        @if ($history->seeder == 0 && $history->active == 1)
                                            <button class="btn btn-warning btn-circle" type="button" data-toggle="tooltip"
                                                    data-original-title="@lang('torrent.currently-leeching')!">
                                                <i class="{{ config('other.font-awesome') }} fa-arrow-down"></i>
                                            </button>
                                        @endif

                                        @if ($history->seeder == 0 && $history->active == 0 && $history->completed_at == null)
                                            <button class="btn btn-info btn-circle" type="button" data-toggle="tooltip"
                                                    data-original-title="@lang('torrent.not-completed')!">
                                                <i class="{{ config('other.font-awesome') }} fa-spinner"></i>
                                            </button>
                                        @endif

                                        @if ($history->seeder == 0 && $history->active == 0 && $history->completed_at != null)
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

                                    <span class="badge-extra text-bold text-pink">
                                            <i class="{{ config('other.font-awesome') }} fa-heart" data-toggle="tooltip"
                                               data-original-title="@lang('torrent.thanks-given')"></i>
                                            {{ $torrent->thanks->count() }}
                                        </span>

                                    <span class="badge-extra text-bold text-green">
                                            <i class="{{ config('other.font-awesome') }} fa-comment" data-toggle="tooltip"
                                               data-original-title="@lang('common.comments')"></i>
                                            {{ $torrent->comments->count() }}
                                        </span>

                                    @if ($torrent->internal == 1)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-magic' data-toggle='tooltip'
                                                   title='' data-original-title='@lang(' torrent.internal-release')'
                                                   style="color: #baaf92;"></i> @lang('torrent.internal')
                                            </span>
                                    @endif

                                    @if ($torrent->stream == 1)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-play text-red' data-toggle='tooltip'
                                                   title='' data-original-title='@lang(' torrent.stream-optimized')'></i>
                                                @lang('torrent.stream-optimized')
                                            </span>
                                    @endif

                                    @if ($torrent->featured == 0)
                                        @if ($torrent->doubleup == 1)
                                            <span class='badge-extra text-bold'>
                                                    <i class='{{ config('other.font-awesome') }} fa-gem text-green'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('
                                                        torrent.double-upload')'></i> @lang('torrent.double-upload')
                                                </span>
                                        @endif
                                        @if ($torrent->free == 1)
                                            <span class='badge-extra text-bold'>
                                                    <i class='{{ config('other.font-awesome') }} fa-star text-gold'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('
                                                        torrent.freeleech')'></i> @lang('torrent.freeleech')
                                                </span>
                                        @endif
                                    @endif

                                    @if ($personal_freeleech)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-id-badge text-orange'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.personal-freeleech')'></i> @lang('torrent.personal-freeleech')
                                            </span>
                                    @endif

                                    @php $freeleech_token = \App\Models\FreeleechToken::where('user_id', '=', $user->id)->where('torrent_id', '=', $torrent->id)->first(); @endphp
                                    @if ($freeleech_token)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-star text-bold'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.freeleech-token')'></i> @lang('torrent.freeleech-token')
                                            </span>
                                    @endif

                                    @if ($torrent->featured == 1)
                                        <span class='badge-extra text-bold' style='background-image:url(/img/sparkels.gif);'>
                                                <i class='{{ config('other.font-awesome') }} fa-certificate text-pink'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.featured')'></i> @lang('torrent.featured')
                                            </span>
                                    @endif

                                    @if ($user->group->is_freeleech == 1)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.special-freeleech')'></i> @lang('torrent.special-freeleech')
                                            </span>
                                    @endif

                                    @if (config('other.freeleech') == 1)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-globe text-blue'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.global-freeleech')'></i> @lang('torrent.global-freeleech')
                                            </span>
                                    @endif

                                    @if (config('other.doubleup') == 1)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-globe text-green'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.double-upload')'></i> @lang('torrent.double-upload')
                                            </span>
                                    @endif

                                    @if ($user->group->is_double_upload == 1)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.special-double_upload')'></i> @lang('torrent.special-double_upload')
                                            </span>
                                    @endif

                                    @if ($torrent->leechers >= 5)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-fire text-orange'
                                                   data-toggle='tooltip' title='' data-original-title='@lang(' common.hot')!'></i>
                                                @lang('common.hot')!
                                            </span>
                                    @endif

                                    @if ($torrent->sticky == 1)
                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-thumbtack text-black'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('
                                                    torrent.sticky')!'></i> @lang('torrent.sticky')
                                            </span>
                                    @endif

                                    @if ($user->updated_at->getTimestamp() < $torrent->created_at->getTimestamp())
                                        <span class='badge-extra text-bold'>
                                                    <i class='{{ config('other.font-awesome') }} fa-magic text-black'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('
                                                        common.new')!'></i> @lang('common.new')
                                                </span>
                                    @endif

                                    @if ($torrent->highspeed == 1)
                                        <span class='badge-extra text-bold'>
                                                    <i class='{{ config('other.font-awesome') }} fa-tachometer text-red'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('
                                                        common.high-speeds')'></i> @lang('common.high-speeds')
                                                </span>
                                    @endif

                                    @if ($torrent->sd == 1)
                                        <span class='badge-extra text-bold'>
                                                    <i class='{{ config('other.font-awesome') }} fa-ticket text-orange'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('
                                                        torrent.sd-content')!'></i> @lang('torrent.sd-content')
                                                </span>
                                    @endif
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
            </div>
        </div>
    </div>
@endsection
