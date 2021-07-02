@extends('layout.default')

@section('title')
    <title>Possible Leech Cheaters - @lang('staff.staff-dashboard') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Possible Leech Cheaters - @lang('staff.staff-dashboard')">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('staff.dashboard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.staff-dashboard')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff.cheaters.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.possible-leech-cheaters')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container">
        <div class="block">
            <h2>@lang('staff.possible-leech-cheaters') (Ghost Leechers)</h2>
            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <p class="text-red">
                        <strong><i class="{{ config('other.font-awesome') }} fa-question"></i>
                            @lang('staff.possible-leech-cheaters')
                        </strong>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('common.user')</th>
                                    <th>@lang('common.group')</th>
                                    <th>@lang('user.member-since')</th>
                                    <th>@lang('user.last-login')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cheaters as $cheater)
                                    <tr>
                                        <td>
                                            <a class="text-bold"
                                                href="{{ route('users.show', ['username' => $cheater->user->username]) }}">{{ $cheater->user->username }}</a>
                                        </td>
                                        <td>
                                            <span class="badge-user text-bold"
                                                style="color:{{ $cheater->user->primaryRole->color }}; background-image:{{ $cheater->user->primaryRole->effect }};">
                                                <i class="{{ $cheater->user->primaryRole->icon }}" data-toggle="tooltip"
                                                    data-original-title="{{ $cheater->user->primaryRole->name }}"></i>
                                                {{ $cheater->user->primaryRole->name }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($cheater->user->created_at != null)
                                                {{ $cheater->user->created_at->toDayDateTimeString() }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if ($cheater->user->last_login != null)
                                                {{ $cheater->user->last_login->toDayDateTimeString() }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="text-center">
                {{ $cheaters->links() }}
            </div>
        </div>
    </div>
@endsection
