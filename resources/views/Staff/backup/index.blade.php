@extends('layout.with-main')

@section('title')
    <title>
        {{ __('backup.backup') }} {{ __('backup.manager') }} - {{ __('staff.staff-dashboard') }}
        - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('backup.backup') }} {{ __('backup.manager') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('backup.backup') }} {{ __('backup.manager') }}</li>
@endsection

@section('page', 'page__staff-backup-manager--index')

@section('main')
    @livewire('backup-panel')
@endsection
