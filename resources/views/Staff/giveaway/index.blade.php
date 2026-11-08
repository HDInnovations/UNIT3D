@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('event.giveaways') }}
    </li>
@endsection

@section('page', 'page__staff-giveaways--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('event.giveaways') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.giveaways.create') }}"
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
                        <th>Starts at</th>
                        <th>Ends at</th>
                        <th>{{ __('common.active') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($giveaways as $giveaway)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('staff.giveaways.edit', ['giveaway' => $giveaway]) }}"
                                >
                                    {{ $giveaway->name }}
                                </a>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $giveaway->starts_at }}"
                                    title="{{ $giveaway->starts_at }}"
                                >
                                    {{ $giveaway->starts_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $giveaway->ends_at }}"
                                    title="{{ $giveaway->ends_at }}"
                                >
                                    {{ $giveaway->ends_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                @if ($giveaway->active)
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
                                            href="{{ route('staff.giveaways.edit', ['giveaway' => $giveaway]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.giveaways.destroy', ['giveaway' => $giveaway]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this giveaway: ' . $giveaway->name . '?') }}"
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
