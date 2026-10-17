<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('mediahub.collections') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        placeholder=" "
                        type="search"
                        autocomplete="off"
                        wire:model.live.debounce.250ms="search"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('torrent.search-by-name') }}
                    </label>
                </div>
            </div>
        </div>
    </header>
    {{ $collections->links('partials.pagination') }}
    <div class="panel__body">
        <ul class="collection__list">
            @foreach ($collections as $collection)
                <li class="collection__list-item">
                    <article class="collection">
                        <h3 class="collection__heading">
                            <a
                                class="collection__link"
                                href="{{ route('mediahub.collections.show', ['id' => $collection->id]) }}"
                            >
                                {{ $collection->name }}
                            </a>
                        </h3>
                        <div class="collection__posters">
                            @foreach ($collection->movies as $movie)
                                <x-movie.poster
                                    :$movie
                                    :categoryId="$movie->torrents_min_category_id"
                                />
                            @endforeach
                        </div>
                    </article>
                </li>
            @endforeach
        </ul>
    </div>
    {{ $collections->links('partials.pagination') }}
</section>
