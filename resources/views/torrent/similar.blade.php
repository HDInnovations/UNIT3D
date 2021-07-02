@extends('layout.default')

@php $meta = null; @endphp
@if ($torrents->first()->category->tv_meta)
    @php $meta = App\Models\Tv::with('genres', 'cast', 'networks', 'seasons')->where('id', '=', $tmdb)->first(); @endphp
@endif
@if ($torrents->first()->category->movie_meta)
    @php $meta = App\Models\Movie::with('genres', 'cast', 'companies', 'collection')->where('id', '=', $tmdb)->first(); @endphp
@endif

@section('title')
    <title>@lang('common.similar') - {{ $meta->title ?? $meta->name }} ({{ substr($meta->release_date ?? $meta->first_air_date, 0, 4) }}) - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="@lang('common.similar') - {{ $meta->title ?? $meta->name }} ({{ substr($meta->release_date ?? $meta->first_air_date, 0, 4) }})">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('torrents') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('torrent.torrents')</span>
        </a>
    </li>
    <li>
        <a href="{{ route('torrents.similar', ['category_id' => $torrents->first()->category_id, 'tmdb' => $torrents->first()->tmdb]) }}"
            itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('common.similar') - {{ $meta->title ?? $meta->name }} ({{ substr($meta->release_date ?? $meta->first_air_date, 0, 4) }})</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block single">
            @if ($torrents->first()->category->movie_meta)
                @include('torrent.partials.movie_meta', ['torrent' => $torrents->first()])
            @endif

            @if ($torrents->first()->category->tv_meta)
                @include('torrent.partials.tv_meta', ['torrent' => $torrents->first()])
            @endif

            @if ($torrents->first()->category->game_meta)
                @include('torrent.partials.game_meta')
            @endif

            <div class="table-responsive mt-20">
                <table class="table table-condensed table-bordered table-striped">
                    @foreach(App\Models\Type::all()->sortBy('position') as $type)
                        @if($torrents->where('type_id', '=', $type->id)->count() > 0)
                            <thead>
                            <tr>
                                <th class="text-center dark-th" colspan="6">
                                    <span style="font-size: 16px;">{{ $type->name }}</span>
                                </th>
                            </tr>
                            </thead>
                        @endif
                        @foreach(App\Models\Resolution::all()->sortBy('position') as $resolution)
                            @if($torrents->where('type_id', '=', $type->id)->where('resolution_id', '=', $resolution->id)->count() > 0)
                            <thead>
                            <tr>
                                <th colspan="6">
                                    <span class="label label-default text-bold">{{ $resolution->name }}</span>
                                </th>
                            </tr>
                            </thead>
                            @endif
                        <tbody>
                        @foreach($torrents->where('type_id', '=', $type->id)->where('resolution_id', '=', $resolution->id)->sortByDesc('created_at') as $torrent)
                            @if($torrent->sticky == 1)
                                <tr class="success">
                            @else
                                <tr>
                                    @endif
                                    <td>
                                        <a class="text-bright" style="font-size: 16px;" href="{{ route('torrent', ['id' => $torrent->id]) }}">
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
                                            <span class="badge-extra">
                                            <i class="{{ config('other.font-awesome') }} fa-upload" data-toggle="tooltip"
                                                data-original-title="@lang('torrent.uploader')"></i> @lang('common.anonymous')
                                            @if ($user->id == $torrent->user->id || $user->hasPrivilegeTo('users_view_private'))
                                                <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                                    ({{ $torrent->user->username }})
                                                </a>
                                                @endif
                                        </span>
                                        @else
                                            <span class="badge-extra">
                                            <i class="{{ config('other.font-awesome') }} fa-upload" data-toggle="tooltip"
                                               data-original-title="@lang('torrent.uploader')"></i>
                                            <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                                {{ $torrent->user->username }}
                                            </a>
                                        </span>
                                        @endif

                                        <span class="badge-extra text-pink">
                                            <i class="{{ config('other.font-awesome') }} fa-heart" data-toggle="tooltip"
                                               data-original-title="@lang('torrent.thanks-given')"></i>
                                            {{ $torrent->thanks_count }}
                                        </span>

                                        <span class="badge-extra text-green">
                                            <i class="{{ config('other.font-awesome') }} fa-comment" data-toggle="tooltip"
                                               data-original-title="@lang('common.comments')"></i>
                                            {{ $torrent->comments_count }}
                                        </span>

                                        @if ($torrent->internal == 1)
                                            <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-magic' data-toggle='tooltip'
                                                   title='' data-original-title='@lang('torrent.internal-release')'
                                                   style="color: #baaf92;"></i>
                                            </span>
                                        @endif

                                        @if ($torrent->stream == 1)
                                            <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-play text-red' data-toggle='tooltip'
                                                   title='' data-original-title='@lang('torrent.stream-optimized')'></i>
                                            </span>
                                        @endif

                                        @if ($torrent->featured == 0)
                                            @if ($torrent->doubleup == 1)
                                                <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-gem text-green'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('torrent.double-upload')'></i>
                                                </span>
                                            @endif
                                            @if ($torrent->free == 1)
                                                <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-star text-gold'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('torrent.freeleech')'></i>
                                                </span>
                                            @endif
                                        @endif

                                        @if ($personal_freeleech)
                                            <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-id-badge text-orange'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('torrent.personal-freeleech')'></i>
                                            </span>
                                        @endif

                                        @php $freeleech_token = \App\Models\FreeleechToken::where('user_id', '=',
                                        $user->id)->where('torrent_id', '=', $torrent->id)->first(); @endphp
                                        @if ($freeleech_token)
                                            <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-star'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('torrent.freeleech-token')'></i>
                                            </span>
                                        @endif

                                        @if ($torrent->featured == 1)
                                            <span class='badge-extra' style='background-image:url(/img/sparkels.gif);'>
                                                <i class='{{ config('other.font-awesome') }} fa-certificate text-pink'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('torrent.featured')'></i>
                                            </span>
                                        @endif
        
                                        @if ($user->hasPrivilegeTo('user_special_freeleech'))
                                            <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('torrent.special-freeleech')'></i>
                                            </span>
                                        @endif

                                        @if (config('other.freeleech') == 1)
                                            <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-globe text-blue'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('torrent.global-freeleech')'></i>
                                            </span>
                                        @endif

                                        @if (config('other.doubleup') == 1)
                                            <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-globe text-green'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('torrent.double-upload')'></i>
                                            </span>
                                        @endif

                                                    @if ($user->hasPrivilegeTo('user_special_double_upload'))
                                                        <span class='badge-extra text-bold'>
                                                <i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('torrent.special-double_upload')'></i>
                                            </span>
                                        @endif

                                        @if ($torrent->leechers >= 5)
                                            <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-fire text-orange'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('common.hot')!'></i>
                                            </span>
                                        @endif

                                        @if ($torrent->sticky == 1)
                                            <span class='badge-extra'>
                                                <i class='{{ config('other.font-awesome') }} fa-thumbtack text-black'
                                                   data-toggle='tooltip' title='' data-original-title='@lang('torrent.sticky')!'></i>
                                            </span>
                                        @endif

                                        @if ($user->updated_at->getTimestamp() < $torrent->created_at->getTimestamp())
                                            <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-magic text-black'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('common.new')!'></i>
                                                </span>
                                        @endif

                                        @if ($torrent->highspeed == 1)
                                            <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-tachometer text-red'
                                                       data-toggle='tooltip' title='' data-original-title='@lang('common.high-speeds')'></i>
                                                </span>
                                        @endif

                                        @if ($torrent->sd == 1)
                                            <span class='badge-extra'>
                                                    <i class='{{ config('other.font-awesome') }} fa-ticket text-orantorrent.sd-content')!'></i>
                                                </span>
                                        @endif
                                    </td>

                                    <td>
                                        <time>{{ $torrent->created_at->diffForHumans() }}</time>
                                    </td>
                                    <td>
                                        <span class='badge-extra text-blue'>{{ $torrent->getSize() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('peers', ['id' => $torrent->id]) }}">
                                        <span class='badge-extra text-green'>
                                            {{ $torrent->seeders }}
                                        </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('peers', ['id' => $torrent->id]) }}">
                                        <span class='badge-extra text-red'>
                                            {{ $torrent->leechers }}
                                        </span>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('history', ['id' => $torrent->id]) }}">
                                        <span class='badge-extra text-orange'>
                                            {{ $torrent->times_completed }} @lang('common.times')
                                        </span>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                        </tbody>
                        @endforeach
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection
