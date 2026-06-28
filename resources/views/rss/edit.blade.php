@extends('layout.with-main')

@section('title')
    <title>{{ __('rss.edit-private-feed') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('rss.index') }}" class="breadcrumb__link"> {{ __('rss.rss') }} </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('rss.edit', ['id' => $rss->id]) }}" class="breadcrumb__link">
            {{ $rss->name }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('common.edit') }}</li>
@endsection

@section('page', 'page__rss--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('rss.edit-public-feed') }}</h2>
        <div class="panel__body">
            <form class="form" method="POST" action="{{ route('rss.update', ['id' => $rss->id]) }}">
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="name"
                        required
                        value="{{ $rss->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('rss.feed') }} {{ __('rss.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="search"
                        class="form__text"
                        name="search"
                        placeholder=" "
                        type="text"
                        value="{{ $rss->object_torrent->search }}"
                    />
                    <label class="form__label form__label--floating" for="search">
                        {{ __('torrent.torrent') }} {{ __('torrent.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="description"
                        type="text"
                        class="form__text"
                        name="description"
                        placeholder=" "
                        value="{{ $rss->object_torrent->description }}"
                    />
                    <label class="form__label form__label--floating" for="description">
                        {{ __('torrent.torrent') }} {{ __('torrent.description') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="uploader"
                        type="text"
                        class="form__text"
                        name="uploader"
                        placeholder=" "
                        value="{{ $rss->object_torrent->uploader }}"
                    />
                    <label class="form__label form__label--floating" for="uploader">
                        {{ __('torrent.torrent') }} {{ __('torrent.uploader') }}
                    </label>
                </p>
                <div class="form__group--horizontal">
                    <p class="form__group">
                        <input
                            id="autotmdb"
                            class="form__text"
                            inputmode="numeric"
                            name="tmdb"
                            pattern="[0-9]*"
                            placeholder=" "
                            type="text"
                            value="{{ $rss->object_torrent->tmdb }}"
                        />
                        <label class="form__label form__label--floating" for="autotmdb">
                            TMDB ID
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="autoimdb"
                            class="form__text"
                            inputmode="numeric"
                            name="imdb"
                            pattern="[0-9]*"
                            placeholder=" "
                            type="text"
                            value="{{ $rss->object_torrent->imdb }}"
                        />
                        <label class="form__label form__label--floating" for="autoimdb">
                            IMDB ID
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="autotvdb"
                            class="form__text"
                            inputmode="numeric"
                            name="tvdb"
                            pattern="[0-9]*"
                            placeholder=" "
                            type="text"
                            value="{{ $rss->object_torrent->tvdb }}"
                        />
                        <label class="form__label form__label--floating" for="autotvdb">
                            TVDB ID
                        </label>
                    </p>
                    <p class="form__group">
                        <input type="hidden" name="mal" value="0" />
                        <input
                            id="automal"
                            class="form__text"
                            inputmode="numeric"
                            name="mal"
                            pattern="[0-9]*"
                            placeholder=" "
                            type="text"
                            value="{{ $rss->object_torrent->mal }}"
                        />
                        <label class="form__label form__label--floating" for="automal">
                            MAL ID
                        </label>
                    </p>
                </div>
                <div class="form__group--horizontal">
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.category') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($categories as $category)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                id="{{ $category->name }}"
                                                class="form__checkbox"
                                                name="categories[]"
                                                type="checkbox"
                                                value="{{ $category->id }}"
                                                @checked(is_array($rss->object_torrent->categories) && in_array((string)$category->id, $rss->object_torrent->categories, true))
                                            />
                                            {{ $category->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.type') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($types as $type)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                id="{{ $type->name }}"
                                                class="form__checkbox"
                                                name="types[]"
                                                type="checkbox"
                                                value="{{ $type->id }}"
                                                @checked(is_array($rss->object_torrent->types) && in_array((string)$type->id, $rss->object_torrent->types, true))
                                            />
                                            {{ $type->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.resolution') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($resolutions as $resolution)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                id="{{ $resolution->name }}"
                                                class="form__checkbox"
                                                name="resolutions[]"
                                                type="checkbox"
                                                value="{{ $resolution->id }}"
                                                @checked(is_array($rss->object_torrent->resolutions) && in_array((string)$resolution->id, $rss->object_torrent->resolutions, true))
                                            />
                                            {{ $resolution->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.genre') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($genres as $genre)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                id="{{ $genre->name }}"
                                                class="form__checkbox"
                                                name="genres[]"
                                                type="checkbox"
                                                value="{{ $genre->id }}"
                                                @checked(is_array($rss->object_torrent->genres) && in_array((string)$genre->id, $rss->object_torrent->genres, true))
                                            />
                                            {{ $genre->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.discounts') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="freeleech"
                                            class="form__checkbox"
                                            name="freeleech"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->freeleech)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-star text-gold"
                                        ></span>
                                        {{ __('torrent.freeleech') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="doubleupload"
                                            class="form__checkbox"
                                            name="doubleupload"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->doubleupload)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-gem text-green"
                                        ></span>
                                        {{ __('torrent.double-upload') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="featured"
                                            class="form__checkbox"
                                            name="featured"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->featured)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-certificate text-pink"
                                        ></span>
                                        {{ __('torrent.featured') }}
                                    </label>
                                </p>
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.special') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="highspeed"
                                            class="form__checkbox"
                                            name="highspeed"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->highspeed)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-tachometer text-red"
                                        ></span>
                                        {{ __('common.high-speeds') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="internal"
                                            class="form__checkbox"
                                            name="internal"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->internal)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-magic"
                                            style="color: #baaf92"
                                        ></span>
                                        {{ __('torrent.internal') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="personalrelease"
                                            class="form__checkbox"
                                            name="personalrelease"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->personalrelease)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-user-plus"
                                            style="color: #865be9"
                                        ></span>
                                        {{ __('torrent.personal-release') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="bookmark"
                                            class="form__checkbox"
                                            name="bookmark"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->bookmark)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-bookmark text-blue"
                                        ></span>
                                        {{ __('torrent.bookmark') }}
                                    </label>
                                </p>
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.health') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="alive"
                                            class="form__checkbox"
                                            name="alive"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->alive)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-smile text-green"
                                        ></span>
                                        {{ __('torrent.alive') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="dying"
                                            class="form__checkbox"
                                            name="dying"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->dying)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-meh text-orange"
                                        ></span>
                                        {{ __('torrent.dying-torrent') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            id="dead"
                                            class="form__checkbox"
                                            name="dead"
                                            type="checkbox"
                                            value="1"
                                            @checked($rss->object_torrent->dead)
                                        />
                                        <span
                                            class="{{ config('other.font-awesome') }} fa-frown text-red"
                                        ></span>
                                        {{ __('torrent.dead-torrent') }}
                                    </label>
                                </p>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.edit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
