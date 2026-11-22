@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Automatic torrent freeleeches</li>
@endsection

@section('page', 'page__staff-automatic-torrent-freeleeches--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Automatic torrent freeleeches</h2>
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
                        <th>Name regex</th>
                        <th>Min. torrent size</th>
                        <th>{{ __('common.category') }}</th>
                        <th>{{ __('common.type') }}</th>
                        <th>{{ __('common.resolution') }}</th>
                        <th>Freeleech percentage</th>
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
                            <td>{{ $automaticTorrentFreeleech->category?->name ?? 'Any' }}</td>
                            <td>{{ $automaticTorrentFreeleech->type?->name ?? 'Any' }}</td>
                            <td>{{ $automaticTorrentFreeleech->resolution?->name ?? 'Any' }}</td>
                            <td>{{ $automaticTorrentFreeleech->freeleech_percentage }}</td>
                            <td>
                                <time
                                    datetime="{{ $automaticTorrentFreeleech->created_at }}"
                                    title="{{ $automaticTorrentFreeleech->created_at }}"
                                >
                                    {{ $automaticTorrentFreeleech->created_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $automaticTorrentFreeleech->updated_at }}"
                                    title="{{ $automaticTorrentFreeleech->updated_at }}"
                                >
                                    {{ $automaticTorrentFreeleech->updated_at->format('Y-m-d') }}
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
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this automatic torrent freeleech: ' . $automaticTorrentFreeleech->name . '?') }}"
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
                            <td colspan="10">No automated torrent freeleeches</td>
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
            When a torrent is uploaded that meets the given criteria, the specified freeleech
            percentage will be automatically applied.
        </div>
    </section>
@endsection
