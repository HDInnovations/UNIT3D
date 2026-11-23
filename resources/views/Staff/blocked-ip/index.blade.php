@extends('layout.with-main')

@section('title')
    <title>
        {{ __('staff.blocked-ips') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('staff.blocked-ips') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.blocked-ips') }}
    </li>
@endsection

@section('page', 'page__staff-blocked-ip--index')

@section('main')
    @livewire('block-ip-address')
@endsection
