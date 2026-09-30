<div style="display: flex; flex-direction: column; row-gap: 1rem">
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('livewire-interface.announces') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="torrent"
                            type="search"
                            autocomplete="off"
                            wire:model.live="torrentId"
                            class="form__text"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="torrent">
                            {{ __('livewire-interface.torrent-id') }}
                        </label>
                    </div>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="user"
                            type="search"
                            autocomplete="off"
                            wire:model.live="userId"
                            class="form__text"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="user">
                            {{ __('livewire-interface.user-id') }}
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
        <div class="panel__body" wire:loading.block>{{ __('livewire-interface.loading') }}</div>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('id')" role="columnheader button">
                            {{ __('livewire-interface.id') }}
                            @include('livewire.includes._sort-icon', ['field' => 'id'])
                        </th>
                        <th wire:click="sortBy('user_id')" role="columnheader button">
                            {{ __('livewire-interface.user-id') }}
                            @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                        </th>
                        <th wire:click="sortBy('torrent_id')" role="columnheader button">
                            {{ __('livewire-interface.torrent-id') }}
                            @include('livewire.includes._sort-icon', ['field' => 'torrent_id'])
                        </th>
                        <th wire:click="sortBy('uploaded')" role="columnheader button">
                            {{ __('torrent.uploaded') }}
                            @include('livewire.includes._sort-icon', ['field' => 'uploaded'])
                        </th>
                        <th wire:click="sortBy('downloaded')" role="columnheader button">
                            {{ __('torrent.downloaded') }}
                            @include('livewire.includes._sort-icon', ['field' => 'downloaded'])
                        </th>
                        <th wire:click="sortBy('left')" role="columnheader button">
                            {{ __('torrent.left') }}
                            @include('livewire.includes._sort-icon', ['field' => 'left'])
                        </th>
                        <th wire:click="sortBy('corrupt')" role="columnheader button">
                            {{ __('livewire-interface.corrupt') }}
                            @include('livewire.includes._sort-icon', ['field' => 'corrupt'])
                        </th>
                        <th wire:click="sortBy('peer_id')" role="columnheader button">
                            {{ __('livewire-interface.peer-id') }}
                            @include('livewire.includes._sort-icon', ['field' => 'peer_id'])
                        </th>
                        <th wire:click="sortBy('port')" role="columnheader button">
                            {{ __('common.port') }}
                            @include('livewire.includes._sort-icon', ['field' => 'port'])
                        </th>
                        <th wire:click="sortBy('numwant')" role="columnheader button">
                            {{ __('livewire-interface.numwant') }}
                            @include('livewire.includes._sort-icon', ['field' => 'numwant'])
                        </th>
                        <th wire:click="sortBy('event')" role="columnheader button">
                            {{ __('livewire-interface.event') }}
                            @include('livewire.includes._sort-icon', ['field' => 'event'])
                        </th>
                        <th wire:click="sortBy('key')" role="columnheader button">
                            {{ __('common.code') }}
                            @include('livewire.includes._sort-icon', ['field' => 'key'])
                        </th>
                        <th wire:click="sortBy('created_at')" role="columnheader button">
                            {{ __('common.created_at') }}
                            @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($announces as $announce)
                        <tr>
                            <td>{{ $announce->id }}</td>
                            <td>{{ $announce->user_id }}</td>
                            <td>{{ $announce->torrent_id }}</td>
                            <td>{{ $announce->uploaded }}</td>
                            <td>{{ $announce->downloaded }}</td>
                            <td>{{ $announce->left }}</td>
                            <td>{{ $announce->corrupt }}</td>
                            <td>{{ $announce->peer_id }}</td>
                            <td>{{ $announce->port }}</td>
                            <td>{{ $announce->numwant }}</td>
                            <td>{{ $announce->event }}</td>
                            <td>{{ $announce->key }}</td>
                            <td>{{ $announce->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $announces->links('partials.pagination') }}
        </div>
    </section>
</div>
