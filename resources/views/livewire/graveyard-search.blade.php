<div>
	<div class="container-fluid">
		<style>
            .form-group {
                margin-bottom: 5px !important;
            }
            .badge-extra {
                margin-bottom: 0;
            }
		</style>
		<div x-data="{ open: false }" class="container box" id="torrent-list-search" style="margin-bottom: 0; padding: 10px 100px; border-radius: 5px;">
			<div class="mt-5">
				<div class="row">
					<div class="form-group col-xs-9">
						<input wire:model="name" type="search" class="form-control" placeholder="Name" />
					</div>
					<div class="form-group col-xs-3">
						<button class="btn btn-md btn-primary" @click="open = ! open" x-text="open ? 'Hide Advanced Search' : 'Advanced Search...'"></button>
					</div>
				</div>
				<div x-show="open">
					<div class="row">
						<div class="form-group col-sm-3 col-xs-6">
							<label for="tmdbId" class="label label-default">TMDb</label>
							<input wire:model="tmdbId" type="text" class="form-control" placeholder="TMDb ID">
						</div>
						<div class="form-group col-sm-3 col-xs-6">
							<label for="imdbId" class="label label-default">IMDb</label>
							<input wire:model="imdbId" type="text" class="form-control" placeholder="IMDb ID">
						</div>
						<div class="form-group col-sm-3 col-xs-6">
							<label for="tvdbId" class="label label-default">TVDb</label>
							<input wire:model="tvdbId" type="text" class="form-control" placeholder="TVDb ID">
						</div>
						<div class="form-group col-sm-3 col-xs-6">
							<label for="malId" class="label label-default">MAL</label>
							<input wire:model="malId" type="text" class="form-control" placeholder="MAL ID">
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12 col-xs-6">
							<label for="categories" class="label label-default">@lang('common.category')</label>
							@foreach (App\Models\Category::select(['id', 'name', 'position'])->get()->sortBy('position') as $category)
								<span class="badge-user">
									<label class="inline">
										<input type="checkbox" wire:model="categories" value="{{ $category->id }}"> {{ $category->name }}
									</label>
								</span>
							@endforeach
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12 col-xs-6">
							<label for="types" class="label label-default">@lang('common.type')</label>
							@foreach (App\Models\Type::select(['id', 'name', 'position'])->get()->sortBy('position') as $type)
								<span class="badge-user">
									<label class="inline">
										<input type="checkbox" wire:model="types" value="{{ $type->id }}"> {{ $type->name }}
									</label>
								</span>
							@endforeach
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12 col-xs-6">
							<label for="resolutions" class="label label-default">@lang('common.resolution')</label>
							@foreach (App\Models\Resolution::select(['id', 'name', 'position'])->get()->sortBy('position') as $resolution)
								<span class="badge-user">
									<label class="inline">
										<input type="checkbox" wire:model="resolutions" value="{{ $resolution->id }}"> {{ $resolution->name }}
									</label>
								</span>
							@endforeach
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12 col-xs-6">
							<label for="extra" class="label label-default">Buff</label>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="free" type="checkbox" value="1">
									Freeleech
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="doubleup" type="checkbox" value="1">
									Double Upload
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="featured" type="checkbox" value="1">
									Featured
								</label>
							</span>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-sm-12 col-xs-6">
							<label for="extra" class="label label-default">Tags</label>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="stream" type="checkbox" value="1">
									Stream Optimized
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="sd" type="checkbox" value="1">
									SD Content
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="highspeed" type="checkbox" value="1">
									Highspeed
								</label>
							</span>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-sm-12 col-xs-6">
							<label for="extra" class="label label-default">@lang('common.extra')</label>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="internal" type="checkbox" value="1">
									Internal
								</label>
							</span>
						</div>
					</div>

				</div>
			</div>
		</div>
		<br>
		<div class="table-responsive block">
			<div class="header gradient green" style="margin-top: 25px;">
				<div class="inner_content">
					<h5 style="font-weight: 900; font-size: 20px; margin: 8px;">
						{{ \number_format($torrentsStat) }} Dead @lang('torrent.torrents')
					</h5>
				</div>
			</div>
			<table class="table table-condensed table-striped table-bordered" id="torrent-list-table">
				<thead>
				<tr>
					<th style="width: 1%;"></th>
					<th class="torrents-filename">
						<div sortable wire:click="sortBy('name')" :direction="$sortField === 'name' ? $sortDirection : null" role="button">
							@lang('common.name')
							@include('livewire.includes._sort-icon', ['field' => 'name'])
						</div>
					</th>
					<th style="width: 3%;">
						<div sortable wire:click="sortBy('size')" :direction="$sortField === 'size' ? $sortDirection : null" role="button">
							<i class="{{ config('other.font-awesome') }} fa-database"></i>
							@include('livewire.includes._sort-icon', ['field' => 'size'])
						</div>
					</th>
					<th style="width: 3%;">
						<div sortable wire:click="sortBy('leechers')" :direction="$sortField === 'leechers' ? $sortDirection : null" role="button">
							<i class="{{ config('other.font-awesome') }} fa-arrow-alt-circle-down"></i>
							@include('livewire.includes._sort-icon', ['field' => 'leechers'])
						</div>
					</th>
					<th style="width: 3%;">
						<div sortable wire:click="sortBy('times_completed')" :direction="$sortField === 'times_completed' ? $sortDirection : null" role="button">
							<i class="{{ config('other.font-awesome') }} fa-check-circle"></i>
							@include('livewire.includes._sort-icon', ['field' => 'times_completed'])
						</div>
					</th>
					<th style="width: 6%;">
						<div sortable wire:click="sortBy('created_at')" :direction="$sortField === 'created_at' ? $sortDirection : null" role="button">
							@lang('common.created_at')
							@include('livewire.includes._sort-icon', ['field' => 'created_at'])
						</div>
					</th>
					<th>@lang('graveyard.resurrect')</th>
				</tr>
				</thead>
				<tbody>
				@foreach($torrents as $torrent)
					@php $history = \App\Models\History::where('info_hash', '=', $torrent->info_hash)->where('user_id', '=', $user->id)->first(); @endphp
					@php $meta = null; @endphp
					@if ($torrent->category->tv_meta)
						@if ($torrent->tmdb || $torrent->tmdb != 0)
							@php $meta = App\Models\Tv::with('genres')->where('id', '=', $torrent->tmdb)->first(); @endphp
						@endif
					@endif
					@if ($torrent->category->movie_meta)
						@if ($torrent->tmdb || $torrent->tmdb != 0)
							@php $meta = App\Models\Movie::with('genres')->where('id', '=', $torrent->tmdb)->first(); @endphp
						@endif
					@endif
						<tr>
							<td style="width: 5%; text-align: center;">
								<a href="{{ route('categories.show', ['id' => $torrent->category->id]) }}">
									<div class="text-center">
										<i class="{{ $torrent->category->icon }} torrent-icon" style="padding-top: 1px; font-size: 20px;"></i>
									</div>
								</a>
								<div class="text-center">
                                <span class="label label-success" style="font-size: 11px">
                                    {{ $torrent->type->name }}
                                </span>
								</div>
								<div class="text-center" style="padding-top: 5px;">
                                <span class="label label-success" style="font-size: 11px">
                                    {{ $torrent->resolution->name ?? 'N/A' }}
                                </span>
								</div>
							</td>
							<td style="vertical-align: middle;" x-data="{ tooltip: false }">
								<a x-on:mouseenter="tooltip = true" x-on:mouseleave="tooltip = false" class="view-torrent" style="font-size: 16px; font-weight: normal;" href="{{ route('torrent', ['id' => $torrent->id]) }}">
									{{ $torrent->name }}
								</a>
								@if (config('torrent.download_check_page') == 1)
									<a href="{{ route('download_check', ['id' => $torrent->id]) }}">
										<button class="btn btn-primary btn-circle" type="button" data-toggle="tooltip"
										        data-original-title="@lang('common.download')">
											<i class="{{ config('other.font-awesome') }} fa-download"></i>
										</button>
									</a>
								@else
									<a href="{{ route('download', ['id' => $torrent->id]) }}">
										<button class="btn btn-primary btn-circle" type="button" data-toggle="tooltip"
										        data-original-title="@lang('common.download')">
											<i class="{{ config('other.font-awesome') }} fa-download"></i>
										</button>
									</a>
								@endif
								@if (config('torrent.magnet') == 1)
									<a href="magnet:?dn={{ $torrent->name }}&xt=urn:btih:{{ $torrent->info_hash }}&as={{ route('torrent.download.rsskey', ['id' => $torrent->id, 'rsskey' => $user->rsskey ]) }}&tr={{ route('announce', ['passkey' => $user->passkey]) }}&xl={{ $torrent->size }}">
										<button class="btn btn-primary btn-circle" type="button" data-toggle="tooltip"
										        data-original-title="@lang('common.magnet')">
											<i class="{{ config('other.font-awesome') }} fa-magnet"></i>
										</button>
									</a>
								@endif
								<br>
								@if ($torrent->anon === 0)
									<span class="badge-extra">
									<i class="{{ config('other.font-awesome') }} {{ $torrent->user->group->icon }}"></i>
                                    <a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                        {{ $torrent->user->username }}
                                    </a>
                                </span>
								@else
									<span class="badge-extra">
									<i class="{{ config('other.font-awesome') }} fa-ghost"></i>
									{{ strtoupper(trans('common.anonymous')) }}
										@if ($user->group->is_modo || $torrent->user->username === $user->username)
											<a href="{{ route('users.show', ['username' => $torrent->user->username]) }}">
                                            ({{ $torrent->user->username }})
                                        </a>
										@endif
                                </span>
								@endif
								@if ($torrent->internal == 1)
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-magic' data-toggle='tooltip' title=''
                                       data-original-title='@lang('torrent.internal-release')' style="color: #baaf92;"></i>
                                </span>
								@endif

								@if ($torrent->stream == 1)
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-play text-red' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.stream-optimized')'></i>
                                </span>
								@endif

								@if ($torrent->featured == 0)
									@if ($torrent->doubleup == 1)
										<span class='badge-extra text-bold'>
                                        <i class='{{ config('other.font-awesome') }} fa-gem text-green' data-toggle='tooltip'
                                           title='' data-original-title='@lang('torrent.double-upload')'></i>
                                    </span>
									@endif
									@if ($torrent->free == 1)
										<span class='badge-extra text-bold'>
                                        <i class='{{ config('other.font-awesome') }} fa-star text-gold' data-toggle='tooltip'
                                           title='' data-original-title='@lang('torrent.freeleech')'></i>
                                    </span>
									@endif
								@endif

								@if ($user->freeleechTokens->where('torrent_id', $torrent->id)->first())
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-star text-bold' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.freeleech-token')'></i>
                                </span>
								@endif

								@if ($torrent->featured == 1)
									<span class='badge-extra text-bold' style='background-image:url(/img/sparkels.gif);'>
                                    <i class='{{ config('other.font-awesome') }} fa-certificate text-pink' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.featured')'></i>
                                </span>
								@endif

								@if ($user->group->is_freeleech == 1)
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-trophy text-purple' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.special-freeleech')'></i>
                                </span>
								@endif

								@if (config('other.freeleech') == 1)
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-globe text-blue' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.global-freeleech')'></i>
                                </span>
								@endif

								@if (config('other.doubleup') == 1)
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-globe text-green' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.global-double-upload')'></i>
                                </span>
								@endif

								@if ($user->group->is_double_upload == 1)
									<span class='badge-extra text-bold'>
									<i class='{{ config('other.font-awesome') }} fa-trophy text-purple'
									   data-toggle='tooltip' title='' data-original-title='@lang('torrent.special-double_upload')'></i>
								</span>
								@endif

								@if ($torrent->leechers >= 5)
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-fire text-orange' data-toggle='tooltip'
                                       title='' data-original-title='@lang('common.hot')'></i>
                                </span>
								@endif

								@if ($torrent->sticky == 1)
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-thumbtack text-black' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.sticky')'></i>
                                </span>
								@endif

								@if ($torrent->highspeed == 1)
									<span class='badge-extra text-bold'>
									<i class='{{ config('other.font-awesome') }} fa-tachometer text-red' data-toggle='tooltip'
									   title='' data-original-title='@lang('common.high-speeds')'></i>
								</span>
								@endif

								@if ($torrent->sd == 1)
									<span class='badge-extra text-bold'>
									<i class='{{ config('other.font-awesome') }} fa-ticket text-orange' data-toggle='tooltip'
									   title='' data-original-title='@lang('torrent.sd-content')'></i>
								</span>
								@endif

								@if ($torrent->bumped_at != $torrent->created_at && $torrent->bumped_at < Carbon\Carbon::now()->addDay(2))
									<span class='badge-extra text-bold'>
                                    <i class='{{ config('other.font-awesome') }} fa-level-up-alt text-gold' data-toggle='tooltip'
                                       title='' data-original-title='@lang('torrent.recent-bumped')'></i>
                                </span>
								@endif

								<div style="z-index: 5; position: absolute;" x-cloak x-show.transition.origin.top="tooltip">
									@include('livewire.includes._tooltip')
								</div>
							</td>
							<td style="vertical-align: middle;">
                            <span class='badge-extra'>
                                {{ $torrent->getSize() }}
                            </span>
							</td>
							<td style="vertical-align: middle;">
                            <span class='badge-extra text-red'>
                                {{ $torrent->leechers }}
                            </span>
							</td>
							<td style="vertical-align: middle;">
                            <span class='badge-extra text-orange'>
                                {{ $torrent->times_completed }}
                            </span>
							</td>
							<td style="vertical-align: middle;">
							<span class='badge-extra'>
								{{ $torrent->created_at->diffForHumans() }}
							</span>
							</td>
							<td style="vertical-align: middle;">
								@php $resurrected = DB::table('graveyard')->where('torrent_id', '=', $torrent->id)->where('rewarded', '=', 0)->first(); @endphp
								@if (! $resurrected)
									<button data-toggle="modal" data-target="#resurrect-{{ $torrent->id }}"
									        class="btn btn-xs btn-success">@lang('graveyard.resurrect')
									</button>
									<div class="modal fade" id="resurrect-{{ $torrent->id }}" tabindex="-1" role="dialog"
									     aria-labelledby="resurrect">
										<div class="modal-dialog{{ \modal_style() }}" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
													<h2>
														<i class="{{ config('other.font-awesome') }} fa-thumbs-up"></i>@lang('graveyard.resurrect')
														{{ strtolower(trans('torrent.torrent')) }} ?
													</h2>
												</div>

												<div class="modal-body">
													<p class="text-center text-bold">
                                                <span class="text-green">
                                                    <em>{{ $torrent->name }} </em>
                                                </span>
													</p>
													<p class="text-center text-bold">
														@lang('graveyard.howto')
													</p>
													<br>
													<div class="text-center">
														<p>{!! trans('graveyard.howto-desc1', ['name' => $torrent->name]) !!}
															<span class="text-red text-bold">
                                                        @if (!$history)
																	0
																@else
																	{{ App\Helpers\StringHelper::timeElapsed($history->seedtime) }}
																@endif
                                                    </span>
															{{ strtolower(trans('graveyard.howto-hits')) }}
															<span class="text-red text-bold">
                                                        @if (!$history)
																	{{ App\Helpers\StringHelper::timeElapsed(config('graveyard.time')) }}
																@else
																	{{ App\Helpers\StringHelper::timeElapsed($history->seedtime + config('graveyard.time')) }}
																@endif
                                                    </span>
															{{ strtolower(trans('graveyard.howto-desc2')) }}
															<span class="badge-user text-bold text-pink"
															      style="background-image:url(/img/sparkels.gif);">
                                                        {{ config('graveyard.reward') }} @lang('torrent.freeleech') Token(s)!
                                                    </span>
														</p>
													</div>
												</div>
												<div class="modal-footer">
													<form id="resurrect-torrent" class="text-center" role="form" method="POST"
													      action="{{ route('graveyard.store', ['id' => $torrent->id]) }}">
														@csrf
														@if (!$history)
															<label for="seedtime"></label><input hidden="seedtime" name="seedtime"
															                                     id="seedtime" value="{{ config('graveyard.time') }}">
														@else
															<label for="seedtime"></label><input hidden="seedtime" name="seedtime"
															                                     id="seedtime"
															                                     value="{{ $history->seedtime + config('graveyard.time') }}">
														@endif
														<button type="button" class="btn btn-primary" data-dismiss="modal">
															@lang('common.cancel')
														</button>
														<button type="submit" class="btn btn-success">
															@lang('graveyard.resurrect')
														</button>
													</form>
												</div>
											</div>
										</div>
									</div>
								@else
									<button class="btn btn-xs btn-warning" disabled>
                                        {{ strtolower(trans('graveyard.pending')) }}
									</button>
								@endif
							</td>
						</tr>
						@endforeach
				</tbody>
			</table>
			@if (! $torrents->count())
				<div class="margin-10">
					@lang('common.no-result')
				</div>
			@endif
			<br>
			<div class="text-center">
				{{ $torrents->links() }}
			</div>
		</div>
	</div>
</div>