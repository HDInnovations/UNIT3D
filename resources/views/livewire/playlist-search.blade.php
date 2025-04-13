<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('playlist.playlists') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        placeholder=" "
                        type="search"
                        autocomplete="off"
                        wire:model.live.debounce.250ms="name"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('torrent.search-by-name') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="username"
                        class="form__text"
                        placeholder=" "
                        type="search"
                        autocomplete="off"
                        wire:model.live.debounce.250ms="username"
                    />
                    <label class="form__label form__label--floating" for="username">
                        {{ __('common.username') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <select
                        id="playlist_category_id"
                        class="form__select"
                        wire:model.live.debounce.250ms="playlistCategoryId"
                        required
                    >
                        <option selected value="__any">Any</option>
                        @foreach ($playlistCategories as $playlistCategory)
                            <option class="form__option" value="{{ $playlistCategory->id }}">
                                {{ $playlistCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="playlist_category_id">
                        {{ __('torrent.category') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <a class="form__button form__button--text" href="{{ route('playlists.create') }}">
                    {{ __('common.add') }}
                </a>
            </div>
        </div>
    </header>
    <div class="panel__body playlists">
        @forelse ($playlists as $playlist)
            <x-playlist.card :$playlist />
        @empty
            {{ __('playlist.about') }}
        @endforelse
    </div>
    {{ $playlists->links('partials.pagination') }}
</section>
