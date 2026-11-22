<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('staff.warnings-log') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="show"
                        class="form__checkbox"
                        type="checkbox"
                        wire:model.live="show"
                    />
                    <label class="form__label" for="show">Show soft deletes</label>
                </div>
            </div>
            <div class="panel__action">
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
                        {{ __('common.user') }} {{ __('common.username') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="sender"
                        class="form__text"
                        type="text"
                        wire:model.live="sender"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="sender">
                        {{ __('common.staff') }} {{ __('common.username') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="torrent"
                        class="form__text"
                        type="text"
                        wire:model.live="torrent"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="torrent">
                        {{ __('torrent.torrent') }} {{ __('common.name') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="reason"
                        class="form__text"
                        type="text"
                        wire:model.live="reason"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="reason">
                        {{ __('common.reason') }}
                    </label>
                </div>
            </div>
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
        </div>
    </header>
    <table class="data-table">
        <thead>
            <tr>
                <th wire:click="sortBy('user_id')" role="columnheader button">
                    {{ __('common.user') }}
                    @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                </th>
                <th wire:click="sortBy('warned_by')" role="columnheader button">
                    {{ __('user.warned-by') }}
                    @include('livewire.includes._sort-icon', ['field' => 'warned_by'])
                </th>
                <th wire:click="sortBy('torrent')" role="columnheader button">
                    {{ __('torrent.torrent') }}
                    @include('livewire.includes._sort-icon', ['field' => 'torrent'])
                </th>
                <th>{{ __('common.reason') }}</th>
                <th wire:click="sortBy('created_at')" role="columnheader button">
                    {{ __('common.created_at') }}
                    @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                </th>
                <th wire:click="sortBy('expires_on')" role="columnheader button">
                    {{ __('user.expires-on') }}
                    @include('livewire.includes._sort-icon', ['field' => 'expires_on'])
                </th>
                <th wire:click="sortBy('active')" role="columnheader button">
                    {{ __('common.active') }}
                    @include('livewire.includes._sort-icon', ['field' => 'active'])
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($warnings as $warning)
                <tr>
                    <td>
                        <x-user-tag :anon="false" :user="$warning->user" />
                    </td>
                    <td>
                        <x-user-tag :anon="false" :user="$warning->staff" />
                    </td>
                    <td>
                        @isset($warning->torrent)
                            <a
                                href="{{ route('torrents.show', ['id' => $warning->torrent->id]) }}"
                            >
                                {{ $warning->torrent->name }}
                            </a>
                        @else
                            n/a
                        @endisset
                    </td>
                    <td>{{ $warning->reason }}</td>
                    <td>
                        <time
                            datetime="{{ $warning->created_at }}"
                            title="{{ $warning->created_at }}"
                        >
                            {{ $warning->created_at }}
                        </time>
                    </td>
                    <td>
                        <time
                            datetime="{{ $warning->expires_on }}"
                            title="{{ $warning->expires_on }}"
                        >
                            {{ $warning->expires_on }}
                        </time>
                    </td>
                    <td>
                        @if ($warning->active)
                            <span class="text-green">{{ __('common.yes') }}</span>
                        @else
                            <span class="text-red">{{ __('common.expired') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No warnings</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $warnings->links('partials.pagination') }}
</section>
