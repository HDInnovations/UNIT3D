@extends('layout.with-main')

@section('title')
    <title>
        {{ __('common.latest-posts') }} - {{ __('forum.forums') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="Forum {{ __('common.latest-posts') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('forums.index') }}" class="breadcrumb__link">
            {{ __('forum.forums') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.posts') }}
    </li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__post--index')

@section('main')
    @livewire('post-search')
@endsection
