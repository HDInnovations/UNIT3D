@extends('layout.with-main')

@section('title')
    <title>
        Failed login log - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="Invites log - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.failed-login-log') }}
    </li>
@endsection

@section('page', 'page__staff-failed-logins--index')

@section('main')
    @livewire('failed-login-search')
@endsection
