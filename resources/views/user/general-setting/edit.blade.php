@extends('layout.with-main')

@section('title')
    <title>
        {{ $user->username }} - Settings - {{ __('common.members') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.settings') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-general-setting--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">General {{ __('user.settings') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('users.general_settings.update', ['user' => $user]) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <select id="locale" class="form__select" name="locale" required>
                        @foreach (App\Models\Language::allowed() as $code => $name)
                            <option
                                class="form__option"
                                value="{{ $code }}"
                                @selected(($user->settings?->locale ?? config('app.locale')) === $code)
                            >
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="locale">Language</label>
                </p>
                <fieldset class="form form__fieldset">
                    <legend class="form__legend">Style</legend>
                    <p class="form__group">
                        <select id="style" class="form__select" name="style" required>
                            <option
                                class="form__option"
                                value="0"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 0)
                            >
                                Light
                            </option>
                            <option
                                class="form__option"
                                value="1"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 1)
                            >
                                Galactic
                            </option>
                            <option
                                class="form__option"
                                value="2"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 2)
                            >
                                Dark Blue
                            </option>
                            <option
                                class="form__option"
                                value="3"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 3)
                            >
                                Dark Green
                            </option>
                            <option
                                class="form__option"
                                value="4"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 4)
                            >
                                Dark Pink
                            </option>
                            <option
                                class="form__option"
                                value="5"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 5)
                            >
                                Dark Purple
                            </option>
                            <option
                                class="form__option"
                                value="6"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 6)
                            >
                                Dark Red
                            </option>
                            <option
                                class="form__option"
                                value="7"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 7)
                            >
                                Dark Teal
                            </option>
                            <option
                                class="form__option"
                                value="8"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 8)
                            >
                                Dark Yellow
                            </option>
                            <option
                                class="form__option"
                                value="9"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 9)
                            >
                                Cosmic Void
                            </option>
                            <option
                                class="form__option"
                                value="10"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 10)
                            >
                                Nord
                            </option>
                            <option
                                class="form__option"
                                value="11"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 11)
                            >
                                Revel (Desktop only)
                            </option>
                            <option
                                class="form__option"
                                value="12"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 12)
                            >
                                Material Design 3 Light
                            </option>
                            <option
                                class="form__option"
                                value="13"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 13)
                            >
                                Material Design 3 Dark
                            </option>
                            <option
                                class="form__option"
                                value="15"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 15)
                            >
                                Material Design 3 Navy
                            </option>

                            <option
                                class="form__option"
                                value="14"
                                @selected(($user->settings?->style ?? config('other.default_style', 0)) === 14)
                            >
                                Material Design 3 Amoled
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="style">Theme</label>
                    </p>
                    <p class="form__group">
                        <input
                            id="custom_css"
                            class="form__text"
                            name="custom_css"
                            placeholder=" "
                            type="url"
                            value="{{ $user->settings?->custom_css }}"
                        />
                        <label class="form__label form__label--floating" for="custom_css">
                            External CSS Stylesheet (Stacks on top of above theme)
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="standalone_css"
                            class="form__text"
                            name="standalone_css"
                            placeholder=" "
                            type="url"
                            value="{{ $user->settings?->standalone_css }}"
                        />
                        <label class="form__label form__label--floating" for="standalone_css">
                            Standalone CSS Stylesheet (No site theme used)
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">Chat</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="censor" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="censor"
                                value="1"
                                @checked($user->settings?->censor)
                            />
                            Language Censor Chat
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form form__fieldset">
                    <legend class="form__legend">{{ __('user.homepage-blocks') }}</legend>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="news_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="news_visible"
                                value="1"
                                @checked($user->settings?->news_visible)
                            />
                            {{ __('user.homepage-block-news-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="chat_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="chat_visible"
                                value="1"
                                @checked($user->settings?->chat_visible)
                            />
                            {{ __('user.homepage-block-chat-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="featured_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="featured_visible"
                                value="1"
                                @checked($user->settings?->featured_visible)
                            />
                            {{ __('user.homepage-block-featured-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="random_media_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="random_media_visible"
                                value="1"
                                @checked($user->settings?->random_media_visible)
                            />
                            {{ __('user.homepage-block-random-media-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="poll_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="poll_visible"
                                value="1"
                                @checked($user->settings?->poll_visible)
                            />
                            {{ __('user.homepage-block-poll-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="top_torrents_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="top_torrents_visible"
                                value="1"
                                @checked($user->settings?->top_torrents_visible)
                            />
                            {{ __('user.homepage-block-top-torrents-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="top_users_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="top_users_visible"
                                value="1"
                                @checked($user->settings?->top_users_visible)
                            />
                            {{ __('user.homepage-block-top-users-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="latest_topics_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="latest_topics_visible"
                                value="1"
                                @checked($user->settings?->latest_topics_visible)
                            />
                            {{ __('user.homepage-block-latest-topics-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="latest_posts_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="latest_posts_visible"
                                value="1"
                                @checked($user->settings?->latest_posts_visible)
                            />
                            {{ __('user.homepage-block-latest-posts-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="latest_comments_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="latest_comments_visible"
                                value="1"
                                @checked($user->settings?->latest_comments_visible)
                            />
                            {{ __('user.homepage-block-latest-comments-visible') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input type="hidden" name="online_visible" value="0" />
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                name="online_visible"
                                value="1"
                                @checked($user->settings?->online_visible)
                            />
                            {{ __('user.homepage-block-online-visible') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form form__fieldset">
                    <legend class="form__legend">Torrent</legend>
                    <p class="form__group">
                        <select
                            id="torrent_layout"
                            class="form__select"
                            name="torrent_layout"
                            required
                        >
                            <option
                                class="form__option"
                                value="0"
                                @selected($user->settings === null || $user->settings?->torrent_layout === 0)
                            >
                                Torrent list
                            </option>
                            <option
                                class="form__option"
                                value="1"
                                @selected($user->settings?->torrent_layout === 1)
                            >
                                Torrent cards
                            </option>
                            <option
                                class="form__option"
                                value="2"
                                @selected($user->settings?->torrent_layout === 2)
                            >
                                Torrent groupings
                            </option>
                            <option
                                class="form__option"
                                value="3"
                                @selected($user->settings?->torrent_layout === 3)
                            >
                                Torrent posters
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="torrent_layout">
                            Default torrent layout
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="torrent_sort_field"
                            class="form__select"
                            name="torrent_sort_field"
                            required
                        >
                            <option
                                class="form__option"
                                value="bumped_at"
                                @selected($user->settings === null || $user->settings?->torrent_sort_field === 'bumped_at')
                            >
                                Most recently bumped
                            </option>
                            <option
                                class="form__option"
                                value="created_at"
                                @selected($user->settings?->torrent_sort_field === 'created_at')
                            >
                                Most recently uploaded
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="torrent_sort_field">
                            Default torrent sort field
                        </label>
                    </p>
                    <div>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="show_poster" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="show_poster"
                                    value="1"
                                    @checked($user->settings?->show_poster)
                                />
                                Show Posters On Torrent List View
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="torrent_search_autofocus" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="torrent_search_autofocus"
                                    value="1"
                                    @checked($user->settings?->torrent_search_autofocus)
                                />
                                Autofocus torrent search on page load
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    type="hidden"
                                    name="unbookmark_torrents_on_completion"
                                    value="0"
                                />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="unbookmark_torrents_on_completion"
                                    value="1"
                                    @checked($user->settings?->unbookmark_torrents_on_completion)
                                />
                                Automatically unbookmark torrents upon completion
                            </label>
                        </p>
                    </div>
                </fieldset>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.save') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
