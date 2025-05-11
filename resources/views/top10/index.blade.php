@extends('layout.with-main')

@section('title')
    <title>{{ __('common.top-10') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a class="breadcrumb__link" href="{{ route('torrents.index') }}">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.top-10') }}
    </li>
@endsection

@section('nav-tabs')
    @include('partials.nav-bar')
@endsection

@section('page', 'page__top10--index')

@section('main')
    <livewire:top-10 lazy />
@endsection
