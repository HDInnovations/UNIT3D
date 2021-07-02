@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.active') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_active', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.active')</span>
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
                        {{ $user->username }} @lang('user.active')
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
                            <label for="search"></label>
                            <input type="text" class="form-control userFilter" trigger="keyup" id="search"
                                placeholder="@lang('torrent.name')">
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.status')</label>
                        <div class="col-sm-10">
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="seeding" value="1" class="userFilter" trigger="click">
                                    @lang('torrent.seeding')
                                </label>
                            </span>
                            <span class="badge-user">
                                <label class="inline">
                                    <input type="checkbox" id="leeching" value="1" class="userFilter" trigger="click">
                                    @lang('torrent.leeching')
                                </label>
                            </span>
                        </div>
                    </div>
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="qty" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.sort')</label>
                        <div class="col-sm-2">
                            <label for="sorting"></label><select id="sorting" name="sorting" trigger="change"
                                class="form-control userFilter">
                                <option value="created_at">@lang('torrent.created_at')</option>
                                <option value="name">@lang('torrent.name')</option>
                                <option value="seeder">@lang('torrent.seeding')</option>
                                <option value="size">@lang('torrent.size')</option>
                                <option value="seeders">@lang('torrent.seeders')</option>
                                <option value="leechers">@lang('torrent.leechers')</option>
                                <option value="uploaded">@lang('common.upload')</option>
                                <option value="downloaded">@lang('common.download')</option>
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
            <div id="userFilter" userName="{{ $user->username }}" userId="{{ $user->id }}" view="active">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                            <th>@lang('torrent.name')</th>
                            <th>@lang('torrent.client')</th>
                            <th>@lang('torrent.size')</th>
                            <th>@lang('torrent.seeders')</th>
                            <th>@lang('torrent.leechers')</th>
                            <th>@lang('common.upload')</th>
                            <th>@lang('common.download')</th>
                            <th>@lang('torrent.remaining')</th>
                            <th>@lang('torrent.progress')</th>
                        </thead>
                        <tbody>
                            @foreach ($active as $p)
                                <tr>
                                    <td>
                                        <a class="view-torrent" href="{{ route('torrent', ['id' => $p->torrent_id]) }}"
                                            data-toggle="tooltip" title="{{ $p->torrent->name }}">
                                            {{ $p->torrent->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span
                                            class="badge-extra text-purple text-bold">{{ $p->agent ?: trans('common.unknown') }}</span>
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
                                        <span class="badge-extra text-green text-bold">
                                            {{ App\Helpers\StringHelper::formatBytes($p->uploaded, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-extra text-red text-bold">
                                            {{ App\Helpers\StringHelper::formatBytes($p->downloaded, 2) }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge-extra text-orange text-bold">{{ \App\Helpers\StringHelper::formatBytes($p->left, 2) }}</span>
                                    </td>
                                    @if ($p->seeder == 0)
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped active" role="progressbar"
                                                    aria-valuenow="{{ ($p->downloaded / $p->torrent->size) * 100 }}"
                                                    aria-valuemin="0" aria-valuemax="100"
                                                    style="width: {{ ($p->downloaded / $p->torrent->size) * 100 }}%;">
                                                    {{ round(($p->downloaded / $p->torrent->size) * 100) }}%
                                                </div>
                                            </div>
                                        </td>
                                    @elseif ($p->seeder == 1)
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar progress-bar-striped active" role="progressbar"
                                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                                    100%
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $active->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
