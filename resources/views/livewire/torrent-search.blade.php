<div class="page__torrents torrent-search__component">
    <search class="compact-search torrent-search__filters" x-data="toggle">
        <div class="compact-search__visible-default">
            <p class="form__group">
                <input
                    id="name"
                    type="search"
                    autocomplete="off"
                    wire:model.live="name"
                    wire:island="results"
                    class="form__text"
                    placeholder=" "
                    title="{{ __('catalog.search.hint') }}"
                    aria-label="{{ __('common.search') }}: {{ __('catalog.search.hint') }}"
                    @if (auth()->user()->settings->torrent_search_autofocus)
                        autofocus
                    @endif
                />
                <label class="form__label form__label--floating" for="name">
                    {{ __('common.search') }}
                </label>
            </p>
            <button
                class="form__button form__standard-icon-button"
                type="button"
                x-on:click="toggle"
                x-bind:aria-expanded="toggleState ? 'true' : 'false'"
                aria-controls="torrent-search-advanced-filters"
                aria-label="{{ __('catalog.search.toggle-filters') }}"
                title="{{ __('catalog.search.toggle-filters') }}"
            >
                <i class="{{ config('other.font-awesome') }} fa-sliders"></i>
            </button>
        </div>
        <form
            id="torrent-search-advanced-filters"
            class="form"
            x-on:submit.prevent
            x-cloak
            x-show="isToggledOn"
        >
            @php
                // Locally-derived upload kinds for the currently selected
                // categories (see App\Helpers\UploadKinds::categoryKind).
                // With no explicit category every kind is applicable, so
                // every kind-specific group below is shown (and left open);
                // an explicit selection narrows both which groups are
                // rendered and which release types are offered, and the
                // Livewire component clears now-inapplicable kind-specific
                // filters again on categoryIds changes (see
                // TorrentSearch::updatedCategoryIds()).
                $noCategorySelected = $categoryIds === [];
                $selectedKinds = $categories
                    ->whereIn('id', $categoryIds)
                    ->map(fn ($category) => \App\Helpers\UploadKinds::categoryKind($category))
                    ->unique()
                    ->values();
                $showVideoResolutionFilter = $noCategorySelected || $selectedKinds->intersect(['movie', 'tv', 'xxx'])->isNotEmpty();
                $showMovieTvFilters = $noCategorySelected || $selectedKinds->intersect(['movie', 'tv'])->isNotEmpty();
                $showMovieOnlyFilters = $noCategorySelected || $selectedKinds->contains('movie');
                $showTvFilters = $noCategorySelected || $selectedKinds->contains('tv');
                $showMusicFilters = $noCategorySelected || $selectedKinds->contains('music');
                $showGameFilters = $noCategorySelected || $selectedKinds->contains('game');
                $showBookFilters = $noCategorySelected || $selectedKinds->contains('book');
            @endphp
            <div class="form__group--short-horizontal">
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.category') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($categories as $category)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $category->id }}"
                                            wire:model.live="categoryIds"
                                        />
                                        {{ $category->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.type') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($types as $type)
                                @continue(
                                    ! $noCategorySelected
                                    && $selectedKinds->intersect(\App\Helpers\UploadKinds::typeKinds($type->name))->isEmpty()
                                )
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $type->id }}"
                                            wire:model.live="typeIds"
                                        />
                                        {{ $type->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="form__group--short-horizontal">
                <p class="form__group">
                    <input
                        id="description"
                        wire:model.live="description"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="description">
                        {{ __('torrent.description') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="mediainfo"
                        wire:model.live="mediainfo"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="mediainfo">
                        {{ __('torrent.media-info') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="keywords"
                        wire:model.live="keywords"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="keywords">
                        {{ __('torrent.keywords') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="uploader"
                        wire:model.live="uploader"
                        class="form__text"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="uploader">
                        {{ __('torrent.uploader') }}
                    </label>
                </p>
            </div>
            <div class="form__group--short-horizontal">
                <div class="form__group--short-horizontal">
                    <p class="form__group">
                        <input
                            id="minSize"
                            wire:model.live="minSize"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="minSize">
                            {{ __('livewire-interface.minimum-size') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="minSizeMultiplier"
                            wire:model.live="minSizeMultiplier"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="1" selected>{{ __('livewire-interface.bytes') }}</option>
                            <option value="1000">KB</option>
                            <option value="1024">KiB</option>
                            <option value="1000000">MB</option>
                            <option value="1048576">MiB</option>
                            <option value="1000000000">GB</option>
                            <option value="1073741824">GiB</option>
                            <option value="1000000000000">TB</option>
                            <option value="1099511627776">TiB</option>
                        </select>
                        <label class="form__label form__label--floating" for="minSizeMultiplier">
                            {{ __('livewire-interface.unit') }}
                        </label>
                    </p>
                </div>
                <div class="form__group--short-horizontal">
                    <p class="form__group">
                        <input
                            id="maxSize"
                            wire:model.live="maxSize"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="maxSize">
                            {{ __('livewire-interface.maximum-size') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="maxSizeMultiplier"
                            wire:model.live="maxSizeMultiplier"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="1" selected>{{ __('livewire-interface.bytes') }}</option>
                            <option value="1000">KB</option>
                            <option value="1024">KiB</option>
                            <option value="1000000">MB</option>
                            <option value="1048576">MiB</option>
                            <option value="1000000000">GB</option>
                            <option value="1073741824">GiB</option>
                            <option value="1000000000000">TB</option>
                            <option value="1099511627776">TiB</option>
                        </select>
                        <label class="form__label form__label--floating" for="maxSizeMultiplier">
                            {{ __('livewire-interface.unit') }}
                        </label>
                    </p>
                </div>
                <p class="form__group">
                    <input
                        id="playlistId"
                        wire:model.live="playlistId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="playlistId">
                        {{ __('livewire-interface.playlist-id') }}
                    </label>
                </p>
            </div>
            @if ($showMovieTvFilters)
            <div class="form__group--short-horizontal">
                <p class="form__group">
                    <input
                        id="tmdbId"
                        wire:model.live="tmdbId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="tmdbId">TMDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="imdbId"
                        wire:model.live="imdbId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]+|tt0*\d{7,}"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="imdbId">IMDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="malId"
                        wire:model.live="malId"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="malId">MAL ID</label>
                </p>
            </div>
            @endif
            <div class="form__group--short-horizontal">
                @if ($showMovieTvFilters)
                    <div class="form__group">
                        <div id="regions" wire:ignore></div>
                    </div>
                    <div class="form__group">
                        <div id="distributors" wire:ignore></div>
                    </div>
                @endif
                @if ($showVideoResolutionFilter)
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.resolution') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($resolutions as $resolution)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                class="form__checkbox"
                                                type="checkbox"
                                                value="{{ $resolution->id }}"
                                                wire:model.live="resolutionIds"
                                            />
                                            {{ $resolution->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                @endif
            </div>
            @if ($showMovieTvFilters)
            <details class="form__group" wire:ignore.self open>
                <summary class="form__legend">{{ __('catalog.groups.movies-tv') }}</summary>
                <div class="form__group--short-horizontal">
                    <p class="form__group" x-data="{ startYear: @entangle('startYear') }">
                        <input
                            id="startYear"
                            x-on:input.debounce.150ms="
                                if ($el.checkValidity()) {
                                    $wire.set('startYear', $event.target.value);
                                }
                            "
                            x-model.live="startYear"
                            class="form__text"
                            inputmode="numeric"
                            minlength="4"
                            pattern="[0-9]{4}"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="startYear">
                            {{ __('torrent.start-year') }}
                        </label>
                    </p>
                    <p class="form__group" x-data="{ endYear: @entangle('endYear') }">
                        <input
                            id="endYear"
                            x-on:input.debounce.150ms="
                                if ($el.checkValidity()) {
                                    $wire.set('endYear', $event.target.value);
                                }
                            "
                            x-model.live="endYear"
                            class="form__text"
                            inputmode="numeric"
                            minlength="4"
                            pattern="[0-9]{4}"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="endYear">
                            {{ __('torrent.end-year') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select id="adult" wire:model.live="adult" class="form__select" placeholder=" ">
                            <option value="any" selected>{{ __('livewire-interface.any') }}</option>
                            <option value="include">{{ __('livewire-interface.include') }}</option>
                            <option value="exclude">{{ __('livewire-interface.exclude') }}</option>
                        </select>
                        <label class="form__label form__label--floating" for="adult">
                            {{ __('livewire-interface.adult') }}
                        </label>
                    </p>
                </div>
                <div class="form__group--short-horizontal">
                    @if ($showMovieOnlyFilters)
                        <p class="form__group">
                            <input
                                id="collectionId"
                                wire:model.live="collectionId"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="collectionId">
                                {{ __('livewire-interface.collection-id') }}
                            </label>
                        </p>
                    @endif
                    <p class="form__group">
                        <input
                            id="companyId"
                            wire:model.live="companyId"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="companyId">
                            {{ __('livewire-interface.company-id') }}
                        </label>
                    </p>
                </div>
                @if ($showTvFilters)
                    <details class="form__group" wire:ignore.self open>
                        <summary class="form__legend">{{ __('catalog.groups.movies-tv-episode') }}</summary>
                        <div class="form__group--short-horizontal">
                            <p class="form__group">
                                <input
                                    id="seasonNumber"
                                    wire:model.live="seasonNumber"
                                    class="form__text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                />
                                <label class="form__label form__label--floating" for="seasonNumber">
                                    {{ __('torrent.season-number') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <input
                                    id="episodeNumber"
                                    wire:model.live="episodeNumber"
                                    class="form__text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                />
                                <label class="form__label form__label--floating" for="episodeNumber">
                                    {{ __('torrent.episode-number') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <input
                                    id="networkId"
                                    wire:model.live="networkId"
                                    class="form__text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                />
                                <label class="form__label form__label--floating" for="networkId">
                                    {{ __('livewire-interface.network-id') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <input
                                    id="tvdbId"
                                    wire:model.live="tvdbId"
                                    class="form__text"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                />
                                <label class="form__label form__label--floating" for="tvdbId">TVDb ID</label>
                            </p>
                        </div>
                    </details>
                @endif
                <div class="form__group--short-horizontal">
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.genre') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($genres as $genre)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                class="form__checkbox"
                                                type="checkbox"
                                                value="{{ $genre->id }}"
                                                wire:model.live="genreIds"
                                            />
                                            {{ $genre->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">
                                {{ __('livewire-interface.primary-language') }}
                            </legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($primaryLanguages as $primaryLanguage)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                class="form__checkbox"
                                                type="checkbox"
                                                value="{{ $primaryLanguage }}"
                                                wire:model.live="primaryLanguageNames"
                                            />
                                            {{ $primaryLanguage }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                </div>
            </details>
            @endif
            @if ($showMusicFilters)
                <details class="form__group" wire:ignore.self open>
                    <summary class="form__legend">{{ __('catalog.groups.music') }}</summary>
                    <div class="form__group--short-horizontal">
                        <p class="form__group">
                            <input
                                id="musicArtist"
                                wire:model.live="musicArtist"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="musicArtist">
                                {{ __('catalog.filters.music-artist') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                id="musicLabel"
                                wire:model.live="musicLabel"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="musicLabel">
                                {{ __('catalog.filters.music-label') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                id="musicFormat"
                                wire:model.live="musicFormat"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="musicFormat">
                                {{ __('catalog.filters.music-format') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                id="musicYear"
                                wire:model.live="musicYear"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="musicYear">
                                {{ __('catalog.filters.music-year') }}
                            </label>
                        </p>
                    </div>
                </details>
            @endif
            @if ($showGameFilters)
                <details class="form__group" wire:ignore.self open>
                    <summary class="form__legend">{{ __('catalog.groups.games') }}</summary>
                    <div class="form__group--short-horizontal">
                        <p class="form__group">
                            <input
                                id="gamePlatform"
                                wire:model.live="gamePlatform"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="gamePlatform">
                                {{ __('catalog.filters.game-platform') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                id="gameGenre"
                                wire:model.live="gameGenre"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="gameGenre">
                                {{ __('catalog.filters.game-genre') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                id="gameDeveloper"
                                wire:model.live="gameDeveloper"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="gameDeveloper">
                                {{ __('catalog.filters.game-developer') }}
                            </label>
                        </p>
                    </div>
                </details>
            @endif
            @if ($showBookFilters)
                <details class="form__group" wire:ignore.self open>
                    <summary class="form__legend">{{ __('catalog.groups.books') }}</summary>
                    <div class="form__group--short-horizontal">
                        <p class="form__group">
                            <input
                                id="bookAuthor"
                                wire:model.live="bookAuthor"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="bookAuthor">
                                {{ __('catalog.filters.book-author') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                id="bookLanguage"
                                wire:model.live="bookLanguage"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="bookLanguage">
                                {{ __('catalog.filters.book-language') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                id="bookPublisher"
                                wire:model.live="bookPublisher"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="bookPublisher">
                                {{ __('catalog.filters.book-publisher') }}
                            </label>
                        </p>
                    </div>
                </details>
            @endif
            <div class="form__group--short-horizontal">
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('livewire-interface.buff') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="0"
                                        wire:model.live="free"
                                    />
                                    {{ __('livewire-interface.percent-freeleech', ['percent' => 0]) }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="25"
                                        wire:model.live="free"
                                    />
                                    {{ __('livewire-interface.percent-freeleech', ['percent' => 25]) }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="50"
                                        wire:model.live="free"
                                    />
                                    {{ __('livewire-interface.percent-freeleech', ['percent' => 50]) }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="75"
                                        wire:model.live="free"
                                    />
                                    {{ __('livewire-interface.percent-freeleech', ['percent' => 75]) }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="100"
                                        wire:model.live="free"
                                    />
                                    {{ __('livewire-interface.percent-freeleech', ['percent' => 100]) }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="doubleup"
                                    />
                                    {{ __('torrent.double-upload') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="featured"
                                    />
                                    {{ __('torrent.featured') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="refundable"
                                    />
                                    {{ __('torrent.refundable') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('livewire-interface.tags') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="internal"
                                    />
                                    {{ __('torrent.internal') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="personalRelease"
                                    />
                                    {{ __('torrent.personal-release') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="trumpable"
                                    />
                                    {{ __('livewire-interface.trumpable') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="highspeed"
                                    />
                                    {{ __('common.high-speeds') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="bookmarked"
                                    />
                                    {{ __('common.bookmarked') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="wished"
                                    />
                                    {{ __('common.wished') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.health') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="alive"
                                    />
                                    {{ __('torrent.alive') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="dying"
                                    />
                                    {{ __('livewire-interface.dying') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="dead"
                                    />
                                    {{ __('livewire-interface.dead') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="graveyard"
                                    />
                                    {{ __('graveyard.graveyard') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.history') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="notDownloaded"
                                    />
                                    {{ __('torrent.have-not-downloaded') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="downloaded"
                                    />
                                    {{ __('torrent.have-downloaded') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="seeding"
                                    />
                                    {{ __('torrent.seeding') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="leeching"
                                    />
                                    {{ __('torrent.leeching') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="incomplete"
                                    />
                                    {{ __('livewire-interface.incomplete') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
            </div>
        </form>
    </search>
    {{--
        The whole results panel is one named, always-rendering island (see
        resources/views/livewire/torrent-search.blade.php): name/sort/
        pagination/layout/per-page controls below target it via wire:island
        so those updates only morph this panel, never re-sending the filter
        form above; a category change instead re-renders the whole component
        (unscoped), which 'always: true' still keeps this panel in sync with.
        Islands can't read this view's local variables (see Livewire 4
        islands docs), so everything inside uses $this->torrents /
        $this->torrentHealth / $this->personalFreeleech rather than the
        $torrents / $torrentHealth / $personalFreeleech used elsewhere.

        The seeders/leechers live-stats poll is deliberately NOT an island
        update: TorrentSearch::refreshDisplayedStats() is #[Renderless] and
        only dispatches a 'torrent-stats-refreshed' browser event with fresh
        per-torrent counts/flags and health totals (see the listener below),
        which patches the existing seeder/leecher/completed cells and the
        header counters in place without re-rendering any HTML at all — not
        even this island's. Livewire pauses polling in background tabs.
    --}}
    <section
        class="panelV2 torrent-search__results"
        wire:poll.15s.visible="refreshDisplayedStats"
    >
    @island(name: 'results', always: true)
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('torrent.torrents') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <span class="panel__action-text">
                        {{ __('common.total') }}: <span data-torrent-health="total">{{ $this->torrentHealth->total }}</span> |
                        {{ __('common.alive') }}: <span data-torrent-health="alive">{{ $this->torrentHealth->alive }}</span> |
                        {{ __('common.dead') }}: <span data-torrent-health="dead">{{ $this->torrentHealth->dead }}</span>
                    </span>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <select
                            id="view"
                            class="form__select"
                            wire:model.live="view"
                            wire:island="results"
                            required
                        >
                            <option value="list">{{ __('torrent.list') }}</option>
                            <option value="card">{{ __('torrent.cards') }}</option>
                            <option value="group">{{ __('torrent.groupings') }}</option>
                            <option value="poster">{{ __('torrent.poster') }}</option>
                        </select>
                        <label class="form__label form__label--floating" for="view">
                            {{ __('livewire-interface.layout') }}
                        </label>
                    </div>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <select
                            id="perPage"
                            class="form__select"
                            wire:model.live="perPage"
                            wire:island="results"
                            required
                        >
                            @if (\in_array($view, ['card', 'poster']))
                                <option>24</option>
                                <option>48</option>
                                <option>72</option>
                                <option>96</option>
                            @else
                                <option>25</option>
                                <option>50</option>
                                <option>75</option>
                                <option>100</option>
                            @endif
                        </select>
                        <label class="form__label form__label--floating" for="perPage">
                            {{ __('common.quantity') }}
                        </label>
                    </div>
                </div>
            </div>
        </header>
        {{ $this->torrents->links('partials.pagination', ['island' => 'results']) }}

        {{--
            Every layout below renders one unique catalogue Work per title
            (see CONTEXT.md: Work -> Edition -> Variant -> Torrent): the same
            'Ano, šéfe!' example never shows as several duplicate entries
            differing only by quality. $this->torrents is a paginated list of
            App\Models\MediaWork (see TorrentSearch::workGroups() /
            workPosters()), each carrying its own bounded, independently
            downloadable variant rows. Only 'bumped_at' (sticky order),
            'created_at' and 'times_completed' are valid sort fields for
            grouped Works (aggregates across every variant), so the other
            per-torrent sort headers (name/rating/size/seeders/leechers) are
            not offered here.
        --}}
        @switch(true)
            @case($view === 'list')
            @case($view === 'card')
            @case($view === 'group')
                <table class="data-table">
                    <thead>
                        <tr>
                            <th
                                class="torrent-search--list__completed-header"
                                wire:click="sortBy('times_completed')"
                                wire:island="results"
                                role="columnheader button"
                                title="{{ __('torrent.completed') }}"
                            >
                                <i class="fas fa-check-circle"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'times_completed'])
                            </th>
                            <th
                                class="torrent-search--list__age-header"
                                wire:click="sortBy('created_at')"
                                wire:island="results"
                                role="columnheader button"
                            >
                                {{ __('common.created_at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                        </tr>
                    </thead>
                </table>
                <div class="panel__body torrent-search--grouped__results">
                    @forelse ($this->torrents as $work)
                        @continue($work === null)

                        <x-media-work.card :work="$work" :personalFreeleech="$this->personalFreeleech" />
                    @empty
                        {{ __('common.no-result') }}
                    @endforelse
                </div>

                @break
            @case($view === 'poster')
                <table class="data-table">
                    <thead>
                        <tr>
                            <th
                                class="torrent-search--list__completed-header"
                                wire:click="sortBy('times_completed')"
                                wire:island="results"
                                role="columnheader button"
                                title="{{ __('torrent.completed') }}"
                            >
                                <i class="fas fa-check-circle"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'times_completed'])
                            </th>
                            <th
                                class="torrent-search--list__age-header"
                                wire:click="sortBy('created_at')"
                                wire:island="results"
                                role="columnheader button"
                            >
                                {{ __('common.created_at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                        </tr>
                    </thead>
                </table>
                <div class="panel__body torrent-search--poster__results">
                    @forelse ($this->torrents as $work)
                        @continue($work === null)

                        <x-media-work.poster :work="$work" />
                    @empty
                        {{ __('common.no-result') }}
                    @endforelse
                </div>

                @break
        @endswitch
        {{ $this->torrents->links('partials.pagination', ['island' => 'results']) }}
    @endisland
    </section>
    <script src="{{ asset('build/unit3d/virtual-select.js') }}" crossorigin="anonymous"></script>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        (function () {
            // The #regions/#distributors mounts only exist while the
            // movies/TV group is shown (see $showVideoFilters); a category
            // change can remove and later re-add them via a Livewire morph,
            // so initialization must be re-attempted after every morph
            // (not just on the one-time livewire:init boot) and must be
            // idempotent to avoid double VirtualSelect instances/listeners
            // on elements that survived a morph untouched (they carry
            // wire:ignore).
            function initRegionsSelect() {
                let regions = document.querySelector('#regions');

                if (!regions || regions.dataset.virtualSelectInitialized === 'true') {
                    return;
                }

                regions.dataset.virtualSelectInitialized = 'true';

                let myRegions = [
                    {
                        label: @js(__('livewire-interface.no-region')), value: "0"
                    },
                    ... {{
                        Js::from(
                            $regions
                                ->each(function ($region) {
                                    $region->label = $region->name . ' (' . __('regions.' . $region->name) . ')';
                                    $region->value = $region->id;
                                })
                                ->select(['label', 'value'])
                        )
                    }}
                ];

                VirtualSelect.init({
                    ele: regions,
                    options: myRegions,
                    selectedValue: @this.get('regionIds'),
                    multiple: true,
                    search: true,
                    placeholder: @js(__('livewire-interface.select-regions')),
                    noOptionsText: @js(__('livewire-interface.no-results-found')),
                })

                regions.addEventListener('change', () => {
                    let data = regions.value
                    @this.set('regionIds', data)
                })
            }

            function initDistributorsSelect() {
                let distributors = document.querySelector('#distributors');

                if (!distributors || distributors.dataset.virtualSelectInitialized === 'true') {
                    return;
                }

                distributors.dataset.virtualSelectInitialized = 'true';

                let myDistributors = [
                    {
                        label: @js(__('livewire-interface.no-distributor')), value: "0"
                    },
                    ... {{
                        Js::from(
                            $distributors
                                ->each(function ($distributor) {
                                    $distributor->label = $distributor->name;
                                    $distributor->value = $distributor->id;
                                })
                                ->select(['label', 'value'])
                        )
                    }}
                ];

                VirtualSelect.init({
                    ele: distributors,
                    options: myDistributors,
                    selectedValue: @this.get('distributorIds'),
                    multiple: true,
                    search: true,
                    placeholder: @js(__('livewire-interface.select-distributor')),
                    noOptionsText: @js(__('livewire-interface.no-results-found')),
                })

                distributors.addEventListener('change', () => {
                    let data = distributors.value
                    @this.set('distributorIds', data)
                })
            }

            function initSelectors() {
                initRegionsSelect();
                initDistributorsSelect();
            }

            // Patches the results panel's live seeder/leecher/completed cells
            // and the header total/alive/dead counters in place from the
            // 'torrent-stats-refreshed' event dispatched by
            // TorrentSearch::refreshDisplayedStats() (see its PHP doc block):
            // no HTML is re-rendered for this, so names/cards/pagination are
            // left completely untouched by the periodic stats poll.
            const torrentStatTitles = {
                seeding: @js(__('torrent.currently-seeding')),
                leeching: @js(__('torrent.currently-leeching')),
                completed: @js(__('torrent.completed')),
            };

            function patchTorrentStatCell(root, torrentId, stat, activityClass, active, text, title) {
                let cell = root.querySelector(
                    '[data-torrent-id="' + torrentId + '"][data-torrent-stat="' + stat + '"]'
                );

                if (!cell) {
                    return;
                }

                let countLink = cell.querySelector('a');

                if (countLink) {
                    countLink.textContent = text;
                }

                cell.classList.toggle(activityClass, active);

                if (active) {
                    cell.setAttribute('title', title);
                } else {
                    cell.removeAttribute('title');
                }
            }

            function applyTorrentStats({ torrents, health }) {
                let root = document.querySelector('.torrent-search__results');

                if (!root) {
                    return;
                }

                if (health) {
                    ['total', 'alive', 'dead'].forEach((key) => {
                        if (!(key in health)) {
                            return;
                        }

                        let counter = root.querySelector('[data-torrent-health="' + key + '"]');

                        if (counter) {
                            counter.textContent = health[key];
                        }
                    });
                }

                (torrents || []).forEach((torrent) => {
                    patchTorrentStatCell(
                        root, torrent.id, 'seeding', 'torrent-activity-indicator--seeding',
                        torrent.seeding, torrent.seeders, torrentStatTitles.seeding,
                    );
                    patchTorrentStatCell(
                        root, torrent.id, 'leeching', 'torrent-activity-indicator--leeching',
                        torrent.leeching, torrent.leechers, torrentStatTitles.leeching,
                    );
                    patchTorrentStatCell(
                        root, torrent.id, 'completed', 'torrent-activity-indicator--completed',
                        torrent.userCompleted, torrent.timesCompleted, torrentStatTitles.completed,
                    );
                });
            }

            document.addEventListener('livewire:init', () => {
                Livewire.hook('component.initialized', ({ component }) => {
                    if (component.el.classList.contains('torrent-search__component')) initSelectors();
                });
                Livewire.hook('morphed', ({ component }) => {
                    if (component.el.classList.contains('torrent-search__component')) initSelectors();
                });
                Livewire.on('torrent-stats-refreshed', applyTorrentStats);
            });
        })();
    </script>
</div>
