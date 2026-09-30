@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ $user->username }} - {{ __('common.members') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('user.profile-desc', ['user' => $user->username, 'title' => config('other.title')]) }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ $user->username }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-profile--show')

@if (auth()->user()->isAllowed($user))
    @section('main')
        <section class="panelV2">
            <header class="panel__header">
                <h2 class="panel__heading">{{ __('member-interface.profile.user-information') }}</h2>
                <div class="panel__actions">
                    @if (auth()->user()->is($user))
                        <div class="panel__action">
                            <a
                                href="{{ route('users.edit', ['user' => $user]) }}"
                                class="form__button form__button--text"
                            >
                                {{ __('common.edit') }}
                            </a>
                        </div>
                    @elseif (auth()->user()->group->is_modo)
                        <div class="panel__action">
                            <a
                                href="{{ route('staff.users.edit', ['user' => $user]) }}"
                                class="form__button form__button--text"
                            >
                                {{ __('common.edit') }}
                            </a>
                        </div>
                        <div class="panel__action">
                            <form
                                action="{{ route('staff.users.destroy', ['user' => $user]) }}"
                                method="POST"
                                x-data="confirmation"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    x-on:click.prevent="confirmAction"
                                    data-b64-deletion-message="{{ base64_encode(__('member-interface.profile.confirm-delete-user', ['username' => $user->username])) }}"
                                    class="form__button form__button--text"
                                >
                                    {{ __('common.delete') }}
                                </button>
                            </form>
                        </div>
                    @endif
                    @if (auth()->id() !== $user->id)
                        <div class="panel__action" x-data="dialog">
                            <button class="form__button form__button--text" x-bind="showDialog">
                                {{ __('common.report') }}
                            </button>
                            <dialog class="dialog" x-bind="dialogElement">
                                <h3 class="dialog__heading">
                                    {{ __('member-interface.profile.report-user-heading', ['username' => $user->username]) }}
                                </h3>
                                <form
                                    class="dialog__form"
                                    method="POST"
                                    action="{{ route('report_user', ['username' => $user->username]) }}"
                                    x-bind="dialogForm"
                                >
                                    @csrf
                                    <p class="form__group">
                                        <textarea
                                            id="report_reason"
                                            class="form__textarea"
                                            name="message"
                                            required
                                        ></textarea>
                                        <label
                                            class="form__label form__label--floating"
                                            for="report_reason"
                                        >
                                            {{ __('common.reason') }}
                                        </label>
                                    </p>
                                    <p class="form__group">
                                        <button class="form__button form__button--filled">
                                            {{ __('common.save') }}
                                        </button>
                                        <button
                                            formmethod="dialog"
                                            formnovalidate
                                            class="form__button form__button--outlined"
                                        >
                                            {{ __('common.cancel') }}
                                        </button>
                                    </p>
                                </form>
                            </dialog>
                        </div>
                    @endif
                </div>
            </header>
            <div class="panel__body">
                <article class="profileV2">
                    <x-user-tag :user="$user" :anon="false" class="profile__username">
                        <x-slot:appendedIcons>
                            @if ($user->isOnline())
                                <i
                                    class="{{ config('other.font-awesome') }} fa-circle text-green"
                                    title="{{ __('user.online') }}"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-circle text-red"
                                    title="{{ __('user.offline') }}"
                                ></i>
                            @endif
                            <a
                                href="{{ route('users.conversations.create', ['user' => auth()->user(), 'username' => $user->username]) }}"
                            >
                                <i
                                    class="{{ config('other.font-awesome') }} fa-envelope text-info"
                                ></i>
                            </a>
                            @if ($user->warnings()->active()->exists())
                                <i
                                    class="{{ config('other.font-awesome') }} fa-exclamation-circle text-orange"
                                    aria-hidden="true"
                                    title="{{ __('user.active-warning') }}"
                                ></i>
                            @endif
                        </x-slot>
                    </x-user-tag>
                    <time
                        datetime="{{ $user->created_at }}"
                        title="{{ $user->created_at }}"
                        class="profile__registration"
                    >
                        {{ __('user.registration-date') }}:
                        {{ $user->created_at?->toDisplayTimezone()->format('Y-m-d') ?? 'N/A' }}
                    </time>
                    <img
                        src="{{ $user->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $user]) }}"
                        alt=""
                        class="profile__avatar"
                    />
                    @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_title') && $user->title)
                        <span class="profile__title">
                            {{ __('user.title') }}: {{ $user->title }}
                        </span>
                    @endif

                    @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_about') && $user->about)
                        <div class="profile__about">
                            {{ __('user.about') }}:
                            <div class="bbcode-rendered">@bbcode($user->about ?? 'N/A')</div>
                        </div>
                    @endif
                </article>
            </div>
        </section>
        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_achievement'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.recent-achievements') }}</h2>
                <div class="panel__body">
                    @forelse ($achievements->take(25) as $achievement)
                        @php
                            $achievementDescription = \App\Achievements\Achievement::descriptionFor($achievement->details);
                        @endphp
                        <img
                            src="/img/badges/{{ $achievement->details->name }}.png"
                            title="{{ $achievementDescription }}"
                            height="50px"
                            alt="{{ $achievementDescription }}"
                        />
                    @empty
                        {{ __('member-interface.profile.no-recent-achievements') }}
                    @endforelse
                </div>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_follower'))
            <section class="panelV2">
                <header class="panel__header">
                    <h2 class="panel__heading">{{ __('user.recent-followers') }}</h2>
                    @if (auth()->id() !== $user->id)
                        <div class="panel__actions">
                            @if ($user->followers()->where('users.id', '=', auth()->id())->exists())
                                <form
                                    action="{{ route('users.followers.destroy', ['user' => $user]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="form__button form__button--text"
                                        id="delete-follow-{{ $user->target_id }}"
                                    >
                                        {{ __('user.unfollow') }}
                                    </button>
                                </form>
                            @else
                                <form
                                    action="{{ route('users.followers.store', ['user' => $user]) }}"
                                    method="POST"
                                >
                                    @csrf
                                    <button
                                        class="form__button form__button--text"
                                        id="follow-user-{{ $user->id }}"
                                    >
                                        {{ __('user.follow') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </header>
                <div class="panel__body">
                    @forelse ($followers as $follower)
                        <a href="{{ route('users.show', ['user' => $follower]) }}">
                            <img
                                class="user-search__avatar"
                                alt="{{ $follower->username }}"
                                height="50px"
                                src="{{ $follower->image === null ? url('img/profile.png') : route('authenticated_images.user_avatar', ['user' => $follower]) }}"
                                title="{{ $follower->username }}"
                            />
                        </a>
                    @empty
                        {{ __('member-interface.profile.no-recent-followers') }}
                    @endforelse
                </div>
            </section>
        @endif

        @if (auth()->user()->is($user) || auth()->user()->group->is_modo)
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.client-list') }}</h2>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('torrent.client') }}</th>
                                <th>{{ __('common.ip') }}</th>
                                <th>{{ __('common.port') }}</th>
                                <th>{{ __('torrent.started') }}</th>
                                <th>{{ __('torrent.last-update') }}</th>
                                <th>{{ __('torrent.peers') }}</th>
                                <th>{{ __('torrent.size') }}</th>
                                @if (\config('announce.connectable_check') === true)
                                    <th>{{ __('member-interface.profile.connectable') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clients as $client)
                                <tr>
                                    <td>{{ $client->agent }}</td>
                                    <td>
                                        @if (auth()->user()->group->is_modo)
                                            <a
                                                href="{{ route('staff.peers.index', ['ip' => $client->ip, 'groupBy' => 'user_ip']) }}"
                                            >
                                                {{ $client->ip }}
                                            </a>
                                        @elseif (auth()->id() === $user->id)
                                            {{ $client->ip }}
                                        @endif
                                    </td>
                                    <td>{{ $client->port }}</td>
                                    <td>
                                        <time
                                            datetime="{{ $client->created_at }}"
                                            title="{{ $client->created_at }}"
                                        >
                                            {{ $client->created_at?->diffForHumans() ?? 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $client->updated_at }}"
                                            title="{{ $client->updated_at }}"
                                        >
                                            {{ $client->updated_at?->diffForHumans() ?? 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('users.peers.index', ['user' => $user, 'ip' => $client->ip, 'port' => $client->port, 'client' => $client->agent]) }}"
                                        >
                                            {{ $client->num_peers }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ App\Helpers\StringHelper::formatBytes($client->size) }}
                                    </td>
                                    @if (\config('announce.connectable_check') == true)
                                        @php
                                            $connectable = false;
                                            if (config('announce.external_tracker.is_enabled')) {
                                                $connectable = $client->connectable;
                                            } elseif (cache()->has('peers:connectable:' . $client->ip . '-' . $client->port . '-' . $client->agent)) {
                                                $connectable = cache()->get('peers:connectable:' . $client->ip . '-' . $client->port . '-' . $client->agent);
                                            }
                                        @endphp

                                        <td>
                                            @choice('user.client-connectable-state', $connectable)
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td
                                        colspan="{{ \config('announce.connectable_check') === true ? 8 : 7 }}"
                                    >
                                        {{ __('member-interface.profile.no-clients') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="{{ 7 + (int) config('announce.connectable_check') }}">
                                    {!! __('member-interface.profile.unrecognized-client-notice', ['link' => '<a href="' . route('tickets.index') . '">' . __('member-interface.profile.create-helpdesk-ticket') . '</a>']) !!}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
        @endif

        @if (auth()->user()->group->is_modo)
            @livewire('user-notes', ['user' => $user])
            @if ($user->application !== null)
                <section class="panelV2">
                    <h2 class="panel__heading">{{ __('staff.application') }}</h2>
                    <div class="data-table-wrapper">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('common.email') }}</th>
                                    <th>{{ __('staff.application-type') }}</th>
                                    <th>{{ __('common.created_at') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td>{{ $user->application->email }}</td>
                                <td>{{ $user->application->type }}</td>
                                <td>
                                    <time
                                        datetime="{{ $user->application->created_at }}"
                                        title="{{ $user->application->created_at }}"
                                    >
                                        {{ $user->application->created_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td>
                                    @switch($user->application->status)
                                        @case(\App\Enums\ModerationStatus::PENDING)
                                            <span class="application--pending">{{ __('torrent.pending') }}</span>

                                            @break
                                        @case(\App\Enums\ModerationStatus::APPROVED)
                                            <span class="application--approved">{{ __('torrent.approved') }}</span>

                                            @break
                                        @case(\App\Enums\ModerationStatus::REJECTED)
                                            <span class="application--rejected">{{ __('torrent.rejected') }}</span>

                                            @break
                                        @default
                                            <span class="application--unknown">{{ __('common.unknown') }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                class="form__button form__button--text"
                                                href="{{ route('staff.applications.show', ['id' => $user->application->id]) }}"
                                            >
                                                {{ __('common.view') }}
                                            </a>
                                        </li>
                                    </menu>
                                </td>
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        @endif

        @if (auth()->user()->group->is_modo ||auth()->user()->is($user))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('ticket.helpdesk') }}</h2>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('ticket.subject') }}</th>
                                <th>{{ __('common.status') }}</th>
                                <th>{{ __('ticket.created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user->tickets as $ticket)
                                <tr>
                                    <td>
                                        <a
                                            href="{{ route('tickets.show', ['ticket' => $ticket]) }}"
                                        >
                                            {{ $ticket->subject }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($ticket->closed_at)
                                            <i class="fas fa-circle text-danger"></i>
                                            {{ __('ticket.closed') }}
                                        @else
                                            <i class="fas fa-circle text-success"></i>
                                            {{ __('forum.open') }}
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if (auth()->user()->group->is_modo)
            @include('user.profile.partials.bans', ['bans' => $user->bans])
        @endif

        @if (auth()->user()->group->is_modo ||auth()->user()->is($user))
            <livewire:user-warnings :user="$user" />
        @endif

        @if (auth()->user()->group->is_modo)
            <section class="panelV2">
                <header class="panel__header">
                    <h2 class="panel__heading">{{ __('member-interface.profile.watchlist') }}</h2>
                    <div class="panel__actions">
                        @if ($watch === null)
                            <div class="panel__action" x-data="dialog">
                                <button
                                    class="form__button form__button--text"
                                    x-bind="showDialog"
                                >
                                    {{ __('member-interface.profile.watch-action') }}
                                </button>
                                <dialog class="dialog" x-bind="dialogElement">
                                    <h3 class="dialog__heading">
                                        {{ __('member-interface.profile.watch-user-heading', ['username' => $user->username]) }}
                                    </h3>
                                    <form
                                        class="dialog__form"
                                        method="POST"
                                        action="{{ route('staff.watchlist.store') }}"
                                        x-bind="dialogForm"
                                    >
                                        @csrf
                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="{{ $user->id }}"
                                        />
                                        <p class="form__group">
                                            <textarea
                                                id="watchlist_reason"
                                                class="form__textarea"
                                                name="message"
                                                required
                                            ></textarea>
                                            <label
                                                class="form__label form__label--floating"
                                                for="watchlist_reason"
                                            >
                                                {{ __('common.reason') }}
                                            </label>
                                        </p>
                                        <p class="form__group">
                                            <button class="form__button form__button--filled">
                                                {{ __('common.save') }}
                                            </button>
                                            <button
                                                formaction="dialog"
                                                formnovalidate
                                                class="form__button form__button--outlined"
                                            >
                                                {{ __('common.cancel') }}
                                            </button>
                                        </p>
                                    </form>
                                </dialog>
                            </div>
                        @else
                            <form
                                class="panel__action"
                                action="{{ route('staff.watchlist.destroy', ['watchlist' => $watch]) }}"
                                method="POST"
                            >
                                @csrf
                                @method('DELETE')
                                <button class="form__button form__button--text">
                                    {{ __('member-interface.profile.unwatch-action') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </header>
                <div class="data-table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>{{ __('member-interface.profile.watched-by') }}</th>
                                <th>{{ __('common.message') }}</th>
                                <th>{{ __('common.created_at') }}</th>
                                <th>{{ __('common.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($watch === null)
                                <tr>
                                    <td colspan="4">{{ __('member-interface.profile.not-watched') }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td>
                                        <x-user-tag :anon="false" :user="$watch->author" />
                                    </td>
                                    <td>{{ $watch->message }}</td>
                                    <td>
                                        <time
                                            datetime="{{ $watch->created_at }}"
                                            title="{{ $watch->created_at }}"
                                        >
                                            {{ $watch->created_at }}
                                        </time>
                                    </td>
                                    <td>
                                        <menu class="data-table__actions">
                                            <li class="data-table__action">
                                                <form
                                                    action="{{ route('staff.watchlist.destroy', ['watchlist' => $watch]) }}"
                                                    method="POST"
                                                    x-data="confirmation"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        x-on:click.prevent="confirmAction"
                                                        data-b64-deletion-message="{{ base64_encode(__('member-interface.profile.confirm-unwatch-user', ['username' => $watch->user->username])) }}"
                                                        class="form__button form__button--text"
                                                    >
                                                        {{ __('member-interface.profile.unwatch-action') }}
                                                    </button>
                                                </form>
                                            </li>
                                        </menu>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    @endsection

    @section('sidebar')
        @if (auth()->user()->group->is_modo ||auth()->user()->is($user))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('member-interface.profile.donations') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('member-interface.profile.active-donor') }}</dt>
                        <dd>
                            @if ($user->is_donor)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('member-interface.profile.lifetime-donor') }}</dt>
                        <dd>
                            @if ($user->is_lifetime)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('member-interface.profile.latest-donation-amount') }}</dt>
                        <dd>
                            {{ $donation->package->cost ?? __('member-interface.profile.na') }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('member-interface.profile.latest-donation-date') }}</dt>
                        <dd>
                            {{ $donation->starts_at ?? __('member-interface.profile.na') }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('member-interface.profile.donation-expire-date') }}</dt>
                        <dd>
                            @if ($user->is_lifetime)
                                {{ __('member-interface.profile.lifetime-donor') }}
                                <i
                                    class="fal fa-star"
                                    id="lifeline"
                                    title="{{ __('member-interface.profile.lifetime-donor') }}"
                                ></i>
                            @else
                                {{ $donation->ends_at ?? __('member-interface.profile.na') }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_warning'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('common.warnings') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('user.active-warnings') }}</dt>
                        <dd>{{ $user->active_warnings_count ?? 0 }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.hit-n-runs-count') }}</dt>
                        <dd>{{ $user->hitandruns }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_torrent_seed'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('member-interface.profile.seed-statistics') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>
                            <abbr
                                title="{{ __('user.total-seedtime') }} ({{ __('user.all-torrents') }})"
                            >
                                {{ __('user.total-seedtime') }}
                            </abbr>
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::timeElapsed($history->seedtime_sum ?? 0) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            <abbr
                                title="{{ __('user.avg-seedtime') }} ({{ __('user.per-torrent') }})"
                            >
                                {{ __('user.avg-seedtime') }}
                            </abbr>
                        </dt>

                        <dd>
                            {{ App\Helpers\StringHelper::timeElapsed(($history->seedtime_sum ?? 0) / max(1, $history->count ?? 0)) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            <abbr
                                title="{{ __('user.seeding-size') }} ({{ __('user.all-torrents') }})"
                            >
                                {{ __('user.seeding-size') }}
                            </abbr>
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($user->seedingTorrents()->sum('size'), 2) }}
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_torrent_count'))
            @if (auth()->user()->is($user) || auth()->user()->group->is_modo)
                <section class="panelV2">
                    <h2 class="panel__heading">{{ __('member-interface.profile.torrent-count') }}</h2>
                    <dl class="key-value">
                        <div class="key-value__group">
                            <dt>
                                <a href="{{ route('users.torrents.index', ['user' => $user]) }}">
                                    {{ __('member-interface.profile.total-uploads-non-anonymous') }}
                                </a>
                            </dt>
                            <dd>{{ $user->non_anon_uploads_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a href="{{ route('users.torrents.index', ['user' => $user]) }}">
                                    {{ __('member-interface.profile.total-uploads-anonymous') }}
                                </a>
                            </dt>
                            <dd>{{ $user->anon_uploads_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('users.history.index', ['user' => $user, 'downloaded' => 'include']) }}"
                                >
                                    {{ __('user.total-downloads') }}
                                </a>
                            </dt>
                            <dd>{{ $history->download_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('users.peers.index', ['user' => $user, 'seeding' => 'include']) }}"
                                >
                                    {{ __('user.total-seeding') }}
                                </a>
                            </dt>
                            <dd>{{ $peers->seeding ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('users.peers.index', ['user' => $user, 'seeding' => 'exclude']) }}"
                                >
                                    {{ __('user.total-leeching') }}
                                </a>
                            </dt>
                            <dd>{{ $peers->leeching ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('users.peers.index', ['user' => $user, 'active' => 'exclude']) }}"
                                >
                                    {{ __('member-interface.profile.total-inactive-peers') }}
                                </a>
                            </dt>
                            <dd>{{ $peers->inactive ?? 0 }}</dd>
                        </div>
                    </dl>
                </section>
            @else
                <section class="panelV2">
                    <h2 class="panel__heading">{{ __('member-interface.profile.torrent-count') }}</h2>
                    <dl class="key-value">
                        <div class="key-value__group">
                            <dt>
                                <a
                                    href="{{ route('torrents.index', ['uploader' => $user->username]) }}"
                                >
                                    {{ __('user.total-uploads') }}
                                </a>
                            </dt>
                            <dd>{{ $user->non_anon_uploads_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-downloads') }}</dt>
                            <dd>{{ $history->download_count ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-seeding') }}</dt>
                            <dd>{{ $peers->seeding ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-leeching') }}</dt>
                            <dd>{{ $peers->leeching ?? 0 }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('member-interface.profile.total-inactive-peers') }}</dt>
                            <dd>{{ $peers->inactive ?? 0 }}</dd>
                        </div>
                    </dl>
                </section>
            @endif
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_torrent_ratio'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('member-interface.profile.traffic-statistics') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('common.ratio') }}</dt>
                        <dd>{{ $user->formatted_ratio }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('member-interface.profile.real-ratio') }}</dt>
                        <dd>
                            {{ $history->download_sum ? round(($history->upload_sum ?? 0) / $history->download_sum, 2) : "\u{221E}" }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('common.buffer') }}</dt>
                        <dd>{{ $user->formatted_buffer }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('common.account') }} {{ __('common.uploaded') }} ({{ __('common.total') }})</dt>
                        <dd>{{ $user->formatted_uploaded }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('common.account') }} {{ __('common.downloaded') }} ({{ __('common.total') }})</dt>
                        <dd>{{ $user->formatted_downloaded }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('torrent.torrent') }} {{ __('torrent.uploaded') }}</dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->upload_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            {{ __('torrent.torrent') }} {{ __('torrent.uploaded') }}
                            ({{ __('torrent.credited') }})
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->credited_upload_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('torrent.torrent') }} {{ __('torrent.downloaded') }}</dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->download_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            {{ __('torrent.torrent') }} {{ __('torrent.downloaded') }}
                            ({{ __('torrent.credited') }})
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->credited_download_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            {{ __('torrent.torrent') }} {{ __('torrent.downloaded') }}
                            ({{ __('torrent.refunded') }})
                        </dt>
                        <dd>
                            {{ App\Helpers\StringHelper::formatBytes($history->refunded_download_sum ?? 0, 2) }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('bon.bon') }} {{ __('common.uploaded') }}</dt>
                        <dd>{{ App\Helpers\StringHelper::formatBytes($boughtUpload, 2) }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (config('announce.external_tracker.is_enabled') && auth()->user()->group->is_modo)
            @if ($externalUser === true)
                <section class="panelV2">
                    <h2 class="panel__heading">{{ __('member-interface.profile.external-tracker') }}</h2>
                    <div class="panel__body">
                        {{ __('member-interface.profile.external-tracker-not-enabled') }}
                    </div>
                </section>
            @elseif ($externalUser === false)
                <section class="panelV2">
                    <h2 class="panel__heading">{{ __('member-interface.profile.external-tracker') }}</h2>
                    <div class="panel__body">
                        {{ __('member-interface.profile.external-user-not-found') }}
                    </div>
                </section>
            @elseif ($externalUser === [])
                <section class="panelV2">
                    <h2 class="panel__heading">{{ __('member-interface.profile.external-tracker') }}</h2>
                    <div class="panel__body">{{ __('member-interface.profile.tracker-error') }}</div>
                </section>
            @else
                <section class="panelV2">
                    <h2 class="panel__heading">{{ __('member-interface.profile.external-tracker') }}</h2>
                    <dl class="key-value">
                        <div class="key-value__group">
                            <dt>{{ __('common.group') }}</dt>
                            <dd>
                                @if (null !== ($group = \App\Models\Group::find($externalUser['group_id'])))
                                    <span class="user-tag">
                                        <span
                                            class="user-tag__link {{ $group->icon }}"
                                            style="color: {{ $group->color }}"
                                            title="{{ $group->name }}"
                                        >
                                            {{ $group->name }}
                                        </span>
                                    </span>
                                @else
                                    {{ __('member-interface.profile.unrecognized-group-id', ['id' => $externalUser['group_id']]) }}
                                @endif
                            </dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.passkey') }}</dt>
                            <dd>
                                <details>
                                    <summary style="cursor: pointer">
                                        {{ __('user.show-passkey') }}
                                    </summary>
                                    <code><pre>{{ $externalUser['passkey'] }}</pre></code>
                                    <span class="text-red">{{ __('user.passkey-warning') }}</span>
                                </details>
                            </dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.can-download') }}</dt>
                            <dd>
                                {{ $externalUser['can_download'] ? __('common.yes') : __('common.no') }}
                            </dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-seeding') }}</dt>
                            <dd>{{ $externalUser['num_seeding'] }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.total-leeching') }}</dt>
                            <dd>{{ $externalUser['num_leeching'] }}</dd>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('member-interface.profile.seed-lists') }}</th>
                                    <th>{{ __('member-interface.profile.window') }}</th>
                                    <th>{{ __('member-interface.profile.max') }}</th>
                                    <th>{{ __('member-interface.profile.lists-per-hour') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($externalUser['receive_seed_list_rates']['rates'] as $rate)
                                    <tr>
                                        <td
                                            title="{{ __('member-interface.profile.updated-at-tooltip', ['date' => ($lastUpdatedAt = \Illuminate\Support\Carbon::createFromTimestampUTC($rate['updated_at'])), 'relative' => $lastUpdatedAt->diffForHumans()]) }}"
                                        >
                                            {{ \number_format($rate['count'], 2, null, "\u{202F}") }}
                                        </td>
                                        <td>{{ $rate['window'] }}</td>
                                        <td>{{ $rate['max_count'] }}</td>
                                        <td>
                                            {{ \number_format((3600 * $rate['count']) / $rate['window'], 1, null, "\u{202F}") }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>{{ __('member-interface.profile.leech-lists') }}</th>
                                    <th>{{ __('member-interface.profile.window') }}</th>
                                    <th>{{ __('member-interface.profile.max') }}</th>
                                    <th>{{ __('member-interface.profile.lists-per-hour') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($externalUser['receive_leech_list_rates']['rates'] as $rate)
                                    <tr>
                                        <td
                                            title="{{ __('member-interface.profile.updated-at-tooltip', ['date' => ($lastUpdatedAt = \Illuminate\Support\Carbon::createFromTimestampUTC($rate['updated_at'])), 'relative' => $lastUpdatedAt->diffForHumans()]) }}"
                                        >
                                            {{ \number_format($rate['count'], 2, null, "\u{202F}") }}
                                        </td>
                                        <td>{{ $rate['window'] }}</td>
                                        <td>{{ $rate['max_count'] }}</td>
                                        <td>
                                            {{ \number_format((3600 * $rate['count']) / $rate['window'], 1, null, "\u{202F}") }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </dl>
                </section>
            @endif
        @endif

        @if (auth()->user()->is($user) || auth()->user()->group->is_modo)
            <section class="panelV2">
                <h2 class="panel__heading">
                    {{ __('user.id-permissions') }}
                </h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('user.invited-by') }}</dt>
                        <dd>
                            @if ($invitedBy)
                                <x-user-tag :user="$invitedBy->sender" :anon="false" />
                            @else
                                <b>{{ __('user.open-registration') }}</b>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.passkey') }}</dt>
                        <dd>
                            <details>
                                <summary style="cursor: pointer">
                                    {{ __('user.show-passkey') }}
                                </summary>
                                <code><pre>{{ $user->passkey }}</pre></code>
                                <span class="text-red">{{ __('user.passkey-warning') }}</span>
                            </details>
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.user-id') }}</dt>
                        <dd>{{ $user->id }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('common.email') }}</dt>
                        <dd>{{ $user->email }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('member-interface.profile.two-factor-enabled') }}</dt>
                        <dd>
                            @if ($user->two_factor_confirmed_at !== null)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-lock text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-lock-open text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.last-login') }}</dt>
                        <dd>
                            @if ($user->last_login === null)
                                {{ __('member-interface.profile.na') }}
                            @else
                                <time
                                    class="{{ $user->last_login }}"
                                    datetime="{{ $user->last_login }}"
                                    title="{{ $user->last_login }}"
                                >
                                    {{ $user->last_login->diffForHumans() }}
                                </time>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('member-interface.profile.last-action') }}</dt>
                        <dd>
                            @if ($user->last_action === null)
                                {{ __('member-interface.profile.na') }}
                            @else
                                <time
                                    class="{{ $user->last_action }}"
                                    datetime="{{ $user->last_action }}"
                                    title="{{ $user->last_action }}"
                                >
                                    {{ $user->last_action->diffForHumans() }}
                                </time>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-upload') }}</dt>
                        <dd>
                            @if ($user->can_upload ?? $user->group->can_upload)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-download') }}</dt>
                        <dd>
                            @if ($user->can_download == 1)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-comment') }}</dt>
                        <dd>
                            @if ($user->can_comment ?? $user->group->can_comment)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-request') }}</dt>
                        <dd>
                            @if ($user->can_request ?? $user->group->can_request)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-chat') }}</dt>
                        <dd>
                            @if ($user->can_chat ?? $user->group->can_chat)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.can-invite') }}</dt>
                        <dd>
                            @if (($user->can_invite ?? $user->group->can_invite) && $user->two_factor_confirmed_at !== null)
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check text-green"
                                ></i>
                            @else
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-red"
                                ></i>
                            @endif
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            <a href="{{ route('users.invites.index', ['user' => $user]) }}">
                                {{ __('user.invites') }}
                            </a>
                        </dt>
                        <dd>{{ $user->invites }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_bon_extra'))
            <section class="panelV2">
                <header class="panel__header">
                    <h2 class="panel__heading">{{ __('user.bon') }}</h2>
                    @if (auth()->user()->isNot($user))
                        <div class="panel__actions">
                            <div class="panel__action" x-data="dialog">
                                <button
                                    class="form__button form__button--text"
                                    x-bind="showDialog"
                                >
                                    {{ __('member-interface.profile.gift-bon-action') }}
                                </button>
                                <dialog class="dialog" x-bind="dialogElement">
                                    <h3 class="dialog__heading">
                                        {{ __('member-interface.profile.gift-bon-heading', ['username' => $user->username]) }}
                                    </h3>
                                    <form
                                        class="dialog__form"
                                        method="POST"
                                        action="{{ route('users.gifts.store', ['user' => auth()->user()]) }}"
                                        x-bind="dialogForm"
                                    >
                                        @csrf
                                        <input
                                            type="hidden"
                                            name="recipient_username"
                                            value="{{ $user->username }}"
                                        />
                                        <p class="form__group">
                                            <input
                                                id="bon"
                                                class="form__text"
                                                name="bon"
                                                type="text"
                                                pattern="[0-9]*"
                                                inputmode="numeric"
                                                placeholder=" "
                                            />
                                            <label
                                                class="form__label form__label--floating"
                                                for="bon"
                                            >
                                                {{ __('bon.amount') }}
                                            </label>
                                        </p>

                                        <p class="form__group">
                                            <textarea
                                                id="message"
                                                class="form__textarea"
                                                name="message"
                                                placeholder=" "
                                            ></textarea>
                                            <label
                                                class="form__label form__label--floating"
                                                for="message"
                                            >
                                                {{ __('pm.message') }}
                                            </label>
                                        </p>
                                        <p class="form__group">
                                            <button class="form__button form__button--filled">
                                                {{ __('bon.gift') }}
                                            </button>
                                            <button
                                                formmethod="dialog"
                                                formnovalidate
                                                class="form__button form__button--outlined"
                                            >
                                                {{ __('common.cancel') }}
                                            </button>
                                        </p>
                                    </form>
                                </dialog>
                            </div>
                        </div>
                    @endif
                </header>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>
                            <a href="{{ route('users.earnings.index', ['user' => $user]) }}">
                                {{ __('bon.bon') }}
                            </a>
                        </dt>
                        <dd>{{ $user->formatted_seedbonus }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.tips-received') }}</dt>
                        <dd>
                            {{ \number_format($user->receivedPostTips()->sum('bon') + $user->receivedTorrentTips()->sum('bon'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.tips-given') }}</dt>
                        <dd>
                            {{ \number_format($user->sentPostTips()->sum('bon') + $user->sentTorrentTips()->sum('bon'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.gift-received') }}</dt>
                        <dd>
                            {{ \number_format($user->receivedGifts()->sum('bon'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.gift-given') }}</dt>
                        <dd>
                            {{ \number_format($user->sentGifts()->sum('bon'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.bounty-received') }}</dt>
                        <dd>
                            {{ \number_format($user->filledRequests()->sum('bounty'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.bounty-given') }}</dt>
                        <dd>
                            {{ \number_format($user->requestBounty()->sum('seedbonus'), 0, null, "\u{202F}") }}
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_torrent_extra'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.torrents') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('common.fl_tokens') }}</dt>
                        <dd>{{ $user->fl_tokens }}</dd>
                    </div>
                    @if (config('other.thanks-system.is-enabled'))
                        <div class="key-value__group">
                            <dt>{{ __('user.thanks-received') }}</dt>
                            <dd>{{ $user->thanksReceived()->count() }}</dd>
                        </div>
                        <div class="key-value__group">
                            <dt>{{ __('user.thanks-given') }}</dt>
                            <dd>{{ $user->thanksGiven()->count() }}</dd>
                        </div>
                    @endif

                    <div class="key-value__group">
                        <dt>{{ __('user.upload-snatches') }}</dt>
                        <dd>{{ $user->uploadSnatches()->count() }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_comment_extra'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.comments') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('user.article-comments') }}</dt>
                        <dd>
                            {{ $user->comments()->whereHasMorph('commentable', [App\Models\Article::class])->count() }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.torrent-comments') }}</dt>
                        <dd>
                            {{ $user->comments()->whereHasMorph('commentable', [App\Models\Torrent::class])->count() }}
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.request-comments') }}</dt>
                        <dd>
                            {{ $user->comments()->whereHasMorph('commentable', [App\Models\TorrentRequest::class])->count() }}
                        </dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_forum_extra'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.forums') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>
                            <a href="{{ route('users.topics.index', ['user' => $user]) }}">
                                {{ __('user.topics-started') }}
                            </a>
                        </dt>
                        <dd>{{ $user->topics_count }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>
                            <a href="{{ route('users.posts.index', ['user' => $user]) }}">
                                {{ __('user.posts-posted') }}
                            </a>
                        </dt>
                        <dd>{{ $user->posts_count }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if (auth()->user()->isAllowed($user, 'profile', 'show_profile_request_extra'))
            <section class="panelV2">
                <h2 class="panel__heading">{{ __('user.requests') }}</h2>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>
                            <a
                                href="{{ route('requests.index', ['requestor' => $user->username]) }}"
                            >
                                {{ __('user.requested') }}
                            </a>
                        </dt>
                        <dd>{{ $user->requests_count }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.filled-request') }}</dt>
                        <dd>{{ $user->filled_requests_count }}</dd>
                    </div>
                </dl>
            </section>
        @endif
    @endsection
@else
    @section('main')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.private-profile') }}</h2>
            <div class="panel__body">{{ __('user.not-authorized') }}</div>
        </section>
    @endsection
@endif
