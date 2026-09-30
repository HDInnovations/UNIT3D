@extends('layout.with-main')

@section('title')
    <title>{{ __('staff-interface.leakers') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('staff-interface.leakers') }}</li>
@endsection

@section('page', 'page__staff-leaker--index')

@section('main')
    @livewire('leaker-search')
@endsection
