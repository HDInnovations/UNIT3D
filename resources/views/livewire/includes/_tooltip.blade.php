<div class="col-md-6">
	<div class="card is-torrent">
		<div class="card_head">

		</div>
		<div class="card_body">
			<div class="body_poster">
				@if ($torrent->category->movie_meta || $torrent->category->tv_meta)
					<img src="{{ isset($meta->poster) ? \tmdb_image('poster_big', $meta->poster) : 'https://via.placeholder.com/600x900' }}"
					     class="show-poster" alt="@lang('torrent.poster')">
				@endif

				@if ($torrent->category->game_meta && isset($meta) && $meta->cover->image_id && $meta->name)
					<img src="https://images.igdb.com/igdb/image/upload/t_original/{{ $meta->cover->image_id }}.jpg" class="show-poster"
					     data-name='<i style="color: #a5a5a5;">{{ $meta->name ?? 'N/A' }}</i>' data-image='<img src="https://images.igdb.com/igdb/image/upload/t_original/{{ $meta->cover->image_id }}.jpg" alt="@lang('torrent.poster')" style="height: 1000px;">'
					     class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
				@endif

				@if ($torrent->category->no_meta || $torrent->category->music_meta)
					<img src="https://via.placeholder.com/600x900" class="show-poster"
					     data-name='<i style="color: #a5a5a5;">N/A</i>' data-image='<img src="https://via.placeholder.com/600x900" alt="@lang('torrent.poster')" style="height: 1000px;">'
					     class="torrent-poster-img-small show-poster" alt="@lang('torrent.poster')">
				@endif
			</div>
			<div class="body_description">
				<h3 class="description_title">
					<a href="{{ route('torrent', ['id' => $torrent->id]) }}">
						@if ($torrent->category->movie_meta)
							{{ $meta->title ?? 'Unknown' }}
						@endif
						@if ($torrent->category->tv_meta)
							{{ $meta->name ?? 'Unknown' }}
						@endif
						@if($torrent->category->movie_meta)
							<span class="text-bold text-pink"> {{ substr($meta->release_date ?? '', 0, 4) ?? '' }}</span>
						@endif
						@if($torrent->category->tv_meta)
							<span class="text-bold text-pink"> {{ substr($meta->first_air_date ?? '', 0, 4) ?? '' }}</span>
						@endif
					</a>
				</h3>
				@if (($torrent->category->movie_meta || $torrent->category->tv_meta) && isset($meta->genres))
					@foreach ($meta->genres as $genre)
						<span class="genre-label">{{ $genre->name }}</span>
					@endforeach
				@endif
				<p class="description_plot">
					{{ $meta->overview ?? '' }}
				</p>
			</div>
		</div>
		<div class="card_footer">
			<span class="badge-user text-bold" style="float: right;">
				<i class="{{ config('other.font-awesome') }} fa-thumbs-up text-gold"></i>
				{{ $meta->vote_average ?? 0 }}/10 ({{ $meta->vote_count ?? 0 }} @lang('torrent.votes'))
			</span>
		</div>
	</div>
</div>