<section class="panelV2" x-data="{ tab: @entangle('tab').live }">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('ticket.helpdesk') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <select id="quantity" class="form__select" wire:model.live="perPage" required>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    <label class="form__label form__label--floating" for="quantity">
                        {{ __('common.quantity') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="search"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="search"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="search">
                        {{ __('ticket.subject') }}
                    </label>
                </div>
            </div>
            @if (auth()->user()->group->is_modo)
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="show"
                            class="form__checkbox"
                            type="checkbox"
                            wire:model.live="show"
                        />
                        <label class="form__label" for="show">My Assigned Tickets</label>
                    </div>
                </div>
            @endif

            <div class="panel__action">
                <a href="{{ route('tickets.create') }}" class="form__button form__button--text">
                    {{ __('ticket.create-ticket') }}
                </a>
            </div>
        </div>
    </header>
    <menu class="panel__tabs panel__tabs--centered">
        <li
            class="panel__tab panel__tab--full-width"
            role="tab"
            x-bind:class="tab === 'open' && 'panel__tab--active'"
            x-on:click="tab = 'open'"
        >
            Open
        </li>
        <li
            class="panel__tab panel__tab--full-width"
            role="tab"
            x-bind:class="tab === 'closed' && 'panel__tab--active'"
            x-on:click="tab = 'closed'"
        >
            Closed
        </li>
    </menu>
    <div class="data-table-wrapper">
        <table class="data-table">
            <tbody>
                <tr>
                    <th wire:click="sortBy('id')" role="columnheader button">
                        #
                        @include('livewire.includes._sort-icon', ['field' => 'id'])
                    </th>
                    <th wire:click="sortBy('subject')" role="columnheader button">
                        {{ __('ticket.subject') }}
                        @include('livewire.includes._sort-icon', ['field' => 'subject'])
                    </th>
                    <th wire:click="sortBy('priority_id')" role="columnheader button">
                        {{ __('ticket.priority') }}
                        @include('livewire.includes._sort-icon', ['field' => 'priority_id'])
                    </th>
                    <th wire:click="sortBy('user_id')" role="columnheader button">
                        {{ __('common.username') }}
                        @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                    </th>
                    <th wire:click="sortBy('staff_id')" role="columnheader button">
                        {{ __('ticket.assigned-staff') }}
                        @include('livewire.includes._sort-icon', ['field' => 'staff_id'])
                    </th>
                    <th wire:click="sortBy('created_at')" role="columnheader button">
                        {{ __('ticket.created') }}
                        @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                    </th>
                    <th wire:click="sortBy('updated_at')" role="columnheader button">
                        {{ __('torrent.updated') }}
                        @include('livewire.includes._sort-icon', ['field' => 'updated_at'])
                    </th>
                    <th wire:click="sortBy('closed_at')" role="columnheader button">
                        {{ __('ticket.closed') }}
                        @include('livewire.includes._sort-icon', ['field' => 'closed_at'])
                    </th>
                    <th>{{ __('common.action') }}</th>
                </tr>
                @forelse ($tickets as $ticket)
                    <tr>
                        <td>
                            {{ $ticket->id }}
                        </td>
                        <td>
                            <a href="{{ route('tickets.show', ['ticket' => $ticket]) }}">
                                {{ $ticket->subject }}
                            </a>
                            @if ((auth()->user()->group->is_modo &&
                                (($ticket->staff_id === auth()->id() && $ticket->staff_read === false) ||
                                    ($ticket->staff_id === null && $ticket->closed_at === null))) ||
                                ($ticket->user_id === auth()->id() && $ticket->user_read === false))
                                <i
                                    style="color: #0dffff; vertical-align: 1px"
                                    class="{{ config('other.font-awesome') }} fa-circle fa-xs"
                                ></i>
                            @endif
                        </td>
                        <td>
                            <i
                                class="{{ config('other.font-awesome') }} {{ $ticket->priority->icon }}"
                                style="color: {{ $ticket->priority->color }}"
                            ></i>
                            {{ $ticket->priority->name }}
                        </td>
                        <td>
                            <x-user-tag :user="$ticket->user" :anon="false" />
                        </td>
                        <td>
                            @if ($ticket->staff)
                                <x-user-tag :user="$ticket->staff" :anon="false" />
                            @else
                                Unassigned
                            @endif
                        </td>
                        <td>
                            <time
                                datetime="{{ $ticket->created_at }}"
                                title="{{ $ticket->created_at }}"
                            >
                                {{ $ticket->created_at->diffForHumans() }}
                            </time>
                        </td>
                        <td>
                            <time
                                datetime="{{ $ticket->updated_at }}"
                                title="{{ $ticket->updated_at }}"
                            >
                                {{ $ticket->updated_at->diffForHumans() }}
                            </time>
                        </td>
                        <td>
                            <time
                                datetime="{{ $ticket->closed_at }}"
                                title="{{ $ticket->closed_at }}"
                            >
                                {{ $ticket->closed_at?->diffForHumans() ?? 'N/A' }}
                            </time>
                        </td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    <form
                                        method="POST"
                                        action="{{ route('tickets.close', ['ticket' => $ticket]) }}"
                                    >
                                        @csrf
                                        <button class="form__button form__button--text">
                                            {{ __('ticket.close') }}
                                        </button>
                                    </form>
                                </li>
                            </menu>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">{{ __('common.no-result') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $tickets->links('partials.pagination') }}
    </div>
</section>
