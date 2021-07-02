<div>
	<div class="mb-10">
		<input type="text" wire:model.debounce.250ms="search" class="form-control" placeholder="@lang('torrent.search-by-name')"/>
	</div>

	<div class="row">
		@foreach ($persons as $person)
			<div class="col-md-2 text-center">
				<div class="thumbnail" style="min-height: 315px;">
					<a href="{{ route('mediahub.persons.show', ['id' => $person->id]) }}">
						<img alt="{{ $person->name }}" src="{{ isset($person->still) ? \tmdb_image('cast_mid', $person->still) : 'https://via.placeholder.com/160x240' }}">
					</a>
					<div class="caption">
						<p class="text-bold">{{ $person->name }}</p>
					</div>
				</div>
			</div>
		@endforeach
	</div>
	<div class="text-center">
		{{ $persons->links() }}
	</div>
</div>
