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
            typeOptions: JSON.parse(atob('{{ base64_encode(json_encode($types)) }}')),
            typeId: '{{ old('type_id', '') }}',
            tmdb_movie_exists: {{ session()->hasOldInput() ? (old('movie_exists_on_tmdb') ? 'true' : 'false') : 'true' }},
            tmdb_tv_exists: {{ session()->hasOldInput() ? (old('tv_exists_on_tmdb') ? 'true' : 'false') : 'true' }},
            imdb_title_exists: {{ session()->hasOldInput() ? (old('title_exists_on_imdb') ? 'true' : 'false') : 'true' }},
            tvdb_tv_exists: {{ session()->hasOldInput() ? (old('tv_exists_on_tvdb') ? 'true' : 'false') : 'true' }},
            mal_anime_exists: {{ old('anime_exists_on_mal') ? 'true' : 'false' }},
            igdb_game_exists: {{ session()->hasOldInput() ? (old('game_exists_on_igdb') ? 'true' : 'false') : 'true' }},
            identifierTick: 0,
            catType() {
                return (this.cats[this.cat] ?? {}).type ?? 'no';
            },
            typeApplicable(type) {
                const kinds = type.upload_kinds;
                return !Array.isArray(kinds) || kinds.length === 0 || kinds.includes(this.catType());
            },
            onCategoryChange() {
                if (this.typeId !== '' && !this.typeApplicable(this.typeOptions.find((type) => String(type.id) === String(this.typeId)) ?? {})) {
                    this.typeId = '';
                }
            },
            touchIdentifier() {
                this.identifierTick++;
            },
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
                <fieldset
                    class="form form__fieldset upload-drafts"
                    x-data="uploadDrafts({
                        indexEndpoint: @js(route('torrents.drafts.index')),
                        storeEndpoint: @js(route('torrents.drafts.store')),
                        i18n: {
                            genericError: @js(__('upload-flow.drafts.generic-error')),
                            saved: @js(__('upload-flow.drafts.saved-status')),
                            restored: @js(__('upload-flow.drafts.restored-status')),
                            deleted: @js(__('upload-flow.drafts.deleted-status')),
                            restoreConfirmTitle: @js(__('upload-flow.drafts.restore-confirm-title')),
                            restoreConfirmText: @js(__('upload-flow.drafts.restore-confirm-text')),
                            deleteConfirmTitle: @js(__('upload-flow.drafts.delete-confirm-title')),
                            deleteConfirmText: @js(__('upload-flow.drafts.delete-confirm-text')),
                            filesNotice: @js(__('upload-flow.drafts.restore-files-notice')),
                        },
                    })"
                    x-init="init()"
                >
                    <legend class="form__legend">{{ __('upload-flow.drafts.heading') }}</legend>
                    <p class="form__hint">{{ __('upload-flow.drafts.hint') }}</p>
                    <input type="hidden" name="upload_draft_id" x-bind:value="currentDraftId || ''" />
                    <div class="upload-drafts__actions">
                        <p class="form__group">
                            <input id="upload-draft-title" class="form__text" type="text" maxlength="191" x-model="draftTitle" placeholder=" " />
                            <label class="form__label form__label--floating" for="upload-draft-title">
                                {{ __('upload-flow.drafts.name-label') }}
                            </label>
                            <span class="form__hint">{{ __('upload-flow.drafts.name-placeholder') }}</span>
                        </p>
                        <button type="button" class="form__button form__button--outlined" x-on:click="save(false)" x-bind:disabled="loading">
                            {{ __('upload-flow.drafts.save-new-button') }}
                        </button>
                        <button type="button" class="form__button form__button--outlined" x-on:click="save(true)" x-bind:disabled="loading || !currentDraftId">
                            {{ __('upload-flow.drafts.save-button') }}
                        </button>
                    </div>
                    <p class="upload-drafts__status" role="status" aria-live="polite" x-show="status" x-text="status"></p>
                    <p class="upload-drafts__error" role="alert" x-show="error" x-text="error"></p>
                    <p class="form__hint" x-show="loading">{{ __('upload-flow.drafts.loading') }}</p>
                    <p class="form__hint" x-show="!loading && drafts.length === 0">{{ __('upload-flow.drafts.empty') }}</p>
                    <ul class="upload-drafts__list" aria-label="{{ __('upload-flow.drafts.list-label') }}" x-show="drafts.length > 0">
                        <template x-for="draft in drafts" x-bind:key="draft.id">
                            <li class="upload-drafts__row">
                                <span class="upload-drafts__row-name" x-text="draft.title || `#${draft.id}`"></span>
                                <span class="upload-drafts__row-meta" x-text="formatDate(draft.updated_at)"></span>
                                <button type="button" class="form__button form__button--text" x-on:click="restore(draft)">
                                    {{ __('upload-flow.drafts.restore-button') }}
                                </button>
                                <button type="button" class="form__button form__button--text" x-on:click="remove(draft)">
                                    {{ __('upload-flow.drafts.delete-button') }}
                                </button>
                            </li>
                        </template>
                    </ul>
                </fieldset>
                <fieldset class="form form__fieldset upload-category-section">
                    <legend class="form__legend">{{ __('media-interface.torrent.category-legend') }}</legend>
                    <p class="form__hint">
                        {{ __('media-interface.torrent.category-hint') }}
                    </p>
                    <p class="form__group">
                        <select
                            x-ref="catId"
                            name="category_id"
                            id="autocat"
                            class="form__select"
                            required
                            x-model="cat"
                            @change="onCategoryChange()"
                        >
                            <option hidden selected disabled value=""></option>
                            @foreach ($categories as $id => $category)
                                <option
                                    class="form__option"
                                    value="{{ $id }}"
                                    data-upload-kind="{{ $category['type'] }}"
                                >
                                    {{ $category['label'] ?? $category['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="autocat">
                            {{ __('torrent.category') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            name="type_id"
                            x-ref="typeId"
                            id="autotype"
                            class="form__select"
                            required
                            x-model="typeId"
                        >
                            <option hidden disabled selected value=""></option>
                            @foreach ($types as $type)
                                <option
                                    value="{{ $type->id }}"
                                    x-show="typeApplicable(typeOptions.find((t) => String(t.id) === '{{ $type->id }}') ?? {})"
                                    x-bind:disabled="!typeApplicable(typeOptions.find((t) => String(t.id) === '{{ $type->id }}') ?? {})"
                                >
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="autotype">
                            {{ __('torrent.type') }}
                        </label>
                    </p>
                </fieldset>
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
                        @change="uploadExtension.hook(); typeId = $refs.typeId.value; onCategoryChange()"
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
                <p class="form__group" x-show="catType() === 'no' || catType() === 'book' || catType() === 'xxx'">
                    <label for="torrent-cover" class="form__label">
                        {{ __('media-interface.torrent.cover-file') }}
                    </label>
                    <input
                        id="torrent-cover"
                        class="upload-form-file form__file"
                        type="file"
                        accept=".jpg, .jpeg"
                        name="torrent-cover"
                        x-bind:disabled="!['no', 'book', 'xxx'].includes(catType())"
                    />
                </p>
                <p class="form__group" x-show="catType() === 'no' || catType() === 'book' || catType() === 'xxx'">
                    <label for="torrent-banner" class="form__label">
                        {{ __('media-interface.torrent.banner-file') }}
                    </label>
                    <input
                        id="torrent-banner"
                        class="upload-form-file form__file"
                        type="file"
                        accept=".jpg, .jpeg"
                        name="torrent-banner"
                        x-bind:disabled="!['no', 'book', 'xxx'].includes(catType())"
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
                <p
                    class="form__group"
                    x-show="catType() === 'movie' || catType() === 'tv' || catType() === 'xxx'"
                >
                    <select
                        name="resolution_id"
                        id="autores"
                        class="form__select"
                        x-bind:disabled="catType() !== 'movie' && catType() !== 'tv' && catType() !== 'xxx'"
                        x-bind:required="catType() === 'movie' || catType() === 'tv'"
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
                    x-show="catType() === 'movie' || catType() === 'tv'"
                >
                    <p class="form__group">
                        <select
                            name="distributor_id"
                            id="autodis"
                            class="form__select"
                            x-data="{ distributor: '' }"
                            x-model="distributor"
                            x-bind:class="distributor === '' ? 'form__select--default' : ''"
                            x-bind:disabled="catType() !== 'movie' && catType() !== 'tv'"
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
                            x-bind:disabled="catType() !== 'movie' && catType() !== 'tv'"
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
                <div class="form__group--horizontal" x-show="catType() === 'tv'">
                    <p class="form__group">
                        <input
                            type="text"
                            name="season_number"
                            id="season_number"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            value="{{ old('season_number') }}"
                            x-bind:required="catType() === 'tv'"
                            x-bind:disabled="catType() !== 'tv'"
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
                            x-bind:required="catType() === 'tv'"
                            x-bind:disabled="catType() !== 'tv'"
                        />
                        <label class="form__label form__label--floating" for="episode_number">
                            {{ __('torrent.episode-number') }}
                        </label>
                        <span class="form__hint">
                            {{ __('media-interface.torrent.episode-number-hint') }}
                        </span>
                    </p>
                </div>
                <fieldset
                    class="form form__fieldset upload-discovery"
                    x-data="titleDiscovery({
                        searchEndpoint: @js(route('torrents.metadata.search')),
                        musicReleasesEndpoint: @js(route('torrents.metadata.music-releases')),
                        fields: {
                            movie: { id: 'auto_tmdb_movie', existsFlag: 'tmdb_movie_exists' },
                            tv: { id: 'auto_tmdb_tv', existsFlag: 'tmdb_tv_exists' },
                            game: { id: 'autoigdb', existsFlag: 'igdb_game_exists' },
                            music: { id: 'musicbrainz_id', existsFlag: null },
                            book: { id: 'open_library_edition_id', existsFlag: null },
                        },
                        i18n: {
                            genericError: @js(__('upload-flow.discovery.generic-error')),
                            trackCount: @js(__('upload-flow.discovery.track-count')),
                        },
                    })"
                    x-show="['movie', 'tv', 'game', 'music', 'book'].includes(catType())"
                >
                    <legend class="form__legend">{{ __('upload-flow.discovery.heading') }}</legend>
                    <p class="form__hint">{{ __('upload-flow.discovery.hint') }}</p>
                    <div class="upload-discovery__actions">
                        <p class="form__group">
                            <input id="upload-discovery-query" class="form__text" type="search" minlength="2" maxlength="200" x-model="query" x-on:keydown.enter.prevent="search()" placeholder=" " />
                            <label class="form__label form__label--floating" for="upload-discovery-query">{{ __('upload-flow.discovery.query-label') }}</label>
                        </p>
                        <button type="button" class="form__button form__button--outlined" x-on:click="search()" x-bind:disabled="loading || query.trim().length < 2">
                            <span x-show="!loading">{{ __('upload-flow.discovery.search-button') }}</span>
                            <span x-cloak x-show="loading">{{ __('upload-flow.discovery.searching') }}</span>
                        </button>
                    </div>
                    <p class="upload-discovery__error" role="alert" x-show="error" x-text="error"></p>
                    <p class="upload-discovery__status" role="status" aria-live="polite" x-show="status" x-text="status"></p>
                    <p class="form__hint" x-show="searched && !loading && results.length === 0 && !error">{{ __('upload-flow.discovery.no-results') }}</p>
                    <ul class="upload-discovery__results" aria-label="{{ __('upload-flow.discovery.results-label') }}" x-show="results.length > 0">
                        <template x-for="result in results" x-bind:key="`${result.entity}:${result.identifier}`">
                            <li class="upload-discovery__result">
                                <img class="upload-discovery__result-cover" x-show="result.cover_url" x-bind:src="result.cover_url" alt="" />
                                <div class="upload-discovery__result-body">
                                    <span class="upload-discovery__result-title" x-text="result.title"></span>
                                    <span class="upload-discovery__result-subtitle" x-text="resultSubtitle(result)"></span>
                                    <button type="button" class="form__button form__button--text" x-show="result.entity !== 'release-group'" x-on:click="pick(result)">{{ __('upload-flow.discovery.pick-button') }}</button>
                                    <button type="button" class="form__button form__button--text" x-show="result.entity === 'release-group'" x-on:click="browseEditions(result)">{{ __('upload-flow.discovery.browse-editions-button') }}</button>
                                </div>
                            </li>
                        </template>
                    </ul>
                    <div class="upload-discovery__editions" x-show="activeReleaseGroup">
                        <h3>{{ __('upload-flow.discovery.editions-heading') }}</h3>
                        <p class="form__hint" x-show="activeReleaseGroup" x-text="activeReleaseGroup && activeReleaseGroup.title"></p>
                        <p>
                            <button type="button" class="form__button form__button--outlined" x-on:click="useReleaseGroupAsAlbum()">{{ __('upload-flow.discovery.use-as-album-button') }}</button>
                            <button type="button" class="form__button form__button--text" x-on:click="clearEditions()">{{ __('upload-flow.discovery.editions-back') }}</button>
                        </p>
                        <p class="form__hint" x-show="editionsLoading">{{ __('upload-flow.discovery.editions-loading') }}</p>
                        <p class="form__hint" x-show="!editionsLoading && editions.length === 0">{{ __('upload-flow.discovery.editions-empty') }}</p>
                        <ul class="upload-discovery__results">
                            <template x-for="edition in editions" x-bind:key="edition.identifier">
                                <li class="upload-discovery__result">
                                    <div class="upload-discovery__result-body">
                                        <span class="upload-discovery__result-title" x-text="edition.title"></span>
                                        <span class="upload-discovery__result-subtitle" x-text="editionSubtitle(edition)"></span>
                                        <button type="button" class="form__button form__button--text" x-on:click="pickEdition(edition)">{{ __('upload-flow.discovery.pick-button') }}</button>
                                    </div>
                                </li>
                            </template>
                        </ul>
                        <button type="button" class="form__button form__button--outlined" x-show="nextOffset !== null" x-on:click="loadEditions(nextOffset)" x-bind:disabled="editionsLoading">{{ __('upload-flow.discovery.editions-load-more') }}</button>
                    </div>
                </fieldset>
                <div
                    class="form__group--horizontal"
                    x-show="catType() === 'movie' || catType() === 'tv' || catType() === 'game' || catType() === 'music' || catType() === 'book'"
                >
                    <div class="form__group--vertical" x-show="catType() === 'movie'">
                        <p class="form__group">
                            <input
                                type="checkbox"
                                class="form__checkbox"
                                id="movie_exists_on_tmdb"
                                name="movie_exists_on_tmdb"
                                value="1"
                                @checked(old('movie_exists_on_tmdb', true))
                                x-model="tmdb_movie_exists"
                                x-bind:disabled="catType() !== 'movie'"
                            />
                            <label class="form__label" for="movie_exists_on_tmdb">
                                {{ __('media-interface.torrent.movie-exists-on-tmdb') }}
                            </label>
                            <output name="apimatch" id="apimatch" for="torrent"></output>
                        </p>
                        <p class="form__group" x-show="tmdb_movie_exists">
                            
                            <input
                                type="text"
                                name="tmdb_movie_id"
                                id="auto_tmdb_movie"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                value="{{ old('tmdb_movie_id', $movieId) }}"
                                x-bind:required="catType() === 'movie' && tmdb_movie_exists"
                                x-bind:disabled="(catType() !== 'movie') || !tmdb_movie_exists"
                                x-on:input="touchIdentifier()"
                            />
                            <label class="form__label form__label--floating" for="auto_tmdb_movie">
                                {{ __('media-interface.torrent.tmdb-movie-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
                        </p>
                    </div>
                    <div class="form__group--vertical" x-show="catType() === 'tv'">
                        <p class="form__group">
                            <input
                                type="checkbox"
                                class="form__checkbox"
                                id="tv_exists_on_tmdb"
                                name="tv_exists_on_tmdb"
                                value="1"
                                @checked(old('tv_exists_on_tmdb', true))
                                x-model="tmdb_tv_exists"
                                x-bind:disabled="catType() !== 'tv'"
                            />
                            <label class="form__label" for="tv_exists_on_tmdb">
                                {{ __('media-interface.torrent.tv-exists-on-tmdb') }}
                            </label>
                            <output name="apimatch" id="apimatch" for="torrent"></output>
                        </p>
                        <p class="form__group" x-show="tmdb_tv_exists">
                            
                            <input
                                type="text"
                                name="tmdb_tv_id"
                                id="auto_tmdb_tv"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                value="{{ old('tmdb_tv_id', $tvId) }}"
                                x-bind:required="catType() === 'tv' && tmdb_tv_exists"
                                x-bind:disabled="(catType() !== 'tv') || !tmdb_tv_exists"
                                x-on:input="touchIdentifier()"
                            />
                            <label class="form__label form__label--floating" for="auto_tmdb_tv">
                                {{ __('media-interface.torrent.tmdb-tv-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
                        </p>
                    </div>
                    <div
                        class="form__group--vertical"
                        x-show="catType() === 'movie' || catType() === 'tv'"
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
                                x-bind:disabled="catType() !== 'movie' && catType() !== 'tv'"
                            />
                            <label class="form__label" for="title_exists_on_imdb">
                                {{ __('media-interface.torrent.title-exists-on-imdb') }}
                            </label>
                        </p>
                        <p class="form__group" x-show="imdb_title_exists">
                            
                            <input
                                type="text"
                                name="imdb"
                                id="autoimdb"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                value="{{ old('imdb', $imdb) }}"
                                x-bind:required="(catType() === 'movie' || catType() === 'tv') && imdb_title_exists"
                                x-bind:disabled="(catType() !== 'movie' && catType() !== 'tv') || !imdb_title_exists"
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
                    <div class="form__group--vertical" x-show="catType() === 'tv'">
                        <p class="form__group">
                            <input
                                type="checkbox"
                                class="form__checkbox"
                                id="tv_exists_on_tvdb"
                                name="tv_exists_on_tvdb"
                                value="1"
                                @checked(old('tv_exists_on_tvdb', true))
                                x-model="tvdb_tv_exists"
                                x-bind:disabled="catType() !== 'tv'"
                            />
                            <label class="form__label" for="tv_exists_on_tvdb">
                                {{ __('media-interface.torrent.tv-exists-on-tvdb') }}
                            </label>
                        </p>
                        <p class="form__group" x-show="tvdb_tv_exists">
                            
                            <input
                                type="text"
                                name="tvdb"
                                id="autotvdb"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                value="{{ old('tvdb', $tvdb) }}"
                                class="form__text"
                                x-bind:required="catType() === 'tv' && tvdb_tv_exists"
                                x-bind:disabled="(catType() !== 'tv') || !tvdb_tv_exists"
                            />
                            <label class="form__label form__label--floating" for="autotvdb">
                                {{ __('media-interface.torrent.tvdb-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
                        </p>
                    </div>
                    <div
                        class="form__group--vertical"
                        x-show="catType() === 'movie' || catType() === 'tv'"
                    >
                        <p class="form__group">
                            <input
                                type="checkbox"
                                class="form__checkbox"
                                id="anime_exists_on_mal"
                                name="anime_exists_on_mal"
                                value="1"
                                @checked(old('anime_exists_on_mal'))
                                x-model="mal_anime_exists"
                                x-bind:disabled="catType() !== 'movie' && catType() !== 'tv'"
                            />
                            <label class="form__label" for="anime_exists_on_mal">
                                {{ __('media-interface.torrent.anime-exists-on-mal') }}
                            </label>
                        </p>
                        <p class="form__group" x-show="mal_anime_exists">
                            
                            <input
                                type="text"
                                name="mal"
                                id="automal"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                value="{{ old('mal', $mal) }}"
                                x-bind:required="(catType() === 'movie' || catType() === 'tv') && mal_anime_exists"
                                x-bind:disabled="(catType() !== 'movie' && catType() !== 'tv') || !mal_anime_exists"
                                class="form__text"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="automal">
                                {{ __('media-interface.torrent.mal-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.numeric-digits-only') }}</span>
                        </p>
                    </div>
                    <div class="form__group--vertical" x-show="catType() === 'game'">
                        <p class="form__group">
                            <input
                                type="checkbox"
                                class="form__checkbox"
                                id="game_exists_on_igdb"
                                name="game_exists_on_igdb"
                                value="1"
                                @checked(old('game_exists_on_igdb', true))
                                x-model="igdb_game_exists"
                                x-bind:disabled="catType() !== 'game'"
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
                                value="{{ old('igdb', $igdb) }}"
                                class="form__text"
                                x-bind:required="catType() === 'game' && igdb_game_exists"
                                x-bind:disabled="(catType() !== 'game') || !igdb_game_exists"
                                x-on:input="touchIdentifier()"
                            />
                            <label class="form__label form__label--floating" for="autoigdb">
                                {{ __('media-interface.torrent.igdb-id') }}
                                <b>({{ __('torrent.required-games') }})</b>
                            </label>
                        </p>
                    </div>
                    <div class="form__group--vertical" x-show="catType() === 'movie' || catType() === 'tv'">
                        <p class="form__group">
                            <select class="form__select" name="edition_kind" x-bind:disabled="catType() !== 'movie' && catType() !== 'tv'">
                                <option value="standard">{{ __('vltava.edition.standard') }}</option>
                                <option value="director_cut">{{ __('vltava.edition.director') }}</option>
                                <option value="extended_cut">{{ __('vltava.edition.extended') }}</option>
                                <option value="restored">{{ __('vltava.edition.restored') }}</option>
                                <option value="fan_edit">{{ __('vltava.edition.fan') }}</option>
                            </select>
                            <label class="form__label">{{ __('vltava.edition.type') }}</label>
                        </p>
                        <p class="form__group"><input class="form__text" name="edition_name" value="{{ old('edition_name') }}" placeholder=" " x-bind:disabled="catType() !== 'movie' && catType() !== 'tv'" /><label class="form__label form__label--floating">{{ __('vltava.edition.name') }}</label></p>
                        <p class="form__group"><textarea class="form__textarea" name="edition_provenance" placeholder="{{ __('vltava.edition.provenance') }}" x-bind:disabled="catType() !== 'movie' && catType() !== 'tv'">{{ old('edition_provenance') }}</textarea></p>
                    </div>
                    <div class="form__group--vertical" x-show="catType() === 'music'">
                        <p class="form__group">
                            <input
                                type="text"
                                name="musicbrainz_id"
                                id="musicbrainz_id"
                                class="form__text"
                                value="{{ old('musicbrainz_id') }}"
                                placeholder=" "
                                x-bind:disabled="catType() !== 'music'"
                                x-on:input="touchIdentifier()"
                            />
                            <label class="form__label form__label--floating" for="musicbrainz_id">
                                {{ __('media-interface.torrent.musicbrainz-id') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.musicbrainz-hint') }}</span>
                        </p>
                    </div>
                    <div class="form__group--vertical" x-show="catType() === 'book'">
                        <p class="form__group">
                            <input
                                type="text"
                                name="open_library_edition_id"
                                id="open_library_edition_id"
                                class="form__text"
                                value="{{ old('open_library_edition_id') }}"
                                placeholder=" "
                                x-bind:disabled="catType() !== 'book'"
                                x-on:input="touchIdentifier()"
                            />
                            <label class="form__label form__label--floating" for="open_library_edition_id">
                                {{ __('media-interface.torrent.open-library-id-label') }}
                            </label>
                            <span class="form__hint">{{ __('media-interface.torrent.open-library-hint') }}</span>
                        </p>
                    </div>
                </div>
                <div
                    class="form__group upload-metadata-fetch"
                    x-on:upload-metadata-refetch.window="fetchMetadata()"
                    x-on:upload-draft-restored.window="clearMetadata()"
                    x-data="torrentMetadataFetch({
                        endpoint: @js(route('torrents.metadata')),
                        fields: {
                            movie: { id: 'auto_tmdb_movie', existsFlag: 'tmdb_movie_exists' },
                            tv: { id: 'auto_tmdb_tv', existsFlag: 'tmdb_tv_exists' },
                            game: { id: 'autoigdb', existsFlag: 'igdb_game_exists' },
                            music: { id: 'musicbrainz_id', existsFlag: null },
                            book: { id: 'open_library_edition_id', existsFlag: null },
                        },
                        visibleFields: @js([
                            'game' => ['genres', 'publishers', 'developers', 'year', 'release_date', 'game_engines', 'platforms', 'game_modes', 'themes', 'player_perspectives', 'age_ratings', 'languages', 'rating'],
                            'movie' => ['genres', 'year', 'release_date', 'companies', 'directors', 'cast', 'runtime', 'countries', 'spoken_languages', 'rating'],
                            'tv' => ['genres', 'year', 'release_date', 'companies', 'networks', 'creators', 'cast', 'seasons', 'episodes', 'rating'],
                            'music' => ['artists', 'year', 'release_date', 'labels', 'genres', 'formats', 'country', 'catalog_numbers'],
                            'book' => ['authors', 'year', 'published_date', 'publisher', 'page_count', 'isbn_10', 'isbn_13', 'subjects', 'language'],
                        ]),
                        fieldLabels: @js(__('metadata.fields')),
                        i18n: {
                            notLoaded: @js(__('metadata.sections.not_loaded')),
                            unavailable: @js(__('metadata.sections.unavailable')),
                            confirmTitle: @js(__('media-interface.torrent.buttons-confirm-title')),
                            confirmReplaceDescription: @js(__('media-interface.torrent.metadata-fetch-confirm-replace-description')),
                            genericError: @js(__('media-interface.torrent.metadata-fetch-error-generic')),
                        },
                    })"
                    x-show="['movie', 'tv', 'game', 'music', 'book'].includes(catType())"
                >
                    <input type="hidden" name="metadata_selection_token" x-bind:value="activeSelectionToken() || ''" />
                    <p class="upload-metadata-fetch__actions">
                        <button
                            type="button"
                            class="form__button form__button--outlined"
                            x-on:click="fetchMetadata()"
                            x-bind:disabled="loading || !identifierValue()"
                        >
                            <span x-show="!loading">{{ __('media-interface.torrent.metadata-fetch-action') }}</span>
                            <span x-cloak x-show="loading">{{ __('media-interface.torrent.metadata-fetch-loading') }}</span>
                        </button>
                        <span class="form__hint upload-metadata-fetch__hint" x-show="!identifierValue()">
                            {{ __('media-interface.torrent.metadata-fetch-hint') }}
                        </span>
                    </p>
                    <p
                        class="upload-metadata-fetch__error"
                        role="alert"
                        x-cloak
                        x-show="error"
                        x-text="error"
                    ></p>
                    <fieldset class="form__fieldset upload-metadata-fields" id="upload-metadata-fields">
                        <legend class="form__legend">{{ __('metadata.sections.form') }}</legend>
                        <p class="form__hint">{{ __('metadata.sections.form_hint') }}</p>
                        <div class="upload-metadata-fields__grid">
                            <template x-for="(row, index) in displayFields()" x-bind:key="`${catType()}:${row.key}:${index}`">
                                <div class="form__group upload-metadata-fields__field" x-bind:data-metadata-key="row.key">
                                    <label
                                        class="form__label"
                                        x-bind:for="`metadata-field-${index}`"
                                        x-text="row.label"
                                    ></label>
                                    <textarea
                                        class="form__textarea"
                                        readonly
                                        x-bind:id="`metadata-field-${index}`"
                                        x-bind:value="row.value"
                                        x-bind:rows="row.value.includes('\n') || row.value.length > 160 ? 4 : 2"
                                        x-bind:placeholder="metadataPlaceholder()"
                                    ></textarea>
                                </div>
                            </template>
                        </div>
                    </fieldset>
                    <div class="upload-metadata-fetch__result" x-cloak x-show="activeResult()">
                        <img
                            class="upload-metadata-fetch__cover"
                            x-show="result && result.cover_url"
                            x-bind:src="result && result.cover_url"
                            alt="{{ __('media-interface.torrent.metadata-fetch-cover-alt') }}"
                        />
                        <dl class="upload-metadata-fetch__details">
                            <div class="upload-metadata-fetch__row" x-show="result && result.title">
                                <dt>{{ __('media-interface.torrent.metadata-fetch-result-title-label') }}</dt>
                                <dd x-text="result && result.title"></dd>
                            </div>
                            <div class="upload-metadata-fetch__row" x-show="result && result.source">
                                <dt>{{ __('media-interface.torrent.metadata-fetch-result-source-label') }}</dt>
                                <dd x-text="result && result.source"></dd>
                            </div>
                            <div
                                class="upload-metadata-fetch__row"
                                x-show="result && result.description_language"
                            >
                                <dt>{{ __('media-interface.torrent.metadata-fetch-result-language-label') }}</dt>
                                <dd x-text="result && result.description_language"></dd>
                            </div>
                        </dl>
                        <div
                            class="upload-metadata-fetch__raw"
                            x-data="{ open: false }"
                            x-show="result && result.raw"
                        >
                            <button
                                type="button"
                                class="form__button form__button--outlined upload-metadata-fetch__raw-toggle"
                                x-on:click="open = !open"
                                x-text="open ? @js(__('metadata.sections.hide_advanced')) : @js(__('metadata.sections.show_advanced'))"
                            ></button>
                            <pre
                                class="upload-metadata-fetch__raw-json"
                                x-show="open"
                                x-text="result && result.raw ? JSON.stringify(result.raw, null, 2) : ''"
                            ></pre>
                        </div>
                        <p
                            class="upload-metadata-fetch__warning"
                            x-show="result && result.warning"
                            x-text="result && result.warning"
                        ></p>
                    </div>
                </div>
                <fieldset
                    class="form form__fieldset upload-work-search"
                    x-data="workSearch({
                        searchEndpoint: @js(route('works.search')),
                        initialMediaWorkId: @js(old('media_work_id', '')),
                        fields: {
                            movie: { id: 'auto_tmdb_movie', existsFlag: 'tmdb_movie_exists' },
                            tv: { id: 'auto_tmdb_tv', existsFlag: 'tmdb_tv_exists' },
                            game: { id: 'autoigdb', existsFlag: 'igdb_game_exists' },
                            music: { id: 'musicbrainz_id', existsFlag: null },
                            book: { id: 'open_library_edition_id', existsFlag: null },
                        },
                        i18n: {
                            genericError: @js(__('upload-flow.work-search.generic-error')),
                            clearStatus: @js(__('upload-flow.work-search.clear-status')),
                            restoredLabel: @js(__('upload-flow.work-search.selected-restored-label')),
                        },
                    })"
                    x-on:upload-draft-restored.window="restoreFromDraft($event.detail)"
                >
                    <legend class="form__legend">{{ __('upload-flow.work-search.heading') }}</legend>
                    <p class="form__hint">{{ __('upload-flow.work-search.hint') }}</p>
                    <p class="form__hint">{{ __('upload-flow.work-search.grouping-hint') }}</p>
                    <input type="hidden" name="media_work_id" x-bind:value="activeSelectedWorkId() || ''" />
                    <div class="upload-work-search__actions">
                        <p class="form__group">
                            <input id="upload-work-query" class="form__text" type="search" maxlength="200" x-model="query" x-on:keydown.enter.prevent="search()" placeholder=" " />
                            <label class="form__label form__label--floating" for="upload-work-query">{{ __('upload-flow.work-search.query-label') }}</label>
                        </p>
                        <button type="button" class="form__button form__button--outlined" x-on:click="search()" x-bind:disabled="loading || query.trim().length < 2">
                            <span x-show="!loading">{{ __('upload-flow.work-search.search-button') }}</span>
                            <span x-cloak x-show="loading">{{ __('upload-flow.work-search.searching') }}</span>
                        </button>
                    </div>
                    <p class="upload-work-search__error" role="alert" x-show="error" x-text="error"></p>
                    <p class="upload-work-search__status" role="status" aria-live="polite" x-show="status" x-text="status"></p>
                    <p class="form__hint" x-show="searched && !loading && results.length === 0 && !error">{{ __('upload-flow.work-search.no-results') }}</p>
                    <ul class="upload-work-search__results" x-show="results.length > 0">
                        <template x-for="work in results" x-bind:key="work.id">
                            <li class="upload-work-search__result">
                                <div class="upload-work-search__result-body">
                                    <span class="upload-work-search__result-title" x-text="work.title"></span>
                                    <span class="form__hint" x-text="workSubtitle(work)"></span>
                                    <button type="button" class="form__button form__button--text" x-on:click="select(work)">{{ __('upload-flow.work-search.select-button') }}</button>
                                </div>
                            </li>
                        </template>
                    </ul>
                    <div class="upload-work-search__selected" x-show="activeSelectedWorkId()">
                        <strong>{{ __('upload-flow.work-search.selected-label') }}:</strong>
                        <span x-text="selectedWorkLabel()"></span>
                        <button type="button" class="form__button form__button--text" x-on:click="clear()">{{ __('upload-flow.work-search.clear-button') }}</button>
                    </div>
                </fieldset>
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
                        {{ __('torrent.keywords') }}
                    </label>
                </p>
                @livewire('bbcode-input', ['name' => 'description', 'label' => __('common.description'), 'required' => true])
                <p
                    class="form__group"
                    x-show="catType() === 'movie' || catType() === 'tv' || catType() === 'xxx' || catType() === 'music'"
                >
                    <textarea
                        id="upload-form-mediainfo"
                        name="mediainfo"
                        class="form__textarea"
                        placeholder=" "
                        x-bind:disabled="catType() !== 'movie' && catType() !== 'tv' && catType() !== 'xxx' && catType() !== 'music'"
                    >
{{ old('mediainfo') }}</textarea
                    >
                    <label class="form__label form__label--floating" for="upload-form-mediainfo">
                        {{ __('torrent.media-info-parser') }}
                    </label>
                </p>
                <p
                    class="form__group"
                    x-show="catType() === 'movie' || catType() === 'tv' || catType() === 'xxx'"
                >
                    <textarea
                        id="upload-form-bdinfo"
                        name="bdinfo"
                        class="form__textarea"
                        placeholder=" "
                        x-bind:disabled="catType() !== 'movie' && catType() !== 'tv' && catType() !== 'xxx'"
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

                <section
                    class="panelV2 upload-preview-panel"
                    x-data="uploadPreview({
                        endpoint: @js(route('torrents.preview')),
                        i18n: {
                            genericError: @js(__('upload-flow.preview.generic-error')),
                            errorHeading: @js(__('upload-flow.preview.error-heading')),
                        },
                    })"
                >
                    <div class="panel__body">
                        <p class="form__hint">{{ __('upload-flow.preview.hint') }}</p>
                        <div class="upload-preview-panel__actions">
                            <button type="button" class="form__button form__button--outlined" x-on:click="preview()" x-bind:disabled="loading">
                                <span x-show="!loading">{{ __('upload-flow.preview.button') }}</span>
                                <span x-cloak x-show="loading">{{ __('upload-flow.preview.button-loading') }}</span>
                            </button>
                        </div>
                        <div class="upload-preview-panel__error" role="alert" x-show="error">
                            <strong x-text="errorTitle"></strong>
                            <ul class="upload-preview-panel__error-list" x-show="errors.length > 0">
                                <template x-for="message in errors" x-bind:key="message">
                                    <li x-text="message"></li>
                                </template>
                            </ul>
                            <p x-show="errors.length === 0" x-text="error"></p>
                        </div>
                        <div class="upload-preview-panel__body" x-html="html" x-show="html"></div>
                    </div>
                </section>

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

            const formatUploadMessage = (message, replacements = {}) => Object.entries(replacements).reduce(
                (text, [key, value]) => text.replaceAll(`:${key}`, value ?? ''),
                message || '',
            );

            const setInputValue = (nameOrId, value) => {
                const el = document.getElementById(nameOrId) || document.getElementsByName(nameOrId)[0];

                if (!el) {
                    return;
                }

                el.value = value ?? '';
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            };

            const setCheckboxValue = (nameOrId, checked) => {
                const el = document.getElementById(nameOrId) || document.getElementsByName(nameOrId)[0];

                if (!el || el.type !== 'checkbox') {
                    return;
                }

                el.checked = Boolean(Number(checked));
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            };

            const getInputValue = (nameOrId) => {
                const el = document.getElementById(nameOrId) || document.getElementsByName(nameOrId)[0];

                if (!el || el.disabled || el.type === 'file') {
                    return '';
                }

                if (el.type === 'checkbox') {
                    return el.checked ? '1' : '0';
                }

                return el.value ?? '';
            };

            const fieldForCategory = (fields, kind) => fields[kind] ?? null;

            Alpine.data('titleDiscovery', (config) => ({
                query: '', loading: false, error: null, status: null, searched: false,
                results: [], activeReleaseGroup: null, editions: [], nextOffset: null, editionsLoading: false,
                searchToken: 0, editionsToken: 0,
                init() {
                    this.$watch('cat', () => {
                        this.searchToken++; this.loading = false; this.results = []; this.searched = false;
                        this.error = null; this.clearEditions();
                    });
                },
                categoryId() { return this.$refs.catId ? this.$refs.catId.value : (document.getElementById('autocat')?.value ?? ''); },
                resultSubtitle(result) { return [result.year, result.artists, result.subtitle].filter(Boolean).join(' · '); },
                editionSubtitle(edition) { return [edition.year, edition.artists, edition.country, edition.format, edition.track_count ? formatUploadMessage(config.i18n.trackCount, { count: edition.track_count }) : null].filter(Boolean).join(' · '); },
                async search() {
                    const query = this.query.trim();
                    const categoryId = this.categoryId();
                    if (query.length < 2 || !categoryId) return;
                    const token = ++this.searchToken;
                    this.clearEditions();
                    this.loading = true; this.error = null; this.status = null; this.searched = true;
                    try {
                        const response = await axios.get(config.searchEndpoint, { params: { category_id: categoryId, query } });
                        if (token === this.searchToken && this.categoryId() === categoryId && this.query.trim() === query) {
                            this.results = response.data.results || [];
                        }
                    } catch (e) {
                        if (token === this.searchToken) {
                            this.error = e.response?.data?.message || config.i18n.genericError;
                            this.results = [];
                        }
                    } finally { if (token === this.searchToken) this.loading = false; }
                },
                applyIdentifier(identifier) {
                    const field = fieldForCategory(config.fields, this.catType());
                    if (!field) return;
                    if (field.existsFlag) this[field.existsFlag] = true;
                    setInputValue(field.id, identifier);
                    this.$nextTick(() => window.dispatchEvent(new CustomEvent('upload-metadata-refetch')));
                },
                pick(result) { this.applyIdentifier(result.identifier); },
                browseEditions(result) { this.clearEditions(); this.activeReleaseGroup = result; this.loadEditions(0); },
                clearEditions() { this.editionsToken++; this.editionsLoading = false; this.activeReleaseGroup = null; this.editions = []; this.nextOffset = null; },
                useReleaseGroupAsAlbum() { if (this.activeReleaseGroup) this.applyIdentifier(this.activeReleaseGroup.identifier); },
                pickEdition(edition) { this.applyIdentifier(edition.identifier); },
                async loadEditions(offset = 0) {
                    if (!this.activeReleaseGroup) return;
                    const categoryId = this.categoryId();
                    const identifier = this.activeReleaseGroup.identifier;
                    const token = ++this.editionsToken;
                    this.editionsLoading = true; this.error = null;
                    try {
                        const response = await axios.get(config.musicReleasesEndpoint, { params: { category_id: categoryId, identifier, offset } });
                        if (token !== this.editionsToken || this.categoryId() !== categoryId || this.activeReleaseGroup?.identifier !== identifier) return;
                        const results = response.data.results || [];
                        this.editions = offset === 0 ? results : this.editions.concat(results);
                        this.nextOffset = response.data.next_offset;
                    } catch (e) {
                        if (token === this.editionsToken) this.error = e.response?.data?.message || config.i18n.genericError;
                    } finally { if (token === this.editionsToken) this.editionsLoading = false; }
                },
            }));

            Alpine.data('workSearch', (config) => ({
                query: '', loading: false, error: null, status: null, searched: false, results: [],
                selectedWork: null, selectedWorkId: '', selectedCategoryId: '', selectedIdentifier: '',
                searchToken: 0,
                init() {
                    if (config.initialMediaWorkId) this.restoreFromDraft({ mediaWorkId: config.initialMediaWorkId });
                    this.$watch('cat', () => {
                        this.searchToken++; this.loading = false; this.results = []; this.searched = false;
                        this.clear(); this.status = null;
                    });
                    this.$watch('identifierTick', () => {
                        if (this.selectedWorkId && !this.activeSelectedWorkId()) this.clear();
                    });
                },
                categoryId() { return this.$refs.catId ? this.$refs.catId.value : (document.getElementById('autocat')?.value ?? ''); },
                currentIdentifier() { this.identifierTick; const field = fieldForCategory(config.fields, this.catType()); const el = field ? document.getElementById(field.id) : null; return el ? el.value.trim() : ''; },
                activeSelectedWorkId() { return this.selectedWorkId && this.categoryId() === this.selectedCategoryId && this.currentIdentifier() === this.selectedIdentifier ? this.selectedWorkId : ''; },
                selectedWorkLabel() { return this.selectedWork?.title || formatUploadMessage(config.i18n.restoredLabel, { id: this.selectedWorkId }); },
                workSubtitle(work) { return [work.kind, work.source, work.year].filter(Boolean).join(' · '); },
                async search() {
                    const query = this.query.trim();
                    const categoryId = this.categoryId();
                    if (query.length < 2 || !categoryId) return;
                    const token = ++this.searchToken;
                    this.loading = true; this.error = null; this.status = null; this.searched = true;
                    try {
                        const response = await axios.get(config.searchEndpoint, { params: { category_id: categoryId, query } });
                        if (token === this.searchToken && this.categoryId() === categoryId && this.query.trim() === query) {
                            this.results = response.data.results || [];
                        }
                    } catch (e) {
                        if (token === this.searchToken) {
                            this.error = e.response?.data?.message || config.i18n.genericError;
                            this.results = [];
                        }
                    } finally { if (token === this.searchToken) this.loading = false; }
                },
                select(work) {
                    this.selectedWork = work; this.selectedWorkId = String(work.id); this.selectedCategoryId = this.categoryId();
                    if (work.source_id) {
                        const field = fieldForCategory(config.fields, this.catType());
                        if (field) { if (field.existsFlag) this[field.existsFlag] = true; setInputValue(field.id, work.source_id); }
                    }
                    this.selectedIdentifier = this.currentIdentifier(); this.status = null;
                    if (work.source_id) this.$nextTick(() => window.dispatchEvent(new CustomEvent('upload-metadata-refetch')));
                },
                clear() { this.selectedWork = null; this.selectedWorkId = ''; this.selectedCategoryId = ''; this.selectedIdentifier = ''; this.status = config.i18n.clearStatus; },
                restoreFromDraft(detail) {
                    if (!detail?.mediaWorkId) { this.clear(); this.status = null; return; }
                    this.selectedWork = null; this.selectedWorkId = String(detail.mediaWorkId); this.selectedCategoryId = this.categoryId(); this.selectedIdentifier = this.currentIdentifier(); this.status = null;
                },
            }));

            Alpine.data('uploadDrafts', (config) => ({
                drafts: [], currentDraftId: @js(old('upload_draft_id', '')), draftTitle: '', loading: false, error: null, status: null,
                init() { this.refresh(); },
                formatDate(value) { return value ? new Date(value).toLocaleString() : ''; },
                async refresh() {
                    this.loading = true; this.error = null;
                    try { const response = await axios.get(config.indexEndpoint); this.drafts = response.data.drafts || []; }
                    catch (e) { this.error = e.response?.data?.message || config.i18n.genericError; }
                    finally { this.loading = false; }
                },
                collectFields() {
                    const fields = {};
                    const set = (nameOrId, key = nameOrId) => { const value = getInputValue(nameOrId); if (value !== '') fields[key] = value; };
                    const setBool = (nameOrId, key = nameOrId) => { const value = getInputValue(nameOrId); if (value !== '') fields[key] = value; };
                    fields.category_id = String(this.cat);
                    set('autotype', 'type_id'); set('title', 'name'); set('autores', 'resolution_id'); set('autoreg', 'region_id'); set('autodis', 'distributor_id');
                    set('season_number'); set('episode_number');
                    setBool('movie_exists_on_tmdb'); set('auto_tmdb_movie', 'tmdb_movie_id'); setBool('tv_exists_on_tmdb'); set('auto_tmdb_tv', 'tmdb_tv_id');
                    setBool('title_exists_on_imdb'); set('autoimdb', 'imdb'); setBool('tv_exists_on_tvdb'); set('autotvdb', 'tvdb'); setBool('anime_exists_on_mal'); set('automal', 'mal');
                    setBool('game_exists_on_igdb'); set('autoigdb', 'igdb'); set('musicbrainz_id'); set('open_library_edition_id');
                    set('edition_kind'); set('edition_name'); set('edition_provenance'); set('autokeywords', 'keywords'); set('bbcode-description', 'description'); set('upload-form-mediainfo', 'mediainfo'); set('upload-form-bdinfo', 'bdinfo');
                    setBool('anon'); setBool('personal_release'); setBool('mod_queue_opt_in'); setBool('internal'); setBool('refundable'); set('free');
                    const mediaWorkId = document.getElementsByName('media_work_id')[0]?.value;
                    if (mediaWorkId) fields.media_work_id = mediaWorkId;
                    return fields;
                },
                async save(overwriteCurrent) {
                    this.loading = true; this.error = null; this.status = null;
                    try {
                        const response = await axios.post(config.storeEndpoint, { id: overwriteCurrent ? this.currentDraftId || null : null, title: this.draftTitle || getInputValue('title') || null, fields: this.collectFields() });
                        this.currentDraftId = response.data.draft.id; this.draftTitle = response.data.draft.title || this.draftTitle; this.status = config.i18n.saved; await this.refresh();
                    } catch (e) { this.error = e.response?.data?.message || config.i18n.genericError; }
                    finally { this.loading = false; }
                },
                async restore(draftSummary) {
                    const confirmation = await Swal.fire({ title: config.i18n.restoreConfirmTitle, text: config.i18n.restoreConfirmText, icon: 'warning', showConfirmButton: true, showCancelButton: true, confirmButtonText: @js(__('common.yes')), cancelButtonText: @js(__('common.cancel')) });
                    if (!confirmation.isConfirmed) return;
                    this.loading = true; this.error = null; this.status = null;
                    try {
                        const response = await axios.get(`${config.indexEndpoint}/${draftSummary.id}`);
                        const draft = response.data.draft;
                        await this.applyDraftFields(draft.fields || {});
                        this.currentDraftId = draft.id; this.draftTitle = draft.title || ''; this.status = config.i18n.restored;
                        Swal.fire({ icon: 'info', title: config.i18n.filesNotice, toast: true, position: 'top-end', showConfirmButton: false, timer: 7000 });
                    } catch (e) { this.error = e.response?.data?.message || config.i18n.genericError; }
                    finally { this.loading = false; }
                },
                async remove(draft) {
                    const confirmation = await Swal.fire({ title: config.i18n.deleteConfirmTitle, text: config.i18n.deleteConfirmText, icon: 'warning', showConfirmButton: true, showCancelButton: true, confirmButtonText: @js(__('common.yes')), cancelButtonText: @js(__('common.cancel')) });
                    if (!confirmation.isConfirmed) return;
                    this.loading = true; this.error = null; this.status = null;
                    try { await axios.delete(`${config.indexEndpoint}/${draft.id}`); if (String(this.currentDraftId) === String(draft.id)) this.currentDraftId = ''; this.status = config.i18n.deleted; await this.refresh(); }
                    catch (e) { this.error = e.response?.data?.message || config.i18n.genericError; }
                    finally { this.loading = false; }
                },
                async applyDraftFields(fields) {
                    document.querySelectorAll('#upload-form input[type="file"]').forEach((input) => {
                        input.value = '';
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    });
                    if (fields.category_id) { this.cat = Number(fields.category_id); setInputValue('autocat', fields.category_id); this.onCategoryChange(); await this.$nextTick(); }
                    setInputValue('autotype', fields.type_id || ''); this.typeId = fields.type_id || '';
                    setInputValue('title', fields.name || ''); setInputValue('autores', fields.resolution_id || ''); setInputValue('autoreg', fields.region_id || ''); setInputValue('autodis', fields.distributor_id || '');
                    setInputValue('season_number', fields.season_number || ''); setInputValue('episode_number', fields.episode_number || '');
                    setCheckboxValue('movie_exists_on_tmdb', fields.movie_exists_on_tmdb ?? '0'); setInputValue('auto_tmdb_movie', fields.tmdb_movie_id || '');
                    setCheckboxValue('tv_exists_on_tmdb', fields.tv_exists_on_tmdb ?? '0'); setInputValue('auto_tmdb_tv', fields.tmdb_tv_id || '');
                    setCheckboxValue('title_exists_on_imdb', fields.title_exists_on_imdb ?? '0'); setInputValue('autoimdb', fields.imdb || '');
                    setCheckboxValue('tv_exists_on_tvdb', fields.tv_exists_on_tvdb ?? '0'); setInputValue('autotvdb', fields.tvdb || '');
                    setCheckboxValue('anime_exists_on_mal', fields.anime_exists_on_mal ?? '0'); setInputValue('automal', fields.mal || '');
                    setCheckboxValue('game_exists_on_igdb', fields.game_exists_on_igdb ?? '0'); setInputValue('autoigdb', fields.igdb || '');
                    setInputValue('musicbrainz_id', fields.musicbrainz_id || ''); setInputValue('open_library_edition_id', fields.open_library_edition_id || '');
                    setInputValue('edition_kind', fields.edition_kind || 'standard'); setInputValue('edition_name', fields.edition_name || ''); setInputValue('edition_provenance', fields.edition_provenance || '');
                    setInputValue('autokeywords', fields.keywords || ''); setInputValue('bbcode-description', fields.description || ''); setInputValue('upload-form-mediainfo', fields.mediainfo || ''); setInputValue('upload-form-bdinfo', fields.bdinfo || '');
                    setCheckboxValue('anon', fields.anon ?? '0'); setCheckboxValue('personal_release', fields.personal_release ?? '0'); setCheckboxValue('mod_queue_opt_in', fields.mod_queue_opt_in ?? '0'); setCheckboxValue('internal', fields.internal ?? '0'); setCheckboxValue('refundable', fields.refundable ?? '0'); setInputValue('free', fields.free ?? '0');
                    this.touchIdentifier(); await this.$nextTick(); window.dispatchEvent(new CustomEvent('upload-draft-restored', { detail: { mediaWorkId: fields.media_work_id || null } }));
                },
            }));

            Alpine.data('uploadPreview', (config) => ({
                loading: false, error: null, errorTitle: null, errors: [], html: '', requestToken: 0,
                init() {
                    const form = document.getElementById('upload-form');
                    const invalidate = () => {
                        this.requestToken++; this.html = ''; this.error = null; this.errors = []; this.loading = false;
                    };
                    form?.addEventListener('input', invalidate);
                    form?.addEventListener('change', invalidate);
                    this.cleanup = () => {
                        form?.removeEventListener('input', invalidate);
                        form?.removeEventListener('change', invalidate);
                    };
                },
                destroy() { this.cleanup?.(); },
                async preview() {
                    const form = document.getElementById('upload-form');
                    if (!form) return;
                    const token = ++this.requestToken;
                    this.loading = true; this.error = null; this.errorTitle = null; this.errors = []; this.html = '';
                    try {
                        const response = await axios.post(config.endpoint, new FormData(form));
                        if (token === this.requestToken) this.html = response.data.html || '';
                    } catch (e) {
                        if (token === this.requestToken) {
                            this.errorTitle = config.i18n.errorHeading;
                            this.error = e.response?.data?.message || config.i18n.genericError;
                            this.errors = Object.values(e.response?.data?.errors || {}).flat();
                        }
                    } finally { if (token === this.requestToken) this.loading = false; }
                },
            }));

            Alpine.data('torrentMetadataFetch', (config) => ({
                loading: false,
                error: null,
                result: null,
                selectionToken: null,
                resultCategoryId: null,
                resultIdentifier: null,
                requestToken: 0,
                init() {
                    this.$watch('cat', () => this.clearMetadata());
                    this.$watch('identifierTick', () => {
                        if (this.loading || (this.result && !this.activeResult())) this.clearMetadata();
                    });
                    ['tmdb_movie_exists', 'tmdb_tv_exists', 'igdb_game_exists'].forEach((flag) => {
                        this.$watch(flag, () => this.clearMetadata());
                    });
                },
                clearMetadata() {
                    this.requestToken++; this.loading = false; this.error = null;
                    this.result = null; this.selectionToken = null;
                    this.resultCategoryId = null; this.resultIdentifier = null;
                },
                activeResult() {
                    return this.result && this.categoryId() === this.resultCategoryId
                        && this.identifierValue() === this.resultIdentifier ? this.result : null;
                },
                activeSelectionToken() {
                    return this.activeResult() ? this.selectionToken : '';
                },
                metadataPlaceholder() {
                    return this.activeResult() ? config.i18n.unavailable : config.i18n.notLoaded;
                },
                displayFields() {
                    const rows = this.activeResult()?.details ?? [];
                    const fields = config.visibleFields[this.catType()] ?? [];
                    const values = new Map(rows.map((row) => [row.key, row.value]));
                    const date = values.get('release_date') || values.get('published_date') || '';
                    const year = date.match(/\b\d{4}\b/)?.[0] ?? '';
                    const visibleKeys = new Set(fields);

                    return fields.map((key) => ({
                        key,
                        label: config.fieldLabels[key],
                        value: key === 'year' ? year : (values.get(key) ?? ''),
                    })).concat(rows.filter((row) => !visibleKeys.has(row.key)));
                },
                identifierField() {
                    const field = config.fields[this.catType()];

                    return field ? document.getElementById(field.id) : null;
                },
                identifierEnabled() {
                    const field = config.fields[this.catType()];

                    if (!field) {
                        return false;
                    }

                    if (field.existsFlag && !this[field.existsFlag]) {
                        return false;
                    }

                    return true;
                },
                identifierValue() {
                    this.identifierTick;

                    if (!this.identifierEnabled()) {
                        return '';
                    }

                    const el = this.identifierField();

                    return el ? el.value.trim() : '';
                },
                categoryId() {
                    return this.$refs.catId ? this.$refs.catId.value : (document.getElementById('autocat')?.value ?? '');
                },
                async fetchMetadata() {
                    const categoryId = this.categoryId();
                    const identifier = this.identifierValue();

                    if (!categoryId || !identifier) {
                        return;
                    }

                    this.error = null;
                    this.result = null;
                    this.selectionToken = null;
                    this.loading = true;

                    const token = ++this.requestToken;
                    const requestCategoryId = categoryId;
                    const requestIdentifier = identifier;

                    try {
                        const response = await axios.get(config.endpoint, {
                            params: { category_id: categoryId, identifier: identifier },
                        });

                        if (token !== this.requestToken) {
                            return;
                        }

                        if (this.categoryId() !== requestCategoryId || this.identifierValue() !== requestIdentifier) {
                            return;
                        }

                        this.applyResult(response.data);
                    } catch (e) {
                        if (token !== this.requestToken || this.categoryId() !== requestCategoryId || this.identifierValue() !== requestIdentifier) {
                            return;
                        }

                        this.error = e.response?.data?.message || config.i18n.genericError;
                    } finally {
                        if (token === this.requestToken) {
                            this.loading = false;
                        }
                    }
                },
                applyResult(data) {
                    this.result = data;
                    this.selectionToken = data.selection_token || null;

                    const titleInput = document.getElementById('title');

                    if (titleInput && titleInput.value.trim() === '' && data.title) {
                        setInputValue('title', data.title);
                    }

                    if (data.description) {
                        this.applyDescription(data.description);
                    }

                    this.applyIdentifiers(data.identifiers || {});
                    this.resultCategoryId = this.categoryId();
                    this.resultIdentifier = this.identifierValue();
                },
                applyDescription(description) {
                    const textarea = document.getElementById('bbcode-description');

                    if (!textarea) {
                        return;
                    }

                    const categoryId = this.categoryId();
                    const identifier = this.identifierValue();
                    const setValue = () => {
                        if (this.categoryId() !== categoryId || this.identifierValue() !== identifier) {
                            return;
                        }
                        textarea.value = description;
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    };

                    if (textarea.value.trim() === '') {
                        setValue();

                        return;
                    }

                    Swal.fire({
                        title: config.i18n.confirmTitle,
                        text: config.i18n.confirmReplaceDescription,
                        confirmButtonText: @js(__('common.yes')),
                        cancelButtonText: @js(__('common.cancel')),
                        icon: 'warning',
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            setValue();
                        }
                    });
                },
                applyIdentifiers(identifiers) {
                    const existsFlagMap = {
                        tmdb_movie_id: 'tmdb_movie_exists',
                        tmdb_tv_id: 'tmdb_tv_exists',
                        imdb: 'imdb_title_exists',
                        igdb: 'igdb_game_exists',
                    };

                    Object.entries(identifiers).forEach(([name, value]) => {
                        if (value === null || value === undefined || value === '') {
                            return;
                        }

                        if (existsFlagMap[name]) {
                            this[existsFlagMap[name]] = true;
                        }

                        const el = document.getElementsByName(name)[0];

                        if (el) {
                            el.value = value;
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                            el.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                },
            }));
        });
    </script>
@endsection
