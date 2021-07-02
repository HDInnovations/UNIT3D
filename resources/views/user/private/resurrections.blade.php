@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.resurrections') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_resurrections', ['username' => $user->username]) }}" itemprop="url"
            class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}
                @lang('user.resurrections')</span>
        </a>
    </li>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="block">
            @include('user.buttons.other')
            <div class="header gradient blue">
                <div class="inner_content">
                    <h1>
                        {{ $user->username }} @lang('user.resurrections')
                    </h1>
                </div>
            </div>
            <hr class="some-padding">
            <div class="container well search mt-5">
                <div class="form-horizontal form-condensed form-torrent-search form-bordered">
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.name')</label>
                        <div class="col-sm-9 fatten-me">
                            <label for="search"></label><input type="text" class="form-control userFilter" trigger="keyup"
                                id="search" placeholder="@lang('torrent.name')">
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.status')</label>
                        <div class="col-sm-10">
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="rewarded" value="1" class="userFilter" trigger="click">
                                    @lang('graveyard.rewarded')
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="notrewarded" value="1" class="userFilter" trigger="click">
                                    @lang('graveyard.not-rewarded')
                                </label>
                            </span>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="qty" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.sort')</label>
                        <div class="col-sm-2">
                            <label for="sorting"></label><select id="sorting" name="sorting" trigger="change"
                                class="form-control userFilter">
                                <option value="created_at">@lang('graveyard.resurrect-date')</option>
                                <option value="name">@lang('torrent.name')</option>
                                <option value="size">@lang('torrent.size')</option>
                                <option value="seeders">@lang('torrent.seeders')</option>
                                <option value="leechers">@lang('torrent.leechers')</option>
                                <option value="times_completed">@lang('torrent.completed-times')</option>
                                <option value="goal">@lang('graveyard.seedtime-goal')</option>
                            </select>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="qty"
                            class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.direction')</label>
                        <div class="col-sm-2">
                            <label for="direction"></label><select id="direction" name="direction" trigger="change"
                                class="form-control userFilter">
                                <option value="desc">@lang('common.descending')</option>
                                <option value="asc">@lang('common.ascending')</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <span id="filterHeader"></span>
            <div id="userFilter" userName="{{ $user->username }}" userId="{{ $user->id }}" view="resurrections">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                            <th>@lang('torrent.name')</th>
                            <th>@lang('torrent.size')</th>
                            <th>@lang('torrent.seeders')</th>
                            <th>@lang('torrent.leechers')</th>
                            <th>@lang('torrent.completed')</th>
                            <th>@lang('graveyard.resurrect-date')</th>
                            <th>@lang('graveyard.current-seedtime')</th>
                            <th>@lang('graveyard.seedtime-goal')</th>
                            <th>@lang('graveyard.rewarded')</th>
                            <th>@lang('common.cancel')</th>
                        </thead>
                        <tbody>
                            @foreach ($resurrections as $resurrection)
                                <tr>
                                    <td>
                                        <div class="torrent-file">
                                            <div>
                                                <a class="view-torrent"
                                                    href="{{ route('torrent', ['id' => $resurrection->torrent->id]) }}">
                                                    {{ $resurrection->torrent->name }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-blue text-bold">
                                            {{ $resurrection->torrent->getSize() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-green text-bold">
                                            {{ $resurrection->torrent->seeders }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-red text-bold">
                                            {{ $resurrection->torrent->leechers }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-orange text-bold">
                                            {{ $resurrection->torrent->times_completed }} @lang('common.times')</span>
                                    </td>
                                    <td>
                                        {{ $resurrection->created_at->diffForHumans() }}
                                    </td>
                                    <td>
                                        @php $torrent = App\Models\Torrent::where('id', '=',
                                        $resurrection->torrent_id)->pluck('info_hash'); @endphp
                                        @php $history = App\Models\History::select(['seedtime'])->where('user_id', '=',
                                        $user->id)->where('info_hash', '=', $torrent)->first(); @endphp
                                        {{ empty($history) ? '0' : App\Helpers\StringHelper::timeElapsed($history->seedtime) }}
                                    </td>
                                    <td>
                                        {{ App\Helpers\StringHelper::timeElapsed($resurrection->seedtime) }}
                                    </td>
                                    <td>
                                        @if ($resurrection->rewarded == 0)
                                            <i class="{{ config('other.font-awesome') }} fa-times text-red"></i>
                                        @else
                                            <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('graveyard.destroy', ['id' => $resurrection->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" @if ($resurrection->rewarded ==
                                                1) disabled @endif>
                                                <i class="{{ config('other.font-awesome') }} fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $resurrections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
