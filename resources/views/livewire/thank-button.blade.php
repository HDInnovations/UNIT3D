@php($thanksCount = $torrent->thanks_count ?? 0)

@if ($iconOnly)
    <button
        wire:click="store"
        class="form__standard-icon-button"
        title="{{ __('torrent.thank') }} ({{ $thanksCount }})"
        aria-label="{{ __('torrent.thank') }} ({{ $thanksCount }})"
    >
        <i class="{{ config('other.font-awesome') }} fa-heart text-pink"></i>
    </button>
@else
    <button
        wire:click="store"
        class="form__button form__button--outlined form__button--centered"
    >
        <i class="{{ config('other.font-awesome') }} fa-heart text-pink"></i>
        {{ __('torrent.thank') }} ({{ $thanksCount }})
    </button>
@endif
