@extends('layout.with-main')

@section('title')
    <title>{{ __('torrent.torrents') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('torrent.torrents') }} {{ config('other.title') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('torrent.torrents') }}
    </li>
@endsection

@section('nav-tabs')
    @include('partials.nav-bar')
@endsection

@section('page', 'page__torrent--index')

@section('main')
    @livewire('torrent-search')
@endsection
