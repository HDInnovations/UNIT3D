@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.seeds') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_seeds', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.seeds')</span>
        </a>
    </li>
@endsection

@section('content')

    <div class="container-fluid">
        <div class="block">
            @include('user.buttons.stats')
            <div class="header gradient blue">
                <div class="inner_content">
                    <h1>
                        {{ $user->username }} @lang('user.seeds')
                    </h1>
                </div>
            </div>
            <div class="button-holder some-padding">
                <div class="button-left">

                </div>
                <div class="button-right">
                    <span class="badge-user"><strong>@lang('user.total-download'):</strong>
                        <span class="badge-extra text-red">{{ App\Helpers\StringHelper::formatBytes($his_downl, 2) }}</span>
                        <span class="badge-extra text-orange" data-toggle="tooltip"
                            data-original-title="@lang('user.credited-download')">{{ App\Helpers\StringHelper::formatBytes($his_downl_cre, 2) }}</span>
                    </span>
                    <span class="badge-user"><strong>@lang('user.total-upload'):</strong>
                        <span class="badge-extra text-green">{{ App\Helpers\StringHelper::formatBytes($his_upl, 2) }}</span>
                        <span class="badge-extra text-blue" data-toggle="tooltip"
                            data-original-title="@lang('user.credited-upload')">{{ App\Helpers\StringHelper::formatBytes($his_upl_cre, 2) }}</span>
                    </span>
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
                        <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">Torrent</label>
                        <div class="col-sm-10">
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="dying" value="1" class="userFilter" trigger="click"> Dying
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="legendary" value="1" class="userFilter" trigger="click">
                                    Legendary
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="old" value="1" class="userFilter" trigger="click"> Old
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="huge" value="1" class="userFilter" trigger="click"> Huge
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="large" value="1" class="userFilter" trigger="click"> Large
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="everyday" value="1" class="userFilter" trigger="click">
                                    Everyday
                                </label>
                            </span>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">Seed</label>
                        <div class="col-sm-10">
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="legendary_seeder" value="1" class="userFilter"
                                        trigger="click"> Legendary
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="mvp_seeder" value="1" class="userFilter" trigger="click"> MVP
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="committed_seeder" value="1" class="userFilter"
                                        trigger="click"> Committed
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="teamplayer_seeder" value="1" class="userFilter"
                                        trigger="click"> Team Player
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="participant_seeder" value="1" class="userFilter"
                                        trigger="click"> Participant
                                </label>
                            </span>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="qty" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.sort')</label>
                        <div class="col-sm-2">
                            <label for="sorting"></label><select id="sorting" name="sorting" trigger="change"
                                class="form-control userFilter">
                                <option value="hcreated_at">@lang('torrent.created_at')</option>
                                <option value="name">@lang('torrent.name')</option>
                                <option value="size">@lang('torrent.size')</option>
                                <option value="seeders">@lang('torrent.seeders')</option>
                                <option value="leechers">@lang('torrent.leechers')</option>
                                <option value="times_completed">@lang('torrent.completed-times')</option>
                                <option value="seedtime">@lang('torrent.seedtime')</option>
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
            <div id="userFilter" userName="{{ $user->username }}" userId="{{ $user->id }}" view="seeds">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                            <th>@lang('torrent.name')</th>
                            <th>@lang('torrent.size')</th>
                            <th>@lang('torrent.seeders')</th>
                            <th>@lang('torrent.leechers')</th>
                            <th>@lang('torrent.completed')</th>
                            <th>@lang('torrent.seedtime')</th>
                            <th>@lang('torrent.created_at')</th>
                        </thead>
                        <tbody>
                            @foreach ($seeds as $p)
                                <tr>
                                    <td>
                                        <a class="view-torrent" href="{{ route('torrent', ['id' => $p->torrent_id]) }}"
                                            data-toggle="tooltip" title="{{ $p->torrent->name }}">
                                            {{ $p->torrent->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-blue text-bold"> {{ $p->torrent->getSize() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-green text-bold"> {{ $p->torrent->seeders }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-red text-bold"> {{ $p->torrent->leechers }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-orange text-bold"> {{ $p->torrent->times_completed }}
                                            @lang('common.times')</span>
                                    </td>
                                    @if ($p->seedtime < config('hitrun.seedtime')) <td>
                                            <span
                                                class="badge-extra text-red">{{ App\Helpers\StringHelper::timeElapsed($p->seedtime) }}</span>
                                            </td>
                                        @else
                                            <td>
                                                <span
                                                    class="badge-extra text-green">{{ App\Helpers\StringHelper::timeElapsed($p->seedtime) }}</span>
                                            </td>
                                        @endif
                                        <td>{{ $p->history_created_at && $p->history_created_at != null ? $p->history_created_at : 'N/A' }}
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $seeds->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
