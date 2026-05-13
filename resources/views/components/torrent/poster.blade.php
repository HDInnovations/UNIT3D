@props([
    'torrent',
    'meta',
])

@php
    $poster = match (true) {
        $torrent->category->movie_meta, $torrent->category->tv_meta => isset($meta->poster) ? tmdb_image('poster_mid', $meta->poster) : 'https://via.placeholder.com/160x240',
        $torrent->category->game_meta && isset($meta->cover_image_id) => 'https://images.igdb.com/igdb/image/upload/t_cover_big/' . $meta->cover_image_id . '.jpg',
        $torrent->category->no_meta && Storage::disk('torrent-covers')->exists("torrent-cover_$torrent->id.jpg") => route('authenticated_images.torrent_cover', ['id' => $torrent->id]),
        default => 'https://via.placeholder.com/160x240',
    };
    $title = match (true) {
        $torrent->category->movie_meta => $meta->title ?? $torrent->name,
        $torrent->category->tv_meta, $torrent->category->game_meta => $meta->name ?? $torrent->name,
        default => $torrent->name,
    };
    $year = match (true) {
        $torrent->category->movie_meta => substr((string) ($meta->release_date ?? ''), 0, 4),
        $torrent->category->tv_meta => substr((string) ($meta->first_air_date ?? ''), 0, 4),
        $torrent->category->game_meta => substr((string) ($meta->first_release_date ?? ''), 0, 4),
        default => '',
    };
@endphp

<article class="torrent-search--poster__result">
    <figure>
        <a
            href="{{ route('torrents.show', ['id' => $torrent->id]) }}"
            class="torrent-search--poster__poster"
        >
            <img src="{{ $poster }}" alt="{{ $title }}" loading="lazy" />
        </a>
        <figcaption class="torrent-search--poster__caption">
            <h2 class="torrent-search--poster__title">
                {{ $title }}
            </h2>
            <h3 class="torrent-search--poster__release-date">
                {{ $year ?: $torrent->created_at->diffForHumans() }}
            </h3>
        </figcaption>
    </figure>
</article>
