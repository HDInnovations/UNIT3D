@extends('layout.default')

@section('title')
    <title>@lang('backup.backup') @lang('backup.manager') - @lang('staff.staff-dashboard')
        - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="@lang('backup.backup') @lang('backup.manager') - @lang('staff.staff-dashboard')">
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('staff.dashboard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('staff.staff-dashboard')</span>
        </a>
    </li>
    <li class="active">
        <a href="{{ route('staff.backups.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('backup.backup')
                @lang('backup.manager')</span>
        </a>
    </li>
@endsection

@section('content')
    <div>
        @livewire('backup-panel')
    </div>
@endsection
