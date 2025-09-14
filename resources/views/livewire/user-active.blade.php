<div style="display: flex; flex-direction: column; gap: 1rem">
    <section class="panelV2 user-peers__filters">
        <h2 class="panel__heading">{{ __('torrent.filters') }}</h2>
        <div class="panel__body">
            <form class="form">
                <p class="form__group">
                    <input
                        id="name"
                        wire:model.live="name"
                        class="form__text"
                        placeholder=" "
                        autofocus=""
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('torrent.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <label
                        style="user-select: none"
                        class="form__label"
                        x-data="{ state: @entangle('seeding').live, ...ternaryCheckbox() }"
                    >
                        <input
                            type="checkbox"
                            class="user-peers__checkbox"
                            x-init="updateTernaryCheckboxProperties($el, state)"
                            x-on:click="
                                state = getNextTernaryCheckboxState(state);
                                updateTernaryCheckboxProperties($el, state)
                            "
                            x-bind:checked="state === 'include'"
                        />
                        {{ __('torrent.seeding') }}
                    </label>
                </p>
                <p class="form__group">
                    <label
                        style="user-select: none"
                        class="form__label"
                        x-data="{ state: @entangle('active').live, ...ternaryCheckbox() }"
                    >
                        <input
                            type="checkbox"
                            class="user-peers__checkbox"
                            x-init="updateTernaryCheckboxProperties($el, state)"
                            x-on:click="
                                state = getNextTernaryCheckboxState(state);
                                updateTernaryCheckboxProperties($el, state)
                            "
                            x-bind:checked="state === 'include'"
                        />
                        {{ __('common.active') }}
                    </label>
                </p>
                <p class="form__group">
                    <label
                        style="user-select: none"
                        class="form__label"
                        x-data="{ state: @entangle('visible'), ...ternaryCheckbox() }"
                    >
                        <input
                            type="checkbox"
                            class="user-peers__checkbox"
                            x-init="updateTernaryCheckboxProperties($el, state)"
                            x-on:click="
                                state = getNextTernaryCheckboxState(state);
                                updateTernaryCheckboxProperties($el, state)
                            "
                            x-bind:checked="state === 'include'"
                        />
                        Visible
                    </label>
                </p>
                <p class="form__group">
                    <label class="form__label">
                        <input
                            type="checkbox"
                            class="user-peers__checkbox"
                            wire:model.live="showMorePrecision"
                        />
                        Show more precision
                    </label>
                </p>
            </form>
        </div>
    </section>
    <section class="panelV2 user-peers">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('user.active-torrents') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <div class="form__group">
                        <select id="perPage" wire:model.live="perPage" class="form__select">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label class="form__label form__label--floating" for="perPage">
                            {{ __('common.quantity') }}
                        </label>
                    </div>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <th
                        class="user-active__name-header"
                        wire:click="sortBy('name')"
                        role="columnheader button"
                    >
                        {{ __('torrent.name') }}
                        @include('livewire.includes._sort-icon', ['field' => 'name'])
                    </th>
                    <th
                        class="user-active__seeders-header"
                        wire:click="sortBy('seeders')"
                        role="columnheader button"
                        title="{{ __('torrent.seeders') }}"
                    >
                        <i class="fas fa-arrow-alt-circle-up"></i>
                        @include('livewire.includes._sort-icon', ['field' => 'seeders'])
                    </th>
                    <th
                        class="user-active__leechers-header"
                        wire:click="sortBy('leechers')"
                        role="columnheader button"
                        title="{{ __('torrent.leechers') }}"
                    >
                        <i class="fas fa-arrow-alt-circle-down"></i>
                        @include('livewire.includes._sort-icon', ['field' => 'leechers'])
                    </th>
                    <th
                        class="user-active__times-header"
                        wire:click="sortBy('times_completed')"
                        role="columnheader button"
                        title="{{ __('torrent.completed') }}"
                    >
                        <i class="fas fa-check-circle"></i>
                        @include('livewire.includes._sort-icon', ['field' => 'times_completed'])
                    </th>
                    <th
                        class="user-active__agent-header"
                        wire:click="sortBy('agent')"
                        role="columnheader button"
                    >
                        {{ __('torrent.client') }}
                        @include('livewire.includes._sort-icon', ['field' => 'agent'])
                    </th>
                    <th
                        class="user-active__ip-header"
                        wire:click="sortBy('ip')"
                        role="columnheader button"
                    >
                        {{ __('common.ip') }}
                        @include('livewire.includes._sort-icon', ['field' => 'ip'])
                    </th>
                    <th
                        class="user-active__port-header"
                        wire:click="sortBy('port')"
                        role="columnheader button"
                    >
                        {{ __('common.port') }}
                        @include('livewire.includes._sort-icon', ['field' => 'port'])
                    </th>
                    @if (\config('announce.connectable_check'))
                        <th class="user-active__connectable-header">
                            <i
                                class="{{ config('other.font-awesome') }} fa-wifi"
                                title="Connectable"
                            ></i>
                        </th>
                    @endif

                    <th
                        class="user-active__visible-header"
                        wire:click="sortBy('visible')"
                        role="columnheader button"
                        title="Visible"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-eye"></i>
                        @include('livewire.includes._sort-icon', ['field' => 'visible'])
                    </th>
                    <th
                        class="user-active__size-header"
                        wire:click="sortBy('size')"
                        role="columnheader button"
                    >
                        {{ __('torrent.size') }}
                        @include('livewire.includes._sort-icon', ['field' => 'size'])
                    </th>
                    <th
                        class="user-active__uploaded-header"
                        wire:click="sortBy('uploaded')"
                        role="columnheader button"
                    >
                        {{ __('torrent.uploaded') }}
                        @include('livewire.includes._sort-icon', ['field' => 'uploaded'])
                    </th>
                    <th
                        class="user-active__downloaded-header"
                        wire:click="sortBy('downloaded')"
                        role="columnheader button"
                    >
                        {{ __('torrent.downloaded') }}
                        @include('livewire.includes._sort-icon', ['field' => 'downloaded'])
                    </th>
                    <th
                        class="user-active__left-header"
                        wire:click="sortBy('left')"
                        role="columnheader button"
                    >
                        {{ __('torrent.left') }}
                        @include('livewire.includes._sort-icon', ['field' => 'left'])
                    </th>
                    <th
                        class="user-active__progress-header"
                        wire:click="sortBy('progress')"
                        role="columnheader button"
                    >
                        {{ __('torrent.progress') }}
                        @include('livewire.includes._sort-icon', ['field' => 'progress'])
                    </th>
                    <th
                        class="user-active__created-at-header"
                        wire:click="sortBy('created_at')"
                        role="columnheader button"
                    >
                        {{ __('torrent.started') }}
                        @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                    </th>
                    <th
                        class="user-active__updated-at-header"
                        wire:click="sortBy('updated_at')"
                        role="columnheader button"
                    >
                        {{ __('torrent.updated') }}
                        @include('livewire.includes._sort-icon', ['field' => 'updated_at'])
                    </th>
                </thead>
                <tbody>
                    @foreach ($actives as $active)
                        <tr>
                            <td>
                                <a
                                    class="user-active__name"
                                    href="{{ route('torrents.show', ['id' => $active->torrent_id]) }}"
                                >
                                    {{ $active->name }}
                                </a>
                            </td>
                            <td
                                @class([
                                    'user-active__seeders',
                                    'torrent-activity-indicator--seeding' => $active->seeding,
                                ])
                                @if ($active->seeding)
                                    title="{{ __('torrent.currently-seeding') }}"
                                @endif
                            >
                                <a
                                    class="torrent__seeder-count"
                                    href="{{ route('peers', ['id' => $active->torrent_id]) }}"
                                >
                                    {{ $active->seeders }}
                                </a>
                            </td>
                            <td
                                @class([
                                    'user-active__leechers',
                                    'torrent-activity-indicator--leeching' => $active->leeching,
                                ])
                                @if ($active->leeching)
                                    title="{{ __('torrent.currently-leeching') }}"
                                @endif
                            >
                                <a
                                    class="torrent__leecher-count"
                                    href="{{ route('peers', ['id' => $active->torrent_id]) }}"
                                >
                                    {{ $active->leechers }}
                                </a>
                            </td>
                            <td class="user-active__times">
                                <a
                                    class="torrent__times-completed-count"
                                    href="{{ route('history', ['id' => $active->torrent_id]) }}"
                                >
                                    {{ $active->times_completed }}
                                </a>
                            </td>
                            <td class="user-active__agent">
                                {{ $active->agent ?: __('common.unknown') }}
                            </td>
                            <td class="user-active__ip">
                                {{ $active->ip ?: __('common.unknown') }}
                            </td>
                            <td class="user-active__port">
                                {{ $active->port ?: __('common.unknown') }}
                            </td>
                            @if (\config('announce.connectable_check'))
                                <td class="user-active__connectable">
                                    @php
                                        $connectable = null;
                                        if (config('announce.external_tracker.is_enabled')) {
                                            $connectable = $active->connectable;
                                        } elseif (cache()->has('peers:connectable:' . $active->ip . '-' . $active->port . '-' . $active->agent)) {
                                            $connectable = cache()->get('peers:connectable:' . $active->ip . '-' . $active->port . '-' . $active->agent);
                                        }
                                    @endphp

                                    @if ($connectable === null)
                                        <i
                                            class="{{ config('other.font-awesome') }} text-blue fa-question"
                                            title="Unknown Connectable Status"
                                        ></i>
                                    @else
                                        @if ($connectable)
                                            <i
                                                class="{{ config('other.font-awesome') }} text-green fa-wifi"
                                                title="Connectable"
                                            ></i>
                                        @else
                                            <i
                                                class="{{ config('other.font-awesome') }} text-red fa-wifi-slash"
                                                title="Not Connectable"
                                            ></i>
                                        @endif
                                    @endif
                                </td>
                            @endif

                            <td
                                class="user-active__visible {{ $active->visible ? 'text-green' : 'text-red' }}"
                            >
                                @if ($active->visible)
                                    <i
                                        class="{{ config('other.font-awesome') }} text-green fa-eye"
                                        title="Visible"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} text-red fa-eye-slash"
                                        title="Invisible"
                                    ></i>
                                @endif
                            </td>
                            <td class="user-active__size">
                                {{ App\Helpers\StringHelper::formatBytes($active->size) }}
                            </td>
                            <td class="user-active__uploaded text-green">
                                {{ App\Helpers\StringHelper::formatBytes($active->uploaded, 2) }}
                            </td>
                            <td class="user-active__downloaded text-red">
                                {{ App\Helpers\StringHelper::formatBytes($active->downloaded, 2) }}
                            </td>
                            <td class="user-active__left">
                                {{ App\Helpers\StringHelper::formatBytes($active->left, 2) }}
                            </td>
                            <td
                                class="user-active__progress"
                                title="{{ __('torrent.progress') }}: {{ $active->progress * 100 }}%"
                            >
                                {{ $active->progress < 100 ? \floor($active->progress * 10000) / 100 : INF }}%
                            </td>
                            @if ($showMorePrecision)
                                <td class="user-active__created-at">
                                    <time
                                        datetime="{{ $active->created_at }}"
                                        title="{{ $active->created_at }}"
                                    >
                                        {{ $active->created_at ?? 'N/A' }}
                                    </time>
                                </td>
                                <td class="user-active__updated-at">
                                    <time
                                        datetime="{{ $active->updated_at }}"
                                        title="{{ $active->updated_at }}"
                                    >
                                        {{ $active->updated_at ?? 'N/A' }}
                                    </time>
                                </td>
                            @else
                                <td class="user-active__created-at">
                                    <time
                                        datetime="{{ $active->created_at }}"
                                        title="{{ $active->created_at }}"
                                    >
                                        {{ isset($active->created_at) ? \explode(' ', $active->created_at)[0] : 'N/A' }}
                                    </time>
                                </td>
                                <td class="user-active__updated-at">
                                    <time
                                        datetime="{{ $active->updated_at }}"
                                        title="{{ $active->updated_at }}"
                                    >
                                        {{ isset($active->updated_at) ? \explode(' ', $active->updated_at)[0] : 'N/A' }}
                                    </time>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $actives->links('partials.pagination') }}
    </section>
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        function ternaryCheckbox() {
            return {
                updateTernaryCheckboxProperties(el, state) {
                    el.indeterminate = state === 'exclude';
                    el.checked = state === 'include';
                },
                getNextTernaryCheckboxState(state) {
                    return state === 'include'
                        ? 'exclude'
                        : state === 'exclude'
                          ? 'any'
                          : 'include';
                },
            };
        }
    </script>
</div>
