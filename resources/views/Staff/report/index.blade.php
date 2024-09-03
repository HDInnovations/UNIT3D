@extends('layout.default')

@section('title')
    <title>
        Reports - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="Reports - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.reports-log') }}
    </li>
@endsection

@section('page', 'page__report--index')

@section('main')
    @livewire('report-search')
@endsection
