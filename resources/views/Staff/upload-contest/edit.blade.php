@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.upload_contests.index') }}" class="breadcrumb__link">
            {{ __('common.upload') }} {{ __('common.contests') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $uploadContest->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-upload-contest--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.edit') }} {{ __('common.contest') }}</h2>
        <form
            class="dialog__form"
            method="POST"
            action="{{ route('staff.upload_contests.update', ['uploadContest' => $uploadContest]) }}"
        >
            @csrf
            @method('PATCH')
            <p class="form__group">
                <input
                    id="name"
                    class="form__text"
                    type="text"
                    autocomplete="off"
                    name="name"
                    required
                    value="{{ $uploadContest->name }}"
                />
                <label class="form__label form__label--floating" for="name">
                    {{ __('common.name') }}
                </label>
            </p>
            <p class="form__group">
                <textarea id="description" class="form__textarea" name="description" required>
{{ $uploadContest->description }}</textarea
                >
                <label class="form__label form__label--floating" for="description">
                    {{ __('common.description') }}
                </label>
            </p>
            <p class="form__group">
                <input
                    id="icon"
                    class="form__text"
                    type="text"
                    autocomplete="off"
                    name="icon"
                    required
                    value="{{ $uploadContest->icon }}"
                />
                <label class="form__label form__label--floating" for="icon">
                    {{ __('common.icon') }}
                </label>
            </p>
            <div class="form__group--horizontal">
                <p class="form__group">
                    <input
                        id="starts_at"
                        class="form__text"
                        name="starts_at"
                        type="date"
                        value="{{ $uploadContest->starts_at->format('Y-m-d') }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="starts_at">
                        {{ __('common.starts-at') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="ends_at"
                        class="form__text"
                        name="ends_at"
                        type="date"
                        value="{{ $uploadContest->ends_at->format('Y-m-d') }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="ends_at">
                        {{ __('common.ends-at') }}
                    </label>
                </p>
            </div>
            <p class="form__group">
                <input type="hidden" name="active" value="0" />
                <input
                    type="checkbox"
                    class="form__checkbox"
                    id="active"
                    name="active"
                    value="1"
                    @checked($uploadContest->active)
                />
                <label class="form__label" for="active">{{ __('common.active') }}?</label>
            </p>
            <p class="form__group">
                <button class="form__button form__button--filled" wire:click="store">
                    {{ __('common.save') }}
                </button>
                <button
                    formmethod="dialog"
                    formnovalidate
                    class="form__button form__button--outlined"
                >
                    {{ __('common.cancel') }}
                </button>
            </p>
        </form>
    </section>
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('contest.prizes') }}</h2>
            <div class="panel__actions">
                <div class="panel__action" x-data="dialog">
                    <button class="form__button form__button--text" x-bind="showDialog">
                        {{ __('common.add') }}
                    </button>
                    <dialog class="dialog" x-bind="dialogElement">
                        <h3 class="dialog__heading">{{ __('event.add-prize') }}</h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('staff.upload_contests.prizes.store', ['uploadContest' => $uploadContest]) }}"
                            x-bind="dialogForm"
                        >
                            @csrf
                            <input
                                type="hidden"
                                name="contest_id"
                                value="{{ $uploadContest->id }}"
                            />
                            <p class="form__group">
                                <select name="type" id="type" class="form__select" required>
                                    <option hidden disabled selected value=""></option>
                                    <option value="bon">{{ __('bon.bon') }}</option>
                                    <option value="fl_tokens">{{ __('common.fl_tokens') }}</option>
                                </select>
                                <label class="form__label form__label--floating" for="type">
                                    {{ __('common.type') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <input
                                    id="amount"
                                    class="form__text"
                                    inputmode="numeric"
                                    name="amount"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                    required
                                    type="text"
                                />
                                <label class="form__label form__label--floating" for="amount">
                                    {{ __('common.amount') }}
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
                                    formmethod="dialog"
                                    formnovalidate
                                    class="form__button form__button--outlined"
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
                    <th>{{ __('common.type') }}</th>
                    <th>{{ __('common.amount') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </thead>
                <tbody>
                    @forelse ($prizes as $prize)
                        <tr>
                            <td>{{ $prize->position }}</td>
                            <td>
                                @switch($prize->type)
                                    @case('bon')
                                        {{ __('bon.bon') }}

                                        @break
                                    @case('fl_tokens')
                                        {{ __('common.fl_tokens') }}

                                        @break
                                @endswitch
                            </td>
                            <td>{{ $prize->amount }}</td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action" x-data="dialog">
                                        <button
                                            class="form__button form__button--text"
                                            x-bind="showDialog"
                                        >
                                            {{ __('common.edit') }}
                                        </button>
                                        <dialog class="dialog" x-bind="dialogElement">
                                            <h3 class="dialog__heading">
                                                {{ __('event.edit-prize') }}
                                            </h3>
                                            <form
                                                class="dialog__form"
                                                method="POST"
                                                action="{{ route('staff.upload_contests.prizes.update', ['uploadContest' => $uploadContest, 'prize' => $prize]) }}"
                                                x-bind="dialogForm"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <input
                                                    type="hidden"
                                                    name="contest_id"
                                                    value="{{ $uploadContest->id }}"
                                                />
                                                <p class="form__group">
                                                    <select
                                                        name="type"
                                                        id="type"
                                                        class="form__select"
                                                        required
                                                    >
                                                        <option
                                                            value="bon"
                                                            @selected($uploadContest->type === 'bon')
                                                        >
                                                            {{ __('bon.bon') }}
                                                        </option>
                                                        <option
                                                            value="fl_tokens"
                                                            @selected($uploadContest->type === 'fl_tokens')
                                                        >
                                                            {{ __('common.fl_tokens') }}
                                                        </option>
                                                    </select>
                                                    <label
                                                        class="form__label form__label--floating"
                                                        for="type"
                                                    >
                                                        {{ __('common.type') }}
                                                    </label>
                                                </p>
                                                <p class="form__group">
                                                    <input
                                                        id="amount"
                                                        class="form__text"
                                                        inputmode="numeric"
                                                        name="amount"
                                                        pattern="[0-9]*"
                                                        placeholder=" "
                                                        required
                                                        type="text"
                                                        value="{{ $prize->amount }}"
                                                    />
                                                    <label
                                                        class="form__label form__label--floating"
                                                        for="min"
                                                    >
                                                        {{ __('common.amount') }}
                                                    </label>
                                                </p>
                                                <p class="form__group">
                                                    <input
                                                        id="position"
                                                        class="form__text"
                                                        inputmode="numeric"
                                                        name="position"
                                                        pattern="[0-9.]*"
                                                        placeholder=" "
                                                        required
                                                        type="text"
                                                        value="{{ $prize->position }}"
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
                                                        {{ __('common.edit') }}
                                                    </button>
                                                    <button
                                                        formmethod="dialog"
                                                        formnovalidate
                                                        class="form__button form__button--outlined"
                                                    >
                                                        {{ __('common.cancel') }}
                                                    </button>
                                                </p>
                                            </form>
                                        </dialog>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.upload_contests.prizes.destroy', ['uploadContest' => $uploadContest, 'prize' => $prize]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                class="form__button form__button--text"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to remove this prize (Type: ' . $prize->type . ', Min: ' . $prize->min . ', Max: ' . $prize->max . ', Weight: ' . $prize->weight . ') from this contest (.' . $uploadContest->name . ')?') }}"
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
                            <td colspan="4">{{ __('event.no-prizes') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
