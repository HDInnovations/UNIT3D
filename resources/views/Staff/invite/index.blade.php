@extends('layout.with-main')

@section('title')
    <title>Invites log - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
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
        {{ __('staff.invites-log') }}
    </li>
@endsection

@section('nav-tabs')
    @include('Staff.partials.user-info-search')
@endsection

@section('page', 'page__staff-invite--index')

@section('main')
    @livewire('invite-log-search')
@endsection
