<button wire:click="store({{ $torrent->id }})" class="btn btn-sm btn-primary">
	<i class="{{ config('other.font-awesome') }} fa-heart text-pink"></i> @lang('torrent.thank') ({{ $torrent->thanks()->count() }})
</button>