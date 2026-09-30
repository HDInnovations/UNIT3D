@php
    $ternaryFilters = [
        'unsatisfied' => __('livewire-interface.history.unsatisfied'),
        'completed'   => __('livewire-interface.history.completed-filter'),
        'prewarn'     => __('livewire-interface.history.prewarn'),
        'hitrun'      => __('livewire-interface.history.hitrun'),
        'immune'      => __('livewire-interface.history.immune'),
        'uploaded'    => __('livewire-interface.history.uploaded-filter'),
        'downloaded'  => __('livewire-interface.history.downloaded-filter'),
    ];
    $moderationFilters = [
        \App\Enums\ModerationStatus::PENDING->value   => __('torrent.pending'),
        \App\Enums\ModerationStatus::APPROVED->value  => __('torrent.approved'),
        \App\Enums\ModerationStatus::REJECTED->value  => __('torrent.rejected'),
        \App\Enums\ModerationStatus::POSTPONED->value => __('torrent.postponed'),
    ];
    $optionalColumns = [
        'client'    => __('torrent.client'),
        'started'   => __('torrent.started'),
        'finished'  => __('torrent.completed'),
        'leechtime' => __('livewire-interface.leeched'),
    ];
    $sortFields = [
        'created_at'        => __('torrent.started'),
        'updated_at'        => __('livewire-interface.history.last-activity'),
        'name'              => __('torrent.name'),
        'size'              => __('torrent.size'),
        'actual_uploaded'   => __('common.uploaded'),
        'actual_downloaded' => __('common.downloaded'),
        'actual_ratio'      => __('common.ratio'),
        'seedtime'          => __('torrent.seed-time'),
    ];
@endphp

<div
    class="user-torrents"
    x-data="userTorrentsColumns(@js(array_keys($optionalColumns)))"
>
    <section class="panelV2 user-torrents__toolbar">
        <p class="user-torrents__search">
            <label class="form__label" for="history-name">
                {{ __('torrent.name') }}
            </label>
            <input
                id="history-name"
                type="search"
                wire:model.live.debounce.400ms="name"
                placeholder="{{ __('livewire-interface.history.search') }}"
            />
        </p>
        <p class="user-torrents__activity">
            <label class="form__label" for="history-activity">
                {{ __('livewire-interface.history.activity') }}
            </label>
            <select id="history-activity" wire:model.live="active">
                <option value="any">{{ __('livewire-interface.history.all') }}</option>
                <option value="include">{{ __('livewire-interface.history.active') }}</option>
                <option value="exclude">{{ __('livewire-interface.history.inactive') }}</option>
            </select>
        </p>
        <details class="user-torrents__menu">
            <summary>
                @if ($status === [])
                    {{ __('livewire-interface.history.moderation-all') }}
                @else
                    {{ __('livewire-interface.history.moderation-count', ['count' => \count($status)]) }}
                @endif
            </summary>
            <div class="user-torrents__menu-body">
                <fieldset>
                    <legend>{{ __('torrent.moderation') }}</legend>
                    @foreach ($moderationFilters as $value => $label)
                        <label>
                            <input
                                type="checkbox"
                                class="user-torrents__checkbox"
                                value="{{ $value }}"
                                wire:model.live="status"
                            />
                            {{ $label }}
                        </label>
                    @endforeach
                </fieldset>
            </div>
        </details>
        <details class="user-torrents__menu">
            <summary>{{ __('livewire-interface.history.columns') }}</summary>
            <div class="user-torrents__menu-body">
                <fieldset>
                    <legend>{{ __('livewire-interface.history.columns') }}</legend>
                    @foreach ($optionalColumns as $column => $label)
                        <label>
                            <input
                                type="checkbox"
                                class="user-torrents__checkbox"
                                x-model="columns"
                                value="{{ $column }}"
                            />
                            {{ $label }}
                        </label>
                    @endforeach
                    <label>
                        <input
                            type="checkbox"
                            class="user-torrents__checkbox"
                            wire:model.live="showMorePrecision"
                        />
                        {{ __('livewire-interface.show-more-precision') }}
                    </label>
                </fieldset>
            </div>
        </details>
        <button
            type="button"
            class="user-torrents__advanced-toggle"
            x-on:click="advanced = !advanced"
            x-bind:aria-expanded="advanced ? 'true' : 'false'"
            aria-controls="history-advanced-filters"
        >
            {{ __('livewire-interface.history.more-filters') }}
        </button>

        @if ($activeFilters !== [])
            <div class="user-torrents__chips">
                @foreach ($activeFilters as $filter)
                    <button
                        type="button"
                        class="user-torrents__chip"
                        wire:click="removeFilter('{{ $filter['filter'] }}'{{ $filter['value'] === null ? '' : ", '".$filter['value']."'" }})"
                        title="{{ __('livewire-interface.history.remove-filter', ['filter' => $filter['label']]) }}"
                    >
                        {{ $filter['label'] }}
                        <i class="{{ config('other.font-awesome') }} fa-times" aria-hidden="true"></i>
                    </button>
                @endforeach
                <button type="button" class="user-torrents__clear" wire:click="clearFilters">
                    {{ __('livewire-interface.history.clear-filters') }}
                </button>
            </div>
        @endif
    </section>

    <section
        class="panelV2 user-torrents__advanced"
        id="history-advanced-filters"
        x-show="advanced"
        x-cloak
    >
        @foreach ($ternaryFilters as $filter => $label)
            <p class="user-torrents__advanced-filter">
                <label class="form__label" for="history-filter-{{ $filter }}">{{ $label }}</label>
                <select id="history-filter-{{ $filter }}" wire:model.live="{{ $filter }}">
                    <option value="any">{{ __('livewire-interface.history.all') }}</option>
                    <option value="include">{{ __('livewire-interface.history.include') }}</option>
                    <option value="exclude">{{ __('livewire-interface.history.exclude') }}</option>
                </select>
            </p>
        @endforeach
    </section>

    <section class="panelV2 user-torrents__results">
        <header class="user-torrents__results-header">
            <h2 class="panel__heading">{{ __('user.torrents-history') }}</h2>
            <span class="user-torrents__total">
                {{ trans_choice('livewire-interface.history.results', $histories->total(), ['count' => $histories->total()]) }}
            </span>
            <p class="user-torrents__sorting">
                <label class="form__label" for="history-sort">
                    {{ __('livewire-interface.history.sort') }}
                </label>
                <select id="history-sort" wire:model.live="sortField">
                    @foreach ($sortFields as $field => $label)
                        <option value="{{ $field }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select
                    wire:model.live="sortDirection"
                    aria-label="{{ __('livewire-interface.history.direction') }}"
                >
                    <option value="asc">{{ __('livewire-interface.history.ascending') }}</option>
                    <option value="desc">{{ __('livewire-interface.history.descending') }}</option>
                </select>
            </p>
            <p class="user-torrents__per-page">
                <label class="form__label" for="history-per-page">
                    {{ __('livewire-interface.history.per-page') }}
                </label>
                <select id="history-per-page" wire:model.live="perPage">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </p>
        </header>

        <div class="user-torrents__table-wrapper">
            <table class="data-table user-torrents__table">
                <thead>
                    <tr>
                        <th scope="col">
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('name')"
                            >
                                {{ __('livewire-interface.history.name') }}
                                @include('livewire.includes._sort-icon', ['field' => 'name'])
                            </button>
                        </th>
                        <th scope="col">{{ __('livewire-interface.history.activity') }}</th>
                        <th scope="col" class="user-torrents__numeric">
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('size')"
                            >
                                {{ __('torrent.size') }}
                                @include('livewire.includes._sort-icon', ['field' => 'size'])
                            </button>
                        </th>
                        <th scope="col" class="user-torrents__numeric">
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('actual_uploaded')"
                            >
                                {{ __('livewire-interface.history.transfers') }}
                                @include('livewire.includes._sort-icon', ['field' => 'actual_uploaded'])
                            </button>
                        </th>
                        <th scope="col" class="user-torrents__numeric">
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('actual_ratio')"
                            >
                                {{ __('common.ratio') }}
                                @include('livewire.includes._sort-icon', ['field' => 'actual_ratio'])
                            </button>
                        </th>
                        <th scope="col" class="user-torrents__numeric">
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('seedtime')"
                            >
                                {{ __('torrent.seed-time') }}
                                @include('livewire.includes._sort-icon', ['field' => 'seedtime'])
                            </button>
                        </th>
                        <th scope="col" x-show="columns.includes('leechtime')" x-cloak class="user-torrents__numeric">
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('leechtime')"
                            >
                                {{ __('livewire-interface.leeched') }}
                                @include('livewire.includes._sort-icon', ['field' => 'leechtime'])
                            </button>
                        </th>
                        <th scope="col" x-show="columns.includes('client')" x-cloak>
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('agent')"
                            >
                                {{ __('torrent.client') }}
                                @include('livewire.includes._sort-icon', ['field' => 'agent'])
                            </button>
                        </th>
                        <th scope="col" x-show="columns.includes('started')" x-cloak>
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('created_at')"
                            >
                                {{ __('torrent.started') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </button>
                        </th>
                        <th scope="col" x-show="columns.includes('finished')" x-cloak>
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('completed_at')"
                            >
                                {{ __('torrent.completed') }}
                                @include('livewire.includes._sort-icon', ['field' => 'completed_at'])
                            </button>
                        </th>
                        <th scope="col">
                            <button
                                type="button"
                                class="user-torrents__sort-button"
                                wire:click="sortBy('updated_at')"
                            >
                                {{ __('livewire-interface.history.last-activity') }}
                                @include('livewire.includes._sort-icon', ['field' => 'updated_at'])
                            </button>
                        </th>
                        <th scope="col">
                            <span class="sr-only">{{ __('livewire-interface.history.detail') }}</span>
                        </th>
                    </tr>
                </thead>
                @forelse ($histories as $history)
                    @php
                        $seeding = (bool) $history->seeding;
                        $leeching = (bool) $history->leeching;
                        $unsatisfied = !$history->immune
                            && ($history->seedtime ?? 0) < config('hitrun.seedtime')
                            && $history->actual_downloaded > $history->size * config('hitrun.buffer') / 100;
                        $actualRatio = $history->actual_ratio >= 1000 ? '∞' : \number_format($history->actual_ratio, 2);
                        $creditedRatio = $history->ratio >= 1000 ? '∞' : \number_format($history->ratio, 2);
                    @endphp
                    <tbody
                        class="user-torrents__entry"
                        x-data="{ expanded: false }"
                        data-history-id="{{ $history->torrent_id }}"
                    >
                        <tr class="user-torrents__row">
                            <td class="user-torrents__name" data-label="{{ __('livewire-interface.history.name') }}">
                                <a
                                    class="user-torrents__name-link"
                                    href="{{ route('torrents.show', ['id' => $history->torrent_id]) }}"
                                >
                                    {{ $history->name }}
                                </a>
                                @if ($history->uploader)
                                    <span class="user-torrents__caption">
                                        {{ __('torrent.uploaded') }}
                                    </span>
                                @endif
                            </td>
                            <td class="user-torrents__state-cell" data-label="{{ __('livewire-interface.history.activity') }}">
                                @if ($seeding)
                                    <span class="user-torrents__state user-torrents__state--seeding">
                                        {{ __('livewire-interface.history.seeding') }}
                                    </span>
                                @elseif ($leeching)
                                    <span class="user-torrents__state user-torrents__state--leeching">
                                        {{ __('livewire-interface.history.leeching') }}
                                    </span>
                                @elseif ($history->completed)
                                    <span class="user-torrents__state user-torrents__state--inactive">
                                        {{ __('livewire-interface.history.completed') }}
                                    </span>
                                @else
                                    <span class="user-torrents__state user-torrents__state--inactive">
                                        {{ __('livewire-interface.history.stopped') }}
                                    </span>
                                @endif
                                @if ($history->hitrun)
                                    <span class="user-torrents__state user-torrents__state--warning">
                                        {{ __('livewire-interface.history.hitrun') }}
                                    </span>
                                @elseif ($unsatisfied)
                                    <span class="user-torrents__state user-torrents__state--warning">
                                        {{ __('livewire-interface.history.unsatisfied') }}
                                    </span>
                                @endif
                            </td>
                            <td class="user-torrents__numeric" data-label="{{ __('torrent.size') }}">
                                <span class="user-torrents__value">
                                    {{ App\Helpers\StringHelper::formatBytes($history->size) }}
                                </span>
                            </td>
                            <td class="user-torrents__transfers" data-label="{{ __('livewire-interface.history.transfers') }}">
                                <div class="user-torrents__transfer-line">
                                    <span>{{ __('common.uploaded') }}</span>
                                    <strong class="user-torrents__value">
                                        {{ App\Helpers\StringHelper::formatBytes($history->actual_uploaded, 2) }}
                                    </strong>
                                    <small class="user-torrents__caption">
                                        {{ __('livewire-interface.history.credited', ['value' => App\Helpers\StringHelper::formatBytes($history->uploaded, 2)]) }}
                                    </small>
                                </div>
                                <div class="user-torrents__transfer-line">
                                    <span>{{ __('common.downloaded') }}</span>
                                    <strong class="user-torrents__value">
                                        {{ App\Helpers\StringHelper::formatBytes($history->actual_downloaded, 2) }}
                                    </strong>
                                    <small class="user-torrents__caption">
                                        {{ __('livewire-interface.history.credited', ['value' => App\Helpers\StringHelper::formatBytes($history->downloaded, 2)]) }}
                                    </small>
                                </div>
                            </td>
                            <td class="user-torrents__numeric" data-label="{{ __('common.ratio') }}">
                                <span class="user-torrents__value">{{ $actualRatio }}</span>
                                <small class="user-torrents__caption">
                                    {{ __('livewire-interface.history.credited-ratio', ['ratio' => $creditedRatio]) }}
                                </small>
                            </td>
                            <td class="user-torrents__numeric" data-label="{{ __('torrent.seed-time') }}">
                                <span class="user-torrents__value">
                                    @if (($history->seedtime ?? 0) === 0)
                                        &mdash;
                                    @else
                                        {{ App\Helpers\StringHelper::timeElapsed($history->seedtime) }}
                                    @endif
                                </span>
                            </td>
                            <td
                                class="user-torrents__numeric"
                                data-label="{{ __('livewire-interface.leeched') }}"
                                x-show="columns.includes('leechtime')"
                                x-cloak
                            >
                                <span class="user-torrents__value">
                                    @if (($history->leechtime ?? 0) === 0)
                                        &mdash;
                                    @else
                                        {{ App\Helpers\StringHelper::timeElapsed($history->leechtime) }}
                                    @endif
                                </span>
                            </td>
                            <td
                                data-label="{{ __('torrent.client') }}"
                                x-show="columns.includes('client')"
                                x-cloak
                            >
                                {{ $history->agent ?: __('common.unknown') }}
                            </td>
                            <td
                                data-label="{{ __('torrent.started') }}"
                                x-show="columns.includes('started')"
                                x-cloak
                            >
                                @if ($history->created_at === null)
                                    &mdash;
                                @else
                                    <time datetime="{{ $history->created_at }}" title="{{ $history->created_at }}">
                                        {{ $showMorePrecision ? $history->created_at : \explode(' ', (string) $history->created_at)[0] }}
                                    </time>
                                @endif
                            </td>
                            <td
                                data-label="{{ __('torrent.completed') }}"
                                x-show="columns.includes('finished')"
                                x-cloak
                            >
                                @if ($history->completed_at === null)
                                    &mdash;
                                @else
                                    <time datetime="{{ $history->completed_at }}" title="{{ $history->completed_at }}">
                                        {{ $showMorePrecision ? $history->completed_at : \explode(' ', (string) $history->completed_at)[0] }}
                                    </time>
                                @endif
                            </td>
                            <td data-label="{{ __('livewire-interface.history.last-activity') }}">
                                @if ($history->updated_at === null)
                                    &mdash;
                                @else
                                    <time datetime="{{ $history->updated_at }}" title="{{ $history->updated_at }}">
                                        {{ $showMorePrecision ? $history->updated_at : \explode(' ', (string) $history->updated_at)[0] }}
                                    </time>
                                @endif
                            </td>
                            <td class="user-torrents__actions">
                                <button
                                    type="button"
                                    class="user-torrents__detail-toggle"
                                    x-on:click="expanded = !expanded"
                                    x-bind:aria-expanded="expanded ? 'true' : 'false'"
                                    aria-label="{{ __('livewire-interface.history.detail-label', ['name' => $history->name]) }}"
                                >
                                    <span x-show="!expanded">{{ __('livewire-interface.history.detail') }}</span>
                                    <span x-show="expanded" x-cloak>
                                        {{ __('livewire-interface.history.close-detail') }}
                                    </span>
                                </button>
                            </td>
                        </tr>
                        <tr class="user-torrents__detail-row" x-show="expanded" x-cloak>
                            <td colspan="30">
                                <dl class="user-torrents__detail-grid">
                                    <div>
                                        <dt>{{ __('torrent.client') }}</dt>
                                        <dd>{{ $history->agent ?: __('common.unknown') }}</dd>
                                    </div>
                                    <div>
                                        <dt>{{ __('torrent.started') }}</dt>
                                        <dd>{{ $history->created_at ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt>{{ __('torrent.completed') }}</dt>
                                        <dd>{{ $history->completed_at ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt>{{ __('livewire-interface.leeched') }}</dt>
                                        <dd>
                                            @if (($history->leechtime ?? 0) === 0)
                                                &mdash;
                                            @else
                                                {{ App\Helpers\StringHelper::timeElapsed($history->leechtime) }}
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>{{ __('torrent.prewarn') }}</dt>
                                        <dd>{{ $history->prewarned_at ?? '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt>{{ __('torrent.seeders') }} / {{ __('torrent.leechers') }}</dt>
                                        <dd>
                                            <a href="{{ route('peers', ['id' => $history->torrent_id]) }}">
                                                {{ $history->seeders }} / {{ $history->leechers }}
                                            </a>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>{{ __('livewire-interface.history.times-completed') }}</dt>
                                        <dd>
                                            <a href="{{ route('history', ['id' => $history->torrent_id]) }}">
                                                {{ $history->times_completed }}
                                            </a>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt>{{ __('torrent.moderation') }}</dt>
                                        <dd>
                                            @switch($history->status)
                                                @case(\App\Enums\ModerationStatus::PENDING->value)
                                                    {{ __('torrent.pending') }}

                                                    @break
                                                @case(\App\Enums\ModerationStatus::APPROVED->value)
                                                    {{ __('torrent.approved') }}

                                                    @break
                                                @case(\App\Enums\ModerationStatus::REJECTED->value)
                                                    {{ __('torrent.rejected') }}

                                                    @break
                                                @case(\App\Enums\ModerationStatus::POSTPONED->value)
                                                    {{ __('torrent.postponed') }}

                                                    @break
                                                @default
                                                    &mdash;
                                            @endswitch
                                        </dd>
                                    </div>
                                    @if ($history->hitrun || $history->immune || $history->uploader || auth()->user()->group->is_modo)
                                    <div>
                                        <dt>{{ __('livewire-interface.history.row-summary') }}</dt>
                                        <dd class="user-torrents__flags">
                                            @if ($history->hitrun)
                                                <span class="user-torrents__state user-torrents__state--warning">
                                                    {{ __('livewire-interface.history.hitrun') }}
                                                </span>
                                            @endif
                                            @if ($history->immune)
                                                <span class="user-torrents__state user-torrents__state--inactive">
                                                    {{ __('livewire-interface.immune') }}
                                                </span>
                                            @endif
                                            @if ($history->uploader)
                                                <span class="user-torrents__state user-torrents__state--seeding">
                                                    {{ __('torrent.uploaded') }}
                                                </span>
                                            @endif
                                            @if (auth()->user()->group->is_modo)
                                                <button
                                                    type="button"
                                                    class="user-torrents__immunity-button"
                                                    x-data="userHistory(@js(__('livewire-interface.are-you-sure')))"
                                                    x-on:click.prevent="updateImmune({{ $history->immune ? 'false' : 'true' }})"
                                                    data-b64-deletion-message="{{ base64_encode($history->immune ? __('livewire-interface.confirm-set-not-immune', ['name' => $history->name]) : __('livewire-interface.confirm-set-immune', ['name' => $history->name])) }}"
                                                >
                                                    {{ __('livewire-interface.history.immunity-edit') }}
                                                </button>
                                            @endif
                                        </dd>
                                    </div>
                                    @endif
                                </dl>
                            </td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="30" class="user-torrents__empty">
                                <p>{{ __('livewire-interface.history.empty') }}</p>
                                <p>{{ __('livewire-interface.history.empty-hint') }}</p>
                            </td>
                        </tr>
                    </tbody>
                @endforelse
            </table>
        </div>
        {{ $histories->links('partials.pagination') }}
    </section>

</div>
