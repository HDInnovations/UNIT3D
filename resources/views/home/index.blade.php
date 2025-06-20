@extends('layout.with-main')

@section('page', 'page__home')

@section('main')
    @if ($user->settings?->news_visible ?? config('other.default_news_visible'))
        @include('blocks.news')
    @endif

    @if ($user->settings?->chat_visible ?? config('other.default_chat_visible'))
        <div id="vue">
            @include('blocks.chat')
        </div>
        @vite('resources/js/unit3d/chat.js')
    @endif

    @if ($user->settings?->featured_visible ?? config('other.default_featured_visible'))
        @include('blocks.featured')
    @endif

    @if ($user->settings?->random_media_visible ?? config('other.default_random_media_visible'))
        @livewire('random-media')
    @endif

    @if ($user->settings?->poll_visible ?? config('other.default_poll_visible'))
        @include('blocks.poll')
    @endif

    @if ($user->settings?->top_torrents_visible ?? config('other.default_top_torrents_visible'))
        @livewire('top-torrents')
    @endif

    @if ($user->settings?->top_users_visible ?? config('other.default_top_users_visible'))
        @livewire('top-users')
    @endif

    @if ($user->settings?->latest_topics_visible ?? config('other.default_latest_topics_visible'))
        @include('blocks.latest-topics')
    @endif

    @if ($user->settings?->latest_posts_visible ?? config('other.default_latest_posts_visible'))
        @include('blocks.latest-posts')
    @endif

    @if ($user->settings?->latest_comments_visible ?? config('other.default_latest_comments_visible'))
        @include('blocks.latest-comments')
    @endif

    @if ($user->settings?->online_visible ?? config('other.default_online_visible'))
        @include('blocks.online')
    @endif
@endsection
