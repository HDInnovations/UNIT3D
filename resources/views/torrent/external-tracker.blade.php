@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ __('media-interface.torrent.external-tracker') }} - {{ $torrent?->name ?? __('media-interface.torrent.not-found') }} - {{ config('other.title') }}
    </title>
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
        <a href="{{ route('torrents.show', ['id' => $id]) }}" class="breadcrumb__link">
            {{ $torrent?->name ?? __('media-interface.torrent.not-found') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('torrent.peers') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('peers', ['id' => $id]) }}">
            {{ __('torrent.peers') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('history', ['id' => $id]) }}">
            {{ __('torrent.history') }}
        </a>
    </li>
    <li class="nav-tab--active">
        <a
            class="nav-tab--active__link"
            href="{{ route('torrents.external_tracker', ['id' => $id]) }}"
        >
            {{ __('media-interface.torrent.external-tracker') }}
        </a>
    </li>
@endsection

@section('page', 'page__torrent-external-tracker--show')

@section('main')
    @if ($externalTorrent === true)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.torrent') }}</h2>
            <div class="panel__body">{{ __('media-interface.torrent.external-tracker-disabled') }}</div>
        </section>
    @elseif ($externalTorrent === false)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.torrent') }}</h2>
            <div class="panel__body">{{ __('media-interface.torrent.torrent-not-found') }}</div>
        </section>
    @elseif ($externalTorrent === [])
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.torrent') }}</h2>
            <div class="panel__body">{{ __('media-interface.torrent.tracker-error') }}</div>
        </section>
    @else
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.torrent') }} {{ __('torrent.peers') }}</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.user') }}</th>
                            <th>{{ __('vltava.media.peer_id') }}</th>
                            <th>{{ __('torrent.progress') }}</th>
                            <th>{{ __('common.uploaded') }}</th>
                            <th>{{ __('common.downloaded') }}</th>
                            <th>{{ __('torrent.left') }}</th>
                            <th>{{ __('common.ip') }}</th>
                            <th>{{ __('common.port') }}</th>
                            <th>{{ __('torrent.last-update') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('vltava.media.visible') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($externalTorrent['peers'] ?? [] as $peerId => $peer)
                            <tr>
                                <td>
                                    @if (null !== ($user = \App\Models\User::find($peer['user_id'])))
                                        @if ($torrent === null)
                                            <x-user-tag :user="$user" :anon="true" />
                                        @else
                                            <x-user-tag
                                                :user="$user"
                                                :anon="
                                                    $user->privacy?->hidden
                                                    || $user->privacy?->show_peer === 0
                                                    || ($user->id == $torrent->user->id && $torrent->anon == 1)
                                                "
                                            />
                                        @endif
                                    @else
                                            {{ __('media-interface.torrent.user-not-found') }}
                                    @endif
                                </td>
                                <td>
                                    {{ implode('', array_map(fn ($char) => ctype_print($char) ? $char : '\x' . bin2hex($char), str_split($peer['peer_id']))) }}
                                </td>
                                <td>
                                    @if ($torrent === null)
                                        {{ __('media-interface.torrent.size-not-available') }}
                                    @else
                                        @php
                                            $progress = (100 * ($peer['downloaded'] % $torrent->size)) / $torrent->size;
                                        @endphp

                                        @if (0 < $progress && $progress < 1)
                                            1%
                                        @elseif (99 < $progress && $progress < 100)
                                            99%
                                        @else
                                            {{ round($progress) }}
                                        @endif
                                    @endif
                                </td>
                                <td class="text-green">
                                    {{ \App\Helpers\StringHelper::formatBytes($peer['uploaded'] ?? 0, 2) }}
                                </td>
                                <td class="text-red">
                                    {{ \App\Helpers\StringHelper::formatBytes($peer['downloaded'] ?? 0, 2) }}
                                </td>
                                <td>
                                    {{ \App\Helpers\StringHelper::formatBytes($peer['left'] ?? 0, 2) }}
                                </td>

                                @if (auth()->user()->group->is_modo || auth()->id() == $peer->user_id)
                                    <td>{{ $peer['ip_address'] }}</td>
                                    <td>{{ $peer['port'] }}</td>
                                @else
                                    <td>---</td>
                                    <td>---</td>
                                @endif
                                <td>
                                    @php
                                        $updatedAt = \Illuminate\Support\Carbon::createFromTimestampUTC($peer['updated_at']);
                                    @endphp

                                    <time datetime="{{ $updatedAt }}" title="{{ $updatedAt }}">
                                        {{ $updatedAt ? $updatedAt->diffForHumans() : __('media-interface.torrent.na') }}
                                    </time>
                                </td>
                                <td
                                    class="{{ $peer['is_active'] ? ($peer['is_seeder'] ? 'text-green' : 'text-red') : 'text-orange' }}"
                                >
                                    @if ($peer['is_active'])
                                        @if ($peer['is_seeder'])
                                            {{ __('torrent.seeder') }}
                                        @else
                                            {{ __('torrent.leecher') }}
                                        @endif
                                    @else
                                            {{ __('media-interface.torrent.inactive') }}
                                    @endif
                                </td>
                                <td class="{{ $peer['is_visible'] ? 'text-green' : 'text-red' }}">
                                    @if ($peer['is_visible'])
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
        @section('sidebar')
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('torrent.torrent') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('common.moderation') }}</dt>
                        <dd>{{ $externalTorrent['status'] }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('torrent.seeders') }}</dt>
                        <dd>{{ $externalTorrent['seeders'] }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('torrent.leechers') }}</dt>
                        <dd>{{ $externalTorrent['leechers'] }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('torrent.completed-times') }}</dt>
                        <dd>{{ $externalTorrent['times_completed'] }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('vltava.media.download_factor') }}</dt>
                        <dd>{{ $externalTorrent['download_factor'] }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('vltava.media.upload_factor') }}</dt>
                        <dd>{{ $externalTorrent['upload_factor'] }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('vltava.media.deleted') }}</dt>
                        <dd>
                            {{ $externalTorrent['is_deleted'] ? __('common.yes') : __('common.no') }}
                        </dd>
                    </div>
                </dl>
            </section>
        @endsection
    @endif
@endsection
