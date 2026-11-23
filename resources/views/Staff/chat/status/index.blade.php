@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('staff.chat') }} {{ __('staff.statuses') }}</li>
@endsection

@section('nav-tabs')
    <li class="nav-tab--active">
        <a class="nav-tab--active__link" href="{{ route('staff.statuses.index') }}">
            {{ __('staff.statuses') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('staff.chatrooms.index') }}">
            {{ __('staff.rooms') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('staff.bots.index') }}">
            {{ __('staff.bots') }}
        </a>
    </li>
@endsection

@section('page', 'page__staff-chat-status--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">
                {{ __('common.user') }} {{ __('staff.chat') }} {{ __('staff.statuses') }}
            </h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.statuses.create') }}"
                    >
                        {{ __('common.add') }}
                    </a>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('common.name') }}</th>
                        <th>Color</th>
                        <th>Icon</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chatstatuses as $chatstatus)
                        <tr>
                            <td>{{ $chatstatus->id }}</td>
                            <td>
                                <a
                                    href="{{ route('staff.statuses.edit', ['chatStatus' => $chatstatus]) }}"
                                >
                                    {{ $chatstatus->name }}
                                </a>
                            </td>
                            <td>
                                <i
                                    class="{{ config('other.font-awesome') }} fa-circle"
                                    style="color: {{ $chatstatus->color }}"
                                ></i>
                                {{ $chatstatus->color }}
                            </td>
                            <td>
                                <i class="{{ $chatstatus->icon }}"></i>
                                [{{ $chatstatus->icon }}]
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            class="form__button form__button--text"
                                            href="{{ route('staff.statuses.edit', ['chatStatus' => $chatstatus]) }}"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            method="POST"
                                            action="{{ route('staff.statuses.destroy', ['chatStatus' => $chatstatus]) }}"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this chat status: ' . $chatstatus->name . '?') }}"
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
@endsection
