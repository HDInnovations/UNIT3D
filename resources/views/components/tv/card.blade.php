@props([
    'media',
    'personalFreeleech',
])

<article class="torrent-search--grouped__result" x-data="torrentGroup">
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
                {{ $media->name ?? '' }} (
                <time class="torrent-search--grouped__title-year">
                    {{ substr($media->first_air_date ?? '', 0, 4) ?? '' }}
                </time>
                )
            </a>
        </h2>
        <address class="torrent-search--grouped__directors">
            @if ($media->creators->isNotEmpty())
                <span class="torrent-search-grouped__directors-by">by</span>
                @foreach ($media->creators as $creator)
                    <a
                        href="{{ route('mediahub.persons.show', ['id' => $creator->id, 'occupationId' => App\Enums\Occupation::CREATOR->value]) }}"
                        class="torrent-search--grouped__director"
                    >
                        {{ $creator->name }}
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
        <p class="torrent-search--grouped__plot">{{ $media->overview ?? '' }}</p>
    </header>
    <section>
        @if (array_key_exists('Complete Pack', $media->torrents))
            <details class="torrent-search--grouped__dropdown" open>
                <summary x-bind="complete">Complete pack</summary>
                <table class="torrent-search--grouped__torrents">
                    <tbody>
                        @foreach ($media->torrents['Complete Pack'] as $type => $torrents)
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
                        @endforeach
                    </tbody>
                </table>
            </details>
        @endif

        @if (array_key_exists('Specials', $media->torrents))
            <details
                class="torrent-search--grouped__dropdown"
                @if (! array_key_exists('Complete Pack', $media->torrents) && ! array_key_exists('Seasons', $media->torrents))
                    open
                @endif
            >
                <summary x-bind="specials">Specials</summary>
                @foreach ($media->torrents['Specials'] as $specialName => $special)
                    <details
                        class="torrent-search--grouped__dropdown"
                        @if ($loop->first)
                            open
                        @endif
                    >
                        <summary x-bind="special">{{ $specialName }}</summary>
                        <table class="torrent-search--grouped__torrents">
                            @foreach ($special as $type => $torrents)
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
                    </details>
                @endforeach
            </details>
        @endif

        @foreach ($media->torrents['Seasons'] ?? [] as $seasonName => $season)
            <details
                class="torrent-search--grouped__dropdown"
                @if ($loop->first)
                    open
                @endif
            >
                <summary x-bind="season">{{ $seasonName }}</summary>
                @if (array_key_exists('Season Pack', $season) && ! array_key_exists('Episodes', $season))
                    <table class="torrent-search--grouped__torrents">
                        @foreach ($season['Season Pack'] as $type => $torrents)
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
                @elseif (array_key_exists('Season Pack', $season))
                    <details open class="torrent-search--grouped__dropdown">
                        <summary x-bind="pack">Season pack</summary>
                        <table class="torrent-search--grouped__torrents">
                            @foreach ($season['Season Pack'] as $type => $torrents)
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
                    </details>
                @endif

                @foreach ($season['Episodes'] ?? [] as $episodeName => $episode)
                    <details
                        class="torrent-search--grouped__dropdown"
                        @if ($loop->first && ! array_key_exists('Season Pack', $season))
                            open
                        @endif
                    >
                        <summary x-bind="episode">{{ $episodeName }}</summary>
                        <table class="torrent-search--grouped__torrents">
                            @foreach ($episode as $type => $torrents)
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
                    </details>
                @endforeach
            </details>
        @endforeach
    </section>
</article>
