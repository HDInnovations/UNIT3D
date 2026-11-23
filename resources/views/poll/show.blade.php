@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('poll.poll') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('polls.index') }}" class="breadcrumb__link">
            {{ __('poll.polls') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $poll->title }}
    </li>
@endsection

@section('page', 'page__poll--show')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $poll->title }}</h2>
        <div class="panel__body">
            @include('poll.forms.vote')
        </div>
    </section>
@endsection

@if ($poll->multiple_choice)
    @section('sidebar')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('common.info') }}</h2>
            <div class="panel__body">{{ __('poll.multiple-choice') }}</div>
        </section>
    @endsection
@endif
