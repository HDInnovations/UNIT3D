@extends('layout.with-main')

@section('title')
    <title>Announces - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Announces</li>
@endsection

@section('page', 'page__staff-announce--index')

@section('main')
    @livewire('announce-search')
@endsection
