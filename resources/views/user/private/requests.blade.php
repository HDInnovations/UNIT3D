@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.requested') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_requested', ['username' => $user->username]) }}" itemprop="url"
            class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.requested')</span>
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
                        {{ $user->username }} @lang('user.requested')
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
                    @if (config('hitrun.enabled') == true)
                        <div class="mx-0 mt-5 form-group fatten-me">
                            <label for="name" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.status')</label>
                            <div class="col-sm-10">
                                <span class="badge-user">
                                    <label class="inline">
                                        <input type="checkbox" id="unfilled" value="1" class="userFilter" trigger="click">
                                        <span class="{{ config('other.font-awesome') }} fa-times-circle text-blue"></span>
                                        @lang('request.unfilled')
                                    </label>
                                </span>
                                <span class="badge-user">
                                    <label class="inline">
                                        <input type="checkbox" id="claimed" value="1" class="userFilter" trigger="click">
                                        <span class="{{ config('other.font-awesome') }} fa-hand-paper text-blue"></span>
                                        @lang('request.claimed')
                                    </label>
                                </span>
                                <span class="badge-user">
                                    <label class="inline">
                                        <input type="checkbox" id="pending" value="1" class="userFilter" trigger="click">
                                        <span class="{{ config('other.font-awesome') }} fa-question-circle text-blue"></span>
                                        @lang('request.pending')
                                    </label>
                                </span>
                                <span class="badge-user">
                                    <label class="inline">
                                        <input type="checkbox" id="filled" value="1" class="userFilter" trigger="click">
                                        <span class="{{ config('other.font-awesome') }} fa-check-circle text-blue"></span>
                                        @lang('request.filled')
                                    </label>
                                </span>
                            </div>
                        </div>
                    @endif
                    <div class="mx-0 mt-5 form-group fatten-me">
                        <label for="qty" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.sort')</label>
                        <div class="col-sm-2">
                            <label for="sorting"></label><select id="sorting" name="sorting" trigger="change"
                                class="form-control userFilter">
                                <option value="date">@lang('bon.date')</option>
                                <option value="name">@lang('torrent.name')</option>
                                <option value="bounty">@lang('request.bounty')</option>
                                <option value="votes">@lang('request.votes')</option>
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
            <div id="userFilter" userName="{{ $user->username }}" userId="{{ $user->id }}" view="requests">
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-bordered">
                        <thead>
                            <th class="torrents-icon">@lang('torrent.category')</th>
                            <th>@lang('torrent.type')</th>
                            <th class="torrents-filename col-sm-6">@lang('request.request')</th>
                            <th>@lang('request.votes')</th>
                            <th>@lang('common.comments')</th>
                            <th>@lang('request.bounty')</th>
                            <th>@lang('request.age')</th>
                            <th>@lang('request.claimed') / @lang('request.filled')</th>
                        </thead>
                        <tbody>
                            @foreach ($torrentRequests as $torrentRequest)
                                <tr>
                                    <td>
                                        <div class="text-center">
                                            <i class="{{ $torrentRequest->category->icon }} torrent-icon" data-toggle="tooltip"
                                                data-original-title="{{ $torrentRequest->category->name }} @lang('request.request')"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="label label-success">
                                            {{ $torrentRequest->type->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <a class="view-torrent" href="{{ route('request', ['id' => $torrentRequest->id]) }}">
                                            {{ $torrentRequest->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge-user">
                                            {{ $torrentRequest->votes }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-user">
                                            {{ $torrentRequest->comments->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-user">
                                            {{ $torrentRequest->bounty }}
                                        </span>
                                    </td>
                                    <td>
                                        <span>
                                            {{ $torrentRequest->created_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($torrentRequest->claimed != null && $torrentRequest->filled_hash == null)
                                            <button class="btn btn-xs btn-primary">
                                                <i class="{{ config('other.font-awesome') }} fa-hand-paper"></i>
                                                @lang('request.claimed')
                                            </button>
                                        @elseif ($torrentRequest->filled_hash != null && $torrentRequest->approved_by == null)
                                            <button class="btn btn-xs btn-info">
                                                <i class="{{ config('other.font-awesome') }} fa-question-circle"></i>
                                                @lang('request.pending')
                                            </button>
                                        @elseif ($torrentRequest->filled_hash == null)
                                            <button class="btn btn-xs btn-danger">
                                                <i class="{{ config('other.font-awesome') }} fa-times-circle"></i>
                                                @lang('request.unfilled')
                                            </button>
                                        @else
                                            <button class="btn btn-xs btn-success">
                                                <i class="{{ config('other.font-awesome') }} fa-check-circle"></i>
                                                @lang('request.filled')
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $torrentRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
