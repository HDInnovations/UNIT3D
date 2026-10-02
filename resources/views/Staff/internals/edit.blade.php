@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.internals.index') }}" class="breadcrumb__link">Internals</a>
    </li>
    <li class="breadcrumbV2">
        {{ $internal->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-internal--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }} internal group: {{ $internal->name }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.internals.update', ['internal' => $internal]) }}"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="name"
                        required
                        type="text"
                        value="{{ $internal->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="icon"
                        class="form__text"
                        name="icon"
                        required
                        type="text"
                        value="{{ $internal->icon }}"
                    />
                    <label class="form__label form__label--floating" for="icon">Icon</label>
                </p>
                <p class="form__group">
                    <input
                        id="effect"
                        class="form__text"
                        name="effect"
                        required
                        type="text"
                        value="{{ $internal->effect }}"
                    />
                    <label class="form__label form__label--floating" for="effect">Effect</label>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('common.members') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <button class="form__button form__button--text" popovertarget="user-add">
                        {{ __('common.add') }}
                    </button>
                    <dialog id="user-add" class="dialog" popover>
                        <h3 class="dialog__heading">Add user to {{ $internal->name }}</h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('staff.internal_users.store') }}"
                        >
                            @csrf
                            <input type="hidden" name="internal_id" value="{{ $internal->id }}" />
                            <p class="form__group">
                                <input
                                    id="username"
                                    class="form__text"
                                    name="username"
                                    placeholder=" "
                                    required
                                    type="text"
                                />
                                <label class="form__label form__label--floating" for="username">
                                    {{ __('common.username') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <input
                                    id="position"
                                    class="form__text"
                                    inputmode="numeric"
                                    name="position"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                    required
                                    type="text"
                                />
                                <label class="form__label form__label--floating" for="position">
                                    {{ __('common.position') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('common.add') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="user-add"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <th>{{ __('common.position') }}</th>
                    <th>{{ __('user.user') }}</th>
                    <th>Join date</th>
                    <th>{{ __('common.actions') }}</th>
                </thead>
                <tbody>
                    @forelse ($internal->users as $user)
                        <tr>
                            <td>{{ $user->pivot->position }}</td>
                            <td>
                                <x-user-tag :user="$user" :anon="false" />
                            </td>
                            <td>
                                <time
                                    datetime="{{ $user->pivot->created_at }}"
                                    title="{{ $user->pivot->created_at }}"
                                >
                                    {{ $user->pivot->created_at }}
                                </time>
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <button
                                            class="form__button form__button--text"
                                            popovertarget="user-edit-{{ $user->username }}"
                                        >
                                            {{ __('common.edit') }}
                                        </button>
                                        <dialog
                                            id="user-edit-{{ $user->username }}"
                                            class="dialog"
                                            popover
                                        >
                                            <h3 class="dialog__heading">Edit internal user</h3>
                                            <form
                                                class="dialog__form"
                                                method="POST"
                                                action="{{ route('staff.internal_users.update', ['internalUser' => $user->pivot->id]) }}"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <input
                                                    type="hidden"
                                                    name="internal_id"
                                                    value="{{ $user->pivot->internal_id }}"
                                                />
                                                <p class="form__group">
                                                    <input
                                                        id="position"
                                                        class="form__text"
                                                        inputmode="numeric"
                                                        name="position"
                                                        pattern="[0-9]*"
                                                        placeholder=" "
                                                        required
                                                        type="text"
                                                        value="{{ $user->pivot->position }}"
                                                    />
                                                    <label
                                                        class="form__label form__label--floating"
                                                        for="position"
                                                    >
                                                        {{ __('common.position') }}
                                                    </label>
                                                </p>
                                                <p class="form__group">
                                                    <button
                                                        class="form__button form__button--filled"
                                                    >
                                                        {{ __('common.add') }}
                                                    </button>
                                                    <button
                                                        class="form__button form__button--outlined"
                                                        type="button"
                                                        popovertarget="user-edit-{{ $user->username }}"
                                                    >
                                                        {{ __('common.cancel') }}
                                                    </button>
                                                </p>
                                            </form>
                                        </dialog>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.internal_users.destroy', ['internalUser' => $user->pivot->id]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                class="form__button form__button--text"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to remove this user (' . $user->username . ') from this internal group (.' . $internal->name . '?') }}"
                                            >
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No members.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
