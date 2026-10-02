@props([
    'work',
    'personalFreeleech',
])

<article class="torrent-search--grouped__result">
    <header class="torrent-search--grouped__header">
        @if (auth()->user()->settings->show_poster)
            <a
                href="{{ route('works.show', ['work' => $work->id]) }}"
                class="torrent-search--grouped__poster"
            >
                <img
                    src="{{ $work->cover_url ?? asset('img/no-poster.png') }}"
                    alt="{{ $work->title }}"
                    loading="lazy"
                />
            </a>
        @endif

        <h2 class="torrent-search--grouped__title-name">
            <a href="{{ route('works.show', ['work' => $work->id]) }}">
                {{ $work->title }}
                @if ($work->year !== null)
                    <time class="work__year">({{ $work->year }})</time>
                @endif
            </a>
        </h2>
        @if ($work->description)
            <p class="torrent-search--grouped__plot">{{ \Illuminate\Support\Str::limit($work->description, 320) }}</p>
        @endif
    </header>
    <section>
        <table class="torrent-search--grouped__torrents">
            @foreach ($work->variantScopes as $scopeLabel => $typeGroups)
                @foreach ($typeGroups as $typeName => $torrents)
                    <tbody>
                        @foreach ($torrents as $torrent)
                            <tr>
                                @if ($loop->first)
                                    <th
                                        class="torrent-search--grouped__type"
                                        scope="rowgroup"
                                        rowspan="{{ $loop->count }}"
                                    >
                                        {{ $scopeLabel !== '' ? "{$scopeLabel} — {$typeName}" : $typeName }}
                                    </th>
                                @endif

                                @include('components.partials._media-work-variant-row')
                            </tr>
                        @endforeach
                    </tbody>
                @endforeach
            @endforeach
        </table>
        @if ($work->variantCount > $work->torrents->count())
            <a
                href="{{ route('works.show', ['work' => $work->id]) }}"
                class="torrent-search--grouped__view-all"
            >
                {{ __('catalog.view-all-variants', ['count' => $work->variantCount]) }}
            </a>
        @endif
    </section>
</article>
