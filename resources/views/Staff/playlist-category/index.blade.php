@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Playlist categories</li>
@endsection

@section('page', 'page__staff-playlist-category--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Playlist categories</h2>
            <div class="panel__actions">
                <form
                    class="panel__action"
                    action="{{ route('staff.playlist_categories.create') }}"
                >
                    <button class="form__button form__button--text">
                        {{ __('common.add') }}
                    </button>
                </form>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('common.position') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.description') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($playlistCategories as $playlistCategory)
                        <tr>
                            <td>{{ $playlistCategory->id }}</td>
                            <td>{{ $playlistCategory->position }}</td>
                            <td>
                                <a
                                    href="{{ route('staff.playlist_categories.edit', ['playlistCategory' => $playlistCategory]) }}"
                                >
                                    {{ $playlistCategory->name }}
                                </a>
                            </td>
                            <td>{{ $playlistCategory->description }}</td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            class="form__button form__button--text"
                                            href="{{ route('staff.playlist_categories.edit', ['playlistCategory' => $playlistCategory]) }}"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.playlist_categories.destroy', ['playlistCategory' => $playlistCategory]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this playlist category: ' . $playlistCategory->name . '?') }}"
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
