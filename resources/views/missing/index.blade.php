@extends('layout.with-main')

@section('title')
    <title>{{ __('media-interface.missing.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">{{ __('media-interface.missing.title') }}</li>
@endsection

@section('page', 'page__missing--index')

@section('main')
    @livewire('missing-media-search')
@endsection
