<div class="text-center form-inline" style="height: 0;">
	<div class="form-group">
		<div>
		<input wire:model.debounce.250ms="movie" type="text" class="form-control" placeholder="Movie"
		       autocomplete="off" wire:blur="$set('movie', '')" style="width: 150px;">
		</div>
		@if ($movie)
			<div style="position: fixed; z-index: 2; background-color: #2b2b2b;">
				@forelse ($search_results as $search_result)
					<div class="box">
						<a href="{{ route('torrents.similar', ['category_id' => '1', 'tmdb' => $search_result->id]) }}" class="pull-left">
							<img src="{{ $search_result->poster }}" style="width: 40px; height: 60px;">
							<span class="text-bold">{{ $search_result->title }} ({{ substr($search_result->release_date, 0, 4) }})</span>
						</a>
					</div>
				@empty
					<div class="px-3 py-3">No results for "{{ $movie }}"</div>
				@endforelse
			</div>
		@endif
	</div>

	<div class="form-group">
		<div>
		<input wire:model.debounce.250ms="series" type="text" class="form-control" placeholder="Series"
		       autocomplete="off" wire:blur="$set('series', '')" style="width: 150px;">
		</div>
		@if ($series)
			<div style="position: fixed; z-index: 2; background-color: #2b2b2b;">
				@forelse ($search_results as $search_result)
					<div class="box">
						<a href="{{ route('torrents.similar', ['category_id' => '2', 'tmdb' => $search_result->id]) }}" class="pull-left">
							<img src="{{ $search_result->poster }}" style="width: 40px; height: 60px;">
							<span class="text-bold">{{ $search_result->name }} ({{ substr($search_result->first_air_date, 0, 4) ?? '' }})</span>
						</a>
					</div>
				@empty
					<div class="px-3 py-3">No results for "{{ $series }}"</div>
				@endforelse
			</div>
		@endif
	</div>

	<div class="form-group">
		<div>
		<input wire:model.debounce.250ms="person" type="text" class="form-control" placeholder="Person"
		       autocomplete="off" wire:blur="$set('person', '')" style="width: 150px;">
		</div>
		@if ($person)
			<div style="position: fixed; z-index: 2; background-color: #2b2b2b;">
				@forelse ($search_results as $search_result)
					<div class="box">
						<a href="{{ route('mediahub.persons.show', ['id' => $search_result->id]) }}" class="pull-left">
							<img src="{{ $search_result->still }}" style="width: 40px; height: 60px;">
							<span class="text-bold">{{ $search_result->name }}</span>
						</a>
					</div>
				@empty
					<div class="px-3 py-3">No results for "{{ $person }}"</div>
				@endforelse
			</div>
		@endif
	</div>
</div>
<br>
