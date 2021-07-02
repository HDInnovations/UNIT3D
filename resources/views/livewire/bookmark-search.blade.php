<div>
	<div class="mb-10 form-inline pull-right">
		<div class="form-group">
			@lang('common.quantity')
			<select wire:model="perPage" class="form-control">
				<option>25</option>
				<option>50</option>
				<option>100</option>
			</select>
		</div>

		<div class="form-group">
			<input type="text" wire:model="search" class="form-control" style="width: 275px;" placeholder="@lang('torrent.search-by-name')"/>
		</div>
	</div>
	<div class="box-body no-padding">
		<table class="table vertical-align table-hover">
			<tbody>
			<tr>
				<th>
					<div sortable wire:click="sortBy('category_id')" :direction="$sortField === 'category_id' ? $sortDirection : null" role="button">
						@lang('torrent.category')
						@include('livewire.includes._sort-icon', ['field' => 'category_id'])
					</div>
				</th>
				<th>
					@lang('torrent.type')/@lang('torrent.resolution')
				</th>
				<th>
					<div sortable wire:click="sortBy('name')" :direction="$sortField === 'name' ? $sortDirection : null" role="button">
						@lang('common.name')
						@include('livewire.includes._sort-icon', ['field' => 'name'])
					</div>
				</th>
				<th>
					<div sortable wire:click="sortBy('size')" :direction="$sortField === 'size' ? $sortDirection : null" role="button">
						@lang('torrent.size')
						@include('livewire.includes._sort-icon', ['field' => 'size'])
					</div>
				</th>
				<th class="hidden-sm hidden-xs">
					<div sortable wire:click="sortBy('seeders')" :direction="$sortField === 'seeders' ? $sortDirection : null"
					     role="button">
						@lang('torrent.seeders')
						@include('livewire.includes._sort-icon', ['field' => 'seeders'])
					</div>
				</th>
				<th class="hidden-sm hidden-xs">
					<div sortable wire:click="sortBy('leechers')" :direction="$sortField === 'leechers' ? $sortDirection : null"
					     role="button">
						@lang('torrent.leechers')
						@include('livewire.includes._sort-icon', ['field' => 'leechers'])
					</div>
				</th>
				<th class="hidden-sm hidden-xs">
					<div sortable wire:click="sortBy('times_completed')" :direction="$sortField === 'times_completed' ? $sortDirection : null"
					     role="button">
						@lang('torrent.completed-times')
						@include('livewire.includes._sort-icon', ['field' => 'times_completed'])
					</div>
				</th>
				<th class="hidden-sm hidden-xs">
					<div sortable wire:click="sortBy('created_at')" :direction="$sortField === 'created_at' ? $sortDirection : null"
					     role="button">
						@lang('torrent.created_at')
						@include('livewire.includes._sort-icon', ['field' => 'created_at'])
					</div>
				</th>
				<th>Action</th>
			</tr>
			@foreach ($bookmarks as $bookmark)
				<tr>
					<td>
						@if ($bookmark->category->image != null)
							<a href="{{ route('categories.show', ['id' => $bookmark->category->id]) }}">
								<div class="text-center">
									<img src="{{ url('files/img/' . $bookmark->category->image) }}" data-toggle="tooltip"
									     data-original-title="{{ $bookmark->category->name }} {{ strtolower(trans('torrent.torrent')) }}"
									     alt="{{ $bookmark->category->name }}">
								</div>
							</a>
						@else
							<a href="{{ route('categories.show', ['id' => $bookmark->category->id]) }}">
								<div class="text-center">
									<i class="{{ $bookmark->category->icon }} torrent-icon" data-toggle="tooltip"
									   data-original-title="{{ $bookmark->category->name }} {{ strtolower(trans('torrent.torrent')) }}"></i>
								</div>
							</a>
						@endif
					</td>
					<td>
						<div class="text-center">
							<span class="label label-success" data-toggle="tooltip"
							      data-original-title="@lang('torrent.type')">
								{{ $bookmark->type->name }}
							</span>
						</div>
						<div class="text-center" style="padding-top: 8px;">
							<span class="label label-success" data-toggle="tooltip"
							      data-original-title="@lang('torrent.resolution')">
								{{ $bookmark->resolution->name ?? 'No Res' }}
							</span>
						</div>
					</td>
					<td>
						<a class="view-torrent" href="{{ route('torrent', ['id' => $bookmark->id]) }}">
							{{ $bookmark->name }}
						</a>
						<br>
						@if ($bookmark->anon == 1)
							<span class="badge-extra text-bold">
                                <i class="{{ config('other.font-awesome') }} fa-upload" data-toggle="tooltip"
                                   data-original-title="@lang('torrent.uploader')"></i> @lang('common.anonymous')
								@if ($user->id == $bookmark->user->id || $user->hasRole('moderator'))
									<a href="{{ route('users.show', ['username' => $bookmark->user->username]) }}">
                                        ({{ $bookmark->user->username }})
                                    </a>
								@endif
                            </span>
						@else
							<span class="badge-extra text-bold">
                                <i class="{{ config('other.font-awesome') }} fa-upload" data-toggle="tooltip"
                                   data-original-title="@lang('torrent.uploader')"></i>
                                <a href="{{ route('users.show', ['username' => $bookmark->user->username]) }}">
                                    {{ $bookmark->user->username }}
                                </a>
                            </span>
						@endif

						<span class="badge-extra text-bold text-pink">
							<i class="{{ config('other.font-awesome') }} fa-heart" data-toggle="tooltip"
							   data-original-title="@lang('torrent.thanks-given')"></i>
							{{ $bookmark->thanks()->count() }}
						</span>

						<a href="{{ route('torrent', ['id' => $bookmark->id, 'hash' => '#comments']) }}">
							<span class="badge-extra text-bold text-green">
								<i class="{{ config('other.font-awesome') }} fa-comment" data-toggle="tooltip"
								   data-original-title="@lang('common.comments')"></i>
								{{ $bookmark->comments()->count() }}
							</span>
						</a>

						@if ($bookmark->internal == 1)
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-magic' data-toggle='tooltip' title=''
								   data-original-title='@lang('torrent.internal-release')' style="color: #baaf92;"></i>
							</span>
						@endif

						@if ($bookmark->stream == 1)
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-play text-red' data-toggle='tooltip'
								   title='' data-original-title='@lang('torrent.stream-optimized')'></i>
							</span>
						@endif

						@if ($bookmark->featured == 0)
							@if ($bookmark->doubleup == 1)
								<span class='badge-extra text-bold'>
									<i class='{{ config('other.font-awesome') }} fa-gem text-green' data-toggle='tooltip'
									   title='' data-original-title='@lang('torrent.double-upload')'></i>
								</span>
							@endif
							@if ($bookmark->free == 1)
								<span class='badge-extra text-bold'>
									<i class='{{ config('other.font-awesome') }} fa-star text-gold' data-toggle='tooltip'
									   title='' data-original-title='@lang('torrent.freeleech')'></i>
								</span>
							@endif
						@endif

						@if ($personal_freeleech)
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-id-badge text-orange' data-toggle='tooltip'
								   title='' data-original-title='@lang('torrent.personal-freeleech')'></i>
							</span>
						@endif

						@if ($user->freeleechTokens->where('torrent_id', $bookmark->id)->first())
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-star text-bold' data-toggle='tooltip'
								   title='' data-original-title='@lang('torrent.freeleech-token')'></i>
							</span>
						@endif

						@if ($bookmark->featured == 1)
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
								   data-toggle='tooltip' title=''
								   data-original-title='@lang('torrent.special-double_upload')'></i>
							</span>
						@endif

						@if ($bookmark->leechers >= 5)
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-fire text-orange' data-toggle='tooltip'
								   title='' data-original-title='@lang('common.hot')'></i>
							</span>
						@endif

						@if ($bookmark->sticky == 1)
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-thumbtack text-black' data-toggle='tooltip'
								   title='' data-original-title='@lang('torrent.sticky')'></i>
							</span>
						@endif

						@if ($bookmark->highspeed == 1)
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-tachometer text-red' data-toggle='tooltip'
								   title='' data-original-title='@lang('common.high-speeds')'></i>
							</span>
						@endif

						@if ($bookmark->sd == 1)
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-ticket text-orange' data-toggle='tooltip'
								   title='' data-original-title='@lang('torrent.sd-content')'></i>
							</span>
						@endif

						@if ($bookmark->bumped_at != $bookmark->created_at && $bookmark->bumped_at < Carbon\Carbon::now()->addDay(2))
							<span class='badge-extra text-bold'>
								<i class='{{ config('other.font-awesome') }} fa-level-up-alt text-gold' data-toggle='tooltip'
								   title='' data-original-title='Recently Bumped!'></i>
							</span>
						@endif
					</td>
					<td>{{ $bookmark->getSize() }}</td>
					<td class="hidden-sm hidden-xs">{{ $bookmark->seeders }}</td>
					<td class="hidden-sm hidden-xs">{{ $bookmark->leechers }}</td>
					<td class="hidden-sm hidden-xs">{{ $bookmark->times_completed }}</td>
					<td class="hidden-sm hidden-xs">{{ $bookmark->created_at->diffForHumans() }}</td>
					<td>
						<div class="dropdown">
							<a class="dropdown-toggle btn btn-default btn-xs" data-toggle="dropdown" href="#" aria-expanded="true">
								Actions
								<i class="fas fa-caret-circle-right"></i>
							</a>
							<ul class="dropdown-menu">
								<li role="presentation">
									<a role="menuitem" tabindex="-1" target="_blank"
									   href="{{ route('torrent', ['id' => $bookmark->id]) }}">@lang('common.view') @lang('torrent.torrent')</a>
								</li>
								<li role="presentation">
									<a role="menuitem" tabindex="-1"
									   href="{{ route('download', ['id' => $bookmark->id]) }}">@lang('common.download') @lang('torrent.torrent')</a>
								</li>
							</ul>
						</div>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		@if (! $bookmarks->count())
			<div class="margin-10">
				<h1>
					<i class="{{ config('other.font-awesome') }} fa-frown"></i>
					@lang('torrent.no-bookmarks')
				</h1>
			</div>
		@endif
		<br>
		<div class="text-center">
			{{ $bookmarks->links() }}
		</div>
	</div>
</div>
