@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('playlists.index') }}" class="breadcrumb__link">
            {{ __('playlist.playlists') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $playlist->name }}
    </li>
@endsection

@section('page', 'page__playlist--show')

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.actions') }}</h2>
        <div class="panel__body">
            @if (auth()->id() === $playlist->user_id || auth()->user()->group->is_modo)
                <div class="form__group form__group--horizontal">
                    <button
                        class="form__button form__button--filled form__button--centered"
                        popovertarget="playlist-item-add"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-search-plus"></i>
                        {{ __('playlist.add-torrent') }}
                    </button>
                    <dialog id="playlist-item-add" class="dialog" popover>
                        <h4 class="dialog__heading">
                            {{ __('playlist.add-to-playlist') }}
                        </h4>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('playlist_torrents.massUpsert') }}"
                        >
                            @csrf
                            @method('PUT')
                            <p class="form__group">
                                <input
                                    id="playlist_id"
                                    name="playlist_id"
                                    type="hidden"
                                    value="{{ $playlist->id }}"
                                />
                            </p>
                            <p class="form__group">
                                <textarea
                                    id="torrent_urls"
                                    class="form__textarea"
                                    name="torrent_urls"
                                    type="text"
                                    required
                                >
{{ old('torrent_urls') }}</textarea
                                >
                                <label class="form__label form__label--floating" for="torrent_urls">
                                    Torrent IDs/URLs (one per line)
                                </label>
                            </p>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('common.add') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="playlist-item-add"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                </div>
                <p class="form__group form__group--horizontal">
                    <a
                        href="{{ route('playlists.edit', ['playlist' => $playlist]) }}"
                        class="form__button form__button--filled form__button--centered"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-edit"></i>
                        {{ __('playlist.edit-playlist') }}
                    </a>
                </p>
                <form
                    action="{{ route('playlists.destroy', ['playlist' => $playlist]) }}"
                    method="POST"
                    x-data="confirmation"
                >
                    @csrf
                    @method('DELETE')
                    <p class="form__group form__group--horizontal">
                        <button
                            x-on:click.prevent="confirmAction"
                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this playlist: ' . $playlist->name . '?') }}"
                            class="form__button form__button--filled form__button--centered"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-trash"></i>
                            {{ __('common.delete') }}
                        </button>
                    </p>
                </form>
            @endif

            @if (auth()->id() !== $playlist->user_id)
                <div class="form__group form__group--horizontal">
                    <button
                        class="form__button form__button--filled form__button--centered"
                        popovertarget="playlist-suggestion-add"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-search-plus"></i>
                        {{ __('playlist.suggest-torrent') }}
                    </button>
                    <dialog id="playlist-suggestion-add" class="dialog" popover>
                        <h4 class="dialog__heading">
                            {{ __('playlist.suggest-torrent') }}
                        </h4>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('playlists.suggestions.store', ['playlist' => $playlist]) }}"
                        >
                            @csrf
                            <p class="form__group">
                                <input
                                    id="suggest_torrent_url"
                                    class="form__text"
                                    name="torrent_url"
                                    required
                                    type="text"
                                    value="{{ old('torrent_url') }}"
                                />
                                <label
                                    class="form__label form__label--floating"
                                    for="suggest_torrent_urls"
                                >
                                    Torrent ID/URL
                                </label>
                            </p>
                            <p class="form__group">
                                <textarea
                                    id="suggest_message"
                                    class="form__textarea"
                                    name="message"
                                    type="text"
                                    required
                                >
{{ old('message') }}</textarea
                                >
                                <label
                                    class="form__label form__label--floating"
                                    for="suggest_message"
                                >
                                    {{ __('common.message') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('playlist.suggest-torrent') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="playlist-suggestion-add"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                </div>
            @endif
        </div>
    </section>

    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.download') }}</h2>
        <div class="panel__body">
            <p class="form__group form__group--horizontal">
                <a
                    href="{{ route('playlist_zips.show', ['playlist' => $playlist]) }}"
                    class="form__button form__button--filled form__button--centered"
                >
                    <i class="{{ config('other.font-awesome') }} fa-download"></i>
                    {{ __('playlist.download-all') }}
                </a>
            </p>
            <p class="form__group form__group--horizontal">
                <a
                    href="{{ route('torrents.index', ['playlistId' => $playlist->id]) }}"
                    class="form__button form__button--filled form__button--centered"
                >
                    <i class="{{ config('other.font-awesome') }} fa-eye"></i>
                    Playlist torrents list
                </a>
            </p>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('common.created_at') }}</dt>
                <dd>
                    <time
                        datetime="{{ $playlist->created_at }}"
                        title="{{ $playlist->created_at }}"
                    >
                        {{ $playlist->created_at->diffForHumans() }}
                    </time>
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.updated_at') }}</dt>
                <dd>
                    <time
                        datetime="{{ $playlist->updated_at }}"
                        title="{{ $playlist->updated_at }}"
                    >
                        {{ $playlist->updated_at->diffForHumans() }}
                    </time>
                </dd>
            </div>
            @if ($latestPlaylistTorrent !== null)
                <div class="key-value__group">
                    <dt>{{ __('playlist.last-addition-at') }}</dt>
                    <dd>
                        <time
                            datetime="{{ $latestPlaylistTorrent->pivot->created_at }}"
                            title="{{ $latestPlaylistTorrent->pivot->created_at }}"
                        >
                            {{ $latestPlaylistTorrent->pivot->created_at->diffForHumans() }}
                        </time>
                    </dd>
                </div>
            @endif
        </dl>
    </section>
@endsection

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $playlist->name }}</h2>
        @php
            $tmdb_backdrop = isset($meta->backdrop)
                ? tmdb_image('back_big', $meta->backdrop)
                : url('img/backdrop-placeholder.svg');
        @endphp

        <div class="playlist__backdrop" style="background-image: url('{{ $tmdb_backdrop }}')">
            <div class="playlist__backdrop-filter">
                <a
                    class="playlist__author-link"
                    href="{{ route('users.show', ['user' => $playlist->user]) }}"
                >
                    <img
                        class="playlist__author-avatar"
                        src="{{ $playlist->user->image ? route('authenticated_images.user_avatar', ['user' => $playlist->user]) : url('img/profile.png') }}"
                        alt="{{ $playlist->user->username }}"
                    />
                </a>
                <p class="playlist__author">
                    <x-user-tag :user="$playlist->user" :anon="false" />
                </p>
                <p class="playlist__description bbcode-rendered">
                    @bbcode($playlist->description)
                </p>
            </div>
        </div>
    </section>
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('torrent.torrents') }}</h2>
            <div class="panel__actions">
                <form
                    class="panel__action"
                    action="{{ route('playlists.show', ['playlist' => $playlist]) }}"
                >
                    <div class="form__group">
                        <input
                            class="form__text"
                            type="text"
                            name="search"
                            placeholder=" "
                            value="{{ $search }}"
                        />
                        <label class="form__label form__label--floating">
                            {{ __('common.search') }}
                        </label>
                    </div>
                </form>
            </div>
        </header>
        <div class="panel__body playlist__torrents">
            @foreach ($torrents as $torrent)
                <div class="playlist__torrent-container">
                    <x-torrent.card :meta="$torrent->meta" :torrent="$torrent" />
                    @if (auth()->id() === $playlist->user_id || auth()->user()->group->is_modo)
                        <form
                            action="{{ route('playlist_torrents.destroy', ['playlistTorrent' => $torrent->pivot]) }}"
                            method="POST"
                        >
                            @csrf
                            @method('DELETE')
                            <button class="form__standard-icon-button">
                                <i class="{{ config('other.font-awesome') }} fa-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>
        {{ $torrents->links('partials.pagination') }}
    </section>
    <section class="panelV2" id="playlist_suggestions">
        <h2 class="panel__heading">Suggestions</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('torrent.torrent') }}</th>
                    <th>{{ __('common.user') }}</th>
                    <th>{{ __('common.message') }}</th>
                    <th>{{ __('common.created_at') }}</th>
                    @if (auth()->id() === $playlist->user_id || auth()->user()->group->is_modo)
                        <th>{{ __('common.actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($playlist->suggestions as $playlistSuggestion)
                    <tr>
                        <td>
                            <a
                                href="{{ route('torrents.show', ['id' => $playlistSuggestion->torrent_id]) }}"
                            >
                                {{ $playlistSuggestion->torrent->name }}
                            </a>
                        </td>
                        <td>
                            <x-user-tag :user="$playlistSuggestion->user" :anon="false" />
                        </td>
                        <td>{{ $playlistSuggestion->message }}</td>
                        <td>
                            <time
                                datetime="{{ $playlistSuggestion->created_at }}"
                                title="{{ $playlistSuggestion->created_at }}"
                            >
                                {{ $playlistSuggestion->created_at->diffForHumans() }}
                            </time>
                        </td>
                        @if (auth()->id() === $playlist->user_id || auth()->user()->group->is_modo)
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <form
                                            method="POST"
                                            action="{{ route('playlists.suggestions.update', ['playlist' => $playlist, 'playlistSuggestion' => $playlistSuggestion]) }}"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <input
                                                type="hidden"
                                                name="status"
                                                value="{{ \App\Enums\ModerationStatus::APPROVED }}"
                                            />
                                            <button class="form__button form__button--text">
                                                {{ __('common.moderation-approve') }}
                                            </button>
                                        </form>
                                    </li>
                                    <li class="data-table__action">
                                        <button
                                            class="form__button form__button--text"
                                            popovertarget="playlist-suggestion-reject-{{ $playlistSuggestion->id }}"
                                        >
                                            {{ __('common.moderation-reject') }}
                                        </button>
                                        <dialog
                                            id="playlist-suggestion-reject-{{ $playlistSuggestion->id }}"
                                            class="dialog"
                                            popover
                                        >
                                            <h3 class="dialog__heading">
                                                {{ __('common.moderation-reject') }}
                                                {{ __('torrent.torrent') }}:
                                                {{ $torrent->name }}
                                            </h3>
                                            <form
                                                class="dialog__form"
                                                method="POST"
                                                action="{{ route('playlists.suggestions.update', ['playlist' => $playlist, 'playlistSuggestion' => $playlistSuggestion]) }}"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="{{ \App\Enums\ModerationStatus::REJECTED }}"
                                                />
                                                <div class="form__group">
                                                    <textarea
                                                        id="rejection_message{{ $playlistSuggestion->id }}"
                                                        class="form__textarea"
                                                        name="rejection_message"
                                                        required
                                                    >
{{ old('rejection_message') }}</textarea
                                                    >
                                                    <label
                                                        for="rejection_message{{ $playlistSuggestion->id }}"
                                                        class="form__label form__label--floating"
                                                    >
                                                        Rejection message
                                                    </label>
                                                    <span class="form__hint">
                                                        This message is sent to the suggester
                                                    </span>
                                                </div>
                                                <p class="form__group">
                                                    <button
                                                        class="form__button form__button--filled"
                                                    >
                                                        {{ __('common.moderation-reject') }}
                                                    </button>
                                                    <button
                                                        class="form__button form__button--outlined"
                                                        type="button"
                                                        popovertarget="playlist-suggestion-reject-{{ $playlistSuggestion->id }}"
                                                    >
                                                        {{ __('common.cancel') }}
                                                    </button>
                                                </p>
                                            </form>
                                        </dialog>
                                    </li>
                                </menu>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="{{ 4 + (int) (auth()->id() === $playlist->user_id || auth()->user()->group->is_modo) }}"
                        >
                            This is where you'll approve or deny playlist suggestions. None at the
                            moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <livewire:comments :model="$playlist" />
@endsection
