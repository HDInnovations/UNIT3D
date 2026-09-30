@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('event.events') }}
    </li>
@endsection

@section('page', 'page__staff-event--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('event.events') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.events.create') }}"
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
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('event.starts-at') }}</th>
                        <th>{{ __('event.ends-at') }}</th>
                        <th>{{ __('common.active') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($events as $event)
                        <tr>
                            <td>
                                <a href="{{ route('staff.events.edit', ['event' => $event]) }}">
                                    {{ $event->name }}
                                </a>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $event->starts_at }}"
                                    title="{{ $event->starts_at }}"
                                >
                                    {{ $event->starts_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $event->ends_at }}"
                                    title="{{ $event->ends_at }}"
                                >
                                    {{ $event->ends_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                @if ($event->active)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.events.edit', ['event' => $event]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.events.destroy', ['event' => $event]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode(__('staff-interface.delete-event-confirm', ['name' => $event->name])) }}"
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
