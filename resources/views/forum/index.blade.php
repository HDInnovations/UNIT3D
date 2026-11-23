@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('forum.forums') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ config('other.title') }} - {{ __('forum.forums') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('forum.forums') }}
    </li>
@endsection

@section('nav-tabs')
    @include('forum.partials.buttons')
@endsection

@section('page', 'page__forum--index')

@section('main')
    @foreach ($categories as $category)
        <section class="panelV2">
            <h2 class="panel__heading">
                <a
                    class="panel__header-link"
                    href="{{ route('forums.categories.show', ['id' => $category->id]) }}"
                >
                    {{ $category->name }}
                </a>
            </h2>
            @if ($category->forums->isNotEmpty())
                <ul class="subforum-listings">
                    @foreach ($category->forums as $forum)
                        <li class="subforum-listings__item">
                            <x-forum.subforum-listing :subforum="$forum" />
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="panel__body">No forums in category.</div>
            @endif
        </section>
    @endforeach
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('forum.stats') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('forum.forums') }}</dt>
                <dd>{{ $num_forums }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('forum.topics') }}</dt>
                <dd>{{ $num_topics }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('forum.posts') }}</dt>
                <dd>{{ $num_posts }}</dd>
            </div>
        </dl>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.actions') }}
        </h2>
        <div class="panel__body">
            <form class="form" action="{{ route('topic_reads.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="catchup_type" value="all" />
                <p class="form__group form__group--horizontal">
                    <button
                        class="form__button form__button--filled form__button--centered"
                        title="Mark all topics as read"
                    >
                        Mark all read
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
