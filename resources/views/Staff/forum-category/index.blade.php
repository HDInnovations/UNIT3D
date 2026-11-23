@extends('layout.with-main-and-sidebar')

@section('title')
    <title>Forums - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Forums - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.forums') }}
    </li>
@endsection

@section('page', 'page__staff-forum-category--index')

@section('main')
    @foreach ($categories as $category)
        <section class="panelV2">
            <header class="panel__header">
                <h2 class="panel__heading">{{ $category->name }}</h2>
                <div class="panel__actions">
                    <div class="panel__action">
                        <a
                            href="{{ route('staff.forums.create', ['forumCategoryId' => $category->id]) }}"
                            class="form__button form__button--text"
                        >
                            {{ __('common.add') }}
                        </a>
                    </div>
                    <div class="panel__action">
                        <a
                            href="{{ route('staff.forum_categories.edit', ['forumCategory' => $category]) }}"
                            class="form__button form__button--text"
                        >
                            {{ __('common.edit') }}
                        </a>
                    </div>
                    <form
                        action="{{ route('staff.forum_categories.destroy', ['forumCategory' => $category]) }}"
                        method="POST"
                        style="display: contents"
                        x-data="confirmation"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            class="form__button form__button--text"
                            x-on:click.prevent="confirmAction"
                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this forum category (' . $category->name . ') and all forums, topics, and posts within?') }}"
                        >
                            {{ __('common.delete') }}
                        </button>
                    </form>
                </div>
            </header>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.position') }}</th>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($category->forums as $forum)
                            <tr>
                                <td>{{ $forum->position }}</td>
                                <td>
                                    <a
                                        href="{{ route('staff.forums.edit', ['forum' => $forum]) }}"
                                    >
                                        {{ $forum->name }}
                                    </a>
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                class="form__button form__button--text"
                                                href="{{ route('staff.forums.edit', ['forum' => $forum]) }}"
                                            >
                                                {{ __('common.edit') }}
                                            </a>
                                        </li>
                                        <li class="data-table__action">
                                            <form
                                                method="POST"
                                                action="{{ route('staff.forums.destroy', ['forum' => $forum]) }}"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    x-on:click.prevent="confirmAction"
                                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this forum (' . $forum->name . ') including all topics and posts within?') }}"
                                                    class="form__button form__button--text"
                                                >
                                                    {{ __('common.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </menu>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.actions') }}</h2>
        <div class="panel__body">
            <p class="form__group form__group--horizontal">
                <a
                    href="{{ route('staff.forum_categories.create') }}"
                    class="form__button form__button--filled form__button--centered"
                >
                    Create new category
                </a>
            </p>
            <p class="form__group form__group--horizontal">
                <a
                    href="{{ route('staff.forums.create') }}"
                    class="form__button form__button--filled form__button--centered"
                >
                    Create new forum
                </a>
            </p>
        </div>
    </section>
@endsection
