@extends('layout.default')

@section('title')
    <title>{{ __('mediahub.persons') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('mediahub.persons') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('mediahub.index') }}" class="breadcrumb__link">
            {{ __('mediahub.title') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('mediahub.persons') }}
    </li>
@endsection

@section('page', 'page__person--index')

@section('content')
    @livewire('tmdb-person-search')
@endsection
