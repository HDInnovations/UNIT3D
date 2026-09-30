@extends('layout.with-main')

@section('page', 'page__home')

@section('main')
    <section class="home-welcome" aria-labelledby="home-welcome-title">
        <div class="home-welcome__content">
            <p class="home-welcome__eyebrow">{{ config('other.title') }} / {{ __('common.community') }}</p>
            <h1 id="home-welcome-title">{{ __('common.welcome-back', ['name' => auth()->user()->username]) }}</h1>
            <p class="home-welcome__description">{{ __('common.home-intro') }}</p>
            <div class="home-welcome__actions">
                <a class="home-welcome__primary" href="{{ route('torrents.index') }}">
                    <i class="{{ config('other.font-awesome') }} fa-search" aria-hidden="true"></i>
                    {{ __('common.browse-library') }}
                </a>
                <a class="home-welcome__secondary" href="{{ route('torrents.create') }}">
                    <i class="{{ config('other.font-awesome') }} fa-upload" aria-hidden="true"></i>
                    {{ __('common.share-release') }}
                </a>
            </div>
        </div>
        <img class="home-welcome__mark" src="{{ url('/img/vltava-mark.svg') }}" alt="" />
    </section>
    @foreach ($blocks as $block)
        @switch($block)
            @case('news')
                @include('blocks.news')

                @break
            @case('chat')
                @include('blocks.chat')
                @vite('resources/js/unit3d/chat.js')

                @break
            @case('featured')
                @include('blocks.featured')

                @break
            @case('random_media')
                @livewire('random-media')

                @break
            @case('poll')
                @include('blocks.poll')

                @break
            @case('top_torrents')
                @livewire('top-torrents')

                @break
            @case('top_users')
                @livewire('top-users')

                @break
            @case('latest_topics')
                @include('blocks.latest-topics')

                @break
            @case('latest_posts')
                @include('blocks.latest-posts')

                @break
            @case('latest_comments')
                @include('blocks.latest-comments')

                @break
            @case('online')
                @include('blocks.online')

                @break
        @endswitch
    @endforeach
@endsection
