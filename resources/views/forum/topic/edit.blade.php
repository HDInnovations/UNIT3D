@extends('layout.with-main')

@section('title')
    <title>{{ __('forum.create-new-topic') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('forum.edit-topic') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('forums.index') }}" class="breadcrumb__link">
            {{ __('forum.forums') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a
            href="{{ route('forums.categories.show', ['id' => $topic->forum->category->id]) }}"
            class="breadcrumb__link"
        >
            {{ $topic->forum->category->name }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('topics.show', ['id' => $topic->id]) }}" class="breadcrumb__link">
            {{ $topic->name }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__topic--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('forum.edit-topic') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('topics.update', ['id' => $topic->id]) }}"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="forum_name"
                        class="form__text"
                        maxlength="75"
                        name="name"
                        placeholder=" "
                        required
                        type="text"
                        value="{{ $topic->name }}"
                    />
                    <label class="form__label form__label--floating" for="forum_name">
                        {{ __('forum.topic-name') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="forum_id" name="forum_id" class="form__select">
                        @foreach ($categories as $name => $forums)
                            <optgroup label="{{ $name }}">
                                @foreach ($forums as $forum)
                                    <option
                                        value="{{ $forum->id }}"
                                        @selected($topic->forum_id === $forum->id)
                                    >
                                        {{ $forum->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="forum_id">
                        {{ __('forum.forum') }}
                    </label>
                </p>
                <button
                    class="form__button form__button--filled"
                    name="post"
                    value="true"
                    id="post"
                >
                    {{ __('forum.edit-topic') }}
                </button>
            </form>
        </div>
    </section>
@endsection
