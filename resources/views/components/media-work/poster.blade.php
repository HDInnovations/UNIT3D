@props([
    'work',
])

<article class="torrent-search--poster__result">
    <figure>
        <a
            href="{{ route('works.show', ['work' => $work->id]) }}"
            class="torrent-search--poster__poster"
            title="{{ $work->title }}"
        >
            <img
                src="{{ $work->cover_url ?? asset('img/no-poster.png') }}"
                alt="{{ $work->title }}"
                loading="lazy"
            />
        </a>
        <figcaption class="torrent-search--poster__caption">
            <h2 class="torrent-search--poster__title">
                <a href="{{ route('works.show', ['work' => $work->id]) }}">
                    {{ $work->title }}
                </a>
            </h2>
            @if ($work->year !== null)
                <h3 class="torrent-search--poster__release-date">
                    {{ $work->year }}
                </h3>
            @endif
        </figcaption>
    </figure>
</article>
