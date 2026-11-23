@extends('layout.with-main')

@section('title')
    <title>
        {{ __('staff.seedboxes') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('staff.seedboxes') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.seedboxes') }}
    </li>
@endsection

@section('page', 'page__staff-seedbox--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff.seedboxes') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.no') }}</th>
                        <th>{{ __('common.user') }}</th>
                        <th>{{ __('common.ip') }}</th>
                        <th>{{ __('user.created-on') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($seedboxes as $seedbox)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <x-user-tag :anon="false" :user="$seedbox->user" />
                            </td>
                            <td>{{ $seedbox->ip }}</td>
                            <td>
                                <time
                                    datetime="{{ $seedbox->created_at }}"
                                    title="{{ $seedbox->created_at }}"
                                >
                                    {{ $seedbox->created_at->diffForHumans() }}
                                </time>
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.seedboxes.destroy', ['seedbox' => $seedbox]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this seedbox: ' . $seedbox->ip . '? (owned by ' . $seedbox->user->username . ')') }}"
                                                class="form__button form__button--text"
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
                            <td colspan="5">No seedboxes</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $seedboxes->links('partials.pagination') }}
    </section>
@endsection
