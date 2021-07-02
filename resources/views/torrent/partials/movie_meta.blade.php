<div class="movie-wrapper">
    <div class="movie-overlay"></div>
    <div class="movie-poster">
        @php $tmdb_poster = ($meta && $meta->poster) ? \tmdb_image('poster_big', $meta->poster) : 'https://via.placeholder.com/400x600'; @endphp
        <img src="{{ $tmdb_poster }}" class="img-responsive" id="meta-poster">
    </div>
    <div class="meta-info">
        <div class="tags">
            {{ $torrent->category->name }}
        </div>

        <div class="movie-right">
            @if(isset($meta->companies) && $meta->companies->isNotEmpty())
            @php $company = $meta->companies->first(); @endphp
            <div class="badge-user">
                <a href="{{ route('mediahub.companies.show', ['id' => $company->id]) }}">
                @if(isset($company->logo))
                    <img class="img-responsive" src="{{ \tmdb_image('logo_small', $company->logo) }}" title="{{ $company->name }}">
                @else
                {{ $company->name }}
                @endif
                </a>
            </div>
            @endif
        </div>

        @php $tmdb_backdrop = ($meta && $meta->backdrop) ? \tmdb_image('back_big', $meta->backdrop) : 'https://via.placeholder.com/960x540'; @endphp
        <div class="movie-backdrop" style="background-image: url('{{ $tmdb_backdrop }}');"></div>

        <div class="movie-top">
            <h1 class="movie-heading">
                <a href="{{ route('torrents.similar', ['category_id' => $torrent->category_id, 'tmdb' => $torrent->tmdb]) }}">
                    <span class="text-bright text-bold">{{ $meta->title ?? 'No Meta Found' }}</span>
                </a>
                @if(isset($meta->release_date))
                <span> ({{ substr($meta->release_date, 0, 4) ?? '' }})</span>
                @endif
            </h1>

            <div class="movie-overview">
                {{ isset($meta->overview) ? Str::limit($meta->overview, $limit = 350, $end = '...') : '' }}
            </div>
        </div>

        <div class="movie-bottom">
            <div class="movie-details">
                @if ($torrent->imdb != 0 && $torrent->imdb != null)
                <span class="badge-user text-bold">
                    <a href="https://www.imdb.com/title/tt{{ $torrent->imdb }}" title="IMDB" target="_blank">
                        <i class="{{ config('other.font-awesome') }} fa-film"></i> IMDB: {{ $torrent->imdb }}
                    </a>
                </span>
                @endif

                @if ($torrent->tmdb != 0 && $torrent->tmdb != null)
                <span class="badge-user text-bold">
                    <a href="https://www.themoviedb.org/movie/{{ $torrent->tmdb }}" title="The Movie Database"
                        target="_blank">
                        <i class="{{ config('other.font-awesome') }} fa-film"></i> TMDB: {{ $torrent->tmdb }}
                    </a>
                </span>
                @endif

                @if ($torrent->mal != 0 && $torrent->mal != null)
                <span class="badge-user text-bold">
                    <a href="https://myanimelist.net/anime/{{ $torrent->mal }}" title="MyAnimeList" target="_blank">
                        <i class="{{ config('other.font-awesome') }} fa-film"></i> MAL: {{ $torrent->mal }}</a>
                </span>
                @endif

                @if(isset($meta->crew))
                    @php $director = $meta->crew->where('known_for_department' ,'=', 'Directing')->sortBy('order')->first(); @endphp
                @if($director)
                    <span class="badge-user text-bold text-purple">
                        <a href="{{ route('mediahub.persons.show', ['id' => $director->id]) }}">
                            <i class="{{ config('other.font-awesome') }} fa-camera-movie"></i> Dir. {{ $director->name }}
                        </a>
                    </span>
                @endif
                @endif

                <br>
                @if (isset($meta->genres) && $meta->genres->isNotEmpty())
                @foreach ($meta->genres as $genre)
                <span class="badge-user text-bold text-green">
                    <a href="{{ route('mediahub.genres.show', ['id' => $genre->id]) }}">
                        <i class="{{ config('other.font-awesome') }} fa-tag"></i> {{ $genre->name }}
                    </a>
                </span>
                @endforeach
                @endif
            </div>

            <div class="movie-details">
                @if(isset($meta) && !empty(trim($meta->homepage)))
                <span class="badge-user text-bold">
                    <a href="{{ $meta->homepage }}" title="Homepage" rel="noopener noreferrer" target="_blank">
                        <i class="{{ config('other.font-awesome') }} fa-external-link-alt"></i> Homepage
                    </a>
                </span>
                @endif

                <span class="badge-user text-bold text-orange">
                    Status: {{ $meta->status ?? 'Unknown' }}
                </span>

                @if (isset($meta->runtime))
                <span class="badge-user text-bold text-orange">
                    @lang('torrent.runtime'): {{ $meta->runtime }}
                    @lang('common.minute')@lang('common.plural-suffix')
                </span>
                @endif

                <span class="badge-user text-bold text-gold">@lang('torrent.rating'):
                    <span class="movie-rating-stars">
                        <i class="{{ config('other.font-awesome') }} fa-star"></i>
                    </span>
                    {{ $meta->vote_average ?? 0 }}/10 ({{ $meta->vote_count ?? 0 }} @lang('torrent.votes'))
                </span>
            </div>

            <div class="cast-list">
                @if (isset($meta->cast) && $meta->cast->isNotEmpty())
                    @foreach ($meta->cast->sortBy('order')->take(7) as $cast)
                    <div class="cast-item">
                        <a href="{{ route('mediahub.persons.show', ['id' => $cast->id]) }}" class="badge-user">
                            @php $tmdb_face = $cast->still ? \tmdb_image('cast_face', $cast->still) : 'https://via.placeholder.com/138x175'; @endphp
                            <img class="img-responsive" src="{{ $tmdb_face }}" alt="{{ $cast->name }}">
                            <div class="cast-name">{{ $cast->name }}</div>
                        </a>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
