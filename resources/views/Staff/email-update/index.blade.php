@extends('layout.with-main')

@section('title')
    <title>
        {{ __('common.user') }} {{ __('user.email-updates') }} -
        {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('user.email-updates') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.email-updates') }}
    </li>
@endsection

@section('page', 'page__staff-email-update--index')

@section('main')
    @livewire('email-update-search')
@endsection
