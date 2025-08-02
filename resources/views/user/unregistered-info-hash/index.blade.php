@extends('layout.with-main')

@section('title')
    <title>
        {{ $user->username }} {{ __('user.unregistered-info-hashes') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.unregistered-info-hashes') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-unregistered-info-hash--index')

@section('main')
    @livewire('user-unregistered-info-hash-search', ['userId' => $user->id])
@endsection
