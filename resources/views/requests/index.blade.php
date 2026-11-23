@extends('layout.with-main')

@section('title')
    <title>{{ __('request.requests') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('request.requests') }}
    </li>
@endsection

@section('page', 'page__request--index')

@section('main')
    @livewire('torrent-request-search')
@endsection
