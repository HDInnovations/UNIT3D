<div style="display: flex; flex-direction: column; row-gap: 1rem">
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('common.search') }}</h2>
        </header>
        <div class="panel__body" style="padding: 5px">
            <form class="form">
                <div class="form__group--short-horizontal">
                    <p class="form__group">
                        <input
                            id="reporter"
                            class="form__text"
                            autocomplete="off"
                            placeholder=" "
                            type="search"
                            wire:model.live="reporter"
                        />
                        <label class="form__label form__label--floating" for="reporter">
                            {{ __('common.reporter') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="reported"
                            class="form__text"
                            autocomplete="off"
                            placeholder=" "
                            type="search"
                            wire:model.live="reported"
                        />
                        <label class="form__label form__label--floating" for="reported">
                            Reported
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="staff"
                            class="form__text"
                            autocomplete="off"
                            placeholder=" "
                            type="search"
                            wire:model.live="staff"
                        />
                        <label class="form__label form__label--floating" for="staff">
                            {{ __('user.judge') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="verdict"
                            class="form__text"
                            autocomplete="off"
                            placeholder=" "
                            type="search"
                            wire:model.live="verdict"
                        />
                        <label class="form__label form__label--floating" for="verdict">
                            Verdict
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="message"
                            class="form__text"
                            autocomplete="off"
                            placeholder=" "
                            type="search"
                            wire:model.live="message"
                        />
                        <label class="form__label form__label--floating" for="message">
                            {{ __('common.message') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="title"
                            class="form__text"
                            autocomplete="off"
                            placeholder=" "
                            type="search"
                            wire:model.live="title"
                        />
                        <label class="form__label form__label--floating" for="title">
                            {{ __('common.title') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="type"
                            wire:model.live="type"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="">Any</option>
                            <option value="Torrent">Torrent</option>
                            <option value="Request">Request</option>
                            <option value="User">User</option>
                        </select>
                        <label class="form__label form__label--floating" for="type">
                            {{ __('common.type') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="status"
                            wire:model.live="status"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="open">Open</option>
                            <option value="snoozed">Snoozed</option>
                            <option value="closed">Closed</option>
                            <option value="all">All</option>
                            <option value="all_open">All open</option>
                        </select>
                        <label class="form__label form__label--floating" for="status">
                            {{ __('common.status') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="quantity"
                            class="form__select"
                            wire:model.live="perPage"
                            required
                        >
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <label class="form__label form__label--floating" for="quantity">
                            {{ __('common.quantity') }}
                        </label>
                    </p>
                </div>
            </form>
        </div>
    </section>
    <div class="panelV2">
        <h2 class="panel__heading">{{ __('staff.reports-log') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('id')" role="columnheader button">
                            ID
                            @include('livewire.includes._sort-icon', ['field' => 'id'])
                        </th>
                        <th wire:click="sortBy('type')" role="columnheader button">
                            Type
                            @include('livewire.includes._sort-icon', ['field' => 'type'])
                        </th>
                        <th wire:click="sortBy('title')" role="columnheader button">
                            {{ __('common.title') }}
                            @include('livewire.includes._sort-icon', ['field' => 'title'])
                        </th>
                        <th wire:click="sortBy('reported_user')" role="columnheader button">
                            Reported
                            @include('livewire.includes._sort-icon', ['field' => 'reported_user'])
                        </th>
                        <th wire:click="sortBy('reporter_id')" role="columnheader button">
                            {{ __('common.reporter') }}
                            @include('livewire.includes._sort-icon', ['field' => 'reporter_id'])
                        </th>
                        <th wire:click="sortBy('assigned_to')" role="columnheader button">
                            {{ __('ticket.assigned-staff') }}
                            @include('livewire.includes._sort-icon', ['field' => 'assigned_to'])
                        </th>
                        <th wire:click="sortBy('created_at')" role="columnheader button">
                            {{ __('user.created-on') }}
                            @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                        </th>
                        <th wire:click="sortBy('solved_by')" role="columnheader button">
                            {{ __('user.judge') }}
                            @include('livewire.includes._sort-icon', ['field' => 'solved_by'])
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ $report->type }}</td>
                            <td>
                                <a href="{{ route('staff.reports.show', ['report' => $report]) }}">
                                    {{ $report->title }}
                                </a>
                            </td>
                            <td>
                                <x-user-tag :anon="false" :user="$report->reported" />
                            </td>
                            <td>
                                <x-user-tag :anon="false" :user="$report->reporter" />
                            </td>
                            <td>
                                @if ($report->assignee)
                                    <x-user-tag :anon="false" :user="$report->assignee" />
                                @else
                                        Unassigned
                                @endif
                            </td>
                            <td>
                                <time
                                    datetime="{{ $report->created_at }}"
                                    title="{{ $report->created_at }}"
                                >
                                    {{ $report->created_at->toDayDateTimeString() }}
                                </time>
                            </td>
                            <td>
                                @if ($report->judge)
                                    <x-user-tag :anon="false" :user="$report->judge" />
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No reports</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->links('partials.pagination') }}
    </div>
</div>
