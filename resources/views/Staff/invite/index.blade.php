@extends('layout.default')

@section('title')
    <title>Invites Log - @lang('staff.staff-dashboard') - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Invites Log - @lang('staff.staff-dashboard')">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('staff.dashboard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.staff-dashboard')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff.invites.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.invites-log')</span>
        </a>
    </li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="block">
            <h2>@lang('staff.invites-log')</h2>
            <hr>
            <div class="row">
                <div class="col-sm-12">
                    <h2><span class="text-blue"><strong><i class="{{ config('other.font-awesome') }} fa-note"></i>
                        {{ $invitecount }} </strong></span>@lang('user.invites-send') </h2>
                    <div class="table-responsive">
                        <table class="table table-condensed table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('user.sender')</th>
                                    <th>@lang('common.email')</th>
                                    <th>Token</th>
                                    <th>@lang('user.created-on')</th>
                                    <th>@lang('user.expires-on')</th>
                                    <th>@lang('user.accepted-by')</th>
                                    <th>@lang('user.accepted-at')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($invites) == 0)
                                    <p>The are no invite logs in the database for this user!</p>
                                @else
                                    @foreach ($invites as $invite)
                                        <tr>
                                            <td>
                                                <a href="{{ route('users.show', ['username' => $invite->sender->username]) }}">
                                                    <span class="text-bold" style="color:{{ $invite->sender->primaryRole->color }}; ">
                                                        <i class="{{ $invite->sender->primaryRole->icon }}"></i>
                                                        {{ $invite->sender->username }}
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                {{ $invite->email }}
                                            </td>
                                            <td>
                                                {{ $invite->code }}
                                            </td>
                                            <td>
                                                {{ $invite->created_at }}
                                            </td>
                                            <td>
                                                {{ $invite->expires_on }}
                                            </td>
                                            <td>
                                                @if ($invite->accepted_by != null && $invite->accepted_by != 1)
                                                    <a href="{{ route('users.show', ['username' => $invite->receiver->username]) }}">
                                                        <span class="text-bold" style="color:{{ $invite->receiver->primaryRole->color }}; ">
                                                            <i class="{{ $invite->receiver->primaryRole->icon }}"></i>
                                                            {{ $invite->receiver->username }}
                                                        </span>
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($invite->accepted_at != null)
                                                    {{ $invite->accepted_at }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="text-center">
                {{ $invites->links() }}
            </div>
        </div>
    </div>
@endsection
