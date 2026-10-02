<td class="torrent-search--grouped__overview">
    <div>
        <h3 class="torrent-search--grouped__name">
            <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}">
                {{ $torrent->name }}
            </a>
            @if ($torrent->resolution)
                <small class="form__hint">{{ $torrent->resolution->name }}</small>
            @endif
        </h3>
        @include('components.partials._torrent-icons')
    </div>
</td>

<td class="torrent-search--grouped__edit">
    @if (auth()->user()->group->is_editor || auth()->user()->group->is_modo || (auth()->id() === $torrent->user_id && ($torrent->status !== \App\Enums\ModerationStatus::APPROVED || now()->isBefore($torrent->created_at->addDay()))))
        <a
            href="{{ route('torrents.edit', ['id' => $torrent->id]) }}"
            title="{{ __('common.edit') }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-pencil-alt"></i>
        </a>
    @endif
</td>

<td class="torrent-search--grouped__bookmark">
    <button
        x-data="bookmark({{ $torrent->id }}, {{ Js::from($torrent->bookmarks_exists) }})"
        x-bind="button"
    >
        <i class="{{ config('other.font-awesome') }}" x-bind="icon"></i>
    </button>
</td>
<td class="torrent-search--grouped__download">
    @if (config('torrent.download_check_page') == 1)
        <a
            href="{{ route('download_check', ['id' => $torrent->id]) }}"
            title="{{ __('common.download-action') }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-download"></i>
        </a>
    @else
        <a
            href="{{ route('download', ['id' => $torrent->id]) }}"
            title="{{ __('common.download-action') }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-download"></i>
        </a>
    @endif
    @if (config('torrent.magnet') == 1)
        <a
            href="magnet:?dn={{ $torrent->name }}&xt=urn:btih:{{ bin2hex($torrent->info_hash) }}&as={{ route('torrent.download.rsskey', ['id' => $torrent->id, 'rsskey' => auth()->user()->rsskey]) }}&tr={{ route('announce', ['passkey' => auth()->user()->passkey]) }}&xl={{ $torrent->size }}"
            title="{{ __('common.magnet') }}"
        >
            <i class="{{ config('other.font-awesome') }} fa-magnet"></i>
        </a>
    @endif
</td>
<td class="torrent-search--grouped__size" data-label="{{ __('torrent.size') }}">
    <span title="{{ $torrent->size }} B">
        {{ $torrent->getSize() }}
    </span>
</td>
<td
    @class([
        'torrent-search--grouped__seeders',
        'torrent-activity-indicator--seeding' => $torrent->seeding,
    ])
    data-label="{{ __('torrent.seeders') }}"
    data-torrent-id="{{ $torrent->id }}"
    data-torrent-stat="seeding"
    @if ($torrent->seeding)
        title="{{ __('torrent.currently-seeding') }}"
    @endif
>
    <a class="torrent__seeder-count" href="{{ route('peers', ['id' => $torrent->id]) }}">
        {{ $torrent->seeders }}
    </a>
</td>
<td
    @class([
        'torrent-search--grouped__leechers',
        'torrent-activity-indicator--leeching' => $torrent->leeching,
    ])
    data-label="{{ __('torrent.leechers') }}"
    data-torrent-id="{{ $torrent->id }}"
    data-torrent-stat="leeching"
    @if ($torrent->leeching)
        title="{{ __('torrent.currently-leeching') }}"
    @endif
>
    <a class="torrent__leecher-count" href="{{ route('peers', ['id' => $torrent->id]) }}">
        {{ $torrent->leechers }}
    </a>
</td>
<td
    @class([
        'torrent-search--grouped__completed',
        'torrent-activity-indicator--completed' => $torrent->completed,
    ])
    data-label="{{ __('torrent.completed') }}"
    data-torrent-id="{{ $torrent->id }}"
    data-torrent-stat="completed"
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
<td class="torrent-search--grouped__age" data-label="{{ __('common.created_at') }}">
    <time datetime="{{ $torrent->created_at }}" title="{{ $torrent->created_at }}">
        {{ $torrent->created_at->diffForHumans() }}
    </time>
</td>
