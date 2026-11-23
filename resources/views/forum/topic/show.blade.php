@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ $topic->name }} - Forums - {{ config('other.title') }}</title>
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
        <a href="{{ route('forums.show', ['id' => $topic->forum->id]) }}" class="breadcrumb__link">
            {{ $topic->forum->name }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $topic->name }}
    </li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__topic--show')

@section('main')
    @livewire('topic-post-search', ['topic' => $topic])
    @if ($topic->state === 'close' && auth()->user()->group->is_modo)
        <p>
            This topic is closed, but you can still reply due to you being
            {{ auth()->user()->group->name }}.
        </p>
    @endif

    @if (($topic->state === 'open' && $permission?->reply_topic) || auth()->user()->group->is_modo)
        <form id="forum_reply_form" method="POST" action="{{ route('posts.store') }}">
            @csrf
            <input type="hidden" name="topic_id" value="{{ $topic->id }}" />
            @livewire('bbcode-input', ['name' => 'content', 'label' => __('forum.post') ])
            <p class="form__group">
                <input type="hidden" name="anon" value="0" />
                <input
                    type="checkbox"
                    class="form__checkbox"
                    id="anon"
                    name="anon"
                    value="1"
                    @checked(old('anon'))
                />
                <label class="form__label" for="anon">{{ __('common.anonymous') }}?</label>
            </p>
            <p class="form__group">
                <button type="submit" class="form__button form__button--filled">
                    {{ __('common.submit') }}
                </button>
            </p>
        </form>
    @elseif ($topic->state === 'close')
        <p>{{ __('forum.topic-closed') }}</p>
    @endif
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.stats') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('forum.author') }}</dt>
                <dd>
                    @if ($topic->user === null)
                        {{ __('common.unknown') }}
                    @else
                        <a href="{{ route('users.show', ['user' => $topic->user]) }}">
                            {{ $topic->user->username }}
                        </a>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('forum.created-at') }}</dt>
                <dd>{{ date('M d Y H:m', strtotime($topic->created_at)) }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('forum.replies') }}</dt>
                <dd>{{ $topic->num_post - 1 }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('forum.views') }}</dt>
                <dd>{{ $topic->views }}</dd>
            </div>
        </dl>
        <div class="panel__body">
            @if ($subscription === null)
                <form class="form" action="{{ route('subscriptions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="topic_id" value="{{ $topic->id }}" />
                    <p class="form__group form__group--horizontal">
                        <button class="form__button form__button--filled form__button--centered">
                            <i class="{{ config('other.font-awesome') }} fa-bell"></i>
                            {{ __('forum.subscribe') }}
                        </button>
                    </p>
                </form>
            @else
                <form
                    class="form"
                    action="{{ route('subscriptions.destroy', ['subscription' => $subscription]) }}"
                    method="POST"
                >
                    @csrf
                    <input type="hidden" name="topic_id" value="{{ $topic->id }}" />
                    <p class="form__group form__group--horizontal">
                        <button class="form__button form__button--filled form__button--centered">
                            <i class="{{ config('other.font-awesome') }} fa-bell-slash"></i>
                            {{ __('forum.unsubscribe') }}
                        </button>
                    </p>
                </form>
            @endif
        </div>
    </section>
    @if (auth()->user()->group->is_modo || $topic->first_post_user_id == auth()->id())
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('forum.topic') }} {{ __('user.settings') }}</h2>
            <div class="panel__body">
                @if (auth()->user()->group->is_modo)
                    @if ($topic->state === 'close')
                        <form
                            class="form"
                            action="{{ route('topics.open', ['id' => $topic->id]) }}"
                            method="POST"
                        >
                            @csrf
                            <p class="form__group form__group--horizontal">
                                <button
                                    class="form__button form__button--filled form__button--centered"
                                >
                                    {{ __('forum.open') }}
                                </button>
                            </p>
                        </form>
                    @else
                        <form
                            class="form"
                            action="{{ route('topics.close', ['id' => $topic->id]) }}"
                            method="POST"
                        >
                            @csrf
                            <p class="form__group form__group--horizontal">
                                <button
                                    class="form__button form__button--filled form__button--centered"
                                >
                                    {{ __('common.close') }}
                                </button>
                            </p>
                        </form>
                    @endif
                @endif

                <div class="form">
                    <p class="form__group form__group--horizontal">
                        <a
                            href="{{ route('topics.edit', ['id' => $topic->id]) }}"
                            class="form__button form__button--filled form__button--centered"
                        >
                            {{ __('common.edit') }}
                        </a>
                    </p>
                </div>
                @if (auth()->user()->group->is_modo)
                    <form
                        class="form"
                        action="{{ route('topics.destroy', ['id' => $topic->id]) }}"
                        method="POST"
                        x-data="confirmation"
                    >
                        @csrf
                        @method('DELETE')
                        <p class="form__group form__group--horizontal">
                            <button
                                class="form__button form__button--filled form__button--centered"
                                x-on:click.prevent="confirmAction"
                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this topic: ' . $topic->name . '?') }}"
                            >
                                {{ __('common.delete') }}
                            </button>
                        </p>
                    </form>
                @endif
            </div>
        </section>
    @endif

    @if (auth()->user()->group->is_modo)
        <section class="panelV2" x-data>
            <h2 class="panel__heading">{{ __('forum.label-system') }}</h2>
            <div class="panel__body">
                <form
                    class="form"
                    action="{{ route('topics.labels', ['topic' => $topic]) }}"
                    method="POST"
                >
                    @csrf
                    @method('PATCH')
                    <p class="form__group">
                        <input type="hidden" name="approved" value="0" />
                        <input
                            id="approved-label"
                            class="form__checkbox"
                            name="approved"
                            type="checkbox"
                            value="1"
                            @checked($topic->approved)
                        />
                        <label for="approved-label">{{ __('forum.approved') }}</label>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="denied" value="0" />
                        <input
                            id="denied-label"
                            class="form__checkbox"
                            name="denied"
                            type="checkbox"
                            value="1"
                            @checked($topic->denied)
                        />
                        <label for="denied-label">{{ __('forum.denied') }}</label>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="solved" value="0" />
                        <input
                            id="solved-label"
                            class="form__checkbox"
                            name="solved"
                            type="checkbox"
                            value="1"
                            @checked($topic->solved)
                        />
                        <label for="solved-label">{{ __('forum.solved') }}</label>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="invalid" value="0" />
                        <input
                            id="invalid-label"
                            class="form__checkbox"
                            name="invalid"
                            type="checkbox"
                            value="1"
                            @checked($topic->invalid)
                        />
                        <label for="invalid-label">{{ __('forum.invalid') }}</label>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="bug" value="0" />
                        <input
                            id="bug-label"
                            class="form__checkbox"
                            name="bug"
                            type="checkbox"
                            value="1"
                            @checked($topic->bug)
                        />
                        <label for="bug-label">{{ __('forum.bug') }}</label>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="suggestion" value="0" />
                        <input
                            id="suggestion-label"
                            class="form__checkbox"
                            name="suggestion"
                            type="checkbox"
                            value="1"
                            @checked($topic->suggestion)
                        />
                        <label for="suggestion-label">{{ __('forum.suggestion') }}</label>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="implemented" value="0" />
                        <input
                            id="implemented-label"
                            class="form__checkbox"
                            name="implemented"
                            type="checkbox"
                            value="1"
                            @checked($topic->implemented)
                        />
                        <label for="implemented-label">{{ __('forum.implemented') }}</label>
                    </p>
                    <p class="form__group">
                        <button class="form__button form__button--filled">
                            {{ __('common.save') }}
                        </button>
                    </p>
                </form>
            </div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">Edit topic priority</h2>
            <div class="panel__body">
                <form
                    class="form"
                    action="{{ route('topics.pin', ['id' => $topic->id]) }}"
                    method="POST"
                >
                    @csrf
                    <p class="form__group form__group--horizontal">
                        <input
                            type="text"
                            name="priority"
                            id="priority"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            value="{{ $topic->priority }}"
                        />
                        <label class="form__label form__label--floating" for="season_number">
                            Priority
                        </label>
                    </p>
                    <p class="form__group">
                        <button class="form__button form__button--filled form__button--centered">
                            {{ __('forum.pin') }}
                        </button>
                        @if ($topic->priority)
                            <button
                                class="form__button form__button--filled form__button--centered"
                                formaction="{{ route('topics.unpin', ['id' => $topic->id]) }}"
                            >
                                {{ __('forum.unpin') }}
                            </button>
                        @endif
                    </p>
                </form>
            </div>
        </section>
    @endif
@endsection
