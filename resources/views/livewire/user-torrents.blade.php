<div style="display: flex; flex-direction: column; gap: 1rem">
    <section class="panelV2 user-torrents__filters">
        <h2 class="panel__heading">{{ __('common.search') }}</h2>
        <div class="panel__body">
            <div class="form__group--horizontal">
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
            </div>
            <div class="form__group--short-horizontal">
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.filters') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label
                                    style="user-select: none"
                                    class="form__label"
                                    x-data="{ state: @entangle('unsatisfied').live, ...ternaryCheckbox() }"
                                >
                                    <input
                                        type="checkbox"
                                        class="user-torrents__checkbox"
                                        x-init="updateTernaryCheckboxProperties($el, state)"
                                        x-on:click="
                                            state = getNextTernaryCheckboxState(state);
                                            updateTernaryCheckboxProperties($el, state)
                                        "
                                        x-bind:checked="state === 'include'"
                                    />
                                    {{ __('torrent.unsatisfieds') }}
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
                                        class="user-torrents__checkbox"
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
                                    x-data="{ state: @entangle('completed').live, ...ternaryCheckbox() }"
                                >
                                    <input
                                        type="checkbox"
                                        class="user-torrents__checkbox"
                                        x-init="updateTernaryCheckboxProperties($el, state)"
                                        x-on:click="
                                            state = getNextTernaryCheckboxState(state);
                                            updateTernaryCheckboxProperties($el, state)
                                        "
                                        x-bind:checked="state === 'include'"
                                    />
                                    {{ __('torrent.completed') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label
                                    style="user-select: none"
                                    class="form__label"
                                    x-data="{ state: @entangle('prewarn').live, ...ternaryCheckbox() }"
                                >
                                    <input
                                        type="checkbox"
                                        class="user-torrents__checkbox"
                                        x-init="updateTernaryCheckboxProperties($el, state)"
                                        x-on:click="
                                            state = getNextTernaryCheckboxState(state);
                                            updateTernaryCheckboxProperties($el, state)
                                        "
                                        x-bind:checked="state === 'include'"
                                    />
                                    {{ __('torrent.prewarn') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label
                                    style="user-select: none"
                                    class="form__label"
                                    x-data="{ state: @entangle('hitrun').live, ...ternaryCheckbox() }"
                                >
                                    <input
                                        type="checkbox"
                                        class="user-torrents__checkbox"
                                        x-init="updateTernaryCheckboxProperties($el, state)"
                                        x-on:click="
                                            state = getNextTernaryCheckboxState(state);
                                            updateTernaryCheckboxProperties($el, state)
                                        "
                                        x-bind:checked="state === 'include'"
                                    />
                                    {{ __('torrent.hitrun') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label
                                    style="user-select: none"
                                    class="form__label"
                                    x-data="{ state: @entangle('immune').live, ...ternaryCheckbox() }"
                                >
                                    <input
                                        type="checkbox"
                                        class="user-torrents__checkbox"
                                        x-init="updateTernaryCheckboxProperties($el, state)"
                                        x-on:click="
                                            state = getNextTernaryCheckboxState(state);
                                            updateTernaryCheckboxProperties($el, state)
                                        "
                                        x-bind:checked="state === 'include'"
                                    />
                                    {{ __('torrent.immune') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label
                                    style="user-select: none"
                                    class="form__label"
                                    x-data="{ state: @entangle('uploaded').live, ...ternaryCheckbox() }"
                                >
                                    <input
                                        type="checkbox"
                                        class="user-torrents__checkbox"
                                        x-init="updateTernaryCheckboxProperties($el, state)"
                                        x-on:click="
                                            state = getNextTernaryCheckboxState(state);
                                            updateTernaryCheckboxProperties($el, state)
                                        "
                                        x-bind:checked="state === 'include'"
                                    />
                                    {{ __('torrent.uploaded') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label
                                    style="user-select: none"
                                    class="form__label"
                                    x-data="{ state: @entangle('downloaded').live, ...ternaryCheckbox() }"
                                >
                                    <input
                                        type="checkbox"
                                        class="user-torrents__checkbox"
                                        x-init="updateTernaryCheckboxProperties($el, state)"
                                        x-on:click="
                                            state = getNextTernaryCheckboxState(state);
                                            updateTernaryCheckboxProperties($el, state)
                                        "
                                        x-bind:checked="state === 'include'"
                                    />
                                    {{ __('torrent.downloaded') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.moderation') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="user-torrents__checkbox"
                                        type="checkbox"
                                        value="{{ \App\Enums\ModerationStatus::PENDING }}"
                                        wire:model.live="status"
                                    />
                                    {{ __('torrent.pending') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="user-torrents__checkbox"
                                        type="checkbox"
                                        value="{{ \App\Enums\ModerationStatus::APPROVED }}"
                                        wire:model.live="status"
                                    />
                                    {{ __('torrent.approved') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="user-torrents__checkbox"
                                        type="checkbox"
                                        value="{{ \App\Enums\ModerationStatus::REJECTED }}"
                                        wire:model.live="status"
                                    />
                                    {{ __('torrent.rejected') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="user-torrents__checkbox"
                                        type="checkbox"
                                        value="{{ \App\Enums\ModerationStatus::POSTPONED }}"
                                        wire:model.live="status"
                                    />
                                    Postponed
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Precision</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        type="checkbox"
                                        class="user-torrents__checkbox"
                                        wire:model.live="showMorePrecision"
                                    />
                                    Show more precision
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </section>
    <section class="panelV2 user-torrents">
        <h2 class="panel__heading">{{ __('user.torrents-history') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <th
                        class="user-torrents__name-header"
                        wire:click="sortBy('name')"
                        role="columnheader button"
                    >
                        {{ __('torrent.name') }}
                        @include('livewire.includes._sort-icon', ['field' => 'name'])
                    </th>
                    <th
                        class="user-torrents__seeders-header"
                        wire:click="sortBy('seeders')"
                        role="columnheader button"
                        title="{{ __('torrent.seeders') }}"
                    >
                        <i class="fas fa-arrow-alt-circle-up"></i>
                        @include('livewire.includes._sort-icon', ['field' => 'seeders'])
                    </th>
                    <th
                        class="user-torrents__leechers-header"
                        wire:click="sortBy('leechers')"
                        role="columnheader button"
                        title="{{ __('torrent.leechers') }}"
                    >
                        <i class="fas fa-arrow-alt-circle-down"></i>
                        @include('livewire.includes._sort-icon', ['field' => 'leechers'])
                    </th>
                    <th
                        class="user-torrents__times-header"
                        wire:click="sortBy('times_completed')"
                        role="columnheader button"
                        title="{{ __('torrent.completed') }}"
                    >
                        <i class="fas fa-check-circle"></i>
                        @include('livewire.includes._sort-icon', ['field' => 'times_completed'])
                    </th>
                    <th
                        class="user-torrents__agent-header"
                        wire:click="sortBy('agent')"
                        role="columnheader button"
                    >
                        {{ __('torrent.client') }}
                        @include('livewire.includes._sort-icon', ['field' => 'agent'])
                    </th>
                    <th
                        class="user-torrents__size-header"
                        wire:click="sortBy('size')"
                        role="columnheader button"
                    >
                        {{ __('torrent.size') }}
                        @include('livewire.includes._sort-icon', ['field' => 'size'])
                    </th>
                    <th
                        class="user-torrents__upload-header"
                        wire:click="sortBy('actual_uploaded')"
                        role="columnheader button"
                    >
                        {{ __('common.upload') }}
                        @include('livewire.includes._sort-icon', ['field' => 'actual_uploaded'])
                    </th>
                    <th
                        class="user-torrents__download-header"
                        wire:click="sortBy('actual_downloaded')"
                        role="columnheader button"
                    >
                        {{ __('common.download') }}
                        @include('livewire.includes._sort-icon', ['field' => 'actual_downloaded'])
                    </th>
                    <th
                        class="user-torrents__ratio-header"
                        wire:click="sortBy('actual_ratio')"
                        role="columnheader button"
                    >
                        {{ __('common.ratio') }}
                        @include('livewire.includes._sort-icon', ['field' => 'actual_ratio'])
                    </th>
                    <th
                        class="user-torrents__leechtime-header"
                        wire:click="sortBy('leechtime')"
                        role="columnheader button"
                    >
                        Leeched
                        @include('livewire.includes._sort-icon', ['field' => 'leechtime'])
                    </th>
                    <th
                        class="user-torrents__seedtime-header"
                        wire:click="sortBy('seedtime')"
                        role="columnheader button"
                    >
                        Seeded
                        @include('livewire.includes._sort-icon', ['field' => 'seedtime'])
                    </th>
                    <th
                        class="user-torrents__created-at-header"
                        wire:click="sortBy('created_at')"
                        role="columnheader button"
                    >
                        {{ __('torrent.started') }}
                        @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                    </th>
                    <th
                        class="user-torrents__updated-at-header"
                        wire:click="sortBy('updated_at')"
                        role="columnheader button"
                    >
                        {{ __('torrent.updated') }}
                        @include('livewire.includes._sort-icon', ['field' => 'updated_at'])
                    </th>
                    <th
                        class="user-torrents__completed-at-header"
                        wire:click="sortBy('completed_at')"
                        role="columnheader button"
                    >
                        {{ __('torrent.completed') }}
                        @include('livewire.includes._sort-icon', ['field' => 'completed_at'])
                    </th>
                    <th
                        class="user-torrents__prewarned-header"
                        wire:click="sortBy('prewarned_at')"
                        role="columnheader button"
                    >
                        {{ __('torrent.prewarn') }}
                        @include('livewire.includes._sort-icon', ['field' => 'prewarned_at'])
                    </th>
                    <th
                        class="user-torrents__warned-header"
                        wire:click="sortBy('hitrun')"
                        role="columnheader button"
                        title="{{ __('torrent.hitrun') }}"
                    >
                        <i class="fas fa-exclamation-triangle"></i>
                    </th>
                    <th
                        class="user-torrents__immune-header"
                        wire:click="sortBy('immune')"
                        role="columnheader button"
                        title="{{ __('torrent.immune') }}"
                    >
                        <i class="fas fa-syringe"></i>
                    </th>
                    <th
                        class="user-torrents__upload-header"
                        wire:click="sortBy('uploaded')"
                        role="columnheader button"
                        title="{{ __('torrent.uploaded') }}"
                    >
                        <i class="fas fa-upload"></i>
                    </th>
                    <th
                        class="user-torrents__status-header"
                        wire:click="sortBy('status')"
                        role="columnheader button"
                        title="{{ __('torrent.approved') }}"
                    >
                        <i class="fas fa-tasks"></i>
                        @include('livewire.includes._sort-icon', ['field' => 'status'])
                    </th>
                </thead>
                <tbody>
                    @foreach ($histories as $history)
                        <tr x-data="userHistory" data-history-id="{{ $history->torrent_id }}">
                            <td>
                                <a
                                    class="user-torrents__name"
                                    href="{{ route('torrents.show', ['id' => $history->torrent_id]) }}"
                                >
                                    {{ $history->name }}
                                </a>
                            </td>
                            <td
                                @class([
                                    'user-torrents__seeders',
                                    'torrent-activity-indicator--seeding' => $history->seeding,
                                ])
                                @if ($history->seeding)
                                    title="{{ __('torrent.currently-seeding') }}"
                                @endif
                            >
                                <a
                                    class="torrent__seeder-count"
                                    href="{{ route('peers', ['id' => $history->torrent_id]) }}"
                                >
                                    {{ $history->seeders }}
                                </a>
                            </td>
                            <td
                                @class([
                                    'user-torrents__leechers',
                                    'torrent-activity-indicator--leeching' => $history->leeching,
                                ])
                                @if ($history->leeching)
                                    title="{{ __('torrent.currently-leeching') }}"
                                @endif
                            >
                                <a
                                    class="torrent__leecher-count"
                                    href="{{ route('peers', ['id' => $history->torrent_id]) }}"
                                >
                                    {{ $history->leechers }}
                                </a>
                            </td>
                            <td
                                @class([
                                    'user-torrents__times',
                                    'torrent-activity-indicator--completed' => $history->completed,
                                ])
                                @if ($history->completed)
                                    title="{{ __('torrent.completed') }}"
                                @endif
                            >
                                <a
                                    class="torrent__times-completed-count"
                                    href="{{ route('history', ['id' => $history->torrent_id]) }}"
                                >
                                    {{ $history->times_completed }}
                                </a>
                            </td>
                            <td class="user-torrents__agent text-purple">
                                {{ $history->agent ?: __('common.unknown') }}
                            </td>
                            <td class="user-torrents__size">
                                {{ App\Helpers\StringHelper::formatBytes($history->size) }}
                            </td>
                            <td
                                class="user-torrents__upload"
                                title="{{ __('user.actual-upload') }}"
                            >
                                <span class="text-green">
                                    {{ App\Helpers\StringHelper::formatBytes($history->actual_uploaded, 2) }}
                                </span>
                                <br />
                                <span class="text-blue" title="{{ __('user.credited-upload') }}">
                                    {{ App\Helpers\StringHelper::formatBytes($history->uploaded, 2) }}
                                </span>
                            </td>
                            <td class="user-torrents__download">
                                <span class="text-red" title="{{ __('user.actual-download') }}">
                                    {{ App\Helpers\StringHelper::formatBytes($history->actual_downloaded, 2) }}
                                </span>
                                <br />
                                <span
                                    class="text-orange"
                                    title="{{ __('user.credited-download') }}"
                                >
                                    {{ App\Helpers\StringHelper::formatBytes($history->downloaded, 2) }}
                                </span>
                            </td>
                            <td class="user-torrents__ratio">
                                @php($ratio = $history->actual_ratio < 1000 ? \number_format($history->actual_ratio, 2) : INF)
                                <span
                                    @if ($ratio < 1)
                                        class="ratio-0{{ \floor($ratio * 10) }}"
                                    @elseif ($ratio < 2)
                                        class="ratio-10"
                                    @elseif ($ratio < 5)
                                        class="ratio-20"
                                    @elseif ($ratio <= INF)
                                        class="ratio-50"
                                    @endif
                                    title="Actual ratio: {{ $history->actual_ratio }}"
                                >
                                    {{ $ratio }}
                                </span>
                                <br />
                                @php($ratio = $history->ratio < 1000 ? \number_format($history->ratio, 2) : INF)
                                <span
                                    @if ($ratio < 1)
                                        class="ratio-0{{ \floor($ratio * 10) }}"
                                    @elseif ($ratio < 2)
                                        class="ratio-10"
                                    @elseif ($ratio < 5)
                                        class="ratio-20"
                                    @elseif ($ratio <= INF)
                                        class="ratio-50"
                                    @endif
                                    title="Credited ratio: {{ $history->ratio }}"
                                >
                                    {{ $ratio }}
                                </span>
                            </td>
                            @if ($showMorePrecision)
                                <td class="user-torrents__leechtime">
                                    @if ($history->leechtime === null)
                                        N/A
                                    @else
                                        {{ App\Helpers\StringHelper::timeElapsed($history->leechtime) }}
                                    @endif
                                </td>
                                <td class="user-torrents__seedtime">
                                    <span
                                        class="{{ ($history->seedtime ?? 0) < config('hitrun.seedtime') ? 'text-red' : 'text-green' }}"
                                    >
                                        @if ($history->seedtime === null)
                                            N/A
                                        @else
                                            {{ App\Helpers\StringHelper::timeElapsed($history->seedtime) }}
                                        @endif
                                    </span>
                                </td>
                                <td class="user-torrents__created-at">
                                    <time
                                        datetime="{{ $history->created_at }}"
                                        title="{{ $history->created_at }}"
                                    >
                                        {{ $history->created_at ?? 'N/A' }}
                                    </time>
                                </td>
                                <td class="user-torrents__updated-at">
                                    <time
                                        datetime="{{ $history->updated_at }}"
                                        title="{{ $history->updated_at }}"
                                    >
                                        {{ $history->updated_at ?? 'N/A' }}
                                    </time>
                                </td>
                                <td class="user-torrents__completed-at">
                                    <time
                                        datetime="{{ $history->completed_at }}"
                                        title="{{ $history->completed_at }}"
                                    >
                                        {{ $history->completed_at ?? 'N/A' }}
                                    </time>
                                </td>
                                <td class="user-torrents__prewarned-at">
                                    <time
                                        datetime="{{ $history->prewarned_at }}"
                                        title="{{ $history->prewarned_at }}"
                                    >
                                        {{ $history->prewarned_at ?? 'N/A' }}
                                    </time>
                                </td>
                            @else
                                <td class="user-torrents__leechtime">
                                    @if ($history->leechtime === null)
                                        N/A
                                    @else
                                        {{ \implode(' ', \array_slice(\explode(' ', App\Helpers\StringHelper::timeElapsed($history->leechtime)), 0, 2)) }}
                                    @endif
                                </td>
                                <td class="user-torrents__seedtime">
                                    @if ($history->seedtime === null)
                                        N/A
                                    @else
                                        <span
                                            class="{{ $history->seedtime < config('hitrun.seedtime') ? 'text-red' : 'text-green' }}"
                                        >
                                            {{ \implode(' ', \array_slice(\explode(' ', App\Helpers\StringHelper::timeElapsed($history->seedtime)), 0, 2)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="user-torrents__created-at">
                                    <time
                                        datetime="{{ $history->created_at }}"
                                        title="{{ $history->created_at }}"
                                    >
                                        {{ $history->created_at === null ? 'N/A' : \explode(' ', $history->created_at)[0] }}
                                    </time>
                                </td>
                                <td class="user-torrents__updated-at">
                                    <time
                                        datetime="{{ $history->updated_at }}"
                                        title="{{ $history->updated_at }}"
                                    >
                                        {{ $history->updated_at === null ? 'N/A' : \explode(' ', $history->updated_at)[0] }}
                                    </time>
                                </td>
                                <td class="user-torrents__completed-at">
                                    <time
                                        datetime="{{ $history->completed_at }}"
                                        title="{{ $history->completed_at }}"
                                    >
                                        {{ $history->completed_at === null ? 'N/A' : \explode(' ', $history->completed_at)[0] }}
                                    </time>
                                </td>

                                <td class="user-torrents__prewarned-at">
                                    <time
                                        datetime="{{ $history->prewarned_at }}"
                                        title="{{ $history->prewarned_at }}"
                                    >
                                        {{ $history->prewarned_at === null ? 'N/A' : \explode(' ', $history->prewarned_at)[0] }}
                                    </time>
                                </td>
                            @endif
                            <td class="user-torrents__warned">
                                @if ($history->hitrun == 1)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                        title="Warned"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                        title="Not warned"
                                    ></i>
                                @endif
                            </td>
                            <td class="user-torrents__immune">
                                @if ($history->immune)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                        title="Immune"
                                        @if (auth()->user()->group->is_modo)
                                            x-on:click.prevent="updateImmune(false)"
                                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to set this history record to not immune: ' . $history->name . '?') }}"
                                            style="cursor: pointer"
                                        @endif
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                        title="Not immune"
                                        @if (auth()->user()->group->is_modo)
                                            x-on:click.prevent="updateImmune(true)"
                                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to set this history record to immune: ' . $history->name . '?') }}"
                                            style="cursor: pointer"
                                        @endif
                                    ></i>
                                @endif
                            </td>
                            <td class="user-torrents__uploader">
                                @if ($history->uploader == 1)
                                    <i
                                        class="{{ config('other.font-awesome') }} text-green fa-check"
                                        title="{{ __('torrent.uploaded') }}"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} text-red fa-times"
                                        title="Not {{ __('torrent.uploaded') }}"
                                    ></i>
                                @endif
                            </td>
                            <td class="user-torrents__status">
                                @switch($history->status)
                                    @case(\App\Enums\ModerationStatus::PENDING)
                                        <span
                                            title="{{ __('torrent.pending') }}"
                                            class="{{ config('other.font-awesome') }} fa-tasks text-orange"
                                        ></span>

                                        @break
                                    @case(\App\Enums\ModerationStatus::APPROVED)
                                        <span
                                            title="{{ __('torrent.approved') }}"
                                            class="{{ config('other.font-awesome') }} fa-check text-green"
                                        ></span>

                                        @break
                                    @case(\App\Enums\ModerationStatus::REJECTED)
                                        <span
                                            title="{{ __('torrent.rejected') }}"
                                            class="{{ config('other.font-awesome') }} fa-times text-red"
                                        ></span>

                                        @break
                                    @case(\App\Enums\ModerationStatus::POSTPONED)
                                        <span
                                            title="Postponed"
                                            class="{{ config('other.font-awesome') }} fa-hourglass text-red"
                                        ></span>

                                        @break
                                @endswitch
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $histories->links('partials.pagination') }}
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

        document.addEventListener('alpine:init', () => {
            Alpine.data('userHistory', () => ({
                updateImmune(immune) {
                    this.confirmAction(() =>
                        this.$wire.updateImmune(this.$root.dataset.historyId, immune),
                    );
                },
                confirmAction(onConfirm) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: atob(this.$el.dataset.b64DeletionMessage),
                        icon: 'warning',
                        showConfirmButton: true,
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onConfirm();
                        }
                    });
                },
            }));
        });
    </script>
</div>
