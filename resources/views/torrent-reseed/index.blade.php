@extends('layout.with-main')

@section('title')
    <title>{{ __('torrent.reseed-requests') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('torrent.reseed-requests') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('home.index') }}" class="breadcrumb__text">
            {{ __('common.home') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('torrent-reseed.index') }}" class="breadcrumb__text">
            {{ __('torrent.reseed-requests') }}
        </a>
    </li>
@endsection

@section('page', 'page__torrent-reseed--index')

@section('main')
    @livewire('torrent-reseed-search')
@endsection
