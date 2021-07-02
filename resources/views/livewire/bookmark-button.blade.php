@if($this->isBookmarked)
	<button wire:click="destroy({{ $torrent->id }})" class="btn btn-sm btn-danger">
		<i class="{{ config('other.font-awesome') }} fa-bookmark"></i> @lang('torrent.unbookmark')
	</button>
@else
	<button wire:click="store({{ $torrent->id }})" class="btn btn-sm btn-primary">
		<i class="{{ config('other.font-awesome') }} fa-bookmark"></i> @lang('torrent.bookmark')
	</button>
@endif