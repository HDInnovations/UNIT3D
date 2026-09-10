@props([
    'tv',
    'categoryId',
    'tmdb',
])

<article class="torrent-search--poster__result">
    <figure>
        <a
            href="{{ $tv?->id ?? $tmdb ? route('torrents.similar', ['category_id' => $categoryId, 'tmdb' => $tv?->id ?? $tmdb]) : '#' }}"
            class="torrent-search--poster__poster"
        >
            <img
                src="{{ isset($tv->poster) ? tmdb_image('poster_mid', $tv->poster) : url('img/poster-placeholder.svg') }}"
                alt="{{ __('torrent.similar') }}"
                loading="lazy"
            />
        </a>
        <figcaption class="torrent-search--poster__caption">
            <h2 class="torrent-search--poster__title">
                {{ $tv->name ?? '' }}
            </h2>
            <h3 class="torrent-search--poster__release-date">
                <time>
                    {{ substr($tv->first_air_date ?? '', 0, 4) ?? '' }}
                </time>
            </h3>
        </figcaption>
    </figure>
</article>
