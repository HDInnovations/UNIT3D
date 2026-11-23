@extends('layout.with-main')

@section('title')
    <title>
        {{ __('common.user') }} {{ __('common.search') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="User search - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('nav-tabs')
    @include('Staff.partials.user-info-search')
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.users') }}
    </li>
@endsection

@section('page', 'page__staff-user--index')

@section('main')
    @livewire('user-search')
@endsection
