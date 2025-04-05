@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ $user->username }} - {{ __('user.invite-tree') }} - {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.invite-tree') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Invite Tree</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.user') }}</th>
                        <th style="text-align: right">
                            {{ __('common.account') }} {{ __('common.upload') }}
                        </th>
                        <th style="text-align: right">
                            {{ __('common.account') }} {{ __('common.download') }}
                        </th>
                        <th style="text-align: right">
                            {{ __('common.account') }} {{ __('common.ratio') }}
                        </th>
                        <th style="text-align: right">{{ __('torrent.seedsize') }}</th>
                        <th style="text-align: right">{{ __('user.avg-seedtime') }}</th>
                        @if (auth()->user()->group->is_modo)
                            <th>{{ __('user.registration-date') }}</th>
                            <th>{{ __('user.last-action') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invites as $invite)
                        <tr>
                            <td style="padding-left: {{ 24 * $invite->depth + 14 }}px">
                                <x-user_tag :user="$invite->receiver" :anon="false">
                                    @if ($user->warnings_count > 1)
                                        <x-slot:appended-icons>
                                            <i
                                                class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange"
                                                title="{{ __('common.active-warning') }} ({{ $user->warnings_count }})"
                                            ></i>
                                        </x-slot>
                                    @endif
                                </x-user_tag>
                            </td>

                            @if (auth()->user()->isAllowed($invite->receiver, 'profile', 'show_profile_torrent_ratio'))
                                <td style="text-align: right">
                                    {{ $invite->receiver->formatted_uploaded }}
                                </td>
                                <td style="text-align: right">
                                    {{ $invite->receiver->formatted_downloaded }}
                                </td>
                                <td style="text-align: right">
                                    {{ $invite->receiver->formatted_ratio }}
                                </td>
                            @else
                                <td>{{ __('common.hidden') }}</td>
                                <td>{{ __('common.hidden') }}</td>
                                <td>{{ __('common.hidden') }}</td>
                            @endif

                            @if (auth()->user()->isAllowed($invite->receiver, 'profile', 'show_profile_torrent_seed'))
                                <td style="text-align: right">
                                    {{ App\Helpers\StringHelper::formatBytes($invite->receiver->seeding_torrents_sum_size ?? 0) }}
                                </td>
                                <td style="text-align: right">
                                    {{ \implode(' ', \array_slice(\explode(' ', App\Helpers\StringHelper::timeElapsed($invite->receiver->history_avg_seedtime ?? 0)), 0, 2)) }}
                                </td>
                            @else
                                <td>{{ __('common.hidden') }}</td>
                                <td>{{ __('common.hidden') }}</td>
                            @endif

                            @if (auth()->user()->group->is_modo)
                                <td>
                                    @if ($invite->receiver->created_at === null)
                                        N/A
                                    @else
                                        <time
                                            class="{{ $invite->receiver->created_at }}"
                                            datetime="{{ $invite->receiver->created_at }}"
                                            title="{{ $invite->receiver->created_at }}"
                                        >
                                            {{ $invite->receiver->created_at->diffForHumans() }}
                                        </time>
                                    @endif
                                </td>
                                <td>
                                    @if ($invite->receiver->last_action === null)
                                        N/A
                                    @else
                                        <time
                                            class="{{ $invite->receiver->last_action }}"
                                            datetime="{{ $invite->receiver->last_action }}"
                                            title="{{ $invite->receiver->last_action }}"
                                        >
                                            {{ $invite->receiver->last_action->diffForHumans() }}
                                        </time>
                                    @endif
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                href="{{ route('users.invite_tree.index', ['user' => $invite->receiver]) }}"
                                                class="form__button form__button--text"
                                                style="margin-top: -4px; margin-bottom: -4px"
                                            >
                                                {{ __('common.view') }}
                                            </a>
                                        </li>
                                    </menu>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->group->is_modo ? 9 : 6 }}">
                                No Invitees
                            </td>
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
        <dl class="key-value">
            <div class="key-value__group">
                <dt>Height</dt>
                <dd>{{ $invites->max('depth') + 1 }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Count</dt>
                <dd>{{ $invites->count() }}</dd>
            </div>
        </dl>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">Ancestors</h2>
        <dl class="key-value">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.user') }}</th>
                            @if (auth()->user()->group->is_modo)
                                <th>{{ __('common.actions') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <x-user_tag :user="$user" :anon="false">
                                    @if ($user->warnings_exists)
                                        <x-slot:appended-icons>
                                            <i
                                                class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange"
                                                title="{{ __('common.active-warning') }} ({{ $user->warnings_count }})"
                                            ></i>
                                        </x-slot>
                                    @endif
                                </x-user_tag>
                            </td>
                            @if (auth()->user()->group->is_modo)
                                <td></td>
                            @endif
                        </tr>
                        @foreach ($inviters as $i => $inviter)
                            <tr>
                                <td>
                                    <x-user_tag :user="$inviter" :anon="false">
                                        @if ($user->warnings_exists)
                                            <x-slot:appended-icons>
                                                <i
                                                    class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange"
                                                    title="{{ __('common.active-warning') }} ({{ $user->warnings_count }})"
                                                ></i>
                                            </x-slot>
                                        @endif
                                    </x-user_tag>
                                </td>
                                @if (auth()->user()->group->is_modo)
                                    <td>
                                        <menu class="data-table__actions">
                                            <li class="data-table__action">
                                                <a
                                                    href="{{ route('users.invite_tree.index', ['user' => $inviter]) }}"
                                                    class="form__button form__button--text"
                                                    style="margin-top: -4px; margin-bottom: -4px"
                                                >
                                                    {{ __('common.view') }}
                                                </a>
                                            </li>
                                        </menu>
                                    </td>
                                @endif
                            </tr>
                        @endforeach

                        <tr>
                            <td>{{ __('user.open-registration') }}</td>
                            @if (auth()->user()->group->is_modo)
                                <td></td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>
        </dl>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.stats') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Stat</th>
                        <th style="text-align: right">Average</th>
                        <th style="text-align: right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>{{ __('common.ratio') }}</th>
                        <td style="text-align: right">{{ number_format($average_ratio, 2) }}</td>
                        <td style="text-align: right">
                            {{ number_format($total_uploaded / ($total_downloaded ?: 1), 2) }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('torrent.uploaded') }}</th>
                        <td style="text-align: right">
                            {{ App\Helpers\StringHelper::formatBytes($total_uploaded / ($invites->count() ?: 1)) }}
                        </td>
                        <td style="text-align: right">
                            {{ App\Helpers\StringHelper::formatBytes($total_uploaded ?? 0) }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('torrent.downloaded') }}</th>
                        <td style="text-align: right">
                            {{ App\Helpers\StringHelper::formatBytes($total_downloaded / ($invites->count() ?: 1)) }}
                        </td>
                        <td style="text-align: right">
                            {{ App\Helpers\StringHelper::formatBytes($total_downloaded ?? 0) }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('torrent.seedtime') }}</th>
                        <td style="text-align: right">
                            {{ \implode(' ', \array_slice(\explode(' ', App\Helpers\StringHelper::timeElapsed($average_seedtime / ($invites->count() ?: 1))), 0, 2)) }}
                        </td>
                        <td style="text-align: right">
                            {{ \implode(' ', \array_slice(\explode(' ', App\Helpers\StringHelper::timeElapsed($total_seedtime ?? 0)), 0, 2)) }}
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('torrent.seedsize') }}</th>
                        <td style="text-align: right">
                            {{ App\Helpers\StringHelper::formatBytes($total_seedsize / ($invites->count() ?: 1)) }}
                        </td>
                        <td style="text-align: right">
                            {{ App\Helpers\StringHelper::formatBytes($total_seedsize ?? 0) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.groups') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.group') }}</th>
                        <th style="text-align: right">Count</th>
                        <th style="text-align: right">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groups as $groups)
                        <tr>
                            <td>
                                <span class="user-tag">
                                    <span
                                        class="user-tag__link {{ $groups->first()->icon }}"
                                        style="color: {{ $groups->first()->color }}"
                                        title="{{ $groups->first()->name }}"
                                    >
                                        {{ $groups->first()->name }}
                                    </span>
                                </span>
                            </td>
                            <td style="text-align: right">{{ $groups->count() }}</td>
                            <td style="text-align: right">
                                {{ number_format($groups->count() / $invites->count(), 2) * 100 }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
