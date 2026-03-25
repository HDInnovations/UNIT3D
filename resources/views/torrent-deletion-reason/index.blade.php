@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('staff.torrent-deletion-reasons') }}
    </li>
@endsection

@section('page', 'page__torrent-deletion-reasons--indexes')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff.torrent-deletion-reasons') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($torrentDeletionReasons as $torrentDeletionReason)
                        <tr>
                            <td>
                                {{ $torrentDeletionReason->name }}
                            </td>
                            <td>
                                {{ $torrentDeletionReason->description }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
