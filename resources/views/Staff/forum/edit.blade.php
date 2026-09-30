@extends('layout.with-main')

@section('title')
    <title>
        {{ __('staff-interface.edit-forums') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('staff-interface.edit-forums') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.forum_categories.index') }}" class="breadcrumb__link">
            {{ __('staff-interface.forum-categories') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $forum->category->name }}
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.forum_categories.index') }}" class="breadcrumb__link">
            {{ __('staff.forums') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $forum->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-forum--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.edit') }} {{ __('forum.forum') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.forums.update', ['forum' => $forum]) }}"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="forum[name]"
                        value="{{ $forum->name }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.title') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        inputmode="numeric"
                        name="forum[position]"
                        pattern="[0-9]*"
                        placeholder=" "
                        type="text"
                        value="{{ $forum->position }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <textarea
                        id="description"
                        name="forum[description]"
                        class="form__textarea"
                        required
                    >
{{ $forum->description }}</textarea
                    >
                    <label class="form__label form__label--floating" for="description">
                        {{ __('common.description') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="forum_category_id"
                        name="forum[forum_category_id]"
                        class="form__select"
                    >
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected($category->id === $forum->category->id)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="forum_category_id">
                        {{ __('staff-interface.forum-category') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="default_topic_state_filter"
                        name="forum[default_topic_state_filter]"
                        class="form__select"
                    >
                        <option default selected value="">{{ __('staff-interface.none') }}</option>
                        <option
                            value="open"
                            @selected($forum->default_topic_state_filter === 'open')
                        >
                            {{ __('forum.open') }}
                        </option>
                        <option
                            value="close"
                            @selected($forum->default_topic_state_filter === 'close')
                        >
                            {{ __('forum.closed') }}
                        </option>
                    </select>
                    <label
                        class="form__label form__label--floating"
                        for="default_topic_state_filter"
                    >
                        {{ __('staff-interface.topic-state-filter') }}
                    </label>
                </p>
                <div class="form__group">
                    <label class="form__label">{{ __('staff-interface.permissions') }}</label>
                    <div class="data-table-wrapper">
                        <table
                            class="data-table data-table--checkbox-grid"
                            x-data="checkboxGrid"
                        >
                            <thead>
                                <tr>
                                    <th x-bind="columnHeader">{{ __('common.groups') }}</th>
                                    <th x-bind="columnHeader">
                                        {{ __('staff-interface.read-topics') }}
                                    </th>
                                    <th x-bind="columnHeader">
                                        {{ __('staff-interface.start-new-topic') }}
                                    </th>
                                    <th x-bind="columnHeader">
                                        {{ __('staff-interface.reply-to-topics') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody x-ref="tbody">
                                @foreach ($groups as $group)
                                    <tr>
                                        <th x-bind="rowHeader">
                                            {{ $group->name }}
                                            <input
                                                type="hidden"
                                                name="permissions[{{ $loop->index }}][group_id]"
                                                value="{{ $group->id }}"
                                            />
                                        </th>
                                        <td>
                                            <input
                                                type="hidden"
                                                name="permissions[{{ $loop->index }}][read_topic]"
                                                value="0"
                                            />
                                            <input
                                                type="checkbox"
                                                name="permissions[{{ $loop->index }}][read_topic]"
                                                value="1"
                                                @checked($forum->permissions->where('group_id', '=', $group->id)->first()?->read_topic)
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="hidden"
                                                name="permissions[{{ $loop->index }}][start_topic]"
                                                value="0"
                                            />
                                            <input
                                                type="checkbox"
                                                name="permissions[{{ $loop->index }}][start_topic]"
                                                value="1"
                                                @checked($forum->permissions->where('group_id', '=', $group->id)->first()?->start_topic)
                                            />
                                        </td>
                                        <td>
                                            <input
                                                type="hidden"
                                                name="permissions[{{ $loop->index }}][reply_topic]"
                                                value="0"
                                            />
                                            <input
                                                type="checkbox"
                                                name="permissions[{{ $loop->index }}][reply_topic]"
                                                value="1"
                                                @checked($forum->permissions->where('group_id', '=', $group->id)->first()?->reply_topic)
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('staff-interface.save-forum') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
