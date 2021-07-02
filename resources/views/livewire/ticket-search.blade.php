<div>
	<div class="mb-10 form-inline pull-left">
		<a href="{{ route('tickets.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> @lang('ticket.create-ticket')</a>
	</div>
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
			<input type="text" wire:model="search" class="form-control" style="width: 275px;" placeholder="@lang('ticket.subject')"/>
		</div>
	</div>
	<div class="box-body no-padding">
		<table class="table vertical-align table-hover">
			<tbody>
			<tr>
				<th>
					<div sortable wire:click="sortBy('id')" :direction="$sortField === 'id' ? $sortDirection : null" role="button">
						#
						@include('livewire.includes._sort-icon', ['field' => 'id'])
					</div>
				</th>
				<th>
					<div sortable wire:click="sortBy('subject')" :direction="$sortField === 'subject' ? $sortDirection : null" role="button">
						@lang('ticket.subject')
						@include('livewire.includes._sort-icon', ['field' => 'subject'])
					</div>
				</th>
				<th>
					<div sortable wire:click="sortBy('priority_id')" :direction="$sortField === 'priority_id' ? $sortDirection : null" role="button">
						@lang('ticket.priority')
						@include('livewire.includes._sort-icon', ['field' => 'priority_id'])
					</div>
				</th>
				<th>
					<div sortable wire:click="sortBy('user_id')" :direction="$sortField === 'user_id' ? $sortDirection : null" role="button">
						@lang('common.username')
						@include('livewire.includes._sort-icon', ['field' => 'user_id'])
					</div>
				</th>
				<th class="hidden-sm hidden-xs">
					<div sortable wire:click="sortBy('closed_at')" :direction="$sortField === 'closed_at' ? $sortDirection : null"
					     role="button">
						@lang('common.status')
						@include('livewire.includes._sort-icon', ['field' => 'closed_at'])
					</div>
				</th>
				<th class="hidden-sm hidden-xs">
					<div sortable wire:click="sortBy('staff_id')" :direction="$sortField === 'staff_id' ? $sortDirection : null"
					     role="button">
						@lang('ticket.assigned-staff')
						@include('livewire.includes._sort-icon', ['field' => 'staff_id'])
					</div>
				</th>
				<th class="hidden-sm hidden-xs">
					<div sortable wire:click="sortBy('created_at')" :direction="$sortField === 'created_at' ? $sortDirection : null"
					     role="button">
						@lang('ticket.created')
						@include('livewire.includes._sort-icon', ['field' => 'created_at'])
					</div>
				</th>
				<th>@lang('common.action')</th>
			</tr>
			@foreach ($tickets as $ticket)
				<tr>
					<td>
						<span class="badge-user">
							{{ $ticket->id }}
						</span>
					</td>
					<td>
						<span>
							<a href="{{ route('tickets.show', ['id' => $ticket->id]) }}">{{ $ticket->subject }}</a>
						</span>
						@if (auth()->user()->hasRole('moderator'))
							@php
								$myTicketUnread = DB::table('tickets')
									->where('id', '=', $ticket->id)
									->where('staff_id', '=', auth()->user()->id)
									->where('staff_read', '=', 0)
									->count();

								$unasignedTicketUnread = DB::table('tickets')
									->where('id', '=', $ticket->id)
									->whereNull('staff_id')
									->whereNull('closed_at')
									->count();
							@endphp
						@else
							@php
								$myTicketUnread = DB::table('tickets')
									->where('id', '=', $ticket->id)
									->where('user_id', '=', auth()->user()->id)
									->where('user_read', '=', 0)
									->count();

								$unasignedTicketUnread = 0;
							@endphp
						@endif

						@if ($myTicketUnread > 0 || $unasignedTicketUnread > 0)
							<i style="color: #0dffff;vertical-align: 1px;" class="fas fa-circle fa-xs"></i>
						@endif
					</td>
					<td>
						<span class="badge-user">
							@if($ticket->priority->name === 'Low')
								<i class="fas fa-circle text-yellow"></i>
							@elseif ($ticket->priority->name === 'Medium')
								<i class="fas fa-circle text-orange"></i>
							@elseif ($ticket->priority->name === 'High')
								<i class="fas fa-circle text-red"></i>
							@endif
							{{ $ticket->priority->name }}
						</span>
					</td>
					<td>
						<span class="badge-user">
							<a href="{{ route('users.show', ['username' => $ticket->user->username]) }}">
								{{ $ticket->user->username }}
							</a>
						</span>
					</td>
					<td>
						<span class="badge-user">
							@if($ticket->closed_at)
								<i class="fas fa-circle text-danger"></i> Closed
							@else
								<i class="fas fa-circle text-success"></i> Open
							@endif
						</span>
					</td>
					<td>
						<span class="badge-user">
							<a href="{{ route('users.show', ['username' => $ticket->staff->username ?? 'System']) }}">
								{{ $ticket->staff->username ?? 'Unassigned' }}
							</a>
						</span>
					</td>
					<td>
						<span class="badge-user">
							{{ $ticket->created_at->diffForHumans() }}
						</span>
					</td>
					<td>
						<div class="dropdown">
							<a class="dropdown-toggle btn btn-default btn-xs" data-toggle="dropdown" href="#" aria-expanded="true">
								@lang('common.actions')
								<i class="fas fa-caret-circle-right"></i>
							</a>
							<ul class="dropdown-menu">
								<li role="presentation">
									<a role="menuitem" tabindex="-1" target="_blank"
									   href="{{ route('tickets.show', ['id' => $ticket->id]) }}">View</a>
								</li>
								<li role="presentation">
									<form method="POST" action="{{ route('tickets.close', ['id' => $ticket->id]) }}">
										@csrf
										<button type="submit" role="menuitem" tabindex="-1" style="background-color: #fff; color: #555;
										font-weight: 400; padding: 7px 15px;">Close</button>
									</form>
								</li>
							</ul>
						</div>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		@if (! $tickets->count())
			<div class="margin-10">
				@lang('common.no-result')
			</div>
		@endif
		<br>
		<div class="text-center">
			{{ $tickets->links() }}
		</div>
	</div>
</div>
