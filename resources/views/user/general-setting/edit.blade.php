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
                        @foreach (App\Helpers\Language::allowed() as $code => $name)
                            <option
                                class="form__option"
                                value="{{ $code }}"
                                @selected($user->settings->locale === $code)
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
                                @selected($user->settings->style === 0)
                            >
                                Light
                            </option>
                            <option
                                class="form__option"
                                value="1"
                                @selected($user->settings->style === 1)
                            >
                                Galactic
                            </option>
                            <option
                                class="form__option"
                                value="2"
                                @selected($user->settings->style === 2)
                            >
                                Dark blue
                            </option>
                            <option
                                class="form__option"
                                value="3"
                                @selected($user->settings->style === 3)
                            >
                                Dark green
                            </option>
                            <option
                                class="form__option"
                                value="4"
                                @selected($user->settings->style === 4)
                            >
                                Dark pink
                            </option>
                            <option
                                class="form__option"
                                value="5"
                                @selected($user->settings->style === 5)
                            >
                                Dark purple
                            </option>
                            <option
                                class="form__option"
                                value="6"
                                @selected($user->settings->style === 6)
                            >
                                Dark red
                            </option>
                            <option
                                class="form__option"
                                value="7"
                                @selected($user->settings->style === 7)
                            >
                                Dark teal
                            </option>
                            <option
                                class="form__option"
                                value="8"
                                @selected($user->settings->style === 8)
                            >
                                Dark yellow
                            </option>
                            <option
                                class="form__option"
                                value="9"
                                @selected($user->settings->style === 9)
                            >
                                Cosmic void
                            </option>
                            <option
                                class="form__option"
                                value="10"
                                @selected($user->settings->style === 10)
                            >
                                Nord
                            </option>
                            <option
                                class="form__option"
                                value="11"
                                @selected($user->settings->style === 11)
                            >
                                Revel (Desktop only)
                            </option>
                            <option
                                class="form__option"
                                value="12"
                                @selected($user->settings->style === 12)
                            >
                                Material design 3 light
                            </option>
                            <option
                                class="form__option"
                                value="13"
                                @selected($user->settings->style === 13)
                            >
                                Material design 3 dark
                            </option>
                            <option
                                class="form__option"
                                value="15"
                                @selected($user->settings->style === 15)
                            >
                                Material design 3 navy
                            </option>

                            <option
                                class="form__option"
                                value="14"
                                @selected($user->settings->style === 14)
                            >
                                Material design 3 amoled
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
                            value="{{ $user->settings->custom_css }}"
                        />
                        <label class="form__label form__label--floating" for="custom_css">
                            External CSS stylesheet (stacks on top of above theme)
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="standalone_css"
                            class="form__text"
                            name="standalone_css"
                            placeholder=" "
                            type="url"
                            value="{{ $user->settings->standalone_css }}"
                        />
                        <label class="form__label form__label--floating" for="standalone_css">
                            Standalone CSS stylesheet (no site theme used)
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
                                @checked($user->settings->censor)
                            />
                            Language censor chat
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form form__fieldset">
                    <legend class="form__legend">{{ __('user.homepage-blocks') }}</legend>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Block visibility</legend>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="news_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="news_block_visible"
                                    value="1"
                                    @checked($user->settings->news_block_visible)
                                />
                                {{ __('user.homepage-block-news-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="chat_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="chat_block_visible"
                                    value="1"
                                    @checked($user->settings->chat_block_visible)
                                />
                                {{ __('user.homepage-block-chat-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="featured_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="featured_block_visible"
                                    value="1"
                                    @checked($user->settings->featured_block_visible)
                                />
                                {{ __('user.homepage-block-featured-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="random_media_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="random_media_block_visible"
                                    value="1"
                                    @checked($user->settings->random_media_block_visible)
                                />
                                {{ __('user.homepage-block-random-media-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="poll_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="poll_block_visible"
                                    value="1"
                                    @checked($user->settings->poll_block_visible)
                                />
                                {{ __('user.homepage-block-poll-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="top_torrents_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="top_torrents_block_visible"
                                    value="1"
                                    @checked($user->settings->top_torrents_block_visible)
                                />
                                {{ __('user.homepage-block-top-torrents-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="top_users_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="top_users_block_visible"
                                    value="1"
                                    @checked($user->settings->top_users_block_visible)
                                />
                                {{ __('user.homepage-block-top-users-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="latest_topics_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="latest_topics_block_visible"
                                    value="1"
                                    @checked($user->settings->latest_topics_block_visible)
                                />
                                {{ __('user.homepage-block-latest-topics-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="latest_posts_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="latest_posts_block_visible"
                                    value="1"
                                    @checked($user->settings->latest_posts_block_visible)
                                />
                                {{ __('user.homepage-block-latest-posts-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    type="hidden"
                                    name="latest_comments_block_visible"
                                    value="0"
                                />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="latest_comments_block_visible"
                                    value="1"
                                    @checked($user->settings->latest_comments_block_visible)
                                />
                                {{ __('user.homepage-block-latest-comments-visible') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="online_block_visible" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="online_block_visible"
                                    value="1"
                                    @checked($user->settings->online_block_visible)
                                />
                                {{ __('user.homepage-block-online-visible') }}
                            </label>
                        </p>
                    </fieldset>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Block order</legend>
                        <ul
                            x-data="{
            blocks: [
                @foreach ([
                    'news' => __('blocks.check-news'),
                    'chat' => __('blocks.chatbox'),
                    'featured' => __('blocks.featured-torrents'),
                    'random_media' => 'Random media',
                    'poll' => 'Polls',
                    'top_torrents' => __('blocks.top-torrents'),
                    'top_users' => 'Top users',
                    'latest_topics' => __('blocks.latest-topics'),
                    'latest_posts' => __('blocks.latest-posts'),
                    'latest_comments' => __('blocks.latest-comments'),
                    'online' => 'Online users'
                ] as $block => $label)
                    {
                        key: '{{ $block }}',
                        label: '{{ $label }}',
                        position: {{ (int) $user->settings->{$block . '_block_position'} }},
                    },
                @endforeach
            ].sort((a, b) => a.position - b.position),
            dragging: null,
            dragOver: null,
            move(from, to) {
                if (from === to) return;
                const moved = this.blocks.splice(from, 1)[0];
                this.blocks.splice(to, 0, moved);
                this.blocks.forEach((block, index) => block.position = index);
            }
        }"
                            class="order__list"
                            style="
                                padding-inline-start: 0;
                                margin-block-start: 0;
                                margin-block-end: 0;
                            "
                        >
                            <template x-for="(block, index) in blocks" :key="block.key">
                                <li
                                    class="order__item"
                                    :data-block="block.key"
                                    draggable="true"
                                    @dragstart="dragging = index"
                                    @dragover.prevent="dragOver = index"
                                    @dragleave="dragOver = null"
                                    @drop="move(dragging, index); dragging = null; dragOver = null"
                                    :class="{'drag-over': dragOver === index}"
                                    style="
                                        cursor: move;
                                        user-select: none;
                                        padding: 4px 0;
                                        list-style: none;
                                    "
                                >
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-arrows-alt"
                                    ></i>
                                    <span x-text="block.label"></span>
                                    <input
                                        type="hidden"
                                        :name="block.key + '_block_position'"
                                        :value="block.position"
                                    />
                                </li>
                            </template>
                        </ul>
                        <small class="text-info">Drag and drop to reorder blocks.</small>
                    </fieldset>
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
                                @selected($user->settings->torrent_layout === 0)
                            >
                                Torrent list
                            </option>
                            <option
                                class="form__option"
                                value="1"
                                @selected($user->settings->torrent_layout === 1)
                            >
                                Torrent cards
                            </option>
                            <option
                                class="form__option"
                                value="2"
                                @selected($user->settings->torrent_layout === 2)
                            >
                                Torrent groupings
                            </option>
                            <option
                                class="form__option"
                                value="3"
                                @selected($user->settings->torrent_layout === 3)
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
                                @selected($user->settings->torrent_sort_field === 'bumped_at')
                            >
                                Most recently bumped
                            </option>
                            <option
                                class="form__option"
                                value="created_at"
                                @selected($user->settings->torrent_sort_field === 'created_at')
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
                                    @checked($user->settings->show_poster)
                                />
                                Show posters on torrent list view
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
                                    @checked($user->settings->torrent_search_autofocus)
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
                                    @checked($user->settings->unbookmark_torrents_on_completion)
                                />
                                Automatically unbookmark torrents upon completion
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input type="hidden" name="show_adult_content" value="0" />
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="show_adult_content"
                                    value="1"
                                    @checked($user->settings->show_adult_content)
                                />
                                {{ __('user.show-adult-content') }}
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
