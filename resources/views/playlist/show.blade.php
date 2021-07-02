@extends('layout.default')

@section('breadcrumb')
	<li>
		<a href="{{ route('playlists.index') }}" itemprop="url" class="l-breadcrumb-item-link">
			<span itemprop="title" class="l-breadcrumb-item-link-title">@lang('playlist.playlists')</span>
		</a>
	</li>
	<li>
		<a href="{{ route('playlists.show', ['id' => $playlist->id]) }}" itemprop="url" class="l-breadcrumb-item-link">
			<span itemprop="title" class="l-breadcrumb-item-link-title">{{ $playlist->name }}</span>
		</a>
	</li>
@endsection

@section('content')
	<div class="container">
		<div class="block">
			@php $tmdb_backdrop = isset($meta->backdrop) ? \tmdb_image('back_big', $meta->backdrop) : 'https://via.placeholder.com/1280x350'; @endphp
			<section class="inner_content header" style="background-image: url('{{ $tmdb_backdrop }}')">
				<div class="bg_filter">
					<div class="single_column">
						<h2>{{ $playlist->name }}</h2>

						<ul class="list_menu_bar">
							<li class="account">
								<a href="{{ route('users.show', ['username' => $playlist->user->username]) }}">
									@if ($playlist->user->image != null)
										<img src="{{ url('files/img/' . $playlist->user->image) }}" alt="{{ $playlist->user->username }}" style=" width: 50px;">
									@else
										<img src="{{ url('img/profile.png') }}" alt="{{ $playlist->user->username }}" style=" width: 50px;">
									@endif
								</a>
								<p>@lang('playlist.list-by')<br><a href="{{ route('users.show', ['username' => $playlist->user->username]) }}">{{ $playlist->user->username }}</a></p>
							</li>
						</ul>

						<h3 class="text-bold">@lang('playlist.list-about')</h3>
						<div class="description">
							<p>{{ $playlist->description }}</p>
							<br>
							@if(auth()->user()->id == $playlist->user_id || auth()->user()->group->is_modo)
								<button data-toggle="modal" data-target="#modal_playlist_torrent" class="btn btn-md btn-success">
									<i class="{{ config('other.font-awesome') }} fa-search-plus"></i> @lang('playlist.add-torrent')
								</button>
								<form action="{{ route('playlists.destroy', ['id' => $playlist->id]) }}" method="POST" style="display: inline;">
									@csrf
									@method('DELETE')
									<a href="{{ route('playlists.edit', ['id' => $playlist->id]) }}" class="btn btn-warning">
										<i class="{{ config('other.font-awesome') }} fa-edit"></i> @lang('playlist.edit-playlist')
									</a>
									<button type="submit" class="btn btn-danger pull-right">
										<i class="{{ config('other.font-awesome') }} fa-trash"></i> @lang('playlist.delete-playlist')
									</button>
								</form>
							@endif
						</div>
					</div>
				</div>
			</section>
		</div>

		<div class="block">
			<div class="row">
				<div class="text-center">
					<a href="{{ route('playlists.download', ['id' => $playlist->id]) }}" role="button"
					   class="btn btn-sm btn-labeled btn-success">
                    <span class='btn-label'>
                        <i class='{{ config('other.font-awesome') }} fa-download'></i> @lang('playlist.download-all')
                    </span>
					</a>
					<a href="{{ route('torrents') }}?perPage=25&playlistId={{ $playlist->id }}" role="button"
					   class="btn btn-sm btn-labeled btn-success">
                    <span class='btn-label'>
                        <i class='{{ config('other.font-awesome') }} fa-eye'></i> Playlist Torrents List
                    </span>
					</a>
				</div>
				@php $meta = null; @endphp
				@foreach($torrents as $t)
					@if ($t->torrent->category->tv_meta)
						@if ($t->torrent->tmdb || $t->torrent->tmdb != 0)
							@php $meta = App\Models\Tv::with('genres', 'networks', 'seasons')->where('id', '=', $t->torrent->tmdb)->first(); @endphp
						@endif
					@endif
					@if ($t->torrent->category->movie_meta)
						@if ($t->torrent->tmdb || $t->torrent->tmdb != 0)
							@php $meta = App\Models\Movie::with('genres', 'cast', 'companies', 'collection')->where('id', '=', $t->torrent->tmdb)->first(); @endphp
						@endif
					@endif
					<div class="col-md-6">
						<div class="card is-torrent">
							<div class="card_head">
								@if ($t->torrent->tmdb != 0)
									<a href="{{ route('torrents.similar', ['category_id' => $t->torrent->category_id, 'tmdb' => $t->torrent->tmdb]) }}" role="button"
									   data-toggle="tooltip" data-placement="top" data-original-title="@lang('torrent.similar')" class="btn btn-xs btn-primary" style="float: left; margin-right: 10px;">
										<i class='{{ config("other.font-awesome") }} fa-copy'></i>
									</a>
								@endif
								&nbsp;
								@if (config('torrent.download_check_page') == 1)
									<a href="{{ route('download_check', ['id' => $t->torrent->id]) }}" role="button"
									   data-toggle="tooltip" data-placement="top" data-original-title="@lang('common.download')" class="btn btn-xs btn-success" style="float: left; margin-right: 10px;">
										<i class='{{ config("other.font-awesome") }} fa-download'></i>
									</a>
								@else
									<a href="{{ route('download', ['id' => $t->torrent->id]) }}" role="button"
									   data-toggle="tooltip" data-placement="top" data-original-title="@lang('common.download')" class="btn btn-xs btn-success" style="float: left; margin-right: 10px;">
										<i class='{{ config("other.font-awesome") }} fa-download'></i>
									</a>
								@endif
								&nbsp;
								@if(auth()->user()->id == $playlist->user_id || auth()->user()->group->is_modo)
									<form action="{{ route('playlists.detach', ['id' => $t->id]) }}" method="POST" style="float: left; margin-right: 10px;"
									      data-toggle="tooltip" data-placement="top" data-original-title="@lang('common.delete')">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-xs btn-danger">
											<i class="{{ config('other.font-awesome') }} fa-trash"></i>
										</button>
									</form>
								@endif

								<span class="badge-user text-bold" style="float:right;">
                                    <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-up text-green"></i> {{ $t->torrent->seeders }} /
                                    <i class="{{ config('other.font-awesome') }} fa-fw fa-arrow-down text-red"></i> {{ $t->torrent->leechers }} /
                                    <i class="{{ config('other.font-awesome') }} fa-fw fa-check text-orange"></i>{{ $t->torrent->times_completed }}
                                </span>&nbsp;
								<span class="badge-user text-bold text-blue" style="float:right;">{{ $t->torrent->getSize() }}</span>
								<span class="badge-user text-bold text-blue" style="float:right;">{{ $t->torrent->resolution->name }}</span>
								<span class="badge-user text-bold text-blue" style="float:right;">{{ $t->torrent->type->name }}</span>
								<span class="badge-user text-bold text-blue" style="float:right;">{{ $t->torrent->category->name }}</span>
							</div>
							<div class="card_body">
								<div class="body_poster">
									@if ($t->torrent->category->movie_meta || $t->torrent->category->tv_meta)
										<img src="{{ isset($meta->poster) ? \tmdb_image('poster_mid', $meta->poster) : 'https://via.placeholder.com/160x240' }}"
										     class="show-poster" alt="@lang('torrent.poster')">
									@endif

									@if ($t->torrent->category->game_meta && isset($t->torrent->meta) && $meta->cover->image_id && $meta->name)
										<img src="https://images.igdb.com/igdb/image/upload/t_cover_big/{{ $t->torrent->meta->cover->image_id }}.jpg"
										     class="show-poster" alt="@lang('torrent.poster')">
									@endif

									@if ($t->torrent->category->no_meta || $t->torrent->category->music_meta)
										<img src="https://via.placeholder.com/160x240"
										     class="show-poster" alt="@lang('torrent.poster')">
									@endif
								</div>
								<div class="body_description">
									<h3 class="description_title">
										<a href="{{ route('torrent', ['id' => $t->torrent->id]) }}">{{ $t->torrent->name }}
											@if($t->torrent->category->movie_meta || ($t->torrent->category->tv_meta && isset($t->torrent->meta) && $meta->releaseYear))
												<span class="text-bold text-pink"> {{ $meta->releaseYear ?? '' }}</span>
											@endif
											@if($t->torrent->category->game_meta && isset($meta) && $meta->first_release_date)
												<span class="text-bold text-pink"> {{ date('Y', strtotime( $meta->first_release_date)) }}</span>
											@endif
										</a>
									</h3>
									@if ($t->torrent->category->movie_meta && isset($meta) && $meta->genres)
										@foreach ($meta->genres as $genre)
											<span class="genre-label">{{ $genre->name }}</span>
										@endforeach
									@endif
									@if ($t->torrent->category->tv_meta && isset($meta) && $meta->genres)
										@foreach ($meta->genres as $genre)
											<span class="genre-label">{{ $genre->name }}</span>
										@endforeach
									@endif
									@if ($t->torrent->category->game_meta && isset($meta) && $meta->genres)
										@foreach ($meta->genres as $genre)
											<span class="genre-label">{{ $genre->name }}</span>
										@endforeach
									@endif
									<p class="description_plot">
										@if($t->torrent->category->movie_meta || ($t->torrent->category->tv_meta && $meta && $meta->plot))
											{{ $meta->overview ?? '' }}
										@endif
									</p>
								</div>
							</div>
							<div class="card_footer">
								<div style="float: left;">
									@if ($t->torrent->anon == 1)
										<span class="badge-user text-orange text-bold">{{ strtoupper(trans('common.anonymous')) }}
											@if (auth()->user()->id == $t->torrent->user->id || auth()->user()->group->is_modo)
												<a href="{{ route('users.show', ['username' => $t->torrent->user->username]) }}">
                                                            ({{ $t->torrent->user->username }})
                                                        </a>
											@endif
                                                </span>
									@else
										<a href="{{ route('users.show', ['username' => $t->torrent->user->username]) }}">
                                            <span class="badge-user text-bold" style="color:{{ $t->torrent->user->group->color }}; background-image:{{ $t->torrent->user->group->effect }};">
                                                <i class="{{ $t->torrent->user->group->icon }}" data-toggle="tooltip" title=""
                                                   data-original-title="{{ $t->torrent->user->group->name }}"></i> {{ $t->torrent->user->username }}
                                            </span>
										</a>
									@endif
								</div>
								<span class="badge-user text-bold" style="float: right;">
									<i class="{{ config('other.font-awesome') }} fa-thumbs-up text-gold"></i>
                                    {{ $meta->vote_average ?? '0' }}/10 ({{ $meta->vote_count ?? '0' }} @lang('torrent.votes'))
								</span>
							</div>
						</div>
					</div>
				@endforeach
			</div>
			<div class="text-center">{{ $torrents->links() }}</div>
		</div>

		<div class="block" id="comments">
			<div class="row ">
				<div class="col-md-12 col-sm-12">
					<div class="panel panel-chat shoutbox">
						<div class="panel-heading">
							<h4>
								<i class="{{ config('other.font-awesome') }} fa-comment"></i> @lang('common.comments')
							</h4>
						</div>
						<div class="panel-body no-padding">
							<ul class="media-list comments-list">
								@if (count($playlist->comments) == 0)
									<div class="text-center"><h4 class="text-bold text-danger">
											<i class="{{ config('other.font-awesome') }} fa-frown"></i> @lang('common.no-comments')!</h4>
									</div>
								@else
									@foreach ($playlist->comments as $comment)
										<li class="media" style="border-left: 5px solid #01BC8C;">
											<div class="media-body">
												@if ($comment->anon == 1)
													<a href="#" class="pull-left" style="padding-right: 10px;">
														<img src="{{ url('img/profile.png') }}" class="img-avatar-48">
														<strong>{{ strtoupper(trans('common.anonymous')) }}</strong></a> @if (auth()->user()->id == $comment->user->id || auth()->user()->group->is_modo)
														<a href="{{ route('users.show', ['username' => $comment->user->username]) }}" style="color:{{ $comment->user->group->color }};">(<span><i class="{{ $comment->user->group->icon }}"></i> {{ $comment->user->username }}</span>)</a> @endif
												@else
													<a href="{{ route('users.show', ['username' => $comment->user->username]) }}"
													   class="pull-left" style="padding-right: 10px;">
														@if ($comment->user->image != null)
															<img src="{{ url('files/img/' . $comment->user->image) }}"
															     alt="{{ $comment->user->username }}" class="img-avatar-48"></a>
													@else
														<img src="{{ url('img/profile.png') }}"
														     alt="{{ $comment->user->username }}" class="img-avatar-48"></a>
													@endif
													<strong><a
																href="{{ route('users.show', ['username' => $comment->user->username]) }}" style="color:{{ $comment->user->group->color }};"><span><i class="{{ $comment->user->group->icon }}"></i> {{ $comment->user->username }}</span></a></strong> @endif
												<span class="text-muted"><small><em>{{ $comment->created_at->toDayDateTimeString() }} ({{ $comment->created_at->diffForHumans() }})</em></small></span>
												@if ($comment->user_id == auth()->id() || auth()->user()->group->is_modo)
													<a title="@lang('common.delete-comment')"
													   href="{{route('comment_delete',['comment_id'=>$comment->id])}}"><i
																class="pull-right {{ config('other.font-awesome') }} fa fa-times" aria-hidden="true"></i></a>
													<a title="@lang('common.edit-comment')" data-toggle="modal"
													   data-target="#modal-comment-edit-{{ $comment->id }}"><i
																class="pull-right {{ config('other.font-awesome') }} fa-pencil"
																aria-hidden="true"></i></a>
												@endif
												<div class="pt-5">
													@joypixels($comment->getContentHtml())
												</div>
											</div>
										</li>
										@include('partials.modals', ['comment' => $comment])
									@endforeach
								@endif
							</ul>
						</div>
					</div>
				</div>
				<br>
				<div class="col-md-12">
					<form role="form" method="POST" action="{{ route('comment_playlist', ['id' => $playlist->id]) }}">
						@csrf
						<div class="form-group">
							<label for="content">@lang('common.your-comment'):</label>
							<span class="badge-extra">BBCode @lang('common.is-allowed')</span>
							<textarea id="content" name="content" cols="30" rows="5" class="form-control"></textarea>
						</div>
						<button type="submit" class="btn btn-danger">@lang('common.submit')</button>
						<label class="radio-inline"><strong>@lang('common.anonymous') @lang('common.comment')
								:</strong></label>
						<input type="radio" value="1" name="anonymous"> @lang('common.yes')
						<input type="radio" value="0" checked="checked" name="anonymous"> @lang('common.no')
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal_playlist_torrent" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog{{ \modal_style() }}">
			<div class="modal-content">
				<div class="container-fluid">
					<form role="form" method="POST" action="{{ route('playlists.attach') }}">
						@csrf
						<input id="playlist_id" name="playlist_id" type="hidden" value="{{ $playlist->id }}">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
							<h4 class="modal-title" id="myModalLabel">@lang('playlist.add-to-playlist')</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="report_reason">Torrent ID</label>
							</div>
							<div class="form-group">
								<label>
									<input type="text" name="torrent_id" class="form-control">
								</label>
							</div>
							<div class="form-group">
								<input class="btn btn-success" type="submit" value="@lang('common.save')">
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-sm btn-primary" type="button" data-dismiss="modal">@lang('common.close')</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('javascripts')
	<script nonce="{{ Bepsvpt\SecureHeaders\SecureHeaders::nonce() }}">
      $(document).ready(function () {
        $('#content').wysibb({});
      })
	</script>
@endsection
