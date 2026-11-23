@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('playlist.playlists') }}
    </li>
@endsection

@section('page', 'page__playlist--index')

@section('main')
    @livewire('playlist-search')
@endsection
