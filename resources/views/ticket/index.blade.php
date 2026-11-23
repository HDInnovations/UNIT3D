@extends('layout.with-main')

@section('title')
    <title>{{ __('ticket.helpdesk') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('ticket.helpdesk') }}
    </li>
@endsection

@section('page', 'page__ticket--index')

@section('main')
    @livewire('ticket-search')
@endsection
