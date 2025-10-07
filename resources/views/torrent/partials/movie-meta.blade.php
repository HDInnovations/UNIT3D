<section class="meta">
    @if ($meta?->backdrop)
        <img class="meta__backdrop" src="{{ tmdb_image('back_big', $meta->backdrop) }}" alt="" />
    @endif

    <a
        class="meta__title-link"
        href="{{ $tmdb ? route('torrents.similar', ['category_id' => $category->id, 'tmdb' => $tmdb]) : '#' }}"
    >
        <h1 class="meta__title">
            {{ $meta->title ?? 'No Meta Found' }}
            ({{ substr($meta->release_date ?? '', 0, 4) ?? '' }})
        </h1>
    </a>
    <a
        class="meta__poster-link"
        href="{{ $tmdb ? route('torrents.similar', ['category_id' => $category->id, 'tmdb' => $tmdb]) : '#' }}"
    >
        <img
            src="{{ $meta?->poster ? tmdb_image('poster_big', $meta->poster) : 'https://via.placeholder.com/400x600' }}"
            class="meta__poster"
        />
    </a>
    <div class="meta__actions">
        <a class="meta__dropdown-button" href="#">
            <i class="{{ config('other.font-awesome') }} fa-ellipsis-v"></i>
        </a>
        <ul class="meta__dropdown">
            <li>
                <a
                    href="{{
                        route('torrents.create', [
                            'category_id' => $category->id,
                            'title' => rawurlencode(($meta?->title ?? '') . ' ' . substr($meta->release_date ?? '', 0, 4) ?? ''),
                            'imdb' => $torrent->imdb ?? '' ?: $meta->imdb_id ?? '' ?: '',
                            'tmdb_movie_id' => $meta?->id ?? '',
                            'mal' => $torrent->mal ?? '',
                            'tvdb' => $torrent->tvdb ?? '',
                            'igdb' => $torrent->igdb ?? '',
                        ])
                    }}"
                >
                    {{ __('common.upload') }}
                </a>
            </li>
            <li>
                <a
                    href="{{
                        route('requests.create', [
                            'category_id' => $category->id,
                            'title' => rawurlencode(($meta?->title ?? '') . ' ' . substr($meta->release_date ?? '', 0, 4) ?? ''),
                            'imdb' => $torrent->imdb ?? '' ?: $meta->imdb_id ?? '' ?: '',
                            'tmdb_movie_id' => $meta?->id ?? '',
                            'mal' => $torrent->mal ?? '',
                            'tvdb' => $torrent->tvdb ?? '',
                            'igdb' => $torrent->igdb ?? '',
                        ])
                    }}"
                >
                    Request similar
                </a>
            </li>
            @if ($meta?->id)
                <li>
                    <form
                        action="{{ route('users.wishes.store', ['user' => auth()->user()]) }}"
                        method="post"
                    >
                        @csrf
                        <input type="hidden" name="meta" value="movie" />
                        <input type="hidden" name="tmdb_movie_id" value="{{ $meta->id }}" />
                        <button
                            style="cursor: pointer"
                            title="Receive notifications every time a new torrent is uploaded."
                        >
                            Notify of New Uploads
                        </button>
                    </form>
                </li>
            @endif

            @if ($meta?->id || $torrent?->tmdb_movie_id ?? null)
                <li>
                    <form
                        action="{{ route('torrents.similar.update', ['category' => $category, 'metaId' => $meta?->id ?? $torrent->tmdb_movie_id]) }}"
                        method="post"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            @if (cache()->has('tmdb-movie-scraper:' . ($meta?->id ?? $torrent->tmdb_movie_id)))
                                disabled
                                title="This item was recently updated. Try again tomorrow."
                            @endif
                            style="cursor: pointer"
                        >
                            Update Metadata
                        </button>
                    </form>
                </li>
            @endif
        </ul>
    </div>
    <ul class="work__tags">
        <li class="work__media-type">
            <a
                class="work__media-type-link"
                href="{{ route('torrents.index', ['categoryIds' => [$category->id]]) }}"
            >
                {{ __('mediahub.movie') }}
            </a>
        </li>
        <li class="work__language">
            <a
                class="work__language-link"
                href="{{ $meta?->original_language === null ? '#' : route('torrents.index', ['primaryLanguageNames' => [$meta->original_language]]) }}"
            >
                {{ $meta->original_language ?? __('common.unknown') }}
            </a>
        </li>
        <li class="work__runtime">
            <span class="work__runtime-text">
                {{ \Carbon\CarbonInterval::minutes($meta->runtime ?? 0)->cascade()->forHumans(null, true) }}
            </span>
        </li>
        <li class="work__rating">
            <span
                class="work__rating-text"
                title="{{ $meta->vote_count ?? 0 }} {{ __('torrent.votes') }}"
            >
                {{ round(($meta->vote_average ?? 0) * 10) }}%
            </span>
        </li>
        @if ($meta?->trailer)
            <li class="work__trailer show-trailer">
                <a class="work__trailer-link" href="#">
                    {{ __('torrent.view-trailer') }}
                </a>
            </li>
        @endif
    </ul>
    <ul class="meta__ids">
        @foreach (array_unique(array_filter([$meta->id ?? 0, $torrent->tmdb_movie_id ?? 0])) as $tmdbId)
            <li class="meta__tmdb">
                <a
                    class="meta-id-tag"
                    href="https://www.themoviedb.org/movie/{{ $tmdbId }}"
                    title="The Movie Database: {{ $tmdbId }}"
                    target="_blank"
                >
                    <img src="{{ url('/img/meta/tmdb.svg') }}" />
                </a>
            </li>
        @endforeach

        @foreach (array_unique(array_filter([(int) ($meta->imdb_id ?? 0), $torrent->imdb ?? 0])) as $imdbId)
            <li class="meta__imdb">
                <a
                    class="meta-id-tag"
                    href="https://www.imdb.com/title/tt{{ \str_pad((string) $imdbId, 7, '0', STR_PAD_LEFT) }}"
                    title="Internet Movie Database: {{ \str_pad((string) $imdbId, 7, '0', STR_PAD_LEFT) }}"
                    target="_blank"
                >
                    <img src="{{ url('/img/meta/imdb.svg') }}" />
                </a>
            </li>
        @endforeach

        @if (($torrent->mal ?? 0) > 0)
            <li class="meta__mal">
                <a
                    class="meta-id-tag"
                    href="https://myanimelist.net/anime/{{ $torrent->mal }}"
                    title="My Anime List: {{ $torrent->mal }}"
                    target="_blank"
                >
                    <img src="{{ url('/img/meta/mal.svg') }}" />
                </a>
            </li>
        @endif

        @if (($torrent->tvdb ?? 0) > 0)
            <li class="meta__tvdb">
                <a
                    class="meta-id-tag"
                    href="https://www.thetvdb.com/?tab=series&id={{ $torrent->tvdb }}"
                    title="The TV Database: {{ $torrent->tvdb }}"
                    target="_blank"
                >
                    <img src="{{ url('/img/meta/tvdb.svg') }}" />
                </a>
            </li>
        @endif

        @if (($meta->id ?? 0) > 0)
            <li class="meta__rotten">
                <a
                    class="meta-id-tag"
                    href="{{ href_rottentomatoes($meta->title, $meta->release_date) }}"
                    title="Rotten Tomatoes: {{ $meta->title ?? '' }}  ({{ substr($meta->release_date ?? '', 0, 4) ?? '' }})"
                    target="_blank"
                    rel="noreferrer"
                >
                    <i
                        class="fad fa-tomato"
                        style="
                            --fa-secondary-opacity: 1;
                            --fa-primary-color: green;
                            --fa-secondary-color: red;
                        "
                    ></i>
                </a>
            </li>
        @endif

        @if (($meta->imdb_id ?? 0) > 0)
            <li class="meta__blu-ray">
                <a
                    class="meta-id-tag"
                    href="https://www.blu-ray.com/search/?quicksearch=1&quicksearch_keyword=tt{{ $meta->imdb_id ?? '' }}&section=theatrical"
                    title="Blu-ray: {{ $meta->title ?? '' }}  ({{ substr($meta->release_date ?? '', 0, 4) ?? '' }})"
                    target="_blank"
                >
                    <img class="" src="{{ url('/img/meta/blu-ray.svg') }}" style="width: 40px" />
                </a>
            </li>
        @endif
    </ul>
    <p class="meta__description">{{ $meta?->overview }}</p>
    <div class="meta__chips">
        <section class="meta__chip-container">
            <h2 class="meta__heading">Cast</h2>
            @foreach ($meta?->credits?->where('occupation_id', '=', App\Enums\Occupation::ACTOR->value)?->sortBy('order') ?? [] as $credit)
                <article class="meta-chip-wrapper">
                    <a
                        href="{{ route('mediahub.persons.show', ['id' => $credit->person->id, 'occupationId' => $credit->occupation_id]) }}"
                        class="meta-chip"
                    >
                        @if ($credit->person->still)
                            <img
                                class="meta-chip__image"
                                src="{{ tmdb_image('cast_face', $credit->person->still) }}"
                                alt=""
                                loading="lazy"
                            />
                        @else
                            <i
                                class="{{ config('other.font-awesome') }} fa-user meta-chip__icon"
                            ></i>
                        @endif
                        <h2 class="meta-chip__name">{{ $credit->person->name }}</h2>
                        <h3 class="meta-chip__value">{{ $credit->character }}</h3>
                    </a>
                </article>
            @endforeach
        </section>
        <section class="meta__chip-container" title="Crew">
            <h2 class="meta__heading">Crew</h2>
            @foreach ($meta?->credits?->where('occupation_id', '!=', App\Enums\Occupation::ACTOR->value)?->sortBy('occupation.position') ?? [] as $credit)
                <article class="meta-chip-wrapper">
                    <a
                        href="{{ route('mediahub.persons.show', ['id' => $credit->person->id, 'occupationId' => $credit->occupation_id]) }}"
                        class="meta-chip"
                    >
                        @if ($credit->person->still)
                            <img
                                class="meta-chip__image"
                                src="{{ tmdb_image('cast_face', $credit->person->still) }}"
                                alt=""
                                loading="lazy"
                            />
                        @else
                            <i
                                class="{{ config('other.font-awesome') }} fa-user meta-chip__icon"
                            ></i>
                        @endif
                        <h2 class="meta-chip__name">{{ $credit->occupation->name }}</h2>
                        <h3 class="meta-chip__value">{{ $credit->person->name }}</h3>
                    </a>
                </article>
            @endforeach
        </section>
        <section class="meta__chip-container">
            <h2 class="meta__heading">Extra Information</h2>
            @if ($meta?->genres?->isNotEmpty())
                <article class="meta__genres">
                    <a
                        class="meta-chip"
                        href="{{ route('torrents.index', ['view' => 'group', 'genreIds' => $meta->genres->pluck('id')->toArray()]) }}"
                    >
                        <i
                            class="{{ config('other.font-awesome') }} fa-theater-masks meta-chip__icon"
                        ></i>
                        <h2 class="meta-chip__name">Genres</h2>
                        <h3 class="meta-chip__value">
                            {{ $meta->genres->pluck('name')->join(' / ') }}
                        </h3>
                    </a>
                </article>
            @endif

            @if ($meta?->collections?->isNotEmpty())
                <article class="meta__collection">
                    <details>
                        <summary class="meta-chip">
                            <i
                                class="{{ config('other.font-awesome') }} fa-rectangle-history meta-chip__icon"
                            ></i>
                            <h2 class="meta-chip__name">Collection</h2>
                            <h3 class="meta-chip__value">
                                {{ $meta?->collections?->first()?->name }}
                            </h3>
                        </summary>
                        <div class="meta-chip__list">
                            <article class="meta__collection-item">
                                <a
                                    class="meta-chip meta-chip--value-only"
                                    href="{{ route('mediahub.collections.show', ['id' => $meta?->collections?->first()?->id]) }}"
                                >
                                    @if ($meta?->collections?->first()->poster)
                                        <img
                                            class="meta-chip__image"
                                            src="{{ tmdb_image('poster_small', $meta?->collections?->first()->poster) }}"
                                            alt=""
                                        />
                                    @else
                                        <i
                                            class="{{ config('other.font-awesome') }} fa-rectangle-history meta-chip__icon"
                                        ></i>
                                    @endif
                                    <h3 class="meta-chip__value">View all</h3>
                                </a>
                            </article>
                            @foreach ($meta?->collections?->first()->movies as $movie)
                                <article class="meta__collection-item">
                                    <a
                                        class="meta-chip meta-chip--value-only"
                                        href="{{ route('torrents.similar', ['tmdb' => $movie->id, 'category_id' => $category->id]) }}"
                                    >
                                        @if ($movie->poster)
                                            <img
                                                class="meta-chip__image"
                                                src="{{ tmdb_image('poster_small', $movie->poster) }}"
                                                alt=""
                                            />
                                        @else
                                            <i
                                                class="{{ config('other.font-awesome') }} fa-film meta-chip__icon"
                                            ></i>
                                        @endif
                                        @if ($movie->is($meta))
                                            <h3 class="meta-chip__value">
                                                <strong>
                                                    {{ $movie->title }}
                                                    ({{ $movie->release_date->format('Y') }})
                                                </strong>
                                            </h3>
                                        @else
                                            <h3 class="meta-chip__value">
                                                {{ $movie->title }}
                                                ({{ $movie->release_date->format('Y') }})
                                            </h3>
                                        @endif
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </details>
                </article>
            @endif

            @foreach ($meta?->companies ?? [] as $company)
                <article class="meta__company">
                    <a
                        class="meta-chip"
                        href="{{ route('torrents.index', ['view' => 'group', 'companyId' => $company->id]) }}"
                    >
                        @if ($company->logo)
                            <img
                                class="meta-chip__image"
                                style="object-fit: scale-down"
                                src="{{ tmdb_image('logo_small', $company->logo) }}"
                                alt=""
                            />
                        @else
                            <i
                                class="{{ config('other.font-awesome') }} fa-camera-movie meta-chip__icon"
                            ></i>
                        @endif
                        <h2 class="meta-chip__name">Company</h2>
                        <h3 class="meta-chip__value">{{ $company->name }}</h3>
                    </a>
                </article>
            @endforeach

            @if (isset($torrent) && $torrent?->keywords?->isNotEmpty())
                <article class="meta__keywords">
                    <a
                        class="meta-chip"
                        href="{{ route('torrents.index', ['view' => 'group', 'keywords' => $torrent->keywords->pluck('name')->join(', ')]) }}"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-tag meta-chip__icon"></i>
                        <h2 class="meta-chip__name">Keywords</h2>
                        <h3 class="meta-chip__value">
                            {{ $torrent->keywords->pluck('name')->join(', ') }}
                        </h3>
                    </a>
                </article>
            @endif
        </section>
    </div>
</section>

@if ($meta?->trailer)
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce() }}">
        document.getElementsByClassName('show-trailer')[0].addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire({
                showConfirmButton: false,
                showCloseButton: true,
                background: 'rgb(35,35,35)',
                width: 970,
                html: '<iframe width="930" height="523" src="https://www.youtube-nocookie.com/embed/{{ $meta->trailer }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>',
                title: '<i style="color: #a5a5a5;">{{ $meta->title }} Trailer</i>',
                text: '',
            });
        });
    </script>
@endif
