@extends('layout.with-main')

@section('title')
    <title>{{ __('torrent.peers') }} - {{ $torrent->name }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('torrent.peers') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.index') }}" class="breadcrumb__link">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}" class="breadcrumb__link">
            {{ $torrent->name }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('torrent.peers') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tab--active">
        <a class="nav-tab--active__link" href="{{ route('peers', ['id' => $torrent]) }}">
            {{ __('torrent.peers') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('history', ['id' => $torrent]) }}">
            {{ __('torrent.history') }}
        </a>
    </li>
    @if (config('announce.external_tracker.is_enabled') && auth()->user()->group->is_modo)
        <li class="nav-tabV2">
            <a
                class="nav-tab__link"
                href="{{ route('torrents.external_tracker', ['id' => $torrent]) }}"
            >
                External tracker
            </a>
        </li>
    @endif
@endsection

@section('page', 'page__torrent-peer--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('torrent.torrent') }} {{ __('torrent.peers') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.user') }}</th>
                        <th>{{ __('torrent.progress') }}</th>
                        <th>{{ __('common.upload') }}</th>
                        <th>{{ __('common.download') }}</th>
                        <th>{{ __('torrent.left') }}</th>
                        <th>{{ __('torrent.client') }}</th>
                        <th>{{ __('common.ip') }}</th>
                        <th>{{ __('common.port') }}</th>
                        @if (\config('announce.connectable_check') == true)
                            <th>Connectable</th>
                        @endif

                        <th>{{ __('torrent.started') }}</th>
                        <th>{{ __('torrent.last-update') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>Visible</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peers as $peer)
                        <tr>
                            <td>
                                <x-user-tag
                                    :user="$peer->user"
                                    :anon="
                                        $peer->user->privacy?->hidden
                                        || $peer->user->privacy?->show_peer === 0
                                        || ($peer->user->id == $torrent->user->id && $torrent->anon == 1)
                                    "
                                />
                            </td>
                            <td>{{ $peer->progress }}%</td>
                            <td class="text-green">
                                {{ \App\Helpers\StringHelper::formatBytes($peer->uploaded, 2) }}
                            </td>
                            <td class="text-red">
                                {{ \App\Helpers\StringHelper::formatBytes($peer->downloaded, 2) }}
                            </td>
                            <td>{{ \App\Helpers\StringHelper::formatBytes($peer->left, 2) }}</td>
                            <td>{{ $peer->agent }}</td>

                            @if (auth()->user()->group->is_modo || auth()->id() == $peer->user_id)
                                <td>{{ $peer->ip }}</td>
                                <td>{{ $peer->port }}</td>
                            @else
                                <td>---</td>
                                <td>---</td>
                            @endif
                            @if (\config('announce.connectable_check') == true)
                                @php
                                    $connectable = false;
                                    if (config('announce.external_tracker.is_enabled')) {
                                        $connectable = $peer->connectable;
                                    } elseif (cache()->has('peers:connectable:' . $peer->ip . '-' . $peer->port . '-' . $peer->agent)) {
                                        $connectable = cache()->get('peers:connectable:' . $peer->ip . '-' . $peer->port . '-' . $peer->agent);
                                    }
                                @endphp

                                <td class="{{ $connectable ? 'text-green' : 'text-red' }}">
                                    @choice('user.client-connectable-state', $connectable)
                                </td>
                            @endif

                            <td>
                                <time
                                    datetime="{{ $peer->created_at }}"
                                    title="{{ $peer->created_at }}"
                                >
                                    {{ $peer->created_at ? $peer->created_at->diffForHumans() : 'N/A' }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $peer->updated_at }}"
                                    title="{{ $peer->updated_at }}"
                                >
                                    {{ $peer->updated_at ? $peer->updated_at->diffForHumans() : 'N/A' }}
                                </time>
                            </td>
                            <td
                                class="{{ $peer->active ? ($peer->seeder ? 'text-green' : 'text-red') : 'text-orange' }}"
                            >
                                @if ($peer->active)
                                    @if ($peer->seeder)
                                        {{ __('torrent.seeder') }}
                                    @else
                                        {{ __('torrent.leecher') }}
                                    @endif
                                @else
                                        Inactive
                                @endif
                            </td>
                            <td class="{{ $peer->visible ? 'text-green' : 'text-red' }}">
                                @if ($peer->visible)
                                    {{ __('common.yes') }}
                                @else
                                    {{ __('common.no') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
