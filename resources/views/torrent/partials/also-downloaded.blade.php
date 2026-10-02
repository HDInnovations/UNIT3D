<div class="panel__body">
    <section class="also-downloaded" x-ref="posters">
        @switch(true)
            @case($torrent->category->movie_meta)
                @forelse ($alsoDownloaded ?? [] as $movie)
                    <figure class="trending-poster">
                        <x-movie.poster :$movie :categoryId="$movie->category_id" />
                        <figcaption
                            class="trending-poster__download-count"
                            title="Times downloaded"
                        >
                            {{ $movie->total }}
                        </figcaption>
                    </figure>
                @empty
                    No other downloads found!
                @endforelse

                @break
            @case($torrent->category->tv_meta)
                @forelse ($alsoDownloaded ?? [] as $tv)
                    <figure class="trending-poster">
                        <x-tv.poster :$tv :categoryId="$tv->category_id" />
                        <figcaption
                            class="trending-poster__download-count"
                            title="Times downloaded"
                        >
                            {{ $tv->total }}
                        </figcaption>
                    </figure>
                @empty
                    No other downloads found!
                @endforelse

                @break
            @case($torrent->category->game_meta)
                @forelse ($alsoDownloaded ?? [] as $game)
                    <figure class="trending-poster">
                        <x-game.poster :$game :categoryId="$game->category_id" />
                        <figcaption
                            class="trending-poster__download-count"
                            title="Times downloaded"
                        >
                            {{ $game->total }}
                        </figcaption>
                    </figure>
                @empty
                    No other downloads found!
                @endforelse

                @break
            @default
                No other downloads found!
        @endswitch
    </section>
</div>
