@extends('layout.default')

@section('title')
    <title>@lang('graveyard.graveyard') - {{ config('other.title') }}</title>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('graveyard.index') }}" itemprop="url" class="l-breadcrumb-item-link">
            <span itemprop="title" class="l-breadcrumb-item-link-title">@lang('graveyard.graveyard')</span>
        </a>
    </li>
@endsection

@section('content')
    <div>
        @livewire('graveyard-search')
    </div>
@endsection
