@extends('layout.with-main')

@section('title')
    <title>Top 10</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('torrent.torrents') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('torrents.index') }}">
            {{ __('common.search') }}
        </a>
    </li>
    <li class="nav-tabV2--active">
        <a class="nav-tab--active__link" href="{{ route('top10.index') }}">Top 10</a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('rss.index') }}">
            {{ __('rss.rss') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('torrents.create') }}">
            {{ __('common.upload') }}
        </a>
    </li>
@endsection

@section('page', 'page__top10--index')

@section('main')
    <livewire:top-10 lazy />
@endsection
