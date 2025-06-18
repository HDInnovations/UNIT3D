@extends('layout.with-main')

@section('page', 'page__home')

@section('main')
    @if (! $user->settings?->news_hidden)
        @include('blocks.news')
    @endif

    @if (! $user->settings?->chat_hidden)
        <div id="vue">
            @include('blocks.chat')
        </div>
        @vite('resources/js/unit3d/chat.js')
    @endif

    @if (! $user->settings?->featured_hidden)
        @include('blocks.featured')
    @endif

    @if (! $user->settings?->random_media_hidden)
        @livewire('random-media')
    @endif

    @if (! $user->settings?->poll_hidden)
        @include('blocks.poll')
    @endif

    @if (! $user->settings?->top_torrents_hidden)
        @livewire('top-torrents')
    @endif

    @if (! $user->settings?->top_users_hidden)
        @livewire('top-users')
    @endif

    @if (! $user->settings?->latest_topics_hidden)
        @include('blocks.latest-topics')
    @endif

    @if (! $user->settings?->latest_posts_hidden)
        @include('blocks.latest-posts')
    @endif

    @if (! $user->settings?->latest_comments_hidden)
        @include('blocks.latest-comments')
    @endif

    @if (! $user->settings?->online_hidden)
        @include('blocks.online')
    @endif
@endsection
