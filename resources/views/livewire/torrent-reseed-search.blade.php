<div>
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('torrent.reseed-requests') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="myRequests"
                            class="form__checkbox"
                            type="checkbox"
                            wire:model.live="show"
                        />
                        <label class="form__label" for="myRequests">
                            {{ __('request.my-requests') }}
                        </label>
                    </div>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="torrentName"
                            class="form__text"
                            type="text"
                            wire:model.live="torrentName"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="torrentName">
                            {{ __('torrent.torrent') }} {{ __('common.name') }}
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
        <div class="data-table-wrapper">
            <table class="data-table">
                <tbody>
                    <tr>
                        <th>{{ __('torrent.torrent') }}</th>
                        <th title="{{ __('torrent.seeders') }}">
                            <i class="fas fa-arrow-alt-circle-up"></i>
                        </th>
                        <th title="{{ __('torrent.leechers') }}">
                            <i class="fas fa-arrow-alt-circle-down"></i>
                        </th>
                        <th title="{{ __('torrent.completed') }}">
                            <i class="fas fa-check-circle"></i>
                        </th>
                        <th wire:click="sortBy('requests_count')" role="columnheader button">
                            {{ __('request.requests') }}
                            @include('livewire.includes._sort-icon', ['field' => 'reseeds_count'])
                        </th>
                        <th wire:click="sortBy('created_at')" role="columnheader button">
                            {{ __('common.created_at') }}
                            @include('livewire.includes._sort-icon', ['field' => 'reseeds_min_created_at'])
                        </th>
                        <th>{{ __('common.user') }}</th>
                    </tr>
                    @forelse ($torrents as $torrent)
                        <tr>
                            <td>
                                <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}">
                                    {{ $torrent->name }}
                                </a>
                            </td>
                            <td>
                                <a
                                    class="torrent__seeder-count"
                                    href="{{ route('peers', ['id' => $torrentReseed->torrent->id]) }}"
                                >
                                    {{ $torrentReseed->torrent->seeders }}
                                </a>
                            </td>
                            <td>
                                <a
                                    class="torrent__leecher-count"
                                    href="{{ route('peers', ['id' => $torrentReseed->torrent->id]) }}"
                                >
                                    {{ $torrentReseed->torrent->leechers }}
                                </a>
                            </td>
                            <td>
                                <a
                                    class="torrent__times-completed-count"
                                    href="{{ route('history', ['id' => $torrentReseed->torrent->id]) }}"
                                >
                                    {{ $torrentReseed->torrent->times_completed }}
                                </a>
                            </td>
                            <td>
                                {{ $torrent->reseeds_count }}
                            </td>
                            <td>
                                <time
                                    datetime="{{ $torrent->reseeds_min_created_at }}"
                                    title="{{ $torrent->reseeds_min_created_at }}"
                                >
                                    {{ $torrent->reseeds_min_created_at->diffForHumans() }}
                                </time>
                            </td>
                            <td colspan="2">
                                @foreach ($torrent->reseeds as $reseed)
                                    <x-user-tag :anon="false" :user="$reseed->user" />
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('common.no-result') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $torrents->links('partials.pagination') }}
    </section>
</div>
