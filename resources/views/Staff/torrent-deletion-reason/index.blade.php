@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.torrent-deletion-reasons') }}
    </li>
@endsection

@section('page', 'page__staff-torrent-deletion-reason--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('staff.torrent-deletion-reasons') }}</h2>
            <div class="panel__actions">
                <a
                    href="{{ route('staff.torrent_deletion_reasons.create') }}"
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
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.description') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($torrentDeletionReasons as $torrentDeletionReason)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('staff.torrent_deletion_reasons.edit', ['torrentDeletionReason' => $torrentDeletionReason]) }}"
                                >
                                    {{ $torrentDeletionReason->name }}
                                </a>
                            </td>
                            <td>
                                {{ $torrentDeletionReason->description }}
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.torrent_deletion_reasons.edit', ['torrentDeletionReason' => $torrentDeletionReason]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.torrent_deletion_reasons.delete', ['torrentDeletionReason' => $torrentDeletionReason]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.delete') }}
                                        </a>
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
