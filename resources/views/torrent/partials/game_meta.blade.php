<div class="movie-wrapper game">
    <div class="movie-overlay"></div>
    <div class="movie-poster">
        @php $igdb_poster = (isset($meta) && $meta->cover) ? 'https://images.igdb.com/igdb/image/upload/t_original/'.$meta->cover->image_id.'.jpg' : 'https://via.placeholder.com/400x600'; @endphp
        <img src="{{ $igdb_poster }}" class="img-responsive" id="meta-poster">
    </div>

    <div class="meta-info">
        <div class="tags">
            {{ $torrent->category->name }}
        </div>

        @php $igdb_backdrop = (isset($meta) && $meta->artworks) ? 'https://images.igdb.com/igdb/image/upload/t_screenshot_big/'.$meta->artworks[0]['image_id'].'.jpg' : 'https://via.placeholder.com/960x540'; @endphp
        <div class="movie-backdrop" style="background-image: url('{{ $igdb_backdrop }}');"></div>

        <div class="movie-top">
            <h1 class="movie-heading">
                @if (isset($meta) && $meta->name)
                    <span class="text-bold">{{ $meta->name }}</span>
                    <span> ({{ date('Y', strtotime($meta->first_release_date)) }})</span>
                @else
                    <span class="text-bold">@lang('torrent.no-meta')</span>
                @endif
            </h1>
            <div class="movie-overview">
                @if (isset($meta) && $meta->summary)
                    {{ Str::limit($meta->summary, $limit = 450, $end = '...') }}
                @endif
            </div>
        </div>

        <div class="movie-bottom">
            <div class="movie-details">
                @if (isset($meta) && $meta->url && $torrent->igdb != 0 && $torrent->igdb != null)
                    <span class="badge-user text-bold text-orange">
                        <a href="{{ $meta->url }}" title="IMDB" target="_blank">
                            <i class="{{ config('other.font-awesome') }} fa-gamepad"></i> IGDB: {{ $torrent->igdb }}
                        </a>
                    </span>
                @endif

                @if (isset($meta) && $meta->genres->isNotEmpty())
                    @foreach ($meta->genres as $genre)
                        <span class="badge-user text-bold text-green">
                            <i class="{{ config('other.font-awesome') }} fa-tag"></i> {{ $genre->name }}
                        </span>
                    @endforeach
                @endif
            </div>

            <div class="movie-details">
                @if (isset($meta) && $meta->rating && $meta->rating_count)
                    <span class="badge-user text-bold text-gold">@lang('torrent.rating'):
                        <span class="movie-rating-stars">
                            <i class="{{ config('other.font-awesome') }} fa-star"></i>
                        </span>
                        {{ \round($meta->rating) }}/100 ({{ $meta->rating_count }} @lang('torrent.votes'))
                    </span>
                @endif
            </div>

            <div class="cast-list">
                @if (isset($characters))
                    @foreach($characters as $character)
                        <div class="cast-item">
                            <a href="{{ route('mediahub.persons.show', ['id' => $character->id]) }}" class="badge-user">
                            <img class="img-responsive" src="{{ $character->img_url }}" alt="{{ $character->name }}">
                            <div class="cast-name">{{ $character->name }}</div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
