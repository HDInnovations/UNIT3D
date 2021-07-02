<div>
	<div class="mb-10">
		<input type="text" wire:model="search" class="form-control" placeholder="Search By Name"/>
	</div>

	<div class="blocks">
		@foreach ($networks as $network)
			<a href="{{ route('mediahub.networks.show', ['id' => $network->id]) }}" style="padding: 0 2px;">
				<div class="general media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
					<h2 class="text-bold">
						@if(isset($network->logo))
						<img src="{{ \tmdb_image('logo_mid', $network->logo) }}" style="max-height: 100px; max-width: 300px; width: auto;" alt="{{ $network->name }}">
						@else
						{{ $network->name }}
						@endif
					</h2>
					<span></span>
					<h2 style="font-size: 14px;"><i class="{{ config('other.font-awesome') }} fa-tv-retro"></i> {{ $network->tv_count }} Shows</h2>
				</div>
			</a>
		@endforeach
	</div>
	<br>
	<div class="text-center">
		{{ $networks->links() }}
	</div>
</div>
