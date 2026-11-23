@extends('layout.default')

@section('title')
    <title>
        {{ __('common.latest-topics') }} - {{ __('forum.forums') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="Forum {{ __('common.latest-topics') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('forums.index') }}" class="breadcrumb__link">
            {{ __('forum.forums') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.topics') }}
    </li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__topic--index')

@section('content')
    @livewire('topic-search')
@endsection
