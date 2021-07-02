<div>
	<div class="mb-10">
		<input type="text" wire:model="search" class="form-control" placeholder="@lang('torrent.search-by-name')"/>
	</div>

	@foreach($collections as $collection)
		<div class="col-md-12 well profile-footer">
			<div class="collection">
				<div class="header collection" style=" background-image: url({{ isset($collection->backdrop) ? \tmdb_image('back_big', $collection->backdrop) : 'https://via.placeholder.com/1280x300' }}); background-size: cover; background-position: 50% 50%;">
					<div class="collection-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: linear-gradient(rgba(0, 0, 0, 0.87), rgba(45, 71, 131, 0.46));"></div>
					<section class="collection">
						<h2>{{ $collection->name }}</h2>
						<p class="text-blue">@lang('mediahub.includes')
							@foreach($collection->movie as $collection_movie)
								{{ $collection_movie->title }},
							@endforeach
						</p>

						<a href="{{ route('mediahub.collections.show', ['id' => $collection->id]) }}" role="button" class="btn btn-labeled btn-primary" style=" margin: 0; text-transform: uppercase; position: absolute; bottom: 50px;">
							<span class="btn-label"><i class="{{ config("other.font-awesome") }} fa-copy"></i> @lang('mediahub.view-collection')</span>
						</a>
					</section>
				</div>
			</div>
		</div>
	@endforeach
	<br>
	<div class="text-center">
		{{ $collections->links() }}
	</div>
</div>
