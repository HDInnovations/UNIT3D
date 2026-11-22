<div style="display: flex; flex-direction: column; row-gap: 1rem">
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Leakers</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="torrent"
                            wire:model.live.debounce.1500ms="torrentIds"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="torrent">
                            Torrent IDs (comma-separated)
                        </label>
                    </div>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="torrent"
                            wire:model.live.debounce.500ms="minutesLeakedWithin"
                            class="form__text"
                            type="search"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="torrent">
                            Minutes leaked within
                        </label>
                    </div>
                </div>
                <div class="panel__action">
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
            </div>
        </header>
        <div class="panel__body" wire:loading.block>Loading...</div>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('user_id')" role="columnheader button">
                            {{ __('user.user') }}
                            @include('livewire.includes._sort-icon', ['field' => 'id'])
                        </th>
                        <th wire:click="sortBy('leak_count')" role="columnheader button">
                            Torrents matched
                            @include('livewire.includes._sort-icon', ['field' => 'leak_count'])
                        </th>
                        <th>User agents</th>
                        <th>IPs</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leakers as $leaker)
                        <tr>
                            <td>
                                <x-user-tag :user="$leaker->user" :anon="false" />
                            </td>
                            <td>
                                {{ round((100 * $leaker->leak_count) / $torrentIdCount) }}%
                                ({{ $leaker->leak_count }})
                            </td>
                            <td>
                                <ul>
                                    @foreach ($leaker->user->history as $history)
                                        <li>{{ $history->agent }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    @foreach ($leaker->user->peers as $peer)
                                        <li>{{ inet_ntop($peer->ip) }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $leakers->links('partials.pagination') }}
        </div>
    </section>
</div>
