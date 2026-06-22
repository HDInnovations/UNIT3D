<div
    class="quick-search"
    x-data="{ ...quickSearchKeyboardNavigation() }"
    x-on:keydown.escape.window="$refs.quickSearch.blur();"
>
    <div class="quick-search__inputs">
        <div class="quick-search__radios">
            <label class="quick-search__radio-label">
                <input
                    type="radio"
                    class="quick-search__radio"
                    name="quicksearchRadio"
                    value="movies"
                    wire:model.live.debounce.0="quicksearchRadio"
                    x-on:click="$nextTick(() => $refs.quickSearch.focus());"
                />
                <i
                    class="quick-search__radio-icon {{ \config('other.font-awesome') }} fa-camera-movie"
                    title="{{ __('mediahub.movies') }}"
                ></i>
            </label>
            <label class="quick-search__radio-label">
                <input
                    type="radio"
                    class="quick-search__radio"
                    name="quicksearchRadio"
                    value="series"
                    wire:model.live.debounce.0="quicksearchRadio"
                    x-on:click="$nextTick(() => $refs.quickSearch.focus());"
                />
                <i
                    class="quick-search__radio-icon {{ \config('other.font-awesome') }} fa-tv-retro"
                    title="{{ __('mediahub.shows') }}"
                ></i>
            </label>
            <label class="quick-search__radio-label">
                <input
                    type="radio"
                    class="quick-search__radio"
                    name="quicksearchRadio"
                    value="persons"
                    wire:model.live.debounce.0="quicksearchRadio"
                    x-on:click="$nextTick(() => $refs.quickSearch.focus());"
                />
                <i
                    class="quick-search__radio-icon {{ \config('other.font-awesome') }} fa-user"
                    title="{{ __('mediahub.persons') }}"
                ></i>
            </label>
        </div>
        <input
            class="quick-search__input"
            wire:model.live.debounce.100ms="quicksearchText"
            type="text"
            placeholder="{{ $quicksearchRadio }}"
            x-ref="quickSearch"
            x-on:keydown.down.prevent="$refs.searchResults.firstElementChild?.firstElementChild?.focus()"
            x-on:keydown.up.prevent="$refs.searchResults.lastElementChild?.firstElementChild?.focus()"
        />
        @if (strlen($quicksearchText) > 0)
            <div class="quick-search__results" x-ref="searchResults">
                @forelse ($search_results['hits'] ?? $search_results as $search_result)
                    <article
                        class="quick-search__result"
                        x-on:keydown.down.prevent="quickSearchArrowDown($el)"
                        x-on:keydown.up.prevent="quickSearchArrowUp($el)"
                    >
                        @switch($quicksearchRadio)
                            @case('movies')
                                <a
                                    class="quick-search__result-link"
                                    href="{{ route('torrents.similar', ['category_id' => $search_result['category']['id'], 'tmdb' => $search_result['tmdb']]) }}"
                                >
                                    <img
                                        class="quick-search__image"
                                        src="{{ isset($search_result['movie']['poster']) ? \tmdb_image('poster_small', $search_result['movie']['poster']) : 'https://via.placeholder.com/90x135' }}"
                                        alt=""
                                    />
                                    <h2 class="quick-search__result-text">
                                        {{ $search_result['movie']['name'] }}
                                        <time
                                            class="quick-search__result-year"
                                            datetime="{{ $search_result['movie']['year'] }}"
                                            title="{{ $search_result['movie']['year'] }}"
                                        >
                                            {{ substr($search_result['movie']['year'], 0, 4) }}
                                        </time>
                                    </h2>
                                </a>

                                @break
                            @case('series')
                                <a
                                    class="quick-search__result-link"
                                    href="{{ route('torrents.similar', ['category_id' => $search_result['category']['id'], 'tmdb' => $search_result['tmdb']]) }}"
                                >
                                    <img
                                        class="quick-search__image"
                                        src="{{ isset($search_result['tv']['poster']) ? \tmdb_image('poster_small', $search_result['tv']['poster']) : 'https://via.placeholder.com/90x135' }}"
                                        alt=""
                                    />
                                    <h2 class="quick-search__result-text">
                                        {{ $search_result['tv']['name'] }}
                                        <time
                                            class="quick-search__result-year"
                                            datetime="{{ $search_result['tv']['year'] }}"
                                            title="{{ $search_result['tv']['year'] }}"
                                        >
                                            {{ $search_result['tv']['year'] }}
                                        </time>
                                    </h2>
                                </a>

                                @break
                            @case('persons')
                                <a
                                    class="quick-search__result-link"
                                    href="{{ route('mediahub.persons.show', ['id' => $search_result['id']]) }}"
                                >
                                    <img
                                        class="quick-search__image"
                                        src="{{ isset($search_result['still']) ? \tmdb_image('poster_small', $search_result['still']) : 'https://via.placeholder.com/90x135' }}"
                                        alt=""
                                    />
                                    <h2 class="quick-search__result-text">
                                        {{ $search_result['name'] }}
                                    </h2>
                                </a>

                                @break
                        @endswitch
                    </article>
                @empty
                    <article class="quick-search__result--empty">
                        <p class="quick-search__result-text">No results found</p>
                    </article>
                @endforelse
            </div>
        @else
            <div class="quick-search__results">
                <article class="quick-search__result--keep-typing">
                    <p class="quick-search__result-text">Keep typing to get results</p>
                </article>
            </div>
        @endif
    </div>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        function quickSearchKeyboardNavigation() {
            return {
                quickSearchArrowDown(el) {
                    if (el.nextElementSibling == null) {
                        el.parentNode?.firstElementChild?.firstElementChild?.focus();
                    } else {
                        el.nextElementSibling?.firstElementChild?.focus();
                    }
                },
                quickSearchArrowUp(el) {
                    if (el.previousElementSibling == null) {
                        document
                            .querySelector(`.quick-search__input:not([style='display: none;'])`)
                            ?.focus();
                    } else {
                        el.previousElementSibling?.firstElementChild?.focus();
                    }
                },
            };
        }
    </script>
</div>
