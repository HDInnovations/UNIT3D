@extends('layout.default')

@section('title')
    <title>{{ __('notification.notifications') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('notification.notifications') }}
    </li>
@endsection

@section('page', 'page__user-notification--index')

@section('content')
    @livewire('notification-search')
@endsection
