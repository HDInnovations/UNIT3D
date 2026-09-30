@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('vltava.staff.automatic_freeleeches') }}</li>
@endsection

@section('page', 'page__staff-automatic-torrent-freeleeches--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('vltava.staff.automatic_freeleeches') }}</h2>
            <div class="panel__actions">
                <a
                    href="{{ route('staff.automatic_torrent_freeleeches.create') }}"
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
                        <th>{{ __('vltava.staff.name_regex') }}</th>
                        <th>{{ __('vltava.staff.minimum_torrent_size') }}</th>
                        <th>{{ __('common.category') }}</th>
                        <th>{{ __('common.type') }}</th>
                        <th>{{ __('common.resolution') }}</th>
                        <th>{{ __('vltava.staff.freeleech_percentage') }}</th>
                        <th>{{ __('common.created_at') }}</th>
                        <th>{{ __('torrent.updated_at') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($automaticTorrentFreeleeches as $automaticTorrentFreeleech)
                        <tr>
                            <td>{{ $automaticTorrentFreeleech->position }}</td>
                            <td>{{ $automaticTorrentFreeleech->name_regex ?? '*' }}</td>
                            <td title="{{ $automaticTorrentFreeleech->size ?? 0 }} B">
                                {{ App\Helpers\StringHelper::formatBytes($automaticTorrentFreeleech->size ?? 0, 2) }}
                            </td>
                            <td>{{ $automaticTorrentFreeleech->category?->name ?? __('vltava.staff.any') }}</td>
                            <td>{{ $automaticTorrentFreeleech->type?->name ?? __('vltava.staff.any') }}</td>
                            <td>{{ $automaticTorrentFreeleech->resolution?->name ?? __('vltava.staff.any') }}</td>
                            <td>{{ $automaticTorrentFreeleech->freeleech_percentage }}</td>
                            <td>
                                <time
                                    datetime="{{ $automaticTorrentFreeleech->created_at }}"
                                    title="{{ $automaticTorrentFreeleech->created_at }}"
                                >
                                    {{ $automaticTorrentFreeleech->created_at->toDisplayTimezone()->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $automaticTorrentFreeleech->updated_at }}"
                                    title="{{ $automaticTorrentFreeleech->updated_at }}"
                                >
                                    {{ $automaticTorrentFreeleech->updated_at->toDisplayTimezone()->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.automatic_torrent_freeleeches.edit', ['automaticTorrentFreeleech' => $automaticTorrentFreeleech]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.automatic_torrent_freeleeches.destroy', ['automaticTorrentFreeleech' => $automaticTorrentFreeleech]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode(__('vltava.staff.delete_freeleech_confirmation', ['name' => $automaticTorrentFreeleech->name])) }}"
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
                            <td colspan="10">{{ __('vltava.staff.no_automatic_freeleeches') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <div class="panel__body">
            {{ __('vltava.staff.automatic_freeleech_explanation') }}
        </div>
    </section>
@endsection
