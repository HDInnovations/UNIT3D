@extends('layout.with-main')

@section('title')
    <title>{{ $user->username }} {{ __('user.posts') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.posts') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-post--index')

@section('main')
    @if (auth()->user()->isAllowed($user, 'forum', 'show_post'))
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('forum.posts') }}</h2>
            <div class="panel__body">
                <ol class="topic-posts">
                    @foreach ($posts as $post)
                        <li class="topic-posts__item">
                            <x-forum.post :post="$post" />
                        </li>
                    @endforeach
                </ol>
            </div>
            {{ $posts->links('partials.pagination') }}
        </section>
    @else
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.private-profile') }}</h2>
            <div class="panel__body">{{ __('user.not-authorized') }}</div>
        </section>
    @endif
@endsection
