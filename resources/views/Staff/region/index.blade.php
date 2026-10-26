@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Torrent regions</li>
@endsection

@section('page', 'page__staff-region--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Torrent regions</h2>
            <div class="panel__actions">
                <a
                    href="{{ route('staff.regions.create') }}"
                    class="panel__action form__button form__button--text"
                >
                    {{ __('common.add') }}
                </a>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.position') }}</th>
                        <th>{{ __('common.code') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($regions as $region)
                        <tr>
                            <td>{{ $region->position }}</td>
                            <td>
                                <a href="{{ route('staff.regions.edit', ['region' => $region]) }}">
                                    {{ $region->name }}
                                </a>
                            </td>
                            <td>
                                {{ __('regions.' . $region->name) }}
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.regions.edit', ['region' => $region]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <button
                                            class="form__button form__button--text"
                                            popovertarget="region-delete-{{ $region->id }}"
                                        >
                                            {{ __('common.delete') }}
                                        </button>
                                        <dialog
                                            id="region-delete-{{ $region->id }}"
                                            class="dialog"
                                            popover
                                        >
                                            <h4 class="dialog__heading">
                                                Delete torrent region:
                                                {{ $region->name . ' (' . __('regions.' . $region->name) . ')' }}
                                            </h4>
                                            <form
                                                class="dialog__form"
                                                method="POST"
                                                action="{{ route('staff.regions.destroy', ['region' => $region]) }}"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <p class="form__group">
                                                    An existing torrent on site may already use this
                                                    region. Would you like to change it?
                                                </p>
                                                <p class="form__group" x-data>
                                                    <select
                                                        name="region_id"
                                                        id="autoreg"
                                                        class="form__select"
                                                        x-data="{ region: '' }"
                                                        x-model="region"
                                                        x-bind:class="region === '' ? 'form__select--default' : ''"
                                                    >
                                                        <option
                                                            hidden
                                                            disabled
                                                            selected
                                                            value=""
                                                        ></option>
                                                        @foreach ($regions as $region)
                                                            <option value="{{ $region->id }}">
                                                                {{ $region->name . ' (' . __('regions.' . $region->name) . ')' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label
                                                        class="form__label form__label--floating"
                                                        for="autoreg"
                                                    >
                                                        Replacement region
                                                    </label>
                                                </p>
                                                <p class="form__group">
                                                    <button
                                                        class="form__button form__button--filled"
                                                    >
                                                        {{ __('common.delete') }}
                                                    </button>
                                                    <button
                                                        class="form__button form__button--outlined"
                                                        type="button"
                                                        popovertarget="region-delete-{{ $region->id }}"
                                                    >
                                                        {{ __('common.cancel') }}
                                                    </button>
                                                </p>
                                            </form>
                                        </dialog>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No regions</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
