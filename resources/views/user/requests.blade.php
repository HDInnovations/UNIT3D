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
        @if (!auth()->user()->isAllowed($user,'request','show_requested'))
            <div class="container pl-0 text-center">
                <div class="jumbotron shadowed">
                    <div class="container">
                        <h1 class="mt-5 text-center">
                            <i class="{{ config('other.font-awesome') }} fa-times text-danger"></i>@lang('user.private-profile')
                        </h1>
                        <div class="separator"></div>
                        <p class="text-center">@lang('user.not-authorized')</p>
                    </div>
                </div>
            </div>
        @else
            <div class="block">
                @include('user.buttons.public')
                <div class="header gradient blue">
                    <div class="inner_content">
                        <h1>
                            {{ $user->username }} @lang('user.requested')
                        </h1>
                    </div>
                </div>
        
                @if ( $user->private_profile == 1 && auth()->user()->id != $user->id && !auth()->user()->hasRole('moderator') )
                    <div class="container">
                        <div class="jumbotron shadowed">
                            <div class="container">
                                <h1 class="mt-5 text-center">
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-danger"></i>@lang('user.private-profile')
                                </h1>
                                <div class="separator"></div>
                                <p class="text-center">@lang('user.not-authorized')</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div view="requests">
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
                @endif
            </div>
        @endif
    </div>
@endsection
