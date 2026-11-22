@extends('layout.with-main')

@section('title')
    <title>
        Watchlist {{ __('common.search') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="Watchlist search - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Watchlist</li>
@endsection

@section('page', 'page__staff-watchlist--index')

@section('main')
    @livewire('watchlist-search')
@endsection
