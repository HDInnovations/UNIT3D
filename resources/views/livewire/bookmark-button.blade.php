<button
    @if ($this->isBookmarked)
        wire:click="destroy({{ $torrent->id }})"
        class="form__button form__button--filled form__button--centered"
    @else
        wire:click="store({{ $torrent->id }})"
        class="form__button form__button--outlined form__button--centered"
    @endif
>
    @if ($this->isBookmarked)
        <i class="{{ config('other.font-awesome') }} fa-bookmark-slash"></i>
        {{ __('torrent.unbookmark') }} ({{ $bookmarksCount }})
    @else
        <i class="{{ config('other.font-awesome') }} fa-bookmark"></i>
        {{ __('torrent.bookmark') }} ({{ $bookmarksCount }})
    @endif
</button>
