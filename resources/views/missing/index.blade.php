@extends('layout.with-main')

@section('title')
    <title>Missing media</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">Missing media</li>
@endsection

@section('page', 'page__missing--index')

@section('main')
    @livewire('missing-media-search')
@endsection
