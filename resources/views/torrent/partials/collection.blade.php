<div class="panel__body collection__posters">
    @if ($meta?->collections?->isNotEmpty() && $torrent->category->movie_meta)
        @foreach ($meta?->collections?->first()?->movies as $movie)
            <x-movie.poster :$movie :categoryId="$movie->torrents_min_category_id" />
        @endforeach
    @else
        No collection found!
    @endif
</div>
