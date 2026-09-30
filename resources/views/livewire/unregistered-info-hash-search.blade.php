<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('livewire-interface.unregistered-info-hashes') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="username"
                        wire:model.live="username"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="username">
                        {{ __('common.username') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <select
                        id="groupBy"
                        wire:model.live="groupBy"
                        class="form__select"
                        placeholder=" "
                    >
                        <option value="none">{{ __('livewire-interface.none') }}</option>
                        <option value="info_hash">{{ __('torrent.info-hash') }}</option>
                    </select>
                    <label class="form__label form__label--floating" for="groupBy">
                        {{ __('livewire-interface.group-by') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <label class="form__label">
                        <input
                            wire:model.live="excludeSoftDeletedTorrents"
                            type="checkbox"
                            class="form__checkbox"
                        />
                        {{ __('livewire-interface.exclude-soft-deleted-torrents') }}
                    </label>
                </div>
            </div>
        </div>
    </header>
    <div class="panel__body" wire:loading.block>{{ __('livewire-interface.loading') }}</div>
    <div class="data-table-wrapper">
        <table class="data-table">
            @switch($groupBy)
                @case('none')
                    <thead>
                        <tr>
                            <th wire:click="sortBy('user_id')" role="columnheader button">
                                {{ __('user.user') }}
                                @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                            </th>
                            <th wire:click="sortBy('info_hash')" role="columnheader button">
                                {{ __('torrent.info-hash') }} {{ __('livewire-interface.hex-encoded') }}
                                @include('livewire.includes._sort-icon', ['field' => 'info_hash'])
                            </th>
                            <th wire:click="sortBy('created_at')" role="columnheader button">
                                {{ __('forum.created-at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                            <th wire:click="sortBy('updated_at')" role="columnheader button">
                                {{ __('torrent.updated_at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'updated_at'])
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unregisteredInfoHashes as $unregisteredInfoHash)
                            <tr>
                                <td>
                                    <x-user-tag
                                        :user="$unregisteredInfoHash->user"
                                        :anon="false"
                                    />
                                </td>
                                <td>{{ bin2hex($unregisteredInfoHash->info_hash) }}</td>
                                <td>
                                    <time
                                        datetime="{{ $unregisteredInfoHash->created_at }}"
                                        title="{{ $unregisteredInfoHash->created_at }}"
                                    >
                                        {{ $unregisteredInfoHash->created_at?->diffForHumans() ?? __('livewire-interface.not-applicable') }}
                                    </time>
                                </td>
                                <td>
                                    <time
                                        datetime="{{ $unregisteredInfoHash->updated_at }}"
                                        title="{{ $unregisteredInfoHash->updated_at }}"
                                    >
                                        {{ $unregisteredInfoHash->updated_at?->diffForHumans() ?? __('livewire-interface.not-applicable') }}
                                    </time>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    @break
                @case('info_hash')
                    <thead>
                        <tr>
                            <th wire:click="sortBy('info_hash')" role="columnheader button">
                                {{ __('torrent.info-hash') }} {{ __('livewire-interface.hex-encoded') }}
                                @include('livewire.includes._sort-icon', ['field' => 'info_hash'])
                            </th>
                            <th wire:click="sortBy('created_at')" role="columnheader button">
                                {{ __('forum.created-at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                            <th wire:click="sortBy('updated_at')" role="columnheader button">
                                {{ __('torrent.updated_at') }}
                                @include('livewire.includes._sort-icon', ['field' => 'updated_at'])
                            </th>
                            <th wire:click="sortBy('amount')" role="columnheader button">
                                {{ __('livewire-interface.user-count') }}
                                @include('livewire.includes._sort-icon', ['field' => 'amount'])
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unregisteredInfoHashes as $unregisteredInfoHash)
                            <tr>
                                <td>{{ bin2hex($unregisteredInfoHash->info_hash) }}</td>
                                <td>
                                    <time
                                        datetime="{{ $unregisteredInfoHash->created_at }}"
                                        title="{{ $unregisteredInfoHash->created_at }}"
                                    >
                                        {{ $unregisteredInfoHash->created_at?->diffForHumans() ?? __('livewire-interface.not-applicable') }}
                                    </time>
                                </td>
                                <td>
                                    <time
                                        datetime="{{ $unregisteredInfoHash->updated_at }}"
                                        title="{{ $unregisteredInfoHash->updated_at }}"
                                    >
                                        {{ $unregisteredInfoHash->updated_at?->diffForHumans() ?? __('livewire-interface.not-applicable') }}
                                    </time>
                                </td>
                                <td>{{ $unregisteredInfoHash->amount }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                    @break
            @endswitch
        </table>
        {{ $unregisteredInfoHashes->links('partials.pagination') }}
    </div>
</section>
