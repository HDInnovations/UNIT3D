@extends('layout.with-main')

@section('title')
    <title>{{ __('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('stat.stats') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('clients') }}">
            {{ __('page.blacklist-clients') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('uploaded') }}">
            {{ __('common.users') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('seeded') }}">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('bountied') }}">
            {{ __('request.requests') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('groups') }}">
            {{ __('common.groups') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('languages') }}">
            {{ __('common.languages') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('themes') }}">Themes</a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('yearly_overviews.index') }}">Overview</a>
    </li>
@endsection

@section('page', 'page__stats--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.nerd-stats') }}</h2>
        <div class="panel__body">{{ __('stat.nerd-stats-desc') }}. {{ __('stat.updated') }}</div>
    </section>
    <div class="stats__panels">
        <livewire:stats.torrent-stats />
        <livewire:stats.user-stats />
        <livewire:stats.peer-stats />
        <livewire:stats.traffic-stats />
    </div>
@endsection
