@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('request.add-request') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('requests.index') }}" class="breadcrumb__link">
            {{ __('request.requests') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__request--create')

@section('main')
    @if ($user->can_request ?? $user->group->can_request)
        <section
            class="panelV2"
            x-data="{
                cat: {{ (int) $category_id }},
                cats: JSON.parse(atob('{{ base64_encode(json_encode($categories)) }}')),
                tmdb_movie_exists: true,
                tmdb_tv_exists: true,
                imdb_title_exists: true,
                tvdb_tv_exists: true,
                mal_anime_exists: true,
                igdb_game_exists: true,
            }"
        >
            <h2 class="panel__heading">{{ __('request.add-request') }}</h2>
            <div class="panel__body">
                <form class="form" method="POST" action="{{ route('requests.store') }}">
                    @csrf
                    <p class="form__group">
                        <input
                            id="title"
                            class="form__text"
                            name="name"
                            required
                            type="text"
                            value="{{ $title ?: old('name') }}"
                        />
                        <label class="form__label form__label--floating" for="title">
                            {{ __('request.title') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            name="category_id"
                            id="category_id"
                            class="form__select"
                            required
                            x-model="cat"
                            x-on:change="cats[cat].type = cats[$event.target.value].type;"
                        >
                            <option hidden selected disabled value=""></option>
                            @foreach ($categories as $id => $category)
                                <option
                                    class="form__option"
                                    value="{{ $id }}"
                                    @selected((old('category_id') ?: $category_id) == $id)
                                >
                                    {{ $category['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="category_id">
                            {{ __('request.category') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select id="type_id" class="form__select" name="type_id">
                            <option selected value="">Any</option>
                            @foreach ($types as $type)
                                <option
                                    value="{{ $type->id }}"
                                    @selected(old('type_id') == $type->id)
                                >
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="type_id">
                            {{ __('request.type') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select id="resolution_id" class="form__select" name="resolution_id">
                            <option selected value="">Any</option>
                            @foreach ($resolutions as $resolution)
                                <option
                                    value="{{ $resolution->id }}"
                                    @selected(old('resolution_id') == $resolution->id)
                                >
                                    {{ $resolution->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="resolution_id">
                            {{ __('request.resolution') }}
                        </label>
                    </p>
                    <div class="form__group--horizontal" x-show="cats[cat].type === 'tv'">
                        <p class="form__group">
                            <input
                                type="text"
                                name="season_number"
                                id="season_number"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                value="{{ old('season_number') }}"
                                x-bind:required="cats[cat].type === 'tv'"
                            />
                            <label class="form__label form__label--floating" for="season_number">
                                {{ __('torrent.season-number') }}
                            </label>
                            <span class="form__hint">
                                Numeric digits only. Use 0 only for specials and complete packs.
                            </span>
                        </p>
                        <p class="form__group">
                            <input
                                type="text"
                                name="episode_number"
                                id="episode_number"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                value="{{ old('episode_number') }}"
                                x-bind:required="cats[cat].type === 'tv'"
                            />
                            <label class="form__label form__label--floating" for="episode_number">
                                {{ __('torrent.episode-number') }}
                            </label>
                            <span class="form__hint">
                                Numeric digits only. Use 0 only for season packs and complete packs.
                            </span>
                        </p>
                    </div>
                    <div
                        class="form__group--horizontal"
                        x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv' || cats[cat].type === 'game'"
                    >
                        <div class="form__group--vertical" x-show="cats[cat].type === 'movie'">
                            <p class="form__group">
                                <input
                                    type="checkbox"
                                    class="form__checkbox"
                                    id="movie_exists_on_tmdb"
                                    name="movie_exists_on_tmdb"
                                    value="1"
                                    @checked(old('movie_exists_on_tmdb', true))
                                    x-model="tmdb_movie_exists"
                                />
                                <label class="form__label" for="movie_exists_on_tmdb">
                                    This movie exists on TMDB
                                </label>
                            </p>
                            <p class="form__group" x-show="tmdb_movie_exists">
                                <input type="hidden" name="tmdb_movie_id" value="0" />
                                <input
                                    id="tmdb_movie_id"
                                    class="form__text"
                                    inputmode="numeric"
                                    name="tmdb_movie_id"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                    type="text"
                                    x-bind:value="cats[cat].type === 'movie' && tmdb_movie_exists ? '{{ old('tmdb_movie_id', $movieId) }}' : ''"
                                    x-bind:required="cats[cat].type === 'movie' && tmdb_movie_exists"
                                />
                                <label
                                    class="form__label form__label--floating"
                                    for="tmdb_movie_id"
                                >
                                    TMDB Movie ID
                                </label>
                                <span class="form__hint">Numeric digits only.</span>
                            </p>
                        </div>
                        <div class="form__group--vertical" x-show="cats[cat].type === 'tv'">
                            <p class="form__group">
                                <input
                                    type="checkbox"
                                    class="form__checkbox"
                                    id="tv_exists_on_tmdb"
                                    name="tv_exists_on_tmdb"
                                    value="1"
                                    @checked(old('tv_exists_on_tmdb', true))
                                    x-model="tmdb_tv_exists"
                                />
                                <label class="form__label" for="tv_exists_on_tmdb">
                                    This TV show exists on TMDB
                                </label>
                            </p>
                            <p class="form__group" x-show="tmdb_tv_exists">
                                <input type="hidden" name="tmdb_tv_id" value="0" />
                                <input
                                    id="tmdb_tv_id"
                                    class="form__text"
                                    inputmode="numeric"
                                    name="tmdb_tv_id"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                    type="text"
                                    x-bind:value="cats[cat].type === 'tv' && tmdb_tv_exists ? '{{ old('tmdb_tv_id', $tvId) }}' : ''"
                                    x-bind:required="cats[cat].type === 'tv' && tmdb_tv_exists"
                                />
                                <label class="form__label form__label--floating" for="tmdb_tv_id">
                                    TMDB TV ID
                                </label>
                                <span class="form__hint">Numeric digits only.</span>
                            </p>
                        </div>
                        <div
                            class="form__group--vertical"
                            x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv'"
                        >
                            <p class="form__group">
                                <input
                                    type="checkbox"
                                    class="form__checkbox"
                                    id="title_exists_on_imdb"
                                    name="title_exists_on_imdb"
                                    value="1"
                                    @checked(old('title_exists_on_imdb', true))
                                    x-model="imdb_title_exists"
                                />
                                <label class="form__label" for="title_exists_on_imdb">
                                    This title exists on IMDB
                                </label>
                            </p>
                            <p class="form__group" x-show="imdb_title_exists">
                                <input type="hidden" name="imdb" value="0" />
                                <input
                                    id="autoimdb"
                                    class="form__text"
                                    inputmode="numeric"
                                    name="imdb"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                    type="text"
                                    x-bind:value="
                                        (cats[cat].type === 'movie' || cats[cat].type === 'tv') && imdb_title_exists
                                            ? '{{ old('imdb', $imdb) }}'
                                            : ''
                                    "
                                    x-bind:required="(cats[cat].type === 'movie' || cats[cat].type === 'tv') && imdb_title_exists"
                                />
                                <label class="form__label form__label--floating" for="autoimdb">
                                    IMDB ID
                                </label>
                                <span class="form__hint">Numeric digits only.</span>
                            </p>
                        </div>
                        <div class="form__group--vertical" x-show="cats[cat].type === 'tv'">
                            <p class="form__group">
                                <input
                                    type="checkbox"
                                    class="form__checkbox"
                                    id="tv_exists_on_tvdb"
                                    name="tv_exists_on_tvdb"
                                    value="1"
                                    @checked(old('tv_exists_on_tvdb', true))
                                    x-model="tvdb_tv_exists"
                                />
                                <label class="form__label" for="tv_exists_on_tvdb">
                                    This TV show exists on TVDB
                                </label>
                            </p>
                            <p class="form__group" x-show="tvdb_tv_exists">
                                <input type="hidden" name="tvdb" value="0" />
                                <input
                                    id="autotvdb"
                                    class="form__text"
                                    inputmode="numeric"
                                    name="tvdb"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                    type="text"
                                    x-bind:value="cats[cat].type === 'tv' && tvdb_tv_exists ? '{{ old('tvdb', $tvdb) }}' : ''"
                                    x-bind:required="cats[cat].type === 'tv' && tvdb_tv_exists"
                                />
                                <label class="form__label form__label--floating" for="autotvdb">
                                    TVDB ID
                                </label>
                                <span class="form__hint">Numeric digits only.</span>
                            </p>
                        </div>
                        <div
                            class="form__group--vertical"
                            x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv'"
                        >
                            <p class="form__group">
                                <input
                                    type="checkbox"
                                    class="form__checkbox"
                                    id="anime_exists_on_mal"
                                    name="anime_exists_on_mal"
                                    value="1"
                                    @checked(old('anime_exists_on_mal', true))
                                    x-model="mal_anime_exists"
                                />
                                <label class="form__label" for="anime_exists_on_mal">
                                    This anime exists on MAL
                                </label>
                            </p>
                            <p class="form__group" x-show="mal_anime_exists">
                                <input type="hidden" name="mal" value="0" />
                                <input
                                    id="automal"
                                    class="form__text"
                                    inputmode="numeric"
                                    name="mal"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                    type="text"
                                    x-bind:value="
                                        (cats[cat].type === 'movie' || cats[cat].type === 'tv') && mal_anime_exists
                                            ? '{{ old('mal', $mal) }}'
                                            : ''
                                    "
                                    x-bind:required="(cats[cat].type === 'movie' || cats[cat].type === 'tv') && mal_anime_exists"
                                />
                                <label class="form__label form__label--floating" for="automal">
                                    MAL ID ({{ __('torrent.required-anime') }})
                                </label>
                                <span class="form__hint">Numeric digits only.</span>
                            </p>
                        </div>
                        <div class="form__group--vertical" x-show="cats[cat].type === 'game'">
                            <p class="form__group">
                                <input
                                    type="checkbox"
                                    class="form__checkbox"
                                    id="game_exists_on_igdb"
                                    name="game_exists_on_igdb"
                                    value="1"
                                    @checked(old('game_exists_on_igdb', true))
                                    x-model="igdb_game_exists"
                                />
                                <label class="form__label" for="game_exists_on_igdb">
                                    This game exists on IGDB
                                </label>
                            </p>
                            <p class="form__group" x-show="igdb_game_exists">
                                <input
                                    id="igdb"
                                    class="form__text"
                                    inputmode="numeric"
                                    name="igdb"
                                    pattern="[0-9]*"
                                    placeholder=" "
                                    type="text"
                                    x-bind:value="cats[cat].type === 'game' && igdb_game_exists ? '{{ old('igdb', $igdb) }}' : ''"
                                    x-bind:required="cats[cat].type === 'game' && igdb_game_exists"
                                />
                                <label class="form__label form__label--floating" for="igdb">
                                    IGDB ID ({{ __('request.required') }} For Games)
                                </label>
                            </p>
                        </div>
                    </div>
                    @livewire('bbcode-input', [
                        'name' => 'description',
                        'label' => __('request.description'),
                        'required' => true
                    ])
                    <p class="form__group">
                        <input
                            id="bounty"
                            class="form__text"
                            name="bounty"
                            type="text"
                            pattern="[0-9]*?[1-9][0-9]{2,}"
                            value="100"
                            required
                        />
                        <label class="form__label form__label--floating" for="bounty">
                            {{ __('request.reward') }} ({{ __('request.reward-desc') }})
                        </label>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="anon" value="0" />
                        <input
                            type="checkbox"
                            class="form__checkbox"
                            id="anon"
                            name="anon"
                            value="1"
                            @checked(old('anon'))
                        />
                        <label class="form__label" for="anon">{{ __('common.anonymous') }}?</label>
                    </p>
                    <p class="form__group">
                        <button class="form__button form__button--filled">
                            {{ __('common.submit') }}
                        </button>
                    </p>
                </form>
            </div>
        </section>
    @else
        <section class="panelV2">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-times text-danger"></i>
                {{ __('request.no-privileges') }}!
            </h2>
            <p class="panel__body">{{ __('request.no-privileges-desc') }}!</p>
        </section>
    @endif
@endsection

@if ($user->can_request ?? $user->group->can_request)
    @section('sidebar')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('common.info') }}</h2>
            <div class="panel__body">
                {{ __('request.no-imdb-id') }}
            </div>
        </section>
    @endsection
@endif
