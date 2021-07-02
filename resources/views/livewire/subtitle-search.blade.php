<div>
	<div class="container-fluid">
		<div class="block">
			<div class="container box search mt-5 fatten-me form-horizontal form-condensed form-torrent-search form-bordered">
				<div class="header gradient silver">
					<div class="inner_content">
						<div class="page-title">
							<h1 style="margin: 0;">@lang('common.subtitles')</h1>
						</div>
					</div>
				</div>
				<br>
				<div class="mx-0 mt-5 form-group fatten-me">
					<label for="search" class="mt-5 col-sm-1 label label-default fatten-me">@lang('torrent.name')</label>
					<div class="col-sm-9 fatten-me">
						<label for="search"></label>
						<input wire:model="search" type="text" class="form-control" placeholder="@lang('torrent.name')">
					</div>
				</div>

				<div class="mx-0 mt-5 form-group fatten-me">
					<label for="language_id" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.language')</label>
					<div class="col-sm-9">
						<select class="form-control" wire:model="language">
							<option value="">--@lang('common.select') @lang('common.language')--</option>
							@foreach (App\Models\MediaLanguage::all()->sortBy('name') as $media_language)
								<option value="{{ $media_language->id }}">{{ $media_language->name }} ({{ $media_language->code }})</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="mx-0 mt-5 form-group fatten-me">
					<label for="categories" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.category')</label>
					<div class="col-sm-9">
						@foreach (App\Models\Category::all()->sortBy('position') as $category)
							<span class="badge-user">
								<label class="inline">
									<input type="checkbox" wire:model="categories" value="{{ $category->id }}"> {{ $category->name }}
								</label>
							</span>
						@endforeach
					</div>
				</div>

				<div class="mx-0 mt-5 form-group fatten-me">
					<label for="perPage" class="mt-5 col-sm-1 label label-default fatten-me">@lang('common.quantity')</label>
					<div class="col-sm-2">
						<select wire:model="perPage" class="form-control">
							<option value="25">25</option>
							<option value="50">50</option>
							<option value="100">100</option>
						</select>
					</div>
				</div>
			</div>
			<br>
			<div class="table-responsive">
				<table class="table table-condensed table-striped table-bordered">
					<thead>
					<tr>
						<th class="torrents-icon"></th>
						<th class="torrents-filename">
							<div sortable wire:click="sortBy('title')" :direction="$sortField === 'title' ? $sortDirection : null" role="button">
								@lang('torrent.torrent')
								@include('livewire.includes._sort-icon', ['field' => 'title'])
							</div>
						</th>
						<th>
							<div sortable wire:click="sortBy('language_id')" :direction="$sortField === 'language_id' ? $sortDirection : null" role="button">
								@lang('common.language')
								@include('livewire.includes._sort-icon', ['field' => 'language_id'])
							</div>
						</th>
						<th>@lang('common.download')</th>
						<th>
							<div sortable wire:click="sortBy('extension')" :direction="$sortField === 'extension' ? $sortDirection : null" role="button">
								@lang('subtitle.extension')
								@include('livewire.includes._sort-icon', ['field' => 'extension'])
							</div>
						</th>
						<th>
							<div sortable wire:click="sortBy('file_size')" :direction="$sortField === 'file_size' ? $sortDirection : null" role="button">
								@lang('subtitle.size')
								@include('livewire.includes._sort-icon', ['field' => 'file_size'])
							</div>
						</th>
						<th>
							<div sortable wire:click="sortBy('downloads')" :direction="$sortField === 'downloads' ? $sortDirection : null" role="button">
								@lang('subtitle.downloads')
								@include('livewire.includes._sort-icon', ['field' => 'downloads'])
							</div>
						</th>
						<th>
							<div sortable wire:click="sortBy('created_at')" :direction="$sortField === 'created_at' ? $sortDirection : null" role="button">
								@lang('subtitle.uploaded')
								@include('livewire.includes._sort-icon', ['field' => 'created_at'])
							</div>
						</th>
						<th>
							<div sortable wire:click="sortBy('user_id')" :direction="$sortField === 'user_id' ? $sortDirection : null" role="button">
								@lang('subtitle.uploader')
								@include('livewire.includes._sort-icon', ['field' => 'user_id'])
							</div>
						</th>
					</tr>
					</thead>
					<tbody>
					@foreach($subtitles as $subtitle)
						<tr>
							<td>
								@if ($subtitle->torrent->category->image != null)
									<a href="{{ route('categories.show', ['id' => $subtitle->torrent->category->id]) }}">
										<div class="text-center">
											<img src="{{ url('files/img/' . $subtitle->torrent->category->image) }}" data-toggle="tooltip"
											     data-original-title="{{$subtitle->torrent->category->name }} {{ strtolower(trans('torrent.torrent')) }}"
											     alt="{{ $subtitle->torrent->category->name }}">
										</div>
									</a>
								@else
									<a href="{{ route('categories.show', ['id' => $subtitle->torrent->category->id]) }}">
										<div class="text-center">
											<i class="{{ $subtitle->torrent->category->icon }} torrent-icon" data-toggle="tooltip"
											   data-original-title="{{ $subtitle->torrent->category->name }} {{ strtolower(trans('torrent.torrent')) }}"></i>
										</div>
									</a>
								@endif
							</td>
							<td>
								<a class="movie-title" href="{{ route('torrent', ['id' => $subtitle->torrent->id]) }}">
									{{ $subtitle->torrent->name }}
								</a>
							</td>
							<td>
								{{ $subtitle->language->name }}
								<i class="{{ config("other.font-awesome") }} fa-closed-captioning" data-toggle="tooltip"
								   data-title="{{ $subtitle->note }}"></i>
							</td>
							<td>
								<a href="{{ route('subtitles.download', ['id' => $subtitle->id]) }}"
								   class="btn btn-xs btn-warning">@lang('common.download')</a>
							</td>
							<td>{{ $subtitle->extension }}</td>
							<td>{{ $subtitle->getSize() }}</td>
							<td>{{ $subtitle->downloads }}</td>
							<td>{{ $subtitle->created_at->diffForHumans() }}</td>
							<td>
								@if ($subtitle->anon == true)
									<span class="badge-user text-orange text-bold">{{ strtoupper(trans('common.anonymous')) }}
										@if (auth()->user()->id == $subtitle->user_id || auth()->user()->hasRole('moderator'))
											<a href="{{ route('users.show', ['username' => $subtitle->user->username]) }}">
                                                ({{ $subtitle->user->username }})
                                            </a>
										@endif
                                    </span>
								@else
									<a href="{{ route('users.show', ['username' => $subtitle->user->username]) }}">
                                        <span class="badge-user text-bold"
                                            style="color:{{ $subtitle->user->group->color }}; background-image:{{ $subtitle->user->group->effect }};">
                                        <i class="{{ $subtitle->user->group->icon }}" data-toggle="tooltip"
                                            data-original-title="{{ $subtitle->user->group->name }}"></i>
	                                        {{ $subtitle->user->username }}
                                        </span>
									</a>
								@endif
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
				<div class="text-center">
					{{ $subtitles->links() }}
				</div>
			</div>
		</div>
	</div>
</div>
