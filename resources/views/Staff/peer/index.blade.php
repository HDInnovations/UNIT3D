@extends('layout.with-main')

@section('title')
    <title>Peers - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Peers</li>
@endsection

@section('nav-tabs')
    @include('Staff.partials.user-info-search')
@endsection

@section('page', 'page__staff-peer--index')

@section('main')
    @livewire('peer-search')
@endsection
