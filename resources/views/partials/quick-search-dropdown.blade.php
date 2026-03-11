<div class="quick-search" x-data="quickSearch" x-on:keydown.escape.window="clearSearch()">
    <div class="quick-search__inputs">
        <input
            class="quick-search__input"
            type="text"
            placeholder="Search"
            x-model="searchText"
            x-on:input.debounce="performSearch"
            x-ref="quickSearch"
            x-on:keydown.down.prevent="focusFirstResult"
            x-on:keydown.up.prevent="focusLastResult"
            x-on:focus="searchPerformed = true"
        />
        <template x-if="searchResults === null">
            <div class="quick-search__results">
                <article class="quick-search__result--default">
                    <p class="quick-search__result-text">Search movies, tv series, or people</p>
                </article>
            </div>
        </template>
        <template x-if="showEmptyResults">
            <div class="quick-search__results">
                <article class="quick-search__result--empty">
                    <p class="quick-search__result-text">No results found</p>
                </article>
            </div>
        </template>
        <template x-if="showNonEmptyResults">
            <div class="quick-search__results" x-ref="searchResults">
                <template x-for="result in searchResults" :key="result.id">
                    <article
                        class="quick-search__result"
                        x-on:keydown.down.prevent="focusNextResult"
                        x-on:keydown.up.prevent="focusPreviousResult"
                    >
                        <a class="quick-search__result-link" :href="result.url">
                            <img class="quick-search__image" :src="getSrc(result.image)" alt="" />
                            <h2 class="quick-search__result-text">
                                <span
                                    class="quick-search__result-name"
                                    x-text="result.name"
                                ></span>
                                <span x-show="result.type != 'Person'">
                                    <time
                                        class="quick-search__result-year"
                                        x-text="result.year"
                                    ></time>
                                    &bull;
                                    <span
                                        class="quick-search__result-type"
                                        x-text="result.type"
                                    ></span>
                                </span>
                            </h2>
                        </a>
                    </article>
                </template>
            </div>
        </template>
    </div>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('quickSearch', () => ({
                searchText: '',
                searchResults: null,
                searchPerformed: false,
                performSearch() {
                    this.searchPerformed = true;
                    if (this.searchText.length === 0) {
                        this.searchResults = null;
                        return;
                    }

                    fetch(`/api/quicksearch?query=${encodeURIComponent(this.searchText)}`)
                        .then((response) => response.json())
                        .then((data) => {
                            this.searchResults = data.results.map((result) => {
                                return result;
                            });
                        });
                },
                get showEmptyResults() {
                    return Array.isArray(this.searchResults) && this.searchResults.length === 0;
                },
                get showNonEmptyResults() {
                    return Array.isArray(this.searchResults) && this.searchResults.length > 0;
                },
                clearSearch() {
                    this.searchText = '';
                    this.searchResults = [];
                    this.searchPerformed = false;
                },
                focusFirstResult() {
                    document.querySelector('[x-ref="searchResults"]').querySelector('a').focus();
                },
                focusLastResult() {
                    document
                        .querySelector('[x-ref="searchResults"]')
                        .querySelector('article:last-child > a')
                        .focus();
                },
                focusNextResult() {
                    const el = this.$el;
                    if (el.nextElementSibling === null) {
                        el.parentNode?.firstElementChild?.nextElementSibling?.firstElementChild?.focus();
                    } else {
                        el.nextElementSibling?.firstElementChild?.focus();
                    }
                },
                focusPreviousResult() {
                    const el = this.$el;
                    if (el.previousElementSibling.tagName === 'TEMPLATE') {
                        this.$refs.quickSearch.focus();
                    } else {
                        el.previousElementSibling?.firstElementChild?.focus();
                    }
                },
                getSrc(image) {
                    return image.length > 2
                        ? image
                        : 'data:image/svg+xml;charset=utf-8,' +
                              `<svg viewBox='0 0 40 60' width='40' height='60' xmlns='http://www.w3.org/2000/svg'>
                                <defs>
                                    <linearGradient id='grad' gradientTransform='rotate(90)'>
                                        <stop offset='0%' stop-color='%23a5abb8' />
                                        <stop offset='100%' stop-color='%23848993' />
                                    </linearGradient>
                                </defs>
                                <rect width='40' height='60' fill='url(%23grad)'></rect>
                                <text
                                    x='50%'
                                    y='50%'
                                    dominant-baseline='central'
                                    text-anchor='middle'
                                    fill='white'
                                    font-family='Arial'
                                    font-size='16px'
                                >
                                    ${image}
                                </text>
                            </svg>`;
                },
            }));
        });
    </script>
</div>
