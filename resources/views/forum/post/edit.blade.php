@extends('layout.with-main')

@section('title')
    <title>
        {{ __('common.edit') }} {{ __('forum.post') }} - {{ $topic->name }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="{{ $forum->name . ' - ' . __('forum.edit-post') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('forums.index') }}" class="breadcrumb__link">
            {{ __('forum.forums') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a
            href="{{ route('forums.categories.show', ['id' => $category->id]) }}"
            class="breadcrumb__link"
        >
            {{ $category->name }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('forums.show', ['id' => $forum->id]) }}" class="breadcrumb__link">
            {{ $forum->name }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('topics.show', ['id' => $topic->id]) }}" class="breadcrumb__link">
            {{ $topic->name }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('common.edit') }}</li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__post--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }} {{ __('forum.post') }} {{ strtolower(__('forum.in')) }}: {{ $forum->name }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('posts.update', ['id' => $post->id]) }}"
            >
                @csrf
                @method('PATCH')
                @livewire('bbcode-input',
                    ['name' => 'content', 'label' => __('forum.post'), 'content' => $post->content])
                <button class="form__button form__button--filled">{{ __('common.submit') }}</button>
            </form>
        </div>
    </section>
@endsection
