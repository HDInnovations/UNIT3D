@extends('layout.with-main')

@section('title')
    <title>{{ $user->username }} {{ __('user.topics') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.topics') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-topic--index')

@section('main')
    @if (auth()->user()->isAllowed($user, 'forum', 'show_topic'))
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('forum.topics') }}</h2>
            <ul class="topic-listings">
                @foreach ($topics as $topic)
                    <li class="topic-listings__item">
                        <x-forum.topic-listing :topic="$topic" />
                    </li>
                @endforeach
            </ul>
            {{ $topics->links('partials.pagination') }}
        </section>
    @else
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.private-profile') }}</h2>
            <div class="panel__body">{{ __('user.not-authorized') }}</div>
        </section>
    @endif
@endsection
