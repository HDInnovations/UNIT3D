<article class="sidebar2">
    <div>
        <section class="panelV2" x-data="toggle">
            <h2 class="panel__heading">{{ __('bon.earnings') }}</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('common.description') }}</th>
                            <th>Per torrent per hour</th>
                            <th>{{ __('torrent.torrents') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bonEarnings as $bonEarning)
                            <tr>
                                <td>{{ $bonEarning->name }}</td>
                                <td>{{ $bonEarning->description }}</td>
                                <td>
                                    @if ($bonEarning->operation === 'multiply')
                                        &times;
                                    @else
                                            &plus;
                                    @endif

                                    {{ preg_replace('/(\.\d+?)0+$/', '$1', $bonEarning->multiplier) }}

                                    @if ($bonEarning->variable != 1)
                                            &times; {{ $bonEarning->variable }}
                                    @endif
                                </td>
                                <td>{{ $bonEarning->torrents_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        <section class="panelV2" x-data="toggle">
            <header class="panel__header">
                <h2 class="panel__heading">{{ __('torrent.torrents') }}</h2>
                <div class="panel__actions">
                    <div class="panel__action">
                        <div class="form__group">
                            <input
                                id="torrentName"
                                class="form__text"
                                wire:model.live="torrentName"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="torrentName">
                                {{ __('torrent.name') }}
                            </label>
                        </div>
                    </div>
                    <label class="panel__action">
                        {{ __('bon.extended-stats') }}
                        <input type="checkbox" x-model="toggleState" />
                    </label>
                </div>
            </header>
            {{ $torrents->links('partials.pagination') }}
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="user-earnings__type-header" wire:click="sortBy('type_id')">
                                {{ __('torrent.type') }}
                                @include('livewire.includes._sort-icon', ['field' => 'type_id'])
                            </th>
                            <th
                                class="user-earnings__name-header"
                                wire:click="sortBy('name')"
                                role="columnheader button"
                            >
                                {{ __('torrent.name') }}
                                @include('livewire.includes._sort-icon', ['field' => 'name'])
                            </th>
                            <th
                                class="user-earnings__size-header"
                                wire:click="sortBy('size')"
                                role="columnheader button"
                            >
                                {{ __('torrent.size') }}
                                @include('livewire.includes._sort-icon', ['field' => 'size'])
                            </th>
                            <th
                                class="user-earnings__seeders-header"
                                wire:click="sortBy('seeders')"
                                role="columnheader button"
                                title="{{ __('torrent.seeders') }}"
                            >
                                <i class="fas fa-arrow-alt-circle-up"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'seeders'])
                            </th>
                            <th
                                class="user-earnings__leechers-header"
                                wire:click="sortBy('leechers')"
                                role="columnheader button"
                                title="{{ __('torrent.leechers') }}"
                            >
                                <i class="fas fa-arrow-alt-circle-down"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'leechers'])
                            </th>
                            <th
                                class="user-earnings__times-header"
                                wire:click="sortBy('times_completed')"
                                role="columnheader button"
                                title="{{ __('torrent.completed') }}"
                            >
                                <i class="fas fa-check-circle"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'times_completed'])
                            </th>
                            <th
                                class="user-earnings__internal-header"
                                wire:click="sortBy('internal')"
                                role="columnheader button"
                                title="{{ __('torrent.internal') }}"
                            >
                                <i class="fas fa-magic"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'internal'])
                            </th>
                            <th
                                class="user-earnings__personal-release-header"
                                wire:click="sortBy('personal_release')"
                                role="columnheader button"
                                title="{{ __('torrent.personal-release') }}"
                            >
                                <i class="fas fa-user-plus"></i>
                                @include('livewire.includes._sort-icon', ['field' => 'personal_release'])
                            </th>
                            <th
                                class="user-earnings__connectable-header"
                                wire:click="sortBy('connectable')"
                                role="columnheader button"
                            >
                                <i
                                    class="{{ config('other.font-awesome') }} fa-wifi"
                                    title="Connectable"
                                ></i>
                                @include('livewire.includes._sort-icon', ['field' => 'connectable'])
                            </th>
                            <th
                                class="user-earnings__seedtime-header"
                                wire:click="sortBy('seedtime')"
                                role="columnheader button"
                            >
                                {{ __('torrent.seedtime') }}
                                @include('livewire.includes._sort-icon', ['field' => 'seedtime'])
                            </th>
                            <th
                                class="user-earnings__age-header"
                                wire:click="sortBy('torrents.created_at')"
                                role="columnheader button"
                            >
                                {{ __('torrent.age') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                            <th
                                class="user-earnings__hourly-header"
                                wire:click="sortBy('hourly_earnings')"
                            >
                                Hourly
                                @include('livewire.includes._sort-icon', ['field' => 'hourly_earnings'])
                            </th>
                            <th class="user-earnings__daily-header" x-cloak x-show="isToggledOn">
                                Daily
                            </th>
                            <th
                                class="user-earnings__weekly-header"
                                x-cloak
                                x-show="isToggledOn"
                            >
                                Weekly
                            </th>
                            <th
                                class="user-earnings__monthly-header"
                                x-cloak
                                x-show="isToggledOn"
                            >
                                Monthly
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($torrents as $torrent)
                            <tr>
                                <td class="user-earnings__type">
                                    {{ $types[$torrent->type_id] ??= \App\Models\Type::find($torrent->type_id)?->name ?? __('common.unknown') }}
                                </td>
                                <td>
                                    <a
                                        class="user-earnings__name"
                                        href="{{ route('torrents.show', ['id' => $torrent->torrent_id]) }}"
                                    >
                                        {{ $torrent->name }}
                                    </a>
                                </td>
                                <td class="user-earnings__size">
                                    {{ \App\Helpers\StringHelper::formatBytes($torrent->size) }}
                                </td>
                                <td class="user-earnings__seeders">
                                    <a
                                        class="torrent__seeder-count"
                                        href="{{ route('peers', ['id' => $torrent->torrent_id]) }}"
                                    >
                                        {{ $torrent->seeders }}
                                    </a>
                                </td>
                                <td class="user-earnings__leechers">
                                    <a
                                        class="torrent__leecher-count"
                                        href="{{ route('peers', ['id' => $torrent->torrent_id]) }}"
                                    >
                                        {{ $torrent->leechers }}
                                    </a>
                                </td>
                                <td class="user-earnings__times">
                                    <a
                                        class="torrent__times-completed-count"
                                        href="{{ route('history', ['id' => $torrent->torrent_id]) }}"
                                    >
                                        {{ $torrent->times_completed }}
                                    </a>
                                </td>
                                <td class="user-earnings__internal">
                                    @if ($torrent->internal)
                                        <i
                                            class="{{ config('other.font-awesome') }} fa-magic"
                                            style="color: var(--torrent-row-internal-fg)"
                                            title="{{ __('torrent.internal') }}"
                                        ></i>
                                    @else
                                        <span title="Not {{ __('torrent.internal') }}">-</span>
                                    @endif
                                </td>
                                <td class="user-earnings__personal-release">
                                    @if ($torrent->personal_release)
                                        <i
                                            class="{{ config('other.font-awesome') }} fa-user-plus"
                                            title="{{ __('torrent.personal-release') }}"
                                            style="color: var(--torrent-row-personal-fg)"
                                        ></i>
                                    @else
                                        <span title="{{ __('torrent.not-personal-release') }}">
                                            -
                                        </span>
                                    @endif
                                </td>
                                <td class="user-earnings__connectable">
                                    @if ($torrent->connectable)
                                        <i
                                            class="{{ config('other.font-awesome') }} text-green fa-wifi"
                                            title="Connectable"
                                        ></i>
                                    @else
                                        <span title="Not Connectable">-</span>
                                    @endif
                                </td>
                                <td class="user-earnings__seedtime">
                                    {{ \App\Helpers\StringHelper::timeElapsed($torrent->seedtime) }}
                                </td>
                                <td class="user-earnings__age">
                                    {{ \App\Helpers\StringHelper::timeElapsed($torrent->age) }}
                                </td>
                                <td class="user-earnings__hourly">
                                    {{ number_format($torrent->hourly_earnings, 4) }}
                                </td>
                                <td class="user-earnings__daily" x-cloak x-show="isToggledOn">
                                    {{ number_format($torrent->hourly_earnings * 24, 4) }}
                                </td>
                                <td class="user-earnings__weekly" x-cloak x-show="isToggledOn">
                                    {{ number_format($torrent->hourly_earnings * 24 * 7, 4) }}
                                </td>
                                <td class="user-earnings__monthly" x-cloak x-show="isToggledOn">
                                    {{ number_format($torrent->hourly_earnings * 24 * 30, 4) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $torrents->links('partials.pagination') }}
        </section>
    </div>
    <aside>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('bon.your-points') }}</h2>
            <div class="panel__body">{{ $bon }}</div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('bon.your-points') }}</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>{{ __('bon.per-second') }}</dt>
                    <dd>{{ number_format($total / 60 / 60, 2) }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('bon.per-minute') }}</dt>
                    <dd>{{ number_format($total / 60, 2) }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('bon.per-hour') }}</dt>
                    <dd>{{ number_format($total, 2) }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('bon.per-day') }}</dt>
                    <dd>{{ number_format($total * 24, 2) }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('bon.per-week') }}</dt>
                    <dd>{{ number_format($total * 24 * 7, 2) }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('bon.per-month') }}</dt>
                    <dd>{{ number_format($total * 24 * 30, 2) }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('bon.per-year') }}</dt>
                    <dd>{{ number_format($total * 24 * 365, 2) }}</dd>
                </div>
            </dl>
        </section>
    </aside>
</article>
