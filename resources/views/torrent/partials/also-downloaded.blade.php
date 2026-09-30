<div class="panel__body">
    <section class="also-downloaded" style="max-height: 330px !important" x-ref="posters">
        @switch(true)
            @case($torrent->category->movie_meta)
                @forelse ($alsoDownloaded ?? [] as $movie)
                    <figure class="trending-poster">
                        <x-movie.poster :$movie :categoryId="$movie->category_id" />
                        <figcaption
                            class="trending-poster__download-count"
                            title="{{ __('media-interface.torrent.also-downloaded-times-tooltip') }}"
                        >
                            {{ $movie->total }}
                        </figcaption>
                    </figure>
                @empty
                    {{ __('media-interface.torrent.also-downloaded-empty') }}
                @endforelse

                @break
            @case($torrent->category->tv_meta)
                @forelse ($alsoDownloaded ?? [] as $tv)
                    <figure class="trending-poster">
                        <x-tv.poster :$tv :categoryId="$tv->category_id" />
                        <figcaption
                            class="trending-poster__download-count"
                            title="{{ __('media-interface.torrent.also-downloaded-times-tooltip') }}"
                        >
                            {{ $tv->total }}
                        </figcaption>
                    </figure>
                @empty
                    {{ __('media-interface.torrent.also-downloaded-empty') }}
                @endforelse

                @break
            @case($torrent->category->game_meta)
                @forelse ($alsoDownloaded ?? [] as $game)
                    <figure class="trending-poster">
                        <x-game.poster :$game :categoryId="$game->category_id" />
                        <figcaption
                            class="trending-poster__download-count"
                            title="{{ __('media-interface.torrent.also-downloaded-times-tooltip') }}"
                        >
                            {{ $game->total }}
                        </figcaption>
                    </figure>
                @empty
                    {{ __('media-interface.torrent.also-downloaded-empty') }}
                @endforelse

                @break
            @default
                {{ __('media-interface.torrent.also-downloaded-empty') }}
        @endswitch
    </section>
</div>
