@props([
    'media',
    'personalFreeleech',
])

<article class="torrent-search--grouped__result">
    <header class="torrent-search--grouped__header">
        @if (auth()->user()->settings->show_poster)
            <a
                href="{{ route('torrents.similar', ['category_id' => $media->category_id, 'tmdb' => $media->id]) }}"
                class="torrent-search--grouped__poster"
            >
                <img
                    src="{{ isset($media->poster) ? tmdb_image('poster_small', $media->poster) : url('img/poster-placeholder.svg') }}"
                    alt="{{ __('torrent.similar') }}"
                    loading="lazy"
                />
            </a>
        @endif

        <h2 class="torrent-search--grouped__title-name">
            <a
                href="{{ route('torrents.similar', ['category_id' => $media->category_id, 'tmdb' => $media->id]) }}"
            >
                {{ $media->title ?? '' }} (
                <time class="torrent-search--grouped__title-year">
                    {{ substr($media->release_date ?? '', 0, 4) ?? '' }}
                </time>
                )
            </a>
        </h2>
        <address class="torrent-search--grouped__directors">
            @if ($media->directors->isNotEmpty())
                <span class="torrent-search-grouped__directors-by">by</span>
                @foreach ($media->directors as $director)
                    <a
                        href="{{ route('mediahub.persons.show', ['id' => $director->id, 'occupationId' => App\Enums\Occupation::DIRECTOR->value]) }}"
                        class="torrent-search--grouped__director"
                    >
                        {{ $director->name }}
                    </a>
                    @if (! $loop->last)
                        ,
                    @endif
                @endforeach
            @endif
        </address>
        <div class="torrent-search--grouped__genres">
            @foreach ($media->genres->take(3) as $genre)
                <a
                    href="{{ route('torrents.index', ['view' => 'group', 'genreIds' => [$genre->id]]) }}"
                    class="torrent-search--grouped__genre"
                >
                    {{ $genre->name }}
                </a>
            @endforeach
        </div>
        <p class="torrent-search--grouped__plot">{{ $media->overview }}</p>
    </header>
    <section>
        <table class="torrent-search--grouped__torrents">
            @foreach ($media->torrents['Movie'] as $type => $torrents)
                <tbody>
                    @foreach ($torrents as $torrent)
                        <tr>
                            @if ($loop->first)
                                <th
                                    class="torrent-search--grouped__type"
                                    scope="rowgroup"
                                    rowspan="{{ $loop->count }}"
                                >
                                    {{ $type }}
                                </th>
                            @endif

                            @include('components.partials._torrent-group-row')
                        </tr>
                    @endforeach
                </tbody>
            @endforeach
        </table>
    </section>
</article>
