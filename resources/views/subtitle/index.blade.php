@extends('layout.default')

@section('title')
    <title>{{ __('common.subtitles') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('common.subtitles') }}
    </li>
@endsection

@section('page', 'page__subtitle--index')

@section('content')
    @livewire('subtitle-search')
@endsection
