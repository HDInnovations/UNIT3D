@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.followers') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('user_followers', ['username' => $user->username]) }}" itemprop="url"
            class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }} @lang('user.followers')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        @if (!auth()->user()->isAllowed($user,'follower','show_follower'))
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
                @if (auth()->user()->id == $user->id || auth()->user()->hasRole('moderator'))
                    @include('user.buttons.follower')
                @else
                    @include('user.buttons.public')
                @endif
                <div class="header gradient blue">
                    <div class="inner_content">
                        <h1>
                            {{ $user->username }} @lang('user.followers')
                        </h1>
                    </div>
                </div>
                <div class="forum-categories">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>@lang('user.avatar')</th>
                                <th>@lang('user.user')</th>
                                <th>@lang('common.created_at')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $f)
                                <tr>
                                    @if ($f->user->image != null)
                                        <td><a href="{{ route('users.show', ['username' => $f->user->username]) }}">
                                                <img src="{{ url('files/img/' . $f->user->image) }}" alt="avatar" data-toggle="tooltip"
                                                    title="{{ $f->user->username }}" height="50px"
                                                    data-original-title="{{ $f->user->username }}">
                                            </a></td>
                                    @else
                                        <td><a href="{{ route('users.show', ['username' => $f->user->username]) }}">
                                                <img src="{{ url('img/profile.png') }}" alt="avatar" data-toggle="tooltip"
                                                    title="{{ $f->user->username }}" height="50px"
                                                    data-original-title="{{ $f->user->username }}">
                                            </a></td>
                                    @endif
                                    <td><a href="{{ route('users.show', ['username' => $f->user->username]) }}">
                                            <span class="badge-user text-bold"
                                                style="color:{{ $f->user->primaryRole->color }};">{{ $f->user->username }}</span>
                                        </a></td>
                                    <td>{{ $f->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center col-md-12">
                    {{ $results->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
