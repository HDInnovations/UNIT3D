@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.index') }}" class="breadcrumb__link">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}" class="breadcrumb__link">
            {{ $torrent->name }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('main')
    <section
        class="panelV2"
        x-data="{
            cat: {{ (int) $torrent->category_id }},
            cats: JSON.parse(atob('{{ base64_encode(json_encode($categories)) }}')),
            type: {{ (int) $torrent->type_id }},
            types: JSON.parse(atob('{{ base64_encode(json_encode($types)) }}')),
            tmdb_movie_exists:
                {{ Js::from(old('movie_exists_on_tmdb', $torrent->tmdb_movie_id) !== null) }},
            tmdb_tv_exists: {{ Js::from(old('tv_exists_on_tmdb', $torrent->tmdb_tv_id) !== null) }},
            imdb_title_exists: {{ Js::from(old('title_exists_on_imdb', $torrent->imdb) !== null) }},
            tvdb_tv_exists: {{ Js::from(old('tv_exists_on_tvdb', $torrent->tvdb) !== null) }},
            mal_anime_exists: {{ Js::from(old('anime_exists_on_mal', $torrent->mal) !== null) }},
            igdb_game_exists: {{ Js::from(old('game_exists_on_igdb', $torrent->igdb) !== null) }},
        }"
    >
        <h2 class="panel__heading">{{ __('common.edit') }}: {{ $torrent->name }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('torrents.update', ['id' => $torrent->id]) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PATCH')
                <p class="form__group" x-show="cats[cat].type === 'no'">
                    <label class="form__label" for="torrent-cover">
                        Cover {{ __('torrent.file') }} ({{ __('torrent.optional') }})
                    </label>
                    <input
                        id="torrent-cover"
                        class="form__file"
                        accept=".jpg, .jpeg, .png"
                        name="torrent-cover"
                        type="file"
                    />
                </p>
                <p class="form__group" x-show="cats[cat].type === 'no'">
                    <label class="form__label" for="torrent-banner">
                        Banner {{ __('torrent.file') }} ({{ __('torrent.optional') }})
                    </label>
                    <input
                        id="torrent-banner"
                        class="form__file"
                        accept=".jpg, .jpeg, .png"
                        name="torrent-banner"
                        type="file"
                    />
                </p>
                <p class="form__group">
                    <input
                        id="name"
                        type="text"
                        class="form__text"
                        name="name"
                        value="{{ old('name') ?? $torrent->name }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('torrent.title') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="category_id"
                        class="form__select"
                        name="category_id"
                        x-model="cat"
                        x-ref="catId"
                        @change="cats[cat].type = cats[$event.target.value].type;"
                    >
                        <option value="{{ old('category_id') ?? $torrent->category_id }}" selected>
                            {{ $torrent->category->name }} ({{ __('torrent.current') }})
                        </option>
                        @foreach ($categories as $id => $category)
                            <option value="{{ $id }}" @selected(old('category_id') === $id)>
                                {{ $category['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="category_id">
                        {{ __('torrent.category') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="type_id"
                        class="form__select"
                        name="type_id"
                        x-model="type"
                        x-ref="typeId"
                        @change="types[type].name = types[$event.target.value].name"
                    >
                        <option value="{{ old('type_id') ?? $torrent->type->id }}" selected>
                            {{ $torrent->type->name }} ({{ __('torrent.current') }})
                        </option>
                        @foreach ($types as $id => $type)
                            <option value="{{ $id }}" @selected(old('type_id') === $id)>
                                {{ $type['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="type_id">
                        {{ __('torrent.type') }}
                    </label>
                </p>
                <p
                    class="form__group"
                    x-show="cats[cat].type === 'movie' || cats[cat].type === 'tv'"
                >
                    <select id="resolution_id" name="resolution_id" class="form__select">
                        @if (! $torrent->resolution)
                            <option hidden="" disabled="disabled" selected="selected" value="">
                                --Select Resolution--
                            </option>
                            )
                        @else
                            <option
                                value="{{ old('resolution_id') ?? $torrent->resolution->id }}"
                                selected
                            >
                                {{ $torrent->resolution->name }} ({{ __('torrent.current') }})
                            </option>
                        @endif
                        @foreach ($resolutions as $resolution)
                            <option
                                value="{{ $resolution->id }}"
                                @selected(old('resolution_id') === $resolution->id)
                            >
                                {{ $resolution->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="resolution_id">
                        {{ __('torrent.resolution') }}
                    </label>
                </p>
                <div
                    class="form__group--horizontal"
                    x-show="(cats[cat].type === 'movie' || cats[cat].type === 'tv') && types[type].name === 'Full Disc'"
                >
                    <p class="form__group">
                        <select id="distributor_id" name="distributor_id" class="form__select">
                            @if (! $torrent->distributor)
                                <option hidden="" disabled="disabled" selected="selected" value="">
                                    --Select Distributor--
                                </option>
                                )
                            @else
                                <option
                                    x-bind:value="
                                        (cats[cat].type === 'movie' || cats[cat].type === 'tv') && types[type].name === 'Full Disc'
                                            ? '{{ $torrent->distributor->id }}'
                                            : ''
                                    "
                                    selected
                                >
                                    {{ $torrent->distributor->name }}
                                    ({{ __('torrent.current') }})
                                </option>
                            @endif
                            <option value="">No Distributor</option>
                            @foreach ($distributors as $distributor)
                                <option
                                    x-bind:value="
                                        (cats[cat].type === 'movie' || cats[cat].type === 'tv') && types[type].name === 'Full Disc'
                                            ? '{{ $distributor->id }}'
                                            : ''
                                    "
                                    value="{{ $distributor->id }}"
                                    @selected(old('distributor_id') === $distributor->id)
                                >
                                    {{ $distributor->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="distributor_id">
                            {{ __('torrent.distributor') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select id="region_id" name="region_id" class="form__select">
                            @if (! $torrent->region)
                                <option hidden="" disabled="disabled" selected="selected" value="">
                                    --Select Region--
                                </option>
                                )
                            @else
                                <option
                                    x-bind:value="
                                        (cats[cat].type === 'movie' || cats[cat].type === 'tv') && types[type].name === 'Full Disc'
                                            ? '{{ $torrent->region->id }}'
                                            : ''
                                    "
                                    selected
                                >
                                    {{ $torrent->region->name }} ({{ __('torrent.current') }})
                                </option>
                            @endif
                            <option value="">No Region</option>
                            @foreach ($regions as $region)
                                <option
                                    x-bind:value="
                                        (cats[cat].type === 'movie' || cats[cat].type === 'tv') && types[type].name === 'Full Disc'
                                            ? '{{ $region->id }}'
                                            : ''
                                    "
                                    @selected(old('region_id') === $region->id)
                                >
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="region_id">
                            {{ __('torrent.region') }}
                        </label>
                    </p>
                </div>
                <div class="form__group--horizontal" x-show="cats[cat].type === 'tv'">
                    <p class="form__group">
                        <input
                            id="season_number"
                            class="form__text"
                            inputmode="numeric"
                            name="season_number"
                            pattern="[0-9]*"
                            x-bind:required="cats[cat].type === 'tv'"
                            type="text"
                            value="{{ old('season_number') ?? $torrent->season_number }}"
                        />
                        <label class="form__label form__label--floating" for="season_number">
                            {{ __('torrent.season-number') }} ({{ __('common.required') }} For TV)
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="episode_number"
                            class="form__text"
                            inputmode="numeric"
                            name="episode_number"
                            pattern="[0-9]*"
                            x-bind:required="cats[cat].type === 'tv'"
                            type="text"
                            value="{{ old('episode_number') ?? $torrent->episode_number }}"
                        />
                        <label class="form__label form__label--floating" for="episode_number">
                            {{ __('torrent.episode-number') }} ({{ __('common.required') }} For
                            TV. Use "0" For Season Packs.)
                        </label>
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
                                value="{{ old('tmdb_movie_id', $torrent->tmdb_movie_id) }}"
                                x-bind:value="
                                    cats[cat].type === 'movie' && tmdb_movie_exists
                                        ? '{{ old('tmdb_movie_id', $torrent->tmdb_movie_id) }}'
                                        : ''
                                "
                                x-bind:required="cats[cat].type === 'movie' && tmdb_movie_exists"
                            />
                            <label class="form__label form__label--floating" for="tmdb_movie_id">
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
                                value="{{ old('tmdb_tv_id', $torrent->tmdb_tv_id) }}"
                                x-bind:value="cats[cat].type === 'tv' && tmdb_tv_exists ? '{{ old('tmdb_tv_id', $torrent->tmdb_tv_id) }}' : ''"
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
                                id="imdb"
                                class="form__text"
                                inputmode="numeric"
                                name="imdb"
                                pattern="[0-9]*"
                                placeholder=" "
                                type="text"
                                value="{{ old('imdb', $torrent->imdb) }}"
                                x-bind:value="
                                    (cats[cat].type === 'movie' || cats[cat].type === 'tv') && imdb_title_exists
                                        ? '{{ old('imdb', $torrent->imdb) }}'
                                        : ''
                                "
                                x-bind:required="(cats[cat].type === 'movie' || cats[cat].type === 'tv') && imdb_title_exists"
                            />
                            <label class="form__label form__label--floating" for="imdb">
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
                                id="tvdb"
                                class="form__text"
                                inputmode="numeric"
                                name="tvdb"
                                pattern="[0-9]*"
                                placeholder=" "
                                type="text"
                                value="{{ old('tvdb', $torrent->tvdb) }}"
                                x-bind:value="cats[cat].type === 'tv' && tvdb_tv_exists ? '{{ old('tvdb', $torrent->tvdb) }}' : ''"
                                x-bind:required="cats[cat].type === 'tv' && tvdb_tv_exists"
                            />
                            <label class="form__label form__label--floating" for="tvdb">
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
                                id="mal"
                                class="form__text"
                                inputmode="numeric"
                                name="mal"
                                pattern="[0-9]*"
                                placeholder=" "
                                type="text"
                                value="{{ old('mal', $torrent->mal) }}"
                                x-bind:value="
                                    (cats[cat].type === 'movie' || cats[cat].type === 'tv') && mal_anime_exists
                                        ? '{{ old('mal', $torrent->mal) }}'
                                        : ''
                                "
                                x-bind:required="(cats[cat].type === 'movie' || cats[cat].type === 'tv') && mal_anime_exists"
                            />
                            <label class="form__label form__label--floating" for="mal">
                                MAL ID
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
                            <input type="hidden" name="igdb" value="0" />
                            <input
                                id="igdb"
                                class="form__text"
                                name="igdb"
                                type="text"
                                value="{{ old('igdb', $torrent->igdb) }}"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                                x-bind:value="cats[cat].type === 'game' && igdb_game_exists ? '{{ old('igdb', $torrent->igdb) }}' : ''"
                                x-bind:required="cats[cat].type === 'game' && igdb_game_exists"
                            />
                            <label class="form__label form__label--floating" for="igdb">
                                IGDB ID
                            </label>
                        </p>
                    </div>
                </div>
                @livewire('bbcode-input', [
                    'name'     => 'description',
                    'label'    => __('common.description'),
                    'required' => true,
                    'content'  => $torrent->description
                ])
                <p class="form__group">
                    <textarea
                        id="description"
                        class="form__textarea"
                        name="mediainfo"
                        placeholder=" "
                    >
{{ old('mediainfo') ?? $torrent->mediainfo }}</textarea
                    >
                    <label class="form__label form__label--floating" for="description">
                        {{ __('torrent.media-info') }}
                    </label>
                </p>

                <p class="form__group">
                    <textarea id="bdinfo" class="form__textarea" name="bdinfo" placeholder=" ">
{{ old('bdinfo') ?? $torrent->bdinfo }}</textarea
                    >
                    <label class="form__label form__label--floating" for="bdinfo">
                        BDInfo (Quick Summary)
                    </label>
                </p>

                @if (auth()->user()->group->is_modo || auth()->id() === $torrent->user_id)
                    <p class="form__group">
                        <input type="hidden" name="anon" value="0" />
                        <input
                            type="checkbox"
                            class="form__checkbox"
                            id="anon"
                            name="anon"
                            value="1"
                            @checked(old('anon') ?? $torrent->anon)
                        />
                        <label class="form__label" for="anon">{{ __('common.anonymous') }}?</label>
                    </p>
                @endif

                @if (auth()->user()->group->is_modo ||auth()->user()->internals()->exists())
                    <p class="form__group">
                        <input type="hidden" name="internal" value="0" />
                        <input
                            type="checkbox"
                            class="form__checkbox"
                            id="internal"
                            name="internal"
                            value="1"
                            @checked(old('internal') ?? $torrent->internal)
                        />
                        <label class="form__label" for="internal">
                            {{ __('torrent.internal') }}?
                        </label>
                    </p>
                @endif

                @if (auth()->user()->group->is_modo || auth()->id() === $torrent->user_id)
                    <p class="form__group">
                        <input type="hidden" name="personal_release" value="0" />
                        <input
                            type="checkbox"
                            class="form__checkbox"
                            id="personal_release"
                            name="personal_release"
                            value="1"
                            @checked(old('personal_release') ?? $torrent->personal_release)
                        />
                        <label class="form__label" for="personal_release">Personal Release?</label>
                    </p>
                @endif

                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
