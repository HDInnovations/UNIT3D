<div style="display: flex; flex-direction: column; row-gap: 1rem">
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('common.search') }}</h2>
        </header>
        <div class="panel__body" style="padding: 5px">
            <form class="form">
                <div class="form__group--short-horizontal">
                    <div class="form__group">
                        <input
                            id="sender"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="sender"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="sender">
                            {{ __('user.sender') }}
                        </label>
                    </div>
                    <div class="form__group">
                        <input
                            id="soundexSender"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="soundexSender"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="soundexSender">
                            Soundex {{ __('user.sender') }}
                        </label>
                    </div>
                    <div class="form__group">
                        <input
                            id="receiver"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="receiver"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="receiver">
                            {{ __('bon.receiver') }}
                        </label>
                    </div>
                    <div class="form__group">
                        <input
                            id="soundexReceiver"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="soundexReceiver"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="soundexReceiver">
                            Soundex {{ __('bon.receiver') }}
                        </label>
                    </div>
                    <div class="form__group">
                        <input
                            id="email"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="email"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="email">
                            {{ __('common.email') }}
                        </label>
                    </div>
                    <div class="form__group">
                        <input
                            id="email"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="soundexEmail"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="soundexEmail">
                            Soundex {{ __('common.email') }}
                        </label>
                    </div>

                    @if ($groupBy === 'user_id')
                        <div class="form__group">
                            <input
                                id="threshold"
                                class="form__text"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                max="100"
                                wire:model.live="threshold"
                                placeholder=" "
                                title="Colors 'Percent inactive' red if above this threshold"
                            />
                            <label class="form__label form__label--floating" for="threshold">
                                Threshold
                            </label>
                        </div>
                    @endif

                    <div class="form__group">
                        <select
                            id="groupBy"
                            wire:model.live="groupBy"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="none">None</option>
                            <option value="user_id">Sender</option>
                        </select>
                        <label class="form__label form__label--floating" for="groupBy">
                            Group by
                        </label>
                    </div>
                    <div class="form__group">
                        <input
                            id="code"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="code"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="code">
                            {{ __('common.code') }}
                        </label>
                    </div>
                    <div class="form__group">
                        <input
                            id="custom"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            wire:model.live="custom"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="custom">
                            {{ __('common.message') }}
                        </label>
                    </div>
                    <div class="form__group">
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
                    </div>
                </div>
            </form>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff.invites-log') }}</h2>
        <div class="data-table-wrapper">
            @switch($groupBy)
                @case('user_id')
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('user_id')" role="columnheader button">
                                    {{ __('user.sender') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                                </th>
                                <th
                                    wire:click="sortBy('created_at_min')"
                                    role="columnheader button"
                                >
                                    First sent at
                                    @include('livewire.includes._sort-icon', ['field' => 'created_at_min'])
                                </th>
                                <th
                                    wire:click="sortBy('created_at_avg')"
                                    role="columnheader button"
                                >
                                    Average sent at
                                    @include('livewire.includes._sort-icon', ['field' => 'created_at_avg'])
                                </th>
                                <th
                                    wire:click="sortBy('created_at_max')"
                                    role="columnheader button"
                                >
                                    Last sent at
                                    @include('livewire.includes._sort-icon', ['field' => 'created_at_max'])
                                </th>
                                <th wire:click="sortBy('sent_count')" role="columnheader button">
                                    Invites sent
                                    @include('livewire.includes._sort-icon', ['field' => 'sent_count'])
                                </th>
                                <th
                                    wire:click="sortBy('accepted_by_count')"
                                    role="columnheader button"
                                >
                                    Invites accepted
                                    @include('livewire.includes._sort-icon', ['field' => 'accepted_by_count'])
                                </th>
                                <th
                                    wire:click="sortBy('inactive_count')"
                                    role="columnheader button"
                                >
                                    Inactive count
                                    @include('livewire.includes._sort-icon', ['field' => 'banned_count'])
                                </th>
                                <th
                                    wire:click="sortBy('inactive_ratio')"
                                    role="columnheader button"
                                >
                                    Percent inactive
                                    @include('livewire.includes._sort-icon', ['field' => 'inactive_ratio'])
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invites as $invite)
                                <tr>
                                    <td>
                                        <x-user-tag :anon="false" :user="$invite->sender" />
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $invite->created_at_min }}"
                                            title="{{ $invite->created_at_min }}"
                                        >
                                            {{ $invite->created_at_min->format('Y-m-d') }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $invite->created_at_avg }}"
                                            title="{{ $invite->created_at_avg }}"
                                        >
                                            {{ $invite->created_at_avg->format('Y-m-d') }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $invite->created_at_max }}"
                                            title="{{ $invite->created_at_max }}"
                                        >
                                            {{ $invite->created_at_max->format('Y-m-d') }}
                                        </time>
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('users.invites.index', ['user' => $invite->sender]) }}"
                                        >
                                            {{ $invite->sent_count ?? 0 }}
                                        </a>
                                    </td>
                                    <td>{{ $invite->accepted_by_count ?? 0 }}</td>
                                    <td>{{ $invite->inactive_count ?? 0 }}</td>
                                    <td
                                        class="{{ $invite->inactive_ratio < $threshold ? 'text-green' : 'text-red' }}"
                                    >
                                        {{ number_format($invite->inactive_ratio, 1) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No invites</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @break
                @default
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('id')" role="columnheader button">
                                    ID
                                    @include('livewire.includes._sort-icon', ['field' => 'id'])
                                </th>
                                <th wire:click="sortBy('user_id')" role="columnheader button">
                                    {{ __('user.sender') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                                </th>
                                <th wire:click="sortBy('email')" role="columnheader button">
                                    {{ __('common.email') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'email'])
                                </th>
                                <th wire:click="sortBy('code')" role="columnheader button">
                                    Code
                                    @include('livewire.includes._sort-icon', ['field' => 'code'])
                                </th>
                                <th wire:click="sortBy('custom')" role="columnheader button">
                                    {{ __('common.message') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'custom'])
                                </th>
                                <th wire:click="sortBy('created_at')" role="columnheader button">
                                    {{ __('user.created-on') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                                </th>
                                <th wire:click="sortBy('expires_on')" role="columnheader button">
                                    {{ __('user.expires-on') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'expires_on'])
                                </th>
                                <th wire:click="sortBy('accepted_by')" role="columnheader button">
                                    {{ __('user.accepted-by') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'accepted_by'])
                                </th>
                                <th wire:click="sortBy('accepted_at')" role="columnheader button">
                                    {{ __('user.accepted-at') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'accepted_at'])
                                </th>
                                <th wire:click="sortBy('deleted_at')" role="columnheader button">
                                    {{ __('user.deleted-on') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'deleted_at'])
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($invites as $invite)
                                <tr>
                                    <td>{{ $invite->id }}</td>
                                    <td>
                                        <x-user-tag :anon="false" :user="$invite->sender" />
                                    </td>
                                    <td>{{ $invite->email }}</td>
                                    <td>{{ $invite->code }}</td>
                                    {{-- format-ignore-start --}}<td style="white-space: pre-wrap">{{ $invite->custom }}</td>{{-- format-ignore-end --}}
                                    <td>
                                        <time
                                            datetime="{{ $invite->created_at }}"
                                            title="{{ $invite->created_at }}"
                                        >
                                            {{ $invite->created_at }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $invite->expires_on }}"
                                            title="{{ $invite->expires_on }}"
                                        >
                                            {{ $invite->expires_on }}
                                        </time>
                                    </td>
                                    <td>
                                        @if ($invite->accepted_by === null)
                                            N/A
                                        @else
                                            <x-user-tag :anon="false" :user="$invite->receiver" />
                                        @endif
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $invite->accepted_at }}"
                                            title="{{ $invite->accepted_at }}"
                                        >
                                            {{ $invite->accepted_at ?? 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $invite->deleted_at }}"
                                            title="{{ $invite->deleted_at }}"
                                        >
                                            {{ $invite->deleted_at ?? 'N/A' }}
                                        </time>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">No invites</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
            @endswitch
        </div>
        {{ $invites->links('partials.pagination') }}
    </section>
</div>
