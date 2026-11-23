@extends('layout.with-main')

@section('title')
    <title>
        {{ __('torrent.history') }} - {{ $torrent->name }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('torrent.history') }}" />
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
        {{ __('torrent.history') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('peers', ['id' => $torrent]) }}">
            {{ __('torrent.peers') }}
        </a>
    </li>
    <li class="nav-tab--active">
        <a class="nav-tab--active__link" href="{{ route('history', ['id' => $torrent]) }}">
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

@section('page', 'page__torrent-history--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">
                {{ __('torrent.torrent') }} {{ __('torrent.history') }}
            </h2>
            <div class="panel__actions">
                <div class="panel__action">
                    Total up:
                    {{ App\Helpers\StringHelper::formatBytes($histories->sum('actual_uploaded'), 2) }}
                </div>
                <div class="panel__action">
                    Total down:
                    {{ App\Helpers\StringHelper::formatBytes($histories->sum('actual_downloaded'), 2) }}
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.user') }}</th>
                        <th>{{ __('torrent.agent') }}</th>
                        <th>{{ __('common.connected') }}</th>
                        <th>{{ __('torrent.completed') }}</th>
                        <th>{{ __('common.upload') }}</th>
                        <th>{{ __('common.download') }}</th>
                        <th>{{ __('torrent.refunded') }}</th>
                        <th>{{ __('common.added') }}</th>
                        <th>{{ __('torrent.last-update') }}</th>
                        <th>{{ __('torrent.completed_at') }}</th>
                        <th>{{ __('torrent.seedtime') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($histories as $history)
                        <tr>
                            <td>
                                <x-user-tag
                                    :user="$history->user"
                                    :anon="
                                        $history->user->privacy?->hidden
                                        || $history->user->privacy?->show_peer === 0
                                        || ($history->user->id == $torrent->user->id && $torrent->anon == 1)
                                    "
                                />
                            </td>

                            @if (auth()->user()->group->is_modo || auth()->id() === $history->user_id)
                                <td>{{ $history->agent }}</td>
                            @else
                                <td>---</td>
                            @endif

                            @if ($history->active)
                                <td class="text-green">{{ strtolower(__('common.yes')) }}</td>
                            @else
                                <td class="text-red">{{ strtolower(__('common.no')) }}</td>
                            @endif

                            @if ($history->seeder)
                                <td class="text-green">{{ strtolower(__('common.yes')) }}</td>
                            @else
                                <td class="text-red">{{ strtolower(__('common.no')) }}</td>
                            @endif
                            <td>
                                <span class="text-green">
                                    {{ App\Helpers\StringHelper::formatBytes($history->actual_uploaded, 2) }}
                                    <span
                                        class="text-blue"
                                        title="{{ __('torrent.credited') }} {{ strtolower(__('common.upload')) }}"
                                    >
                                        ({{ App\Helpers\StringHelper::formatBytes($history->uploaded, 2) }})
                                    </span>
                                </span>
                            </td>
                            <td>
                                <span class="text-red">
                                    {{ App\Helpers\StringHelper::formatBytes($history->actual_downloaded, 2) }}
                                    <span
                                        class="text-orange"
                                        title="{{ __('torrent.credited') }} {{ strtolower(__('common.download')) }}"
                                    >
                                        ({{ App\Helpers\StringHelper::formatBytes($history->downloaded, 2) }})
                                    </span>
                                </span>
                            </td>
                            <td>
                                <span
                                    class="text-info"
                                    title="{{ __('torrent.refunded') }} {{ strtolower(__('common.download')) }}"
                                >
                                    ({{ App\Helpers\StringHelper::formatBytes($history->refunded_download, 2) }})
                                </span>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $history->created_at }}"
                                    title="{{ $history->created_at }}"
                                >
                                    {{ $history->created_at ? $history->created_at->diffForHumans() : 'N/A' }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $history->updated_at }}"
                                    title="{{ $history->updated_at }}"
                                >
                                    {{ $history->updated_at ? $history->updated_at->diffForHumans() : 'N/A' }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $history->completed_at }}"
                                    title="{{ $history->completed_at }}"
                                >
                                    {{ $history->completed_at ? $history->completed_at->diffForHumans() : 'N/A' }}
                                </time>
                            </td>

                            @if ($history->seedtime < config('hitrun.seedtime'))
                                <td class="text-red">
                                    {{ App\Helpers\StringHelper::timeElapsed($history->seedtime) }}
                                </td>
                            @else
                                <td class="text-green">
                                    {{ App\Helpers\StringHelper::timeElapsed($history->seedtime) }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
