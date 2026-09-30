@extends('layout.with-main-and-sidebar')

@section('title')
    <title>Upload - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.index') }}" class="breadcrumb__link">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.upload-action') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('torrents.index') }}">
            {{ __('torrent.search') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('trending.index') }}">
            {{ __('common.trending') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('rss.index') }}">
            {{ __('rss.rss') }}
        </a>
    </li>
    <li class="nav-tab--active">
        <a class="nav-tab--active__link" href="{{ route('torrents.create') }}">
            {{ __('common.upload-action') }}
        </a>
    </li>
@endsection

@section('page', 'page__torrent--create')

@section('main')
    <section
        class="upload panelV2"
        x-data="{
            cat: {{ old('category_id', (int) $category_id) }},
            cats: JSON.parse(atob('{{ base64_encode(json_encode($categories)) }}')),
            tmdb_movie_exists: true,
            tmdb_tv_exists: true,
            imdb_title_exists: true,
            tvdb_tv_exists: true,
            mal_anime_exists: true,
            igdb_game_exists: true,
        }"
    >
        <h2 class="upload-title panel__heading">
            <i class="{{ config('other.font-awesome') }} fa-file"></i>
            {{ __('torrent.torrent') }}
        </h2>
        <div class="panel__body">
            <form
                name="upload"
                class="upload-form form"
                id="upload-form"
                method="POST"
                action="{{ route('torrents.store') }}"
                enctype="multipart/form-data"
            >
                @csrf
                <p class="form__group">
                    <label for="torrent" class="form__label">
                        {{ __('media-interface.torrent.torrent-file') }}
                    </label>
                    <input
                        class="upload-form-file form__file"
                        type="file"
                        accept=".torrent"
                        name="torrent"
                        id="torrent"
                        required
                        @change="uploadExtension.hook(); cat = $refs.catId.value"
                    />
                </p>
                <p class="form__group">
                    <label for="nfo" class="form__label">
                        {{ __('media-interface.torrent.nfo-file') }}
                    </label>
                    <input
                        id="nfo"
                        class="upload-form-file form__file"
                        type="file"
                        accept=".nfo"
                        name="nfo"
                    />
                </p>
                <p class="form__group" x-show="cats[cat].type === 'no' || cats[cat].type === 'book'">
                    <label for="torrent-cover" class="form__label">
                        {{ __('media-interface.torrent.cover-file') }}
                    </label>
                    <input
                        id="torrent-cover"
                        class="upload-form-file form__file"
                        type="file"
                        accept=".jpg, .jpeg"
                        name="torrent-cover"
                    />
                </p>
                <p class="form__group" x-show="cats[cat].type === 'no' || cats[cat].type === 'book'">
                    <label for="torrent-banner" class="form__label">
                        {{ __('media-interface.torrent.banner-file') }}
                    </label>
                    <input
                        id="torrent-banner"
                        class="upload-form-file form__file"
                        type="file"
                        accept=".jpg, .jpeg"
                        name="torrent-banner"
                    />
                </p>
                <p class="form__group">
                    <input
                        type="text"
                        name="name"
                        id="title"
                        class="form__text"
                        value="{{ $title ?: old('name') }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="title">
                        {{ __('torrent.title') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        x-ref="catId"
                        name="category_id"
                        id="autocat"
                        class="form__select"
                        required
                        x-model="cat"
                        @change="cats[cat].type = cats[$event.target.value].type;"
                    >
                        <option hidden selected disabled value=""></option>
                        @foreach ($categories as $id => $category)
                            <option class="form__option" value="{{ $id }}">
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="autocat">
                        {{ __('torrent.category') }}
                    </label>
                </p>
                <p class="form__group">
                    <select name="type_id" id="autotype" class="form__select" required>
                        <option hidden disabled selected value=""></option>
                        @foreach ($types as $type)
                            <option
                                value="{{ $type->id }}"
                                @selected(old('type_id') == $type->id)
                            >
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="autotype">
                        {{ __('torrent.type') }}
                    </label>
                </p>
                <p
                    class="form__group"
                    x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv'"
                >
                    <select
                        name="resolution_id"
                        id="autores"
                        class="form__select"
                        x-bind:required="cats[cat].type === 'movie' || cats[cat].type === 'tv'"
                    >
                        <option hidden disabled selected value=""></option>
                        @foreach ($resolutions as $resolution)
                            <option
                                value="{{ $resolution->id }}"
                                @selected(old('resolution_id') == $resolution->id)
                            >
                                {{ $resolution->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="autores">
                        {{ __('torrent.resolution') }}
                    </label>
                </p>
                <div
                    class="form__group--horizontal"
                    x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv'"
                >
                    <p class="form__group">
                        <select
                            name="distributor_id"
                            id="autodis"
                            class="form__select"
                            x-data="{ distributor: '' }"
                            x-model="distributor"
                            x-bind:class="distributor === '' ? 'form__select--default' : ''"
                        >
                            <option value="">{{ __('common.other') }}</option>
                            <option selected disabled hidden value=""></option>
                            @foreach ($distributors as $distributor)
                                <option
                                    value="{{ $distributor->id }}"
                                    @selected(old('distributor_id') == $distributor->id)
                                >
                                    {{ $distributor->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="autodis">
                            {{ __('media-interface.torrent.distributor-full-disc-hint') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            name="region_id"
                            id="autoreg"
                            class="form__select"
                            x-data="{ region: '' }"
                            x-model="region"
                            x-bind:class="region === '' ? 'form__select--default' : ''"
                        >
                            <option value="">{{ __('common.other') }}</option>
                            <option selected disabled hidden value=""></option>
                            @foreach ($regions as $region)
                                <option
                                    value="{{ $region->id }}"
                                    @selected(old('region_id') == $region->id)
                                >
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="autoreg">
                            {{ __('media-interface.torrent.region-full-disc-hint') }}
                        </label>
                    </p>
                </div>
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
                            {{ __('media-interface.torrent.season-number-hint') }}
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
                            {{ __('media-interface.torrent.episode-number-hint') }}
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
                                {{ __('media-interface.torrent.movie-exists-on-tmdb') }}
                            </label>
                            <output name="apimatch" id="apimatch" for="torrent"></output>
                        </p>
                        <p class="form__group" x-show="tmdb_movie_exists">
                            <input type="hidden" name="tmdb_movie_id" value="0" />
                            <input
                                type="text"
                                name="tmdb_movie_id"
                                id="auto_tmdb_movie"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                x-bind:value="cats[cat].type === 'movie' && tmdb_movie_exists ? '{{ old('tmdb_movie_id', $movieId) }}' : ''"
                                x-bind:required="cats[cat].type === 'movie' && tmdb_movie_exists"
                            />
                            <label class="form__label form__label--floating" for="auto_tmdb_movie">
                                {{ __('media-interface.torrent.tmdb-movie-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
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
                                {{ __('media-interface.torrent.tv-exists-on-tmdb') }}
                            </label>
                            <output name="apimatch" id="apimatch" for="torrent"></output>
                        </p>
                        <p class="form__group" x-show="tmdb_tv_exists">
                            <input type="hidden" name="tmdb_tv_id" value="0" />
                            <input
                                type="text"
                                name="tmdb_tv_id"
                                id="auto_tmdb_tv"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                x-bind:value="cats[cat].type === 'tv' && tmdb_tv_exists ? '{{ old('tmdb_tv_id', $tvId) }}' : ''"
                                x-bind:required="cats[cat].type === 'tv' && tmdb_tv_exists"
                            />
                            <label class="form__label form__label--floating" for="auto_tmdb_tv">
                                {{ __('media-interface.torrent.tmdb-tv-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
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
                                {{ __('media-interface.torrent.title-exists-on-imdb') }}
                            </label>
                        </p>
                        <p class="form__group" x-show="imdb_title_exists">
                            <input type="hidden" name="imdb" value="0" />
                            <input
                                type="text"
                                name="imdb"
                                id="autoimdb"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                x-bind:value="
                                    (cats[cat].type === 'movie' || cats[cat].type === 'tv') && imdb_title_exists
                                        ? '{{ old('imdb', $imdb) }}'
                                        : ''
                                "
                                x-bind:required="(cats[cat].type === 'movie' || cats[cat].type === 'tv') && imdb_title_exists"
                                x-on:paste="
                                    matches = $event.clipboardData.getData('text').match(/tt0*(\d{7,})/);

                                    if (matches !== null) {
                                        $el.value = Number(matches[1]);
                                        $event.preventDefault();
                                    }
                                "
                            />
                            <label class="form__label form__label--floating" for="autoimdb">
                                {{ __('media-interface.torrent.imdb-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
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
                                {{ __('media-interface.torrent.tv-exists-on-tvdb') }}
                            </label>
                        </p>
                        <p class="form__group" x-show="tvdb_tv_exists">
                            <input type="hidden" name="tvdb" value="0" />
                            <input
                                type="text"
                                name="tvdb"
                                id="autotvdb"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                x-bind:value="cats[cat].type === 'tv' && tvdb_tv_exists ? '{{ old('tvdb', $tvdb) }}' : ''"
                                class="form__text"
                                x-bind:required="cats[cat].type === 'tv' && tvdb_tv_exists"
                            />
                            <label class="form__label form__label--floating" for="autotvdb">
                                {{ __('media-interface.torrent.tvdb-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
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
                                {{ __('media-interface.torrent.anime-exists-on-mal') }}
                            </label>
                        </p>
                        <p class="form__group" x-show="mal_anime_exists">
                            <input type="hidden" name="mal" value="0" />
                            <input
                                type="text"
                                name="mal"
                                id="automal"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                x-bind:value="
                                    (cats[cat].type === 'movie' || cats[cat].type === 'tv') && mal_anime_exists
                                        ? '{{ old('mal', $mal) }}'
                                        : ''
                                "
                                x-bind:required="(cats[cat].type === 'movie' || cats[cat].type === 'tv') && mal_anime_exists"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="automal">
                                {{ __('media-interface.torrent.mal-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
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
                                {{ __('media-interface.torrent.game-exists-on-igdb') }}
                            </label>
                        </p>
                        <p class="form__group" x-show="igdb_game_exists">
                            <input
                                type="text"
                                name="igdb"
                                id="autoigdb"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                x-bind:value="cats[cat].type === 'game' && igdb_game_exists ? '{{ old('igdb', $igdb) }}' : ''"
                                class="form__text"
                                x-bind:required="cats[cat].type === 'game' && igdb_game_exists"
                            />
                            <label class="form__label form__label--floating" for="autoigdb">
                                {{ __('media-interface.torrent.igdb-id') }}
                                <b>({{ __('torrent.required-games') }})</b>
                            </label>
                        </p>
                    </div>
                    <div class="form__group--vertical" x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv'">
                        <p class="form__group">
                            <select class="form__select" name="edition_kind" x-bind:disabled="cats[cat].type !== 'movie' && cats[cat].type !== 'tv'">
                                <option value="standard">{{ __('vltava.edition.standard') }}</option>
                                <option value="director_cut">{{ __('vltava.edition.director') }}</option>
                                <option value="extended_cut">{{ __('vltava.edition.extended') }}</option>
                                <option value="restored">{{ __('vltava.edition.restored') }}</option>
                                <option value="fan_edit">{{ __('vltava.edition.fan') }}</option>
                            </select>
                            <label class="form__label">{{ __('vltava.edition.type') }}</label>
                        </p>
                        <p class="form__group"><input class="form__text" name="edition_name" value="{{ old('edition_name') }}" placeholder=" " /><label class="form__label form__label--floating">{{ __('vltava.edition.name') }}</label></p>
                        <p class="form__group"><textarea class="form__textarea" name="edition_provenance" placeholder="{{ __('vltava.edition.provenance') }}">{{ old('edition_provenance') }}</textarea></p>
                    </div>
                    <div class="form__group--vertical" x-show="cats[cat].type === 'music'">
                        <p class="form__group">
                            <input
                                type="text"
                                name="musicbrainz_release_id"
                                id="musicbrainz_release_id"
                                class="form__text"
                                value="{{ old('musicbrainz_release_id') }}"
                                placeholder=" "
                                x-bind:disabled="cats[cat].type !== 'music'"
                            />
                            <label class="form__label form__label--floating" for="musicbrainz_release_id">
                                {{ __('media-interface.torrent.musicbrainz-release-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.musicbrainz-hint') }}</span>
                        </p>
                    </div>
                    <div class="form__group--vertical" x-show="cats[cat].type === 'book'">
                        <p class="form__group">
                            <input
                                type="text"
                                name="open_library_edition_id"
                                id="open_library_edition_id"
                                class="form__text"
                                value="{{ old('open_library_edition_id') }}"
                                placeholder=" "
                                x-bind:disabled="cats[cat].type !== 'book'"
                            />
                            <label class="form__label form__label--floating" for="open_library_edition_id">
                                {{ __('media-interface.torrent.open-library-id-label') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.open-library-hint') }}</span>
                        </p>
                    </div>
                </div>
                <p class="form__group">
                    <input
                        type="text"
                        name="keywords"
                        id="autokeywords"
                        class="form__text"
                        value="{{ old('keywords') }}"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="autokeywords">
                        {{ __('torrent.keywords') }} (
                        <i>{{ __('torrent.keywords-example') }}</i>
                        )
                    </label>
                </p>
                @livewire('bbcode-input', ['name' => 'description', 'label' => __('common.description'), 'required' => true])
                <p
                    class="form__group"
                    x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv'"
                >
                    <textarea
                        id="upload-form-mediainfo"
                        name="mediainfo"
                        class="form__textarea"
                        placeholder=" "
                    >
{{ old('mediainfo') }}</textarea
                    >
                    <label class="form__label form__label--floating" for="upload-form-mediainfo">
                        {{ __('torrent.media-info-parser') }}
                    </label>
                </p>
                <p
                    class="form__group"
                    x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv'"
                >
                    <textarea
                        id="upload-form-bdinfo"
                        name="bdinfo"
                        class="form__textarea"
                        placeholder=" "
                    >
{{ old('bdinfo') }}</textarea
                    >
                    <label class="form__label form__label--floating" for="upload-form-bdinfo">
                        {{ __('media-interface.torrent.bdinfo-quick-summary') }}
                    </label>
                </p>
                <fieldset class="form form__fieldset">
                    <legend class="form__legend">{{ __('media-interface.torrent.publish-visibility-legend') }}</legend>
                    <p class="form__hint">
                        {{ __('media-interface.torrent.publish-visibility-hint') }}
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
                        <label class="form__label" for="anon">{{ __('media-interface.torrent.upload-anon-label') }}</label>
                        <span class="form__hint">{{ __('media-interface.torrent.upload-anon-hint') }}</span>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="personal_release" value="0" />
                        <input
                            type="checkbox"
                            class="form__checkbox"
                            id="personal_release"
                            name="personal_release"
                            value="1"
                            @checked(old('personal_release'))
                        />
                        <label class="form__label" for="personal_release">{{ __('torrent.personal-release') }}</label>
                        <span class="form__hint">{{ __('media-interface.torrent.personal-release-hint') }}</span>
                    </p>
                    @if ($user->group->is_trusted)
                        <p class="form__group">
                            <input type="hidden" name="mod_queue_opt_in" value="0" />
                            <input
                                type="checkbox"
                                class="form__checkbox"
                                id="mod_queue_opt_in"
                                name="mod_queue_opt_in"
                                value="1"
                                @checked(old('mod_queue_opt_in'))
                            />
                            <label class="form__label" for="mod_queue_opt_in">
                                {{ __('media-interface.torrent.mod-queue-opt-in-label') }}
                            </label>
                            <span class="form__hint">
                                {{ __('media-interface.torrent.mod-queue-opt-in-hint') }}
                            </span>
                        </p>
                    @endif
                </fieldset>

                @if (auth()->user()->group->is_modo || auth()->user()->internals()->exists())
                    <fieldset class="form form__fieldset">
                        <legend class="form__legend">Správcovské nastavení vydání</legend>
                        <p class="form__hint">
                            Tyto volby jsou dostupné pouze správě a členům interní skupiny.
                        </p>
                        <p class="form__group">
                            <input type="hidden" name="internal" value="0" />
                            <input
                                type="checkbox"
                                class="form__checkbox"
                                id="internal"
                                name="internal"
                                value="1"
                                @checked(old('internal'))
                            />
                            <label class="form__label" for="internal">{{ __('torrent.internal') }}</label>
                            <span class="form__hint">{{ __('media-interface.torrent.internal-hint') }}</span>
                        </p>
                        <p class="form__group">
                            <input type="hidden" name="refundable" value="0" />
                            <input
                                type="checkbox"
                                class="form__checkbox"
                                id="refundable"
                                name="refundable"
                                value="1"
                                @checked(old('refundable'))
                            />
                            <label class="form__label" for="refundable">{{ __('torrent.refundable') }}</label>
                            <span class="form__hint">{{ __('media-interface.torrent.refundable-hint') }}</span>
                        </p>
                        <p class="form__group">
                            <select name="free" id="free" class="form__select">
                                <option
                                    value="0"
                                    @selected(old('free') === '0' || old('free') === null)
                                >
                                    {{ __('common.no') }}
                                </option>
                                <option value="25" @selected(old('free') === '25')>25%</option>
                                <option value="50" @selected(old('free') === '50')>50%</option>
                                <option value="75" @selected(old('free') === '75')>75%</option>
                                <option value="100" @selected(old('free') === '100')>100%</option>
                            </select>
                            <label class="form__label form__label--floating" for="free">
                                {{ __('torrent.freeleech') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.freeleech-hint') }}</span>
                        </p>
                    </fieldset>
                @endif

                <p class="form__group">
                    <button
                        type="submit"
                        class="form__button form__button--filled"
                        name="post"
                        value="true"
                        id="post"
                    >
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection

@if ($user->can_upload ?? $user->group->can_upload)
    @section('sidebar')
        <section class="panelV2">
            <h2 class="panel__heading">
                <i class="{{ config('other.font-awesome') }} fa-info"></i>
                {{ __('common.info') }}
            </h2>
            <div class="panel__body">
                <p>
                    {{ __('torrent.announce-url') }}:
                    <a
                        x-data="upload"
                        data-announce-url="{{ route('announce', ['passkey' => $user->passkey]) }}"
                        x-on:click.prevent="copy"
                        href="{{ route('announce', ['passkey' => $user->passkey]) }}"
                    >
                        {{ route('announce', ['passkey' => $user->passkey]) }}
                    </a>
                </p>
                <p>
                    {{ __('torrent.announce-url-desc', ['source' => config('torrent.source')]) }}
                </p>
                <a href="{{ config('other.upload-guide_url') }}">
                    {{ __('torrent.announce-url-desc-url') }}
                </a>
            </div>
        </section>
    @endsection
@endif

@section('javascripts')
    <script src="{{ asset('build/unit3d/tmdb.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('build/unit3d/parser.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('build/unit3d/helper.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('build/unit3d/imgbb.js') }}" crossorigin="anonymous"></script>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('alpine:init', () => {
            Alpine.data('upload', () => ({
                copy() {
                    navigator.clipboard.writeText(this.$el.dataset.announceUrl);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        title: @js(__('media-interface.torrent.clipboard-copied-message')),
                    });
                },
            }));
        });
    </script>
@endsection
