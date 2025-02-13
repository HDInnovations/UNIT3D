@extends('layout.with-main')

@section('title')
    <title>Leakers - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Leakers</li>
@endsection

@section('page', 'page__leakers--index')

@section('main')
    @livewire('leaker-search')
@endsection
