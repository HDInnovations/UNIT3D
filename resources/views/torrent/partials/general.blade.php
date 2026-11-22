<ul class="torrent__tags">
    <li class="torrent__category">
        <a
            class="torrent__category-link"
            href="{{ route('torrents.index', ['categoryIds' => [$torrent->category->id]]) }}"
        >
            {{ $torrent->category->name }}
        </a>
    </li>
    @if ($torrent->resolution)
        <li class="torrent__resolution">
            <a
                class="torrent__resolution-link"
                href="{{ route('torrents.index', ['resolutionIds' => [$torrent->category->id]]) }}"
            >
                {{ $torrent->resolution->name }}
            </a>
        </li>
    @endif

    @isset($torrent->region)
        <li class="torrent__region">
            <a
                class="torrent__region-link"
                href="{{ route('torrents.index', ['regionIds' => [$torrent->region->id]]) }}"
            >
                {{ $torrent->region->name }}
            </a>
        </li>
    @endisset

    @isset($torrent->type)
        <li class="torrent__type">
            <a
                class="torrent__type-link"
                href="{{ route('torrents.index', ['typeIds' => [$torrent->type->id]]) }}"
            >
                {{ $torrent->type->name }}
            </a>
        </li>
    @endisset

    @isset($torrent->distributor)
        <li class="torrent__distributor">
            <a
                class="torrent__distributor-link"
                href="{{ route('torrents.index', ['distributorIds' => [$torrent->distributor->id]]) }}"
            >
                {{ $torrent->distributor->name }}
            </a>
        </li>
    @endisset

    <li class="torrent__size">
        <span class="torrent__size-link" title="{{ $torrent->size }}&#x202F;B">
            {{ $torrent->getSize() }}
        </span>
    </li>
    <li
        @class([
            'torrent__seeders',
            'torrent-activity-indicator--seeding' => $torrent->seeding,
        ])
    >
        <a
            class="torrent__seeders-link torrent__seeder-count"
            href="{{ route('peers', ['id' => $torrent->id]) }}"
            title="{{ $torrent->seeds_count }} {{ __('torrent.seeders') }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-arrow-up"></i>
            {{ $torrent->seeds_count }}
        </a>
    </li>
    <li
        @class([
            'torrent__leechers',
            'torrent-activity-indicator--leeching' => $torrent->leeching,
        ])
    >
        <a
            class="torrent__leechers-link torrent__leecher-count"
            href="{{ route('peers', ['id' => $torrent->id]) }}"
            title="{{ $torrent->leeches_count }} {{ __('torrent.leechers') }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-arrow-down"></i>
            {{ $torrent->leeches_count }}
        </a>
    </li>
    <li
        @class([
            'torrent__completed',
            'torrent-activity-indicator--completed' => $torrent->completed,
        ])
    >
        <a
            class="torrent__completed-link torrent__times-completed-count"
            href="{{ route('history', ['id' => $torrent->id]) }}"
            title="{{ $torrent->times_completed }} {{ __('torrent.times') }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-check"></i>
            {{ $torrent->times_completed }}
        </a>
    </li>
    <li class="torrent__uploader">
        <x-user-tag :user="$torrent->user" :anon="$torrent->anon" />
    </li>
    <li class="torrent__uploaded-at">
        <time datetime="{{ $torrent->created_at }}" title="{{ $torrent->created_at }}">
            {{ $torrent->created_at->diffForHumans() }}
        </time>
    </li>
    @if ($torrent->seeders === 0)
        <li class="torrent__activity">
            <span class="torrent__activity-link">
                {{ __('torrent.last-seed-activity') }}:
                {{ $torrent->history_max_updated_at ?? __('common.unknown') }}
            </span>
        </li>
    @endif

    <li>
        @include('components.partials._torrent-icons', ['personalFreeleech' => $personal_freeleech])
    </li>
</ul>
