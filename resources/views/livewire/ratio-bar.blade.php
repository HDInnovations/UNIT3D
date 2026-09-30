<ul class="top-nav__ratio-bar" x-bind:class="expanded && 'mobile'" wire:poll.10s>
    <li class="ratio-bar__uploaded" title="{{ __('common.uploaded') }}">
        <a href="{{ route('users.torrents.index', ['user' => $user]) }}">
            <i class="{{ config('other.font-awesome') }} fa-arrow-up" data-label="{{ __('common.uploaded').': ' }}"></i>
            {{ $user->formatted_uploaded }}
        </a>
    </li>
    <li class="ratio-bar__downloaded" title="{{ __('common.downloaded') }}">
        <a href="{{ route('users.history.index', ['user' => $user, 'downloaded' => 'include']) }}">
            <i class="{{ config('other.font-awesome') }} fa-arrow-down" data-label="{{ __('common.downloaded').': ' }}"></i>
            {{ $user->formatted_downloaded }}
        </a>
    </li>

    <li class="ratio-bar__seeding" title="{{ __('torrent.seeding') }}">
        <a href="{{ route('users.peers.index', ['user' => $user]) }}">
            <i class="{{ config('other.font-awesome') }} fa-upload" data-label="{{ __('torrent.seeding').': ' }}"></i>
            {{ $seeding }}
        </a>
    </li>
    <li class="ratio-bar__leeching" title="{{ __('torrent.leeching') }}">
        <a href="{{ route('users.peers.index', ['user' => $user, 'seeding' => 'exclude']) }}">
            <i class="{{ config('other.font-awesome') }} fa-download" data-label="{{ __('torrent.leeching').': ' }}"></i>
            {{ $leeching }}
        </a>
    </li>
    <li class="ratio-bar__buffer" title="{{ __('common.buffer') }}">
        <a href="{{ route('users.history.index', ['user' => $user]) }}">
            <i class="{{ config('other.font-awesome') }} fa-exchange" data-label="{{ __('common.buffer').': ' }}"></i>
            {{ $user->formatted_buffer }}
        </a>
    </li>
    <li class="ratio-bar__points" title="{{ __('user.my-bonus-points') }}">
        <a href="{{ route('users.earnings.index', ['user' => $user]) }}">
            <i class="{{ config('other.font-awesome') }} fa-coins" data-label="{{ __('user.my-bonus-points').': ' }}"></i>
            {{ $user->formatted_seedbonus }}
        </a>
    </li>
    <li class="ratio-bar__ratio" title="{{ __('common.ratio') }}">
        <a href="{{ route('users.history.index', ['user' => $user]) }}">
            <i class="{{ config('other.font-awesome') }} fa-sync-alt" data-label="{{ __('common.ratio').': ' }}"></i>
            {{ $user->formatted_ratio }}
        </a>
    </li>
    <li class="ratio-bar__tokens" title="{{ __('user.my-fl-tokens') }}">
        <a href="{{ route('users.show', ['user' => $user]) }}">
            <i class="{{ config('other.font-awesome') }} fa-star" data-label="{{ __('user.my-fl-tokens').': ' }}"></i>
            {{ $user->fl_tokens }}
        </a>
    </li>
</ul>
