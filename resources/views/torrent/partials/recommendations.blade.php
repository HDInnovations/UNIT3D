<div class="panel__body">
    <section class="recommendations" style="max-height: 330px !important" x-ref="posters">
        @switch(true)
            @case($torrent->category->movie_meta)
                @forelse ($meta->recommendedMovies ?? [] as $movie)
                    <x-movie.poster :$movie :categoryId="$movie->torrents_min_category_id" />
                @empty
                    No recommendations found!
                @endforelse

                @break
            @case($torrent->category->tv_meta)
                @forelse ($meta->recommendedTv ?? [] as $tv)
                    <x-tv.poster :$tv :categoryId="$tv->torrents_min_category_id" />
                @empty
                    No recommendations found!
                @endforelse

                @break
            @default
                No recommendations Found!
        @endswitch
    </section>
</div>
