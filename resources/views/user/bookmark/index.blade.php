@extends('layout.with-main')

@section('title')
    <title>
        {{ $user->username }} {{ __('torrent.bookmarks') }} - {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('torrent.bookmarks') }}
    </li>
@endsection

@section('page', 'page__user-bookmark--index')

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('main')
    @livewire('user-bookmarks', ['userId' => $user->id])
@endsection
