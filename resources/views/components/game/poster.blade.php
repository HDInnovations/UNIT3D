@props([
    'game',
    'categoryId',
    'igdb',
])

<article class="torrent-search--poster__result">
    <figure>
        <a
            href="{{ $game?->id ?? $igdb ? route('torrents.similar', ['category_id' => $categoryId, 'tmdb' => $game?->id ?? $igdb]) : '#' }}"
            class="torrent-search--poster__poster"
        >
            <img
                src="{{ isset($game->cover_image_id) ? 'https://images.igdb.com/igdb/image/upload/t_cover_small_2x/' . $game->cover_image_id . '.png' : url('img/poster-placeholder.svg') }}"
                alt="{{ __('torrent.similar') }}"
                loading="lazy"
            />
        </a>
        <figcaption class="torrent-search--poster__caption">
            <h2 class="torrent-search--poster__title">
                {{ $game->name ?? '' }}
            </h2>
            <h3 class="torrent-search--poster__release-date">
                <time>
                    {{ substr($game->first_release_date ?? '', 0, 4) ?? '' }}
                </time>
            </h3>
        </figcaption>
    </figure>
</article>
