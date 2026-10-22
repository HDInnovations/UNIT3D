@props([
    'movie',
    'categoryId',
    'tmdb',
])

<article class="torrent-search--poster__result">
    <figure>
        <a
            href="{{ route('torrents.similar', ['category_id' => $categoryId, 'tmdb' => $movie?->id ?? $tmdb]) }}"
            class="torrent-search--poster__poster"
        >
            <img
                src="{{ isset($movie->poster) ? tmdb_image('poster_mid', $movie->poster) : url('img/poster-placeholder.svg') }}"
                alt="{{ __('torrent.similar') }}"
                loading="lazy"
            />
        </a>
        <figcaption class="torrent-search--poster__caption">
            <h2 class="torrent-search--poster__title">
                {{ $movie->title ?? '' }}
            </h2>
            <h3 class="torrent-search--poster__release-date">
                {{ substr($movie->release_date ?? '', 0, 4) ?? '' }}
            </h3>
        </figcaption>
    </figure>
</article>
