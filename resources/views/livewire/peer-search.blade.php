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
                            id="torrent"
                            wire:model.live="torrent"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="torrent">
                            Torrent name
                        </label>
                    </p>
                    <p class="form__group">
                        <input id="ip" wire:model.live="ip" class="form__text" placeholder=" " />
                        <label class="form__label form__label--floating" for="ip">IP address</label>
                    </p>
                    <p class="form__group">
                        <input
                            id="port"
                            wire:model.live="port"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="port">Port</label>
                    </p>
                    <p class="form__group">
                        <input
                            id="agent"
                            wire:model.live="agent"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="agent">Agent</label>
                    </p>
                    <p class="form__group">
                        <select
                            id="connectivity"
                            wire:model.live="connectivity"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="any">Any</option>
                            <option value="connectable">Connectable</option>
                            <option value="unconnectable">Unconnectable</option>
                        </select>
                        <label class="form__label form__label--floating" for="connectivity">
                            Connectivity
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="active"
                            wire:model.live="active"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="any">Any</option>
                            <option value="exclude">Inactive</option>
                            <option value="include">Active</option>
                        </select>
                        <label class="form__label form__label--floating" for="active">Active</label>
                    </p>
                    <p class="form__group">
                        <select
                            id="groupBy"
                            wire:model.live="groupBy"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="none">None</option>
                            <option value="user_session">User session</option>
                            <option value="user_ip">User IP</option>
                            <option value="user">User</option>
                        </select>
                        <label class="form__label form__label--floating" for="groupBy">
                            Group by
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input
                                wire:model.live="sharedIpsOnly"
                                type="checkbox"
                                class="form__checkbox"
                            />
                            Shared IPs only
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input
                                wire:model.live="sharedSocketsOnly"
                                type="checkbox"
                                class="form__checkbox"
                            />
                            Shared sockets only
                        </label>
                    </p>
                    <p class="form__group">
                        <label class="form__label">
                            <input
                                wire:model.live="includeSeedsize"
                                type="checkbox"
                                class="form__checkbox"
                            />
                            Include seedsize
                        </label>
                    </p>
                </div>
            </form>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">Peers</h2>
        <div class="panel__body" wire:loading.block>Loading...</div>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('peers.user_id')" role="columnheader button">
                            {{ __('user.user') }}
                            @include('livewire.includes._sort-icon', ['field' => 'peers.user_id'])
                        </th>
                        @if ($groupBy !== 'none')
                            <th
                                wire:click="sortBy('peer_count')"
                                role="columnheader button"
                                style="text-align: right"
                            >
                                {{ __('torrent.peers') }}
                                @include('livewire.includes._sort-icon', ['field' => 'peer_count'])
                            </th>
                        @endif

                        <th wire:click="sortBy('torrent_id')" role="columnheader button">
                            @if ($groupBy === 'none')
                                {{ __('torrent.torrent') }}
                            @else
                                {{ __('torrent.torrents') }}
                                @include('livewire.includes._sort-icon', ['field' => 'torrent_id'])
                            @endif
                        </th>
                        <th wire:click="sortBy('agent')" role="columnheader button">
                            @if ($groupBy === 'user_ip' || $groupBy === 'user')
                                Agents
                            @else
                                {{ __('torrent.agent') }}
                            @endif
                            @include('livewire.includes._sort-icon', ['field' => 'agent'])
                        </th>
                        <th
                            wire:click="sortBy('ip')"
                            role="columnheader button"
                            style="text-align: right"
                        >
                            @if ($groupBy === 'none' || $groupBy === 'user_ip' || $groupBy === 'user_session')
                                IP
                            @else
                                IPs
                            @endif
                            @include('livewire.includes._sort-icon', ['field' => 'ip'])
                        </th>
                        <th
                            wire:click="sortBy('port')"
                            role="columnheader button"
                            style="text-align: right"
                        >
                            @if ($groupBy === 'user_ip' || $groupBy === 'user')
                                Ports
                            @else
                                Port
                            @endif
                            @include('livewire.includes._sort-icon', ['field' => 'port'])
                        </th>
                        <th
                            wire:click="sortBy('uploaded')"
                            role="columnheader button"
                            style="text-align: right"
                        >
                            {{ __('torrent.uploaded') }}
                            @include('livewire.includes._sort-icon', ['field' => 'uploaded'])
                        </th>
                        <th
                            wire:click="sortBy('downloaded')"
                            role="columnheader button"
                            style="text-align: right"
                        >
                            {{ __('torrent.downloaded') }}
                            @include('livewire.includes._sort-icon', ['field' => 'downloaded'])
                        </th>
                        <th
                            wire:click="sortBy('left')"
                            role="columnheader button"
                            style="text-align: right"
                        >
                            {{ __('torrent.left') }}
                            @include('livewire.includes._sort-icon', ['field' => 'left'])
                        </th>
                        @if ($groupBy === 'none')
                            @if ($includeSeedsize)
                                <th
                                    wire:click="sortBy('size')"
                                    wire:key="size"
                                    role="columnheader button"
                                    style="text-align: right"
                                >
                                    {{ __('torrent.size') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'size'])
                                </th>
                            @else
                                <th style="text-align: right">{{ __('torrent.size') }}</th>
                            @endif
                        @else
                            @if ($includeSeedsize)
                                <th
                                    wire:click="sortBy('size')"
                                    role="columnheader button"
                                    style="text-align: right"
                                >
                                    {{ __('torrent.size') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'size'])
                                </th>
                                @if (\config('announce.connectable_check'))
                                    <th
                                        wire:click="sortBy('connectable_size')"
                                        role="columnheader button"
                                        style="text-align: right"
                                    >
                                        Connectable {{ __('torrent.size') }}
                                        @include('livewire.includes._sort-icon', ['field' => 'connectable_size'])
                                    </th>
                                    <th
                                        wire:click="sortBy('unconnectable_size')"
                                        role="columnheader button"
                                        style="text-align: right"
                                    >
                                        Unconnectable {{ __('torrent.size') }}
                                        @include('livewire.includes._sort-icon', ['field' => 'unconnectable_size'])
                                    </th>
                                @endif
                            @endif
                        @endif
                        @if (\config('announce.connectable_check'))
                            @if ($groupBy === 'none')
                                <th
                                    wire:click="sortBy('connectable')"
                                    role="columnheader button"
                                    style="text-align: right"
                                >
                                    Connectable
                                    @include('livewire.includes._sort-icon', ['field' => 'connectable'])
                                </th>
                            @else
                                <th
                                    wire:click="sortBy('connectable_count')"
                                    role="columnheader button"
                                    style="text-align: right"
                                >
                                    Connectable {{ __('torrent.peers') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'connectable_count'])
                                </th>
                                <th
                                    wire:click="sortBy('unconnectable_count')"
                                    role="columnheader button"
                                    style="text-align: right"
                                >
                                    Unconnectable {{ __('torrent.peers') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'unconnectable_count'])
                                </th>
                            @endif
                        @endif

                        @if ($groupBy === 'none')
                            <th
                                wire:click="sortBy('active')"
                                role="columnheader button"
                                style="text-align: right"
                            >
                                {{ __('common.active') }}
                                @include('livewire.includes._sort-icon', ['field' => 'active'])
                            </th>
                        @else
                            <th
                                wire:click="sortBy('active_count')"
                                role="columnheader button"
                                style="text-align: right"
                            >
                                {{ __('common.active') }} {{ __('torrent.peers') }}
                                @include('livewire.includes._sort-icon', ['field' => 'active_count'])
                            </th>
                            <th
                                wire:click="sortBy('inactive_count')"
                                role="columnheader button"
                                style="text-align: right"
                            >
                                Inactive {{ __('torrent.peers') }}
                                @include('livewire.includes._sort-icon', ['field' => 'inactive_count'])
                            </th>
                            <th
                                wire:click="sortBy('inactive_ratio')"
                                role="columnheader button"
                                style="text-align: right"
                            >
                                Inactive/active ratio
                                @include('livewire.includes._sort-icon', ['field' => 'inactive_ratio'])
                            </th>
                        @endif
                        <th
                            wire:click="sortBy('created_at')"
                            role="columnheader button"
                            style="text-align: right"
                        >
                            Started
                            @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                        </th>
                        <th
                            wire:click="sortBy('updated_at')"
                            role="columnheader button"
                            style="text-align: right"
                        >
                            Announced
                            @include('livewire.includes._sort-icon', ['field' => 'updated_at'])
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($peers as $peer)
                        <tr>
                            <td>
                                <x-user-tag :user="$peer->user" :anon="false" />
                            </td>
                            @if ($groupBy !== 'none')
                                <td style="text-align: right">
                                    {{ $peer->peer_count }}
                                </td>
                            @endif

                            @if ($groupBy === 'none')
                                <td>
                                    <a
                                        href="{{ route('torrents.show', ['id' => $peer->torrent_id]) }}"
                                    >
                                        {{ $peer->torrent->name ?? '' }}
                                    </a>
                                </td>
                            @else
                                <td style="text-align: right">
                                    {{ $peer->torrent_id }}
                                </td>
                            @endif

                            @if ($groupBy === 'none' || $groupBy === 'user_session')
                                <td>{{ $peer->agent }}</td>
                            @else
                                <td style="text-align: right">
                                    {{ $peer->agent }}
                                </td>
                            @endif
                            <td style="text-align: right">
                                {{ $peer->ip }}
                            </td>
                            <td style="text-align: right">
                                {{ $peer->port }}
                            </td>
                            <td style="text-align: right">
                                {{ App\Helpers\StringHelper::formatBytes($peer->uploaded, 2) }}
                            </td>
                            <td style="text-align: right">
                                {{ App\Helpers\StringHelper::formatBytes($peer->downloaded, 2) }}
                            </td>
                            <td style="text-align: right">
                                {{ App\Helpers\StringHelper::formatBytes($peer->left, 2) }}
                            </td>
                            @if ($groupBy === 'none')
                                <td style="text-align: right">
                                    {{ App\Helpers\StringHelper::formatBytes($peer->torrent->size ?? 0) }}
                                </td>
                            @else
                                @if ($includeSeedsize)
                                    <td style="text-align: right">
                                        {{ App\Helpers\StringHelper::formatBytes($peer->size ?? 0) }}
                                    </td>
                                    @if (\config('announce.connectable_check'))
                                        <td style="text-align: right">
                                            {{ App\Helpers\StringHelper::formatBytes($peer->connectable_size ?? 0) }}
                                        </td>
                                        <td style="text-align: right">
                                            {{ App\Helpers\StringHelper::formatBytes($peer->unconnectable_size ?? 0) }}
                                        </td>
                                    @endif
                                @endif
                            @endif
                            @if (\config('announce.connectable_check'))
                                @if ($groupBy === 'none')
                                    <td style="text-align: right">
                                        @if ($peer->connectable)
                                            <i
                                                class="{{ config('other.font-awesome') }} text-green fa-check"
                                                title="Connectable"
                                            ></i>
                                        @else
                                            <i
                                                class="{{ config('other.font-awesome') }} text-red fa-times"
                                                title="Not connectable"
                                            ></i>
                                        @endif
                                    </td>
                                @else
                                    <td style="text-align: right">
                                        {{ $peer->connectable_count }}
                                    </td>
                                    <td style="text-align: right">
                                        {{ $peer->unconnectable_count }}
                                    </td>
                                @endif
                            @endif

                            @if ($groupBy === 'none')
                                <td style="text-align: right">
                                    @if ($peer->active)
                                        <i
                                            class="{{ config('other.font-awesome') }} text-green fa-check"
                                            title="Active"
                                        ></i>
                                    @else
                                        <i
                                            class="{{ config('other.font-awesome') }} text-red fa-times"
                                            title="Inactive"
                                        ></i>
                                    @endif
                                </td>
                            @else
                                <td style="text-align: right">{{ $peer->active_count }}</td>
                                <td style="text-align: right">{{ $peer->inactive_count }}</td>
                                <td style="text-align: right">{{ $peer->inactive_ratio }}</td>
                            @endif
                            <td style="text-align: right">
                                <time
                                    datetime="{{ $peer->created_at }}"
                                    title="{{ $peer->created_at }}"
                                >
                                    {{ $peer->created_at?->diffForHumans() ?? 'N/A' }}
                                </time>
                            </td>
                            <td style="text-align: right">
                                <time
                                    datetime="{{ $peer->updated_at }}"
                                    title="{{ $peer->updated_at }}"
                                >
                                    {{ $peer->updated_at?->diffForHumans() ?? 'N/A' }}
                                </time>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $peers->links('partials.pagination') }}
        </div>
    </section>
</div>
