@extends('layout.default')

@section('title')
    <title>{{ $forum->name }} - {{ __('forum.forums') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('forum.display-forum') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('forums.index') }}" class="breadcrumb__link">
            {{ __('forum.forums') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a
            href="{{ route('forums.categories.show', ['id' => $forum->category->id]) }}"
            class="breadcrumb__link"
        >
            {{ $forum->category->name }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $forum->name }}
    </li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__forum-topic--index')

@section('content')
    @livewire('forum-topic-search', ['forum' => $forum])
@endsection
