@extends('layout.default')

@section('title')
    <title>{{ $user->username }} @lang('user.achievements') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('users.show', ['username' => $user->username]) }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}</span>
        </a>
    </li>
    <li>
        <a href="{{ route('achievements.show', ['username' => $user->username]) }}" itemprop="url"
            class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">{{ $user->username }}
                @lang('user.achievements')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        @if (!auth()->user()->isAllowed($user,'achievement','show_achievement'))
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
                @if(auth()->user()->group && auth()->user()->hasRole('moderator') == 1)
                    @include('user.buttons.achievement')
                @else
                    @include('user.buttons.public')
                @endif
                <div class="header gradient blue">
                    <div class="inner_content">
                        <h1>
                            {{ $user->username }} @lang('user.achievements')
                        </h1>
                    </div>
                </div>
                <div class="some-padding">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="panel panel-default">
                                    <div class="panel-heading">@lang('user.unlocked-achievements')</div>
                                    <div class="panel-body">
                                        <br />
                                        <div class="table-responsive">
                                            <table class="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th>@lang('common.name')</th>
                                                        <th>@lang('common.description')</th>
                                                        <th>@lang('common.progress')</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($achievements as $item)
                                                        <tr>
                                                            <td><img src="/img/badges/{{ $item->details->name }}.png"
                                                                    alt="{{ $item->details->name }}" data-toggle="tooltip"
                                                                    data-original-title="{{ $item->details->name }}"></td>
                                                            <td>{{ $item->details->description }}</td>
                                                            @if ($item->isUnlocked())
                                                                <td><span class="label label-success">@lang('user.unlocked')</span></td>
                                                            @else
                                                                <td><span class="label label-warning">@lang('common.progress'):
                                                                        {{ $item->points }}/{{ $item->details->points }}</span></td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4 text-center">
                                <div class="text-green well well-sm">
                                    <h3>
                                        <strong>@lang('user.unlocked-achievements'): </strong>
                                        {{ $user->unlockedAchievements()->count() }}
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
