@extends('layout.with-main')

@section('title')
    <title>{{ __('mediahub.networks') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('mediahub.networks') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('mediahub.networks') }}
    </li>
@endsection

@section('page', 'page__network--index')

@section('main')
    @livewire('tmdb-network-search')
@endsection
