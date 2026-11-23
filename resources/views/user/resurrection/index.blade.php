@extends('layout.with-main')

@section('title')
    <title>
        {{ $user->username }} {{ __('user.resurrections') }} - {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.resurrections') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-resurrection--index')

@section('main')
    @livewire('user-resurrections', ['userId' => $user->id])
@endsection
