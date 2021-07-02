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
		<div x-data="{ open: false }" class="container box" style="margin-bottom: 0; padding: 10px 100px; border-radius: 5px;">
			<div class="mt-5">
				<div class="row">
					<div class="form-group col-xs-9">
						<input wire:model="name" type="search" class="form-control" placeholder="@lang('common.name')" />
					</div>
					<div class="form-group col-xs-3">
						<button class="btn btn-md btn-primary" @click="open = ! open" x-text="open ? '@lang('common.search-hide')' : '@lang('common.search-advanced')'"></button>
					</div>
				</div>
				<div x-show="open">
					<div class="row">
						<div class="form-group col-xs-9">
							<input wire:model="requestor" type="search" class="form-control" placeholder="@lang('common.author')" />
						</div>
					</div>
					<div class="row">
						<div class="form-group form-inline col-sm-3 col-xs-6">
							<label for="meta" class="label label-default">Meta IDs</label>
							<input wire:model="tmdbId" type="text" class="form-control" placeholder="TMDb ID" style="width: 65%;">
						</div>
						<div class="form-group col-sm-2 col-xs-6">
							<input wire:model="imdbId" type="text" class="form-control" placeholder="IMDb ID">
						</div>
						<div class="form-group col-sm-2 col-xs-6">
							<input wire:model="tvdbId" type="text" class="form-control" placeholder="TVDb ID">
						</div>
						<div class="form-group col-sm-2 col-xs-6">
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
							<label for="extra" class="label label-default">@lang('common.status')</label>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="unfilled" type="checkbox" value="1">
									<span class="{{ config('other.font-awesome') }} fa-times-circle text-blue"></span>
									@lang('request.unfilled')
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="claimed" type="checkbox" value="1">
									<span class="{{ config('other.font-awesome') }} fa-hand-paper text-blue"></span>
									@lang('request.claimed')
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="pending" type="checkbox" value="1">
									<span class="{{ config('other.font-awesome') }} fa-question-circle text-blue"></span>
									@lang('request.pending')
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="filled" type="checkbox" value="1">
									<span class="{{ config('other.font-awesome') }} fa-check-circle text-blue"></span>
									@lang('request.filled')
								</label>
							</span>
						</div>
					</div>

					<div class="row">
						<div class="form-group col-sm-12 col-xs-6">
							<label for="extra" class="label label-default">@lang('common.extra')</label>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="myRequests" type="checkbox" value="{{ $user->id }}">
									@lang('request.my-requests')
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="myClaims" type="checkbox" value="1">
									@lang('request.my-claims')
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="myVoted" type="checkbox" value="1">
									@lang('request.my-voted')
								</label>
							</span>
							<span class="badge-user">
								<label class="inline">
									<input wire:model="myFilled" type="checkbox" value="1">
									@lang('request.my-filled')
								</label>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br>
		<div class="table-responsive block">
			<span class="badge-user" style="float: right;">
				<strong>@lang('request.requests'):</strong> {{ \number_format($torrentRequestStat->total) }} |
				<strong>@lang('request.filled'):</strong> {{ \number_format($torrentRequestStat->filled) }} |
				<strong>@lang('request.unfilled'):</strong> {{ \number_format($torrentRequestStat->unfilled) }} |
				<strong>@lang('request.total-bounty'):</strong> {{ \number_format($torrentRequestBountyStat->total) }} @lang('bon.bon') |
				<strong>@lang('request.bounty-claimed'):</strong> {{ \number_format($torrentRequestBountyStat->claimed) }} @lang('bon.bon') |
				<strong>@lang('request.bounty-unclaimed'):</strong> {{ \number_format($torrentRequestBountyStat->unclaimed) }} @lang('bon.bon')
			</span>
			<a href="{{ route('add_request') }}" role="button" class="btn btn-xs btn-success">
				@lang('request.add-request')
			</a>
			<div class="header gradient green" style="margin-top: 10px;">
				<div class="inner_content">
					<h5 style="font-weight: 900; font-size: 20px; margin: 8px;">
						@lang('request.requests')
					</h5>
				</div>
			</div>
			<table class="table table-condensed table-striped table-bordered" id="requests-table">
				<thead>
				<tr>
					<th class="torrents-filename">
						<div sortable wire:click="sortBy('name')" :direction="$sortField === 'name' ? $sortDirection : null" role="button">
							@lang('common.name')
							@include('livewire.includes._sort-icon', ['field' => 'name'])
						</div>
					</th>
					<th>
						<div sortable wire:click="sortBy('category_id')" :direction="$sortField === 'category_id' ? $sortDirection : null" role="button">
							@lang('common.category')
							@include('livewire.includes._sort-icon', ['field' => 'category_id'])
						</div>
					</th>
					<th>
						<div sortable wire:click="sortBy('type_id')" :direction="$sortField === 'type_id' ? $sortDirection : null" role="button">
							@lang('common.type')
							@include('livewire.includes._sort-icon', ['field' => 'type_id'])
						</div>
					</th>
					<th>
						<div sortable wire:click="sortBy('resolution_id')" :direction="$sortField === 'resolution_id' ? $sortDirection : null" role="button">
							@lang('common.resolution')
							@include('livewire.includes._sort-icon', ['field' => 'resolution_id'])
						</div>
					</th>
					<th>
						<div sortable wire:click="sortBy('user_id')" :direction="$sortField === 'user_id' ? $sortDirection : null" role="button">
							@lang('common.author')
							@include('livewire.includes._sort-icon', ['field' => 'user_id'])
						</div>
					</th>
					<th>
						<div sortable wire:click="sortBy('votes')" :direction="$sortField === 'votes' ? $sortDirection : null" role="button">
							<i class="{{ config('other.font-awesome') }} fa-thumbs-up"></i>
							@include('livewire.includes._sort-icon', ['field' => 'votes'])
						</div>
					</th>
					<th>
						<i class="{{ config('other.font-awesome') }} fa-comment-alt-lines"></i>
					</th>
					<th>
						<div sortable wire:click="sortBy('bounty')" :direction="$sortField === 'bounty' ? $sortDirection : null" role="button">
							<i class="{{ config('other.font-awesome') }} fa-coins"></i>
							@include('livewire.includes._sort-icon', ['field' => 'bounty'])
						</div>
					</th>
					<th>
						<div sortable wire:click="sortBy('created_at')" :direction="$sortField === 'created_at' ? $sortDirection : null" role="button">
							@lang('common.created_at')
							@include('livewire.includes._sort-icon', ['field' => 'created_at'])
						</div>
					</th>
					<th>
						@lang('common.status')
					</th>
				</tr>
				</thead>
				<tbody>
				@foreach($torrentRequests as $torrentRequest)
					<tr>							
						<td style="vertical-align: middle;">
							<a class="view-torrent" style="font-size: 16px; font-weight: normal;" href="{{ route('request', ['id' => $torrentRequest->id]) }}">
								{{ $torrentRequest->name }}
							</a>
						</td>
						<td style="vertical-align: middle;">
							<span class='badge-extra'>
								{{ $torrentRequest->category->name }}
							</span>
						</td>
						<td style="vertical-align: middle;">
							<span class='badge-extra'>
								{{ $torrentRequest->type->name }}
							</span>
						</td>
						<td style="vertical-align: middle;">
							<span class='badge-extra'>
								{{ $torrentRequest->resolution->name ?? 'Unknown' }}
							</span>
						</td>
						<td style="vertical-align: middle;">
							@if ($torrentRequest->anon === 0)
								<span class="badge-user">
                                    <a href="{{ route('users.show', ['username' => $torrentRequest->user->username]) }}">
                                        {{ $torrentRequest->user->username }}
                                    </a>
                                </span>
							@else
								<span class="badge-user">{{ strtoupper(trans('common.anonymous')) }}
									@if ($user->hasRole('moderator') || $torrentRequest->user->username === $user->username)
										<a href="{{ route('users.show', ['username' => $torrentRequest->user->username]) }}">
                                            ({{ $torrentRequest->user->username }})
                                        </a>
									@endif
                                </span>
							@endif
						</td>
						<td style="vertical-align: middle;">
                            <span class='badge-extra text-green'>
                                {{ $torrentRequest->votes }}
                            </span>
						</td>
						<td style="vertical-align: middle;">
                            <span class='badge-extra text-green'>
                                {{ $torrentRequest->comments_count }}
                            </span>
						</td>
						<td style="vertical-align: middle;">
                            <span class='badge-extra text-green'>
                                {{ \number_format($torrentRequest->bounty) }}
                            </span>
						</td>
						<td style="vertical-align: middle;">
							<span class='badge-extra'>
								{{ $torrentRequest->created_at->diffForHumans() }}
							</span>
						</td>
						<td style="vertical-align: middle;">
							@if ($torrentRequest->claimed != null && $torrentRequest->filled_hash == null)
								<span class="label label-primary">
									<i class="{{ config('other.font-awesome') }} fa-hand-paper"></i> @lang('request.claimed')
								</span>
							@elseif ($torrentRequest->filled_hash != null && $torrentRequest->approved_by == null)
								<span class="label label-info">
									<i class="{{ config('other.font-awesome') }} fa-question-circle"></i> @lang('request.pending')
								</span>
							@elseif ($torrentRequest->filled_hash == null)
								<span class="label label-danger">
									<i class="{{ config('other.font-awesome') }} fa-times-circle"></i> @lang('request.unfilled')
								</span>
							@else
								<span class="label label-success">
									<i class="{{ config('other.font-awesome') }} fa-check-circle"></i> @lang('request.filled')
								</span>
							@endif
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
			@if (! $torrentRequests->count())
				<div class="margin-10">
					@lang('common.no-result')
				</div>
			@endif
			<br>
			<div class="text-center">
				{{ $torrentRequests->links() }}
			</div>
		</div>
	</div>
</div>

