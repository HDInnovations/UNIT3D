<section class="panelV2">
    <h2 class="panel__heading">Unregistered info hashes</h2>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th wire:click="sortBy('torrents.name')" role="columnheader button">
                        {{ __('common.name') }}
                        @include('livewire.includes._sort-icon', ['field' => 'torrents.name'])
                    </th>
                    <th wire:click="sortBy('torrents.size')" role="columnheader button">
                        {{ __('torrent.size') }}
                        @include('livewire.includes._sort-icon', ['field' => 'torrents.size'])
                    </th>
                    <th wire:click="sortBy('torrents.info_hash')" role="columnheader button">
                        {{ __('torrent.info-hash') }} (Hex-encoded)
                        @include('livewire.includes._sort-icon', ['field' => 'torrents.info_hash'])
                    </th>
                    <th wire:click="sortBy('torrents.deleted_at')" role="columnheader button">
                        {{ __('common.deleted_at') }}
                        @include('livewire.includes._sort-icon', ['field' => 'torrents.deleted_at'])
                    </th>
                    <th
                        wire:click="sortBy('unregistered_info_hashes.updated_at')"
                        role="columnheader button"
                    >
                        {{ __('torrent.updated_at') }}
                        @include('livewire.includes._sort-icon', ['field' => 'unregistered_info_hashes.updated_at'])
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unregisteredInfoHashes as $unregisteredInfoHash)
                    <tr>
                        <td>
                            <a
                                href="{{ route('torrents.show', ['id' => $unregisteredInfoHash->id]) }}"
                            >
                                {{ $unregisteredInfoHash->name }}
                            </a>
                        </td>
                        <td title="{{ $unregisteredInfoHash->size }} B">
                            {{ \App\Helpers\StringHelper::formatBytes($unregisteredInfoHash->size) }}
                        </td>
                        <td>{{ bin2hex($unregisteredInfoHash->info_hash) }}</td>
                        <td>
                            <time
                                datetime="{{ $unregisteredInfoHash->deleted_at }}"
                                title="{{ $unregisteredInfoHash->deleted_at }}"
                            >
                                {{ $unregisteredInfoHash->deleted_at?->diffForHumans() ?? 'N/A' }}
                            </time>
                        </td>
                        <td>
                            <time
                                datetime="{{ $unregisteredInfoHash->updated_at }}"
                                title="{{ $unregisteredInfoHash->updated_at }}"
                            >
                                {{ $unregisteredInfoHash->updated_at?->diffForHumans() ?? 'N/A' }}
                            </time>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $unregisteredInfoHashes->links('partials.pagination') }}
    </div>
</section>
