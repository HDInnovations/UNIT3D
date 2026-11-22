@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Internals</li>
@endsection

@section('page', 'page__staff-internal--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Internal groups</h2>
            <div class="panel__actions">
                <a
                    href="{{ route('staff.internals.create') }}"
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
                        <th>ID</th>
                        <th>{{ __('common.name') }}</th>
                        <th>Icon</th>
                        <th>Effect</th>
                        <th width="15%">{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($internalGroups as $internalGroup)
                        <tr>
                            <td>{{ $internalGroup->id }}</td>
                            <td>
                                <a
                                    href="{{ route('staff.internals.edit', ['internal' => $internalGroup]) }}"
                                >
                                    {{ $internalGroup->name }}
                                </a>
                            </td>
                            <td>{{ $internalGroup->icon }}</td>
                            <td>{{ $internalGroup->effect }}</td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.internals.edit', ['internal' => $internalGroup]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            method="POST"
                                            action="{{ route('staff.internals.destroy', ['internal' => $internalGroup]) }}"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this internal group: ' . $internalGroup->name . '?') }}"
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

    <section class="panelV2">
        <h2 class="panel__heading">{{ __('torrent.internal') }} {{ __('common.stats') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.internal') }} {{ __('common.groups') }}</th>
                        <th>{{ __('user.total-uploads') }}</th>
                        <th>{{ __('user.uploads') }} {{ __('stat.last60days') }}</th>
                        <th>{{ __('user.total-personal-releases') }}</th>
                        <th>{{ __('user.personal-releases') }} {{ __('stat.last60days') }}</th>
                        <th>{{ __('user.total-internal-releases') }}</th>
                        <th>{{ __('user.internal-releases') }} {{ __('stat.last60days') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($internalUsers as $user)
                        <tr>
                            <td>
                                <x-user-tag :anon="false" :user="$user" />
                            </td>
                            <td>{{ $user->internals->pluck('name')->implode(', ') }}</td>
                            <td>{{ $user->total_uploads }}</td>
                            <td>{{ $user->recent_uploads }}</td>
                            <td>{{ $user->total_personal_releases }}</td>
                            <td>{{ $user->recent_personal_releases }}</td>
                            <td>{{ $user->total_internal_releases }}</td>
                            <td>{{ $user->recent_internal_releases }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
