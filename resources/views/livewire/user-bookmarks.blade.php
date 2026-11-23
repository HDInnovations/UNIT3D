<section class="panelV2 user-bookmarks">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('user.bookmarks') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <a
                    class="form__button form__button--text"
                    href="{{ route('torrents.index', ['bookmarked' => 1]) }}"
                >
                    Torrents list
                </a>
            </div>
        </div>
    </header>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <th
                    class="user-bookmarks__name-header"
                    wire:click="sortBy('torrents.name')"
                    role="columnheader button"
                >
                    {{ __('torrent.name') }}
                    @include('livewire.includes._sort-icon', ['field' => 'torrents.name'])
                </th>
                <th
                    class="user-bookmarks__size-header"
                    wire:click="sortBy('torrents.size')"
                    role="columnheader button"
                >
                    {{ __('torrent.size') }}
                    @include('livewire.includes._sort-icon', ['field' => 'torrents.size'])
                </th>
                <th
                    class="user-bookmarks__seeders-header"
                    wire:click="sortBy('torrents.seeders')"
                    role="columnheader button"
                    title="{{ __('torrent.seeders') }}"
                >
                    <i class="fas fa-arrow-alt-circle-up"></i>
                    @include('livewire.includes._sort-icon', ['field' => 'torrents.seeders'])
                </th>
                <th
                    class="user-bookmarks__leechers-header"
                    wire:click="sortBy('torrents.leechers')"
                    role="columnheader button"
                    title="{{ __('torrent.leechers') }}"
                >
                    <i class="fas fa-arrow-alt-circle-down"></i>
                    @include('livewire.includes._sort-icon', ['field' => 'torrents.leechers'])
                </th>
                <th
                    class="user-bookmarks__times-completed-header"
                    wire:click="sortBy('torrents.times_completed')"
                    role="columnheader button"
                    title="{{ __('torrent.completed') }}"
                >
                    <i class="fas fa-check-circle"></i>
                    @include('livewire.includes._sort-icon', ['field' => 'torrents.times_completed'])
                </th>
                <th
                    class="user-bookmarks__created-at-header"
                    wire:click="sortBy('torrents.created_at')"
                    role="columnheader button"
                >
                    Uploaded at
                    @include('livewire.includes._sort-icon', ['field' => 'torrents.created_at'])
                </th>
                <th
                    class="user-bookmarks__created-at-header"
                    wire:click="sortBy('bookmarks.created_at')"
                    role="columnheader button"
                >
                    Bookmarked at
                    @include('livewire.includes._sort-icon', ['field' => 'bookmarks.created_at'])
                </th>
                <th class="user-bookmarks__actions-header">
                    {{ __('common.actions') }}
                </th>
            </thead>
            <tbody>
                @foreach ($bookmarks as $bookmark)
                    <tr>
                        <td class="user-bookmarks__name">
                            <a
                                href="{{ route('torrents.show', ['id' => $bookmark->torrent_id]) }}"
                            >
                                {{ $bookmark->name }}
                            </a>
                        </td>
                        <td class="user-bookmarks__size">
                            {{ App\Helpers\StringHelper::formatBytes($bookmark->size) }}
                        </td>
                        <td
                            @class([
                                'user-bookmarks__seeders',
                                'torrent-activity-indicator--seeding' => $bookmark->seeding,
                            ])
                            @if ($bookmark->seeding)
                                title="{{ __('torrent.currently-seeding') }}"
                            @endif
                        >
                            <a
                                class="torrent__seeder-count"
                                href="{{ route('peers', ['id' => $bookmark->torrent_id]) }}"
                            >
                                {{ $bookmark->seeders }}
                            </a>
                        </td>
                        <td
                            @class([
                                'user-bookmarks__leechers',
                                'torrent-activity-indicator--leeching' => $bookmark->leeching,
                            ])
                            @if ($bookmark->leeching)
                                title="{{ __('torrent.currently-leeching') }}"
                            @endif
                        >
                            <a
                                class="torrent__leecher-count"
                                href="{{ route('peers', ['id' => $bookmark->torrent_id]) }}"
                            >
                                {{ $bookmark->leechers }}
                            </a>
                        </td>
                        <td
                            @class([
                                'user-bookmarks__times_completed',
                                'torrent-activity-indicator--completed' => $bookmark->completed,
                            ])
                            @if ($bookmark->completed)
                                title="{{ __('torrent.completed') }}"
                            @endif
                        >
                            <a
                                class="torrent__times-completed-count"
                                href="{{ route('history', ['id' => $bookmark->torrent_id]) }}"
                            >
                                {{ $bookmark->times_completed }}
                            </a>
                        </td>
                        <td class="user-bookmarks__created-at">
                            <time
                                datetime="{{ $bookmark->torrent_created_at }}"
                                title="{{ $bookmark->torrent_created_at }}"
                            >
                                {{ $bookmark->torrent_created_at->diffForHumans() }}
                            </time>
                        </td>
                        <td class="user-bookmarks__created-at">
                            <time
                                datetime="{{ $bookmark->bookmark_created_at }}"
                                title="{{ $bookmark->bookmark_created_at }}"
                            >
                                {{ $bookmark->bookmark_created_at->diffForHumans() }}
                            </time>
                        </td>
                        <td class="user-bookmarks__actions">
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    <button
                                        class="form__standard-icon-button"
                                        x-data="bookmark({{ $bookmark->torrent_id }}, true)"
                                        x-bind="button"
                                    >
                                        <i
                                            class="{{ config('other.font-awesome') }}"
                                            x-bind="icon"
                                        ></i>
                                    </button>
                                </li>
                                <li class="data-table__action">
                                    @if (config('torrent.download_check_page') == 1)
                                        <a
                                            class="form__standard-icon-button"
                                            href="{{ route('download_check', ['id' => $bookmark->torrent_id]) }}"
                                            title="{{ __('common.download') }}"
                                        >
                                            <i
                                                class="{{ config('other.font-awesome') }} fa-download"
                                            ></i>
                                        </a>
                                    @else
                                        <a
                                            class="form__standard-icon-button"
                                            href="{{ route('download', ['id' => $bookmark->torrent_id]) }}"
                                            title="{{ __('common.download') }}"
                                        >
                                            <i
                                                class="{{ config('other.font-awesome') }} fa-download"
                                            ></i>
                                        </a>
                                    @endif
                                </li>
                            </menu>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $bookmarks->links('partials.pagination') }}
</section>
