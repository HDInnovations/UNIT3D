<div>
	<div>
		<div class="mb-10">
			<input type="text" wire:model="search" class="form-control" placeholder="@lang('torrent.search-by-name')"/>
		</div>

		@foreach($movies as $movie)
			<div class="col-md-12">
				<div class="card is-torrent" style=" height: 265px;">
					<div class="card_head">
						<span class="badge-user text-bold" style="float:right;">
							{{ $movie->torrents_count }} @lang('torrent.torrents-matched')
						</span>
						@if ($movie->companies)
							@foreach ($movie->companies as $company)
								<span class="badge-user text-bold" style="float:right;">
									{{ $company->name }}
								</span>
							@endforeach
						@endif
					</div>
					<div class="card_body">
						<div class="body_poster">
							<img src="{{ isset($movie->poster) ? \tmdb_image('poster_mid', $movie->poster) : 'https://via.placeholder.com/200x300' }}" class="show-poster">
						</div>
						<div class="body_description">
							<h3 class="description_title">
								<a href="{{ route('mediahub.movies.show', ['id' => $movie->id]) }}">{{ $movie->title }}
									@if($movie->release_date)
										<span class="text-bold text-pink"> {{ $movie->release_date }}</span>
									@endif
								</a>
							</h3>
							@if ($movie->genres)
								@foreach ($movie->genres as $genre)
									<span class="genre-label">{{ $genre->name }}</span>
								@endforeach
							@endif
							<p class="description_plot">
								{{ $movie->overview }}
							</p>
						</div>
					</div>
				</div>
			</div>
		@endforeach
		<br>
		<div class="text-center">
			{{ $movies->links() }}
		</div>
	</div>
</div>
