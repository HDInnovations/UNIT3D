@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('staff.staff-dashboard') }}
    </li>
@endsection

@section('page', 'page__dashboard')

@section('main')
    <div class="dashboard__menus">
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-link"></i>
                {{ __('staff.links') }}
            </h2>
            <div class="panel__body">
                <p class="form__group form__group--horizontal">
                    <a class="form__button form__button--text" href="{{ route('home.index') }}">
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        {{ __('staff.frontend') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.dashboard.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        {{ __('staff.staff-dashboard') }}
                    </a>
                </p>
                @if (auth()->user()->group->is_owner)
                    <p class="form__group form__group--horizontal">
                        <a
                            class="form__button form__button--text"
                            href="{{ route('staff.backups.index') }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-hdd"></i>
                            {{ __('backup.backup') }}
                            {{ __('backup.manager') }}
                        </a>
                    </p>
                    <p class="form__group form__group--horizontal">
                        <a
                            class="form__button form__button--text"
                            href="{{ route('staff.commands.index') }}"
                        >
                            <i class="fab fa-laravel"></i>
                            Commands
                        </a>
                    </p>

                    @if (config('donation.is_enabled'))
                        <p class="form__group form__group--horizontal">
                            <a
                                class="form__button form__button--text"
                                href="{{ route('staff.donations.index') }}"
                            >
                                <i class="{{ config('other.font-awesome') }} fa-money-bill"></i>
                                Donations
                            </a>
                        </p>
                        <p class="form__group form__group--horizontal">
                            <a
                                class="form__button form__button--text"
                                href="{{ route('staff.gateways.index') }}"
                            >
                                <i class="{{ config('other.font-awesome') }} fa-money-bill"></i>
                                Gateways
                            </a>
                        </p>
                        <p class="form__group form__group--horizontal">
                            <a
                                class="form__button form__button--text"
                                href="{{ route('staff.packages.index') }}"
                            >
                                <i class="{{ config('other.font-awesome') }} fa-money-bill"></i>
                                Packages
                            </a>
                        </p>
                    @endif
                @endif
            </div>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-wrench"></i>
                {{ __('staff.chat-tools') }}
            </h2>
            <div class="panel__body">
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.statuses.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-comment-dots"></i>
                        {{ __('staff.statuses') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.chatrooms.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-comment-dots"></i>
                        {{ __('staff.rooms') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.bots.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-robot"></i>
                        {{ __('staff.bots') }}
                    </a>
                </p>
                <div class="form__group form__group--horizontal">
                    <form
                        method="POST"
                        action="{{ route('staff.flush.chat') }}"
                        x-data="confirmation"
                    >
                        @csrf
                        <button
                            x-on:click.prevent="confirmAction"
                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete all chatbox messages in all chatrooms (including private chatbox messages)?') }}"
                            class="form__button form__button--text"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-broom"></i>
                            {{ __('staff.flush-chat') }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-wrench"></i>
                {{ __('staff.general-tools') }}
            </h2>
            <div class="panel__body">
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.articles.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-newspaper"></i>
                        {{ __('staff.articles') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.events.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-calendar-star"></i>
                        {{ __('event.events') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.bon_exchanges.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-coins"></i>
                        {{ __('staff.bon-exchange') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.bon_earnings.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-coins"></i>
                        {{ __('staff.bon-earnings') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.blacklisted_clients.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-ban"></i>
                        {{ __('common.blacklist') }}
                    </a>
                </p>
                @if (auth()->user()->group->is_admin)
                    <p class="form__group form__group--horizontal">
                        <a
                            class="form__button form__button--text"
                            href="{{ route('staff.forum_categories.index') }}"
                        >
                            <i class="fab fa-wpforms"></i>
                            {{ __('staff.forums') }}
                        </a>
                    </p>
                @endif

                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.pages.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.pages') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.polls.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-chart-pie"></i>
                        {{ __('staff.polls') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.whitelisted_image_urls.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-globe"></i>
                        Whitelisted Image URLs
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.wiki_categories.index') }}"
                    >
                        <i class="fab fa-wikipedia-w"></i>
                        Wikis
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.blocked_ips.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-ban"></i>
                        {{ __('staff.blocked-ips') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.playlist_categories.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-list"></i>
                        Playlist Categories
                    </a>
                </p>
            </div>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-wrench"></i>
                {{ __('staff.torrent-tools') }}
            </h2>
            <div class="panel__body">
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.moderation.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        {{ __('staff.torrent-moderation') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.categories.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        {{ __('staff.torrent-categories') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.types.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        {{ __('staff.torrent-types') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.resolutions.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        {{ __('staff.torrent-resolutions') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.regions.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        Torrent Regions
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.distributors.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        Torrent Distributors
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.automatic_torrent_freeleeches.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        Automatic Torrent Freeleeches
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.peers.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        Peers
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.histories.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        Histories
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.torrent_downloads.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        Downloads
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.torrent_trumps.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        Trumps
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.unregistered_info_hashes.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        Unregistered Info Hashes
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.rss.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-rss"></i>
                        {{ __('staff.rss') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.media_languages.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-columns"></i>
                        {{ __('common.media-languages') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.cheated_torrents.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-question"></i>
                        Cheated Torrents
                    </a>
                </p>
                @if (config('announce.log_announces'))
                    <p class="form__group form__group--horizontal">
                        <a
                            class="form__button form__button--text"
                            href="{{ route('staff.announces.index') }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-chart-bar"></i>
                            Announces
                        </a>
                    </p>
                @endif

                @if (! config('announce.external_tracker.is_enabled'))
                    <div class="form__group form__group--horizontal">
                        <form
                            method="POST"
                            action="{{ route('staff.flush.peers') }}"
                            x-data="confirmation"
                        >
                            @csrf
                            <button
                                x-on:click.prevent="confirmAction"
                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete all ghost peers?') }}"
                                class="form__button form__button--text"
                            >
                                <i class="{{ config('other.font-awesome') }} fa-ghost"></i>
                                {{ __('staff.flush-ghost-peers') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-wrench"></i>
                {{ __('staff.user-tools') }}
            </h2>
            <div class="panel__body">
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.applications.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-list"></i>
                        {{ __('staff.applications') }} ({{ $pendingApplicationsCount }})
                        @if ($pendingApplicationsCount > 0)
                            <x-animation.notification />
                        @endif
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.users.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-users"></i>
                        {{ __('staff.user-search') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.apikeys.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-key"></i>
                        {{ __('user.apikeys') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.passkeys.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-key"></i>
                        {{ __('staff.passkeys') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.rsskeys.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-key"></i>
                        {{ __('user.rsskeys') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.email_updates.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-key"></i>
                        {{ __('user.email-updates') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.password_reset_histories.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-key"></i>
                        {{ __('user.password-resets') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.watchlist.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-eye"></i>
                        Watchlist
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.mass_private_message.create') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-envelope-square"></i>
                        {{ __('staff.mass-pm') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.mass_email.create') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-paper-plane"></i>
                        {{ __('staff.mass-email') }}
                    </a>
                </p>
                <div class="form__group form__group--horizontal">
                    <form
                        method="GET"
                        action="{{ route('staff.mass-actions.validate') }}"
                        x-data="confirmation"
                    >
                        @csrf
                        <button
                            x-on:click.prevent="confirmAction"
                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to automatically validate all users even if their email address isn\'t confirmed?') }}"
                            class="form__button form__button--text"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-history"></i>
                            {{ __('staff.mass-validate-users') }}
                        </button>
                    </form>
                </div>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.cheaters.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-question"></i>
                        {{ __('staff.possible-leech-cheaters') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.leakers.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-faucet-drip"></i>
                        Leakers
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.seedboxes.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-server"></i>
                        {{ __('staff.seedboxes') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.uploaders.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-upload"></i>
                        {{ __('torrent.uploader') }} {{ __('common.stats') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.internals.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-magic"></i>
                        Internals
                    </a>
                </p>
                @if (auth()->user()->group->is_admin)
                    <p class="form__group form__group--horizontal">
                        <a
                            class="form__button form__button--text"
                            href="{{ route('staff.groups.index') }}"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-users"></i>
                            {{ __('staff.groups') }}
                        </a>
                    </p>
                @endif
            </div>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-file"></i>
                {{ __('staff.logs') }}
            </h2>
            <div class="panel__body">
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.audits.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.audit-log') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.bans.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.bans-log') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.authentications.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.failed-login-log') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.gifts.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.gifts-log') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.invites.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.invites-log') }}
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.notes.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.user-notes') }}
                    </a>
                </p>
                @if (auth()->user()->group->is_owner)
                    <p class="form__group form__group--horizontal">
                        <a
                            class="form__button form__button--text"
                            href="{{ route('staff.laravel-log.index') }}"
                        >
                            <i class="fa fa-file"></i>
                            {{ __('staff.laravel-log') }}
                        </a>
                    </p>
                @endif

                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.reports.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.reports-log') }} ({{ $unsolvedReportsCount }})
                        @if ($unsolvedReportsCount > 0)
                            <x-animation.notification />
                        @endif
                    </a>
                </p>
                <p class="form__group form__group--horizontal">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.warnings.index') }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-file"></i>
                        {{ __('staff.warnings-log') }}
                    </a>
                </p>
            </div>
        </section>
    </div>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">SSL Certificate</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>URL</dt>
                <dd>{{ config('app.url') }}</dd>
            </div>
            @if (request()->secure())
                <div class="key-value__group">
                    <dt>Connection</dt>
                    <dd>Secure</dd>
                </div>
                <div class="key-value__group">
                    <dt>Issued By</dt>
                    <dd>
                        {{ ! is_string($certificate) ? $certificate->getIssuer() : 'No Certificate Info Found' }}
                    </dd>
                </div>
                <div class="key-value__group">
                    <dt>Expires</dt>
                    <dd>
                        {{ ! is_string($certificate) ? $certificate->expirationDate()->diffForHumans() : 'No Certificate Info Found' }}
                    </dd>
                </div>
            @else
                <div class="key-value__group">
                    <dt>Connection</dt>
                    <dd>
                        <strong>Not Secure</strong>
                    </dd>
                </div>
                <div class="key-value__group">
                    <dt>Issued By</dt>
                    <dd>N/A</dd>
                </div>
                <div class="key-value__group">
                    <dt>Expires</dt>
                    <dd>N/A</dd>
                </div>
            @endif
        </dl>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">Server Information</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>OS</dt>
                <dd>{{ $basic['os'] }}</dd>
            </div>
            <div class="key-value__group">
                <dt>PHP</dt>
                <dd>{{ $basic['php'] }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Database</dt>
                <dd>{{ $basic['database'] }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Laravel</dt>
                <dd>{{ $basic['laravel'] }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ config('unit3d.codebase') }}</dt>
                <dd>{{ config('unit3d.version') }}</dd>
            </div>
        </dl>
    </section>
    <div class="dashboard__stats">
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">Torrents</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>Total</dt>
                    <dd>{{ $torrents->total }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Pending</dt>
                    <dd>{{ $torrents->pending }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Approved</dt>
                    <dd>{{ $torrents->approved }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Postponed</dt>
                    <dd>{{ $torrents->postponed }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Rejected</dt>
                    <dd>{{ $torrents->rejected }}</dd>
                </div>
            </dl>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">Peers</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>Total</dt>
                    <dd>{{ $peers->total }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Active</dt>
                    <dd>{{ $peers->active }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Inactive</dt>
                    <dd>{{ $peers->inactive }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Seeds</dt>
                    <dd>{{ $peers->seeders }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Leeches</dt>
                    <dd>{{ $peers->leechers }}</dd>
                </div>
            </dl>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">Users</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>Total</dt>
                    <dd>{{ $users->total }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Validating</dt>
                    <dd>{{ $users->validating }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Banned</dt>
                    <dd>{{ $users->banned }}</dd>
                </div>
            </dl>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">RAM</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>Total</dt>
                    <dd>{{ $ram['total'] }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Used</dt>
                    <dd>{{ $ram['used'] }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Free</dt>
                    <dd>{{ $ram['available'] }}</dd>
                </div>
            </dl>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">Disk</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>Total</dt>
                    <dd>{{ $disk['total'] }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Used</dt>
                    <dd>{{ $disk['used'] }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Free</dt>
                    <dd>{{ $disk['free'] }}</dd>
                </div>
            </dl>
        </section>
        <section class="panelV2 panel--grid-item">
            <h2 class="panel__heading">Load Average</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>1 minute</dt>
                    <dd>{{ $avg['1-minute'] ?? 'N/A' }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>5 minutes</dt>
                    <dd>{{ $avg['5-minute'] ?? 'N/A' }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>15 minutes</dt>
                    <dd>{{ $avg['15-minute'] ?? 'N/A' }}</dd>
                </div>
            </dl>
        </section>
    </div>
    <section class="panelV2">
        <h2 class="panel__heading">Directory Permissions</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Directory</th>
                        <th>Current</th>
                        <th><abbr title="Recommended">Rec.</abbr></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($file_permissions as $permission)
                        <tr>
                            <td>{{ $permission['directory'] }}</td>
                            <td>
                                @if ($permission['permission'] === $permission['recommended'])
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check-circle"
                                    ></i>
                                    {{ $permission['permission'] }}
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times-circle"
                                    ></i>
                                    {{ $permission['permission'] }}
                                @endif
                            </td>
                            <td>{{ $permission['recommended'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @if (config('announce.external_tracker.is_enabled'))
        @if ($externalTrackerStats === true)
            <section class="panelV2">
                <h2 class="panel__heading">External Tracker Stats</h2>
                <div class="panel__body">External tracker not enabled.</div>
            </section>
        @elseif ($externalTrackerStats === false)
            <section class="panelV2">
                <h2 class="panel__heading">External Tracker Stats</h2>
                <div class="panel__body">Stats endpoint not found.</div>
            </section>
        @elseif ($externalTrackerStats === [])
            <section class="panelV2">
                <h2 class="panel__heading">External Tracker Stats</h2>
                <div class="panel__body">Tracker returned an error.</div>
            </section>
        @else
            <section class="panelV2">
                <h2 class="panel__heading">External Tracker Stats</h2>
                <dl class="key-value">
                    @php
                        $createdAt = \Illuminate\Support\Carbon::createFromTimestampUTC($externalTrackerStats['created_at']);
                        $lastRequestAt = \Illuminate\Support\Carbon::createFromTimestampUTC($externalTrackerStats['last_request_at']);
                        $lastAnnounceResponseAt = \Illuminate\Support\Carbon::createFromTimestampUTC($externalTrackerStats['last_announce_response_at']);
                    @endphp

                    <div class="key-value__group">
                        <dt>{{ __('torrent.started') }}</dt>
                        <dd>
                            <time
                                title="{{ $createdAt->format('Y-m-d h:i:s') }}"
                                datetime="{{ $createdAt->format('Y-m-d h:i:s') }}"
                            >
                                {{ $createdAt->diffForHumans() }}
                            </time>
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>Last Request At</dt>
                        <dd>
                            <time
                                title="{{ $lastRequestAt->format('Y-m-d h:i:s') }}"
                                datetime="{{ $lastRequestAt->format('Y-m-d h:i:s') }}"
                            >
                                {{ $lastRequestAt->diffForHumans() }}
                            </time>
                        </dd>
                    </div>
                    <div class="key-value__group">
                        <dt>Last Successful Response At</dt>
                        <dd>
                            <time
                                title="{{ $lastAnnounceResponseAt->format('Y-m-d h:i:s') }}"
                                datetime="{{ $lastAnnounceResponseAt->format('Y-m-d h:i:s') }}"
                            >
                                {{ $lastAnnounceResponseAt->diffForHumans() }}
                            </time>
                        </dd>
                    </div>
                </dl>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="text-align: right">Interval (s)</th>
                            <th style="text-align: right">In (req/s)</th>
                            <th style="text-align: right">Out (req/s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ([1, 10, 60, 900, 7200] as $interval)
                            <tr>
                                <td style="text-align: right">{{ $interval }}</td>
                                <td style="text-align: right">
                                    {{ \number_format($externalTrackerStats['requests_per_' . $interval . 's'], 0, null, "\u{202F}") }}
                                </td>
                                <td style="text-align: right">
                                    {{ \number_format($externalTrackerStats['announce_responses_per_' . $interval . 's'], 0, null, "\u{202F}") }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        @endif
    @endif
@endsection
