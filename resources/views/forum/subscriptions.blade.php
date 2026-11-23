@extends('layout.with-main')

@section('title')
    <title>
        {{ __('common.subscriptions') }} - {{ __('forum.forums') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="Forum {{ __('common.subscriptions') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('forums.index') }}" class="breadcrumb__link">
            {{ __('forum.forums') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.subscriptions') }}
    </li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__subscription--index')

@section('main')
    @livewire('subscribed-forum', [], key('forums'))
    @livewire('subscribed-topic', [], key('topics'))
@endsection
