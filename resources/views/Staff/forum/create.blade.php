@extends('layout.with-main')

@section('title')
    <title>Add forums - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Add forums - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.forum_categories.index') }}" class="breadcrumb__link">
            {{ __('staff.forums') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('common.new-adj') }}</li>
@endsection

@section('page', 'page__staff-forum--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Add a new Forum</h2>
        <div class="panel__body">
            <form class="form" method="POST" action="{{ route('staff.forums.store') }}">
                @csrf
                <p class="form__group">
                    <input id="name" class="form__text" type="text" name="forum[name]" required />
                    <label class="form__label form__label--floating" for="name">Title</label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        inputmode="numeric"
                        name="forum[position]"
                        pattern="[0-9]*"
                        required
                        type="text"
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <textarea
                        id="description"
                        class="form__textarea"
                        name="forum[description]"
                        required
                    ></textarea>
                    <label class="form__label form__label--floating" for="description">
                        Description
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="forum_category_id"
                        name="forum[forum_category_id]"
                        class="form__select"
                        x-data="{ selected: {{ Js::from($forumCategoryId) }} || '' }"
                        x-model="selected"
                        x-bind:class="selected === '' ? 'form__selected--default' : ''"
                        required
                    >
                        <option disabled hidden></option>
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected($category->id === $forumCategoryId)
                            >
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="forum_category_id">
                        Forum category
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="default_topic_state_filter"
                        name="forum[default_topic_state_filter]"
                        class="form__select"
                    >
                        <option value="" selected>None</option>
                        <option value="open">{{ __('forum.open') }}</option>
                        <option value="close">{{ __('forum.closed') }}</option>
                    </select>
                    <label
                        class="form__label form__label--floating"
                        for="default_topic_state_filter"
                    >
                        Default topic state filter
                    </label>
                </p>
                <div class="form__group">
                    <h3>Permissions</h3>
                    <div class="data-table-wrapper" x-data="checkboxGrid">
                        <table class="data-table data-table--checkbox-grid">
                            <thead>
                                <tr>
                                    <th x-bind="columnHeader">Groups</th>
                                    <th x-bind="columnHeader">Read topics</th>
                                    <th x-bind="columnHeader">Start new topic</th>
                                    <th x-bind="columnHeader">Reply to topics</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                                checked
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
                                                checked
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
                                                checked
                                            />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <p class="form__group">
                    <button class="form__button form__button--filled">Save forum</button>
                </p>
            </form>
        </div>
    </section>
@endsection
