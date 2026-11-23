@extends('layout.with-main')

@section('title')
    <title>{{ __('mediahub.companies') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('mediahub.companies') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('mediahub.companies') }}
    </li>
@endsection

@section('page', 'page__company--index')

@section('main')
    @livewire('tmdb-company-search')
@endsection
