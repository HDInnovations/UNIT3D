@extends('layout.with-main')

@section('title')
    <title>Reports - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Reports" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('common.reports') }}
    </li>
@endsection

@section('page', 'page__report--index')

@section('main')
    @livewire('report-search')
@endsection
