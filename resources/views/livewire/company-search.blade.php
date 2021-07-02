<div>
	<div class="mb-10">
		<input type="text" wire:model="search" class="form-control" placeholder="Search By Name"/>
	</div>

	<div class="blocks">
		@foreach ($companies as $company)
			<a href="{{ route('mediahub.companies.show', ['id' => $company->id]) }}" style="padding: 0 2px;">
				<div class="general media_blocks" style="background-color: rgba(0, 0, 0, 0.33);">
					<h2 class="text-bold">
						@if(isset($company->logo))
						<img src="{{ \tmdb_image('logo_mid', $company->logo) }}" style="max-height: 100px; max-width: 300px; width: auto;" alt="{{ $company->name }}">
						@else
						{{ $company->name }}
						@endif
					</h2>
					<span></span>
					<h2 style="font-size: 14px;"><i class="{{ config('other.font-awesome') }} fa-tv-retro"></i> {{ $company->tv_count }} Shows | {{ $company->movie_count }} Movies</h2>
				</div>
			</a>
		@endforeach
	</div>
	<br>
	<div class="text-center">
		{{ $companies->links() }}
	</div>
</div>
