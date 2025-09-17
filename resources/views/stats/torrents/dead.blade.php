@extends('layout.with-main')

@section('title')
    <title>{{ __('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('stats') }}" class="breadcrumb__link">
            {{ __('stat.stats') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('torrent.torrents') }}
    </li>
@endsection

@section('nav-tabs')
    @include('partials.statstorrentmenu')
@endsection

@section('page', 'page__stats--dead')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.top-dead') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('torrent.torrent') }}</th>
                        <th
                            title="{{ __('torrent.seeders') }}"
                            style="text-align: right; min-width: 40px"
                        >
                            <i class="fas fa-arrow-alt-circle-up"></i>
                        </th>
                        <th
                            title="{{ __('torrent.leechers') }}"
                            style="text-align: right; min-width: 40px"
                        >
                            <i class="fas fa-arrow-alt-circle-down"></i>
                        </th>
                        <th
                            title="{{ __('torrent.completed') }}"
                            style="text-align: right; min-width: 40px"
                        >
                            <i class="fas fa-check-circle"></i>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dead as $torrent)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}">
                                    {{ $torrent->name }}
                                </a>
                            </td>
                            <td style="text-align: right">
                                <a
                                    class="torrent__seeder-count"
                                    href="{{ route('peers', ['id' => $torrent->id]) }}"
                                >
                                    {{ $torrent->seeders }}
                                </a>
                            </td>
                            <td style="text-align: right">
                                <a
                                    class="torrent__leecher-count"
                                    href="{{ route('peers', ['id' => $torrent->id]) }}"
                                >
                                    {{ $torrent->leechers }}
                                </a>
                            </td>
                            <td style="text-align: right">
                                <a
                                    class="torrent__times-completed-count"
                                    href="{{ route('history', ['id' => $torrent->id]) }}"
                                >
                                    {{ $torrent->times_completed }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
