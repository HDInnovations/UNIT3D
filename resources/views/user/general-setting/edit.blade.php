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
        <h2 class="panel__heading">{{ __('user.general-settings') }}</h2>
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
                    <label class="form__label form__label--floating" for="locale">{{ __('common.language') }}</label>
                </p>
                <fieldset class="form form__fieldset">
                    <legend class="form__legend">{{ __('member-interface.settings.style') }}</legend>
                    <p class="form__group">
                        <select id="style" class="form__select" name="style" required>
                            @foreach (\App\Enums\Theme::grouped() as $group => $themes)
                                <optgroup label="{{ $group }}">
                                    @foreach ($themes as $theme)
                                        <option
                                            class="form__option"
                                            value="{{ $theme->value }}"
                                            @selected($user->settings->style === $theme->value)
                                        >
                                            {{ $theme->label() }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="style">{{ __('member-interface.settings.style') }}</label>
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
                            {{ __('member-interface.settings.external-css') }}
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
                            {{ __('member-interface.settings.standalone-css') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('common.chat') }}</legend>
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
                            {{ __('member-interface.settings.language-censor-chat') }}
                        </label>
                    </p>
                </fieldset>
                <fieldset class="form form__fieldset">
                    <legend class="form__legend">{{ __('user.homepage-blocks') }}</legend>
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('member-interface.settings.block-visibility') }}</legend>
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
                        <legend class="form__legend">{{ __('member-interface.settings.block-order') }}</legend>
                        <ul
                            x-data="{
            blocks: [
                @foreach ([
                    'news' => __('blocks.check-news'),
                    'chat' => __('blocks.chatbox'),
                    'featured' => __('blocks.featured-torrents'),
                    'random_media' => __('member-interface.settings.random-media'),
                    'poll' => __('member-interface.settings.polls'),
                    'top_torrents' => __('blocks.top-torrents'),
                    'top_users' => __('member-interface.settings.top-users'),
                    'latest_topics' => __('blocks.latest-topics'),
                    'latest_posts' => __('blocks.latest-posts'),
                    'latest_comments' => __('blocks.latest-comments'),
                    'online' => __('member-interface.settings.online-users')
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
                        <small class="text-info">{{ __('member-interface.settings.drag-drop-reorder') }}</small>
                    </fieldset>
                </fieldset>
                <fieldset class="form form__fieldset">
                    <legend class="form__legend">{{ __('member-interface.settings.torrent') }}</legend>
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
                                {{ __('member-interface.settings.torrent-list') }}
                            </option>
                            <option
                                class="form__option"
                                value="1"
                                @selected($user->settings->torrent_layout === 1)
                            >
                                {{ __('member-interface.settings.torrent-cards') }}
                            </option>
                            <option
                                class="form__option"
                                value="2"
                                @selected($user->settings->torrent_layout === 2)
                            >
                                {{ __('member-interface.settings.torrent-groupings') }}
                            </option>
                            <option
                                class="form__option"
                                value="3"
                                @selected($user->settings->torrent_layout === 3)
                            >
                                {{ __('member-interface.settings.torrent-posters') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="torrent_layout">
                            {{ __('member-interface.settings.default-torrent-layout') }}
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
                                {{ __('member-interface.settings.most-recently-bumped') }}
                            </option>
                            <option
                                class="form__option"
                                value="created_at"
                                @selected($user->settings->torrent_sort_field === 'created_at')
                            >
                                {{ __('member-interface.settings.most-recently-uploaded') }}
                            </option>
                        </select>
                        <label class="form__label form__label--floating" for="torrent_sort_field">
                            {{ __('member-interface.settings.default-torrent-sort-field') }}
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
                                {{ __('member-interface.settings.show-posters-torrent-list') }}
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
                                {{ __('member-interface.settings.autofocus-torrent-search') }}
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
                                {{ __('member-interface.settings.unbookmark-on-completion') }}
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
