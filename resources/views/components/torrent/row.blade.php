@props([
    'torrent',
    'meta',
    'personalFreeleech',
])

<tr
    @class([
        'torrent-search--list__row' => auth()->user()->settings->show_poster,
        'torrent-search--list__no-poster-row' => ! auth()->user()->settings->show_poster,
        'torrent-search--list__sticky-row' => $torrent->sticky,
    ])
    data-torrent-id="{{ $torrent->id }}"
    data-igdb-id="{{ $torrent->igdb }}"
    data-imdb-id="{{ $torrent->imdb }}"
    data-tmdb-id="{{ $torrent->tmdb }}"
    data-tvdb-id="{{ $torrent->tvdb }}"
    data-mal-id="{{ $torrent->mal }}"
    data-category-id="{{ $torrent->category_id }}"
    data-type-id="{{ $torrent->type_id }}"
    data-resolution-id="{{ $torrent->resolution_id }}"
    wire:key="torrent-search-row-{{ $torrent->id }}"
>
    @if (auth()->user()->settings->show_poster)
        <td
            class="torrent-search--list__poster"
            x-data="{ metaPopup: false }"
            @mouseenter="metaPopup = true"
            @mouseleave="metaPopup = false"
        >
            <a
                href="{{
                    match (true) {
                        $torrent->tmdb_movie_id !== null => route('torrents.similar', ['category_id' => $torrent->category_id, 'tmdb' => $torrent->tmdb_movie_id]),
                        $torrent->tmdb_tv_id !== null => route('torrents.similar', ['category_id' => $torrent->category_id, 'tmdb' => $torrent->tmdb_tv_id]),
                        $torrent->igdb !== null => route('torrents.similar', ['category_id' => $torrent->category_id, 'tmdb' => $torrent->igdb]),
                        default => '#',
                    }
                }}"
            >
                @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                    <img
                        src="{{ isset($meta->poster) ? tmdb_image('poster_small', $meta->poster) : 'https://via.placeholder.com/90x135' }}"
                        class="torrent-search--list__poster-img"
                        loading="lazy"
                        alt="{{ __('torrent.similar') }}"
                    />
                    @include('torrent.partials.meta-popup', ['meta' => $meta])
                @endif

                @if ($torrent->category->game_meta)
                    <img
                        style="height: 80px"
                        src="{{ isset($meta->cover_image_id) ? 'https://images.igdb.com/igdb/image/upload/t_cover_small_2x/' . $meta->cover_image_id . '.png' : 'https://via.placeholder.com/90x135' }}"
                        class="torrent-search--list__poster-img"
                        loading="lazy"
                        alt="{{ __('torrent.similar') }}"
                    />
                @endif

                @if ($torrent->category->music_meta)
                    <img
                        src="https://via.placeholder.com/90x135"
                        class="torrent-search--list__poster-img"
                        loading="lazy"
                        alt="{{ __('torrent.similar') }}"
                    />
                @endif

                @if ($torrent->category->no_meta)
                    @if (Storage::disk('torrent-covers')->exists("torrent-cover_$torrent->id.jpg"))
                        <img
                            src="{{ route('authenticated_images.torrent_cover', ['id' => $torrent->id]) }}"
                            class="torrent-search--list__poster-img"
                            loading="lazy"
                            alt="{{ __('torrent.similar') }}"
                        />
                    @else
                        <img
                            src="https://via.placeholder.com/400x600"
                            class="torrent-search--list__poster-img"
                            loading="lazy"
                            alt="{{ __('torrent.similar') }}"
                        />
                    @endif
                @endif
            </a>
        </td>
    @endif

    <td class="torrent-search--list__format">
        <div>
            <div class="torrent-search--list__category">
                @if ($torrent->category->image !== null)
                    <img
                        src="{{ route('authenticated_images.category_image', ['category' => $torrent->category]) }}"
                        title="{{ $torrent->category->name }} {{ strtolower(__('torrent.torrent')) }}"
                        alt="{{ $torrent->category->name }}"
                        loading="lazy"
                        @style([
                            'height: 32px',
                            'padding-top: 1px' => $torrent->category->movie_meta || $torrent->category->tv_meta,
                            'padding-top: 12px' => ! ($torrent->category->movie_meta || $torrent->category->tv_meta),
                        ])
                    />
                @else
                    <i
                        class="{{ $torrent->category->icon }} category__icon"
                        @style([
                            'font-size: 24px',
                            'padding-top: 1px' => $torrent->category->movie_meta || $torrent->category->tv_meta,
                            'padding-top: 12px' => ! ($torrent->category->movie_meta || $torrent->category->tv_meta),
                        ])
                    ></i>
                @endif
            </div>
            <div class="torrent-search--list__resolution-and-type">
                @if ($torrent->category->movie_meta || $torrent->category->tv_meta)
                    <span class="torrent-search--list__resolution">
                        {{ $torrent->resolution->name ?? 'No res' }}
                    </span>
                @endif

                <span class="torrent-search--list__type">
                    {{ $torrent->type->name }}
                </span>
            </div>
        </div>
    </td>
    <td class="torrent-search--list__overview">
        <div>
            <a
                class="torrent-search--list__name"
                href="{{ route('torrents.show', ['id' => $torrent->id]) }}"
            >
                {{ $torrent->name }}
            </a>
            <x-user-tag
                class="torrent-search--list__uploader"
                :user="$torrent->user"
                :anon="$torrent->anon"
            />
            @include('components.partials._torrent-icons')
        </div>
    </td>
    <td class="torrent-search--list__buttons">
        <div>
            @if (auth()->user()->group->is_editor || auth()->user()->group->is_modo || auth()->id() === $torrent->user_id)
                <a
                    class="torrent-search--list__edit form__standard-icon-button"
                    href="{{ route('torrents.edit', ['id' => $torrent->id]) }}"
                    title="{{ __('common.edit') }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-pencil-alt"></i>
                </a>
            @endif

            <button
                class="form__standard-icon-button"
                x-data="bookmark({{ $torrent->id }}, {{ Js::from($torrent->bookmarks_exists) }})"
                x-bind="button"
            >
                <i class="{{ config('other.font-awesome') }}" x-bind="icon"></i>
            </button>

            @if (config('torrent.download_check_page'))
                <a
                    class="torrent-search--list__file form__standard-icon-button"
                    href="{{ route('download_check', ['id' => $torrent->id]) }}"
                    title="{{ __('common.download') }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-download"></i>
                </a>
            @else
                <a
                    class="torrent-search--list__file form__standard-icon-button"
                    href="{{ route('download', ['id' => $torrent->id]) }}"
                    title="{{ __('common.download') }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-download"></i>
                </a>
            @endif
            @if (config('torrent.magnet'))
                <a
                    class="torrent-search--list__magnet form__contained-icon-button form__contained-icon-button--filled"
                    href="magnet:?dn={{ $torrent->name }}&xt=urn:btih:{{ bin2hex($torrent->info_hash) }}&as={{ route('torrent.download.rsskey', ['id' => $torrent->id, 'rsskey' => auth()->user()->rsskey]) }}&tr={{ route('announce', ['passkey' => auth()->user()->passkey]) }}&xl={{ $torrent->size }}"
                    download
                    title="{{ __('common.magnet') }}"
                >
                    <i class="{{ config('other.font-awesome') }} fa-magnet"></i>
                </a>
            @endif
        </div>
    </td>

    @if ($torrent->category->game_meta)
        <td
            class="torrent-search--list__rating {{ rating_color($meta->rating ?? 0) ?? 'text-white' }}"
        >
            <span>{{ round($meta->rating ?? 0) }}%</span>
        </td>
    @elseif ($torrent->category->movie_meta || $torrent->category->tv_meta)
        <td class="torrent-search--list__rating" title="{{ $meta->vote_count ?? 0 }} votes">
            <span class="{{ rating_color($meta->vote_average ?? 0) ?? 'text-white' }}">
                {{ round(($meta->vote_average ?? 0) * 10) }}%
            </span>
        </td>
    @else
        <td class="torrent-search--list__rating">N/A</td>
    @endif
    <td class="torrent-search--list__size">
        <span>{{ $torrent->getSize() }}</span>
    </td>
    <td
        @class([
            'torrent-search--list__seeders',
            'torrent-activity-indicator--seeding' => $torrent->seeding,
        ])
        @if ($torrent->seeding)
            title="{{ __('torrent.currently-seeding') }}"
        @endif
    >
        <a class="torrent__seeder-count" href="{{ route('peers', ['id' => $torrent->id]) }}">
            {{ $torrent->seeds_count ?? $torrent->seeders }}
        </a>
    </td>
    <td
        @class([
            'torrent-search--list__leechers',
            'torrent-activity-indicator--leeching' => $torrent->leeching,
        ])
        @if ($torrent->leeching)
            title="{{ __('torrent.currently-leeching') }}"
        @endif
    >
        <a class="torrent__leecher-count" href="{{ route('peers', ['id' => $torrent->id]) }}">
            {{ $torrent->leeches_count ?? $torrent->leechers }}
        </a>
    </td>
    <td
        @class([
            'torrent-search--list__completed',
            'torrent-activity-indicator--completed' => $torrent->completed,
        ])
        @if ($torrent->completed)
            title="{{ __('torrent.completed') }}"
        @endif
    >
        <a
            class="torrent__times-completed-count"
            href="{{ route('history', ['id' => $torrent->id]) }}"
        >
            {{ $torrent->times_completed }}
        </a>
    </td>
    <td class="torrent-search--list__age">
        <time datetime="{{ $torrent->created_at }}" title="{{ $torrent->created_at }}">
            {{ $torrent->created_at->diffForHumans() }}
        </time>
    </td>
</tr>
