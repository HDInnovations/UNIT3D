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
                        <input
                            id="user"
                            wire:model.live="user"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="user">Username</label>
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
                            id="seeder"
                            wire:model.live="seeder"
                            class="form__select"
                            placeholder=" "
                        >
                            <option value="any">Any</option>
                            <option value="include">Completed</option>
                            <option value="exclude">Incomplete</option>
                        </select>
                        <label class="form__label form__label--floating" for="seeder">
                            Completed
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
                            <option value="user_id">User</option>
                        </select>
                        <label class="form__label form__label--floating" for="groupBy">
                            Group by
                        </label>
                    </p>
                </div>
            </form>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">Histories</h2>
        <div class="panel__body" wire:loading.block>Loading...</div>
        <div class="data-table-wrapper">
            @switch($groupBy)
                @case('user_id')
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th
                                    wire:click="sortBy('history.user_id')"
                                    role="columnheader button"
                                >
                                    {{ __('user.user') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.user_id'])
                                </th>
                                <th wire:click="sortBy('torrent_count')" role="columnheader button">
                                    {{ __('torrent.torrents') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'torrent_count'])
                                </th>
                                <th wire:click="sortBy('uploaded_sum')" role="columnheader button">
                                    {{ __('user.credited-upload') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.uploaded_sum'])
                                </th>
                                <th
                                    wire:click="sortBy('actual_uploaded_sum')"
                                    role="columnheader button"
                                >
                                    {{ __('user.upload-true') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.actual_uploaded_sum'])
                                </th>
                                <th
                                    wire:click="sortBy('client_uploaded_sum')"
                                    role="columnheader button"
                                >
                                    Client upload
                                    @include('livewire.includes._sort-icon', ['field' => 'history.client_uploaded_sum'])
                                </th>
                                <th
                                    wire:click="sortBy('downloaded_sum')"
                                    role="columnheader button"
                                >
                                    {{ __('user.credited-download') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.downloaded_sum'])
                                </th>
                                <th
                                    wire:click="sortBy('actual_downloaded_sum')"
                                    role="columnheader button"
                                >
                                    {{ __('user.download-true') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.actual_downloaded_sum'])
                                </th>
                                <th
                                    wire:click="sortBy('client_downloaded_sum')"
                                    role="columnheader button"
                                >
                                    Client download
                                    @include('livewire.includes._sort-icon', ['field' => 'history.client_downloaded_sum'])
                                </th>
                                <th
                                    wire:click="sortBy('refunded_download_sum')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.refunded') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.refunded_download_sum'])
                                </th>
                                <th
                                    wire:click="sortBy('created_at_min')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.started') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.created_at_min'])
                                </th>
                                <th
                                    wire:click="sortBy('updated_at_max')"
                                    role="columnheader button"
                                >
                                    Announced
                                    @include('livewire.includes._sort-icon', ['field' => 'history.updated_at_max'])
                                </th>
                                <th wire:click="sortBy('seedtime_avg')" role="columnheader button">
                                    {{ __('user.avg-seedtime') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.seedtime_avg'])
                                </th>
                                <th wire:click="sortBy('seeding_count')" role="columnheader button">
                                    {{ __('torrent.seeding') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'seeding_count'])
                                </th>
                                <th
                                    wire:click="sortBy('leeching_count')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.leeching') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'leeching_count'])
                                </th>
                                <th
                                    wire:click="sortBy('prewarn_count')"
                                    role="columnheader button"
                                    title="{{ __('torrent.prewarn') }}"
                                >
                                    <i class="fas fa-exclamation"></i>
                                </th>
                                <th
                                    wire:click="sortBy('hitrun_count')"
                                    role="columnheader button"
                                    title="{{ __('torrent.hitrun') }}"
                                >
                                    <i class="fas fa-exclamation-triangle"></i>
                                </th>
                                <th
                                    wire:click="sortBy('immune_count')"
                                    role="columnheader button"
                                    title="{{ __('torrent.immune') }}"
                                >
                                    <i class="fas fa-syringe"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($histories as $history)
                                <tr>
                                    <td>
                                        <x-user-tag :user="$history->user" :anon="false" />
                                    </td>
                                    <td>
                                        {{ $history->torrent_count }}
                                    </td>
                                    <td title="{{ $history->uploaded_sum }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->uploaded_sum, 2) }}
                                    </td>
                                    <td title="{{ $history->actual_uploaded_sum }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->actual_uploaded_sum, 2) }}
                                    </td>
                                    <td title="{{ $history->client_uploaded_sum }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->client_uploaded_sum, 2) }}
                                    </td>
                                    <td title="{{ $history->downloaded_sum }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->downloaded_sum, 2) }}
                                    </td>
                                    <td title="{{ $history->actual_downloaded_sum }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->actual_downloaded_sum, 2) }}
                                    </td>
                                    <td title="{{ $history->client_downloaded_sum }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->client_downloaded_sum, 2) }}
                                    </td>
                                    <td title="{{ $history->refunded_download_sum }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->refunded_download_sum, 2) }}
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $history->created_at_min }}"
                                            title="{{ $history->created_at_min }}"
                                        >
                                            {{ $history->created_at_min ? $history->created_at_min->diffForHumans() : 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $history->updated_at_max }}"
                                            title="{{ $history->updated_at_max }}"
                                        >
                                            {{ $history->updated_at_max ? $history->updated_at_max->diffForHumans() : 'N/A' }}
                                        </time>
                                    </td>

                                    @if ($history->seedtime_avg < config('hitrun.seedtime'))
                                        <td
                                            class="text-red"
                                            title="{{ App\Helpers\StringHelper::timeElapsed($history->seedtime_avg) }}"
                                        >
                                            {{ $weeks = intdiv($history->seedtime_avg ?? 0, 3600 * 24 * 7) }}w
                                            {{ intdiv(($history->seedtime_avg ?? 0) - $weeks * 3600 * 24 * 7, 3600) }}h
                                        </td>
                                    @else
                                        <td
                                            class="text-green"
                                            title="{{ App\Helpers\StringHelper::timeElapsed($history->seedtime_avg) }}"
                                        >
                                            {{ $weeks = intdiv($history->seedtime_avg ?? 0, 3600 * 24 * 7) }}w
                                            {{ intdiv(($history->seedtime_avg ?? 0) - $weeks * 3600 * 24 * 7, 3600) }}h
                                        </td>
                                    @endif
                                    <td>{{ $history->seeding_count }}</td>
                                    <td>{{ $history->leeching_count }}</td>
                                    <td>{{ $history->prewarn_count }}</td>
                                    <td>{{ $history->hitrun_count }}</td>
                                    <td>{{ $history->immune_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @break
                @default
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th
                                    wire:click="sortBy('history.user_id')"
                                    role="columnheader button"
                                >
                                    {{ __('user.user') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.user_id'])
                                </th>
                                <th
                                    wire:click="sortBy('history.torrent_id')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.torrent') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.torrent_id'])
                                </th>
                                <th wire:click="sortBy('history.agent')" role="columnheader button">
                                    {{ __('torrent.agent') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.agent'])
                                </th>
                                <th
                                    wire:click="sortBy('history.uploaded')"
                                    role="columnheader button"
                                >
                                    {{ __('user.credited-upload') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.uploaded'])
                                </th>
                                <th
                                    wire:click="sortBy('history.actual_uploaded')"
                                    role="columnheader button"
                                >
                                    {{ __('user.upload-true') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.actual_uploaded'])
                                </th>
                                <th
                                    wire:click="sortBy('history.client_uploaded')"
                                    role="columnheader button"
                                >
                                    Client upload
                                    @include('livewire.includes._sort-icon', ['field' => 'history.client_uploaded'])
                                </th>
                                <th
                                    wire:click="sortBy('history.downloaded')"
                                    role="columnheader button"
                                >
                                    {{ __('user.credited-download') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.downloaded'])
                                </th>
                                <th
                                    wire:click="sortBy('history.actual_downloaded')"
                                    role="columnheader button"
                                >
                                    {{ __('user.download-true') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.actual_downloaded'])
                                </th>
                                <th
                                    wire:click="sortBy('history.client_downloaded')"
                                    role="columnheader button"
                                >
                                    Client download
                                    @include('livewire.includes._sort-icon', ['field' => 'history.client_downloaded'])
                                </th>
                                <th
                                    wire:click="sortBy('history.refunded_download')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.refunded') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.refunded_download'])
                                </th>
                                <th
                                    wire:click="sortBy('history.created_at')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.started') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.created_at'])
                                </th>
                                <th
                                    wire:click="sortBy('history.updated_at')"
                                    role="columnheader button"
                                >
                                    Announced
                                    @include('livewire.includes._sort-icon', ['field' => 'history.updated_at'])
                                </th>
                                <th
                                    wire:click="sortBy('history.completed_at')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.completed_at') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.completed_at'])
                                </th>
                                <th
                                    wire:click="sortBy('history.prewarned_at')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.prewarn') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.prewarned_at'])
                                </th>
                                <th
                                    wire:click="sortBy('history.seedtime')"
                                    role="columnheader button"
                                >
                                    {{ __('torrent.seedtime') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'history.seedtime'])
                                </th>
                                <th wire:click="sortBy('seeding')" role="columnheader button">
                                    {{ __('torrent.seeding') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'seeding'])
                                </th>
                                <th wire:click="sortBy('leeching')" role="columnheader button">
                                    {{ __('torrent.leeching') }}
                                    @include('livewire.includes._sort-icon', ['field' => 'leeching'])
                                </th>
                                <th
                                    wire:click="sortBy('hitrun')"
                                    role="columnheader button"
                                    title="{{ __('torrent.hitrun') }}"
                                >
                                    <i class="fas fa-exclamation-triangle"></i>
                                </th>
                                <th
                                    wire:click="sortBy('immune')"
                                    role="columnheader button"
                                    title="{{ __('torrent.immune') }}"
                                >
                                    <i class="fas fa-syringe"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($histories as $history)
                                <tr>
                                    <td>
                                        <x-user-tag :user="$history->user" :anon="false" />
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('torrents.show', ['id' => $history->torrent_id]) }}"
                                        >
                                            {{ $history->torrent->name ?? '' }}
                                        </a>
                                    </td>
                                    <td>{{ $history->agent }}</td>
                                    <td title="{{ $history->uploaded }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->uploaded, 2) }}
                                    </td>
                                    <td title="{{ $history->actual_uploaded }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->actual_uploaded, 2) }}
                                    </td>
                                    <td title="{{ $history->client_uploaded }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->client_uploaded, 2) }}
                                    </td>
                                    <td title="{{ $history->downloaded }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->downloaded, 2) }}
                                    </td>
                                    <td title="{{ $history->actual_downloaded }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->actual_downloaded, 2) }}
                                    </td>
                                    <td title="{{ $history->client_downloaded }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->client_downloaded, 2) }}
                                    </td>
                                    <td title="{{ $history->refunded_download }}">
                                        {{ App\Helpers\StringHelper::formatBytes($history->refunded_download, 2) }}
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $history->created_at }}"
                                            title="{{ $history->created_at }}"
                                        >
                                            {{ $history->created_at ? $history->created_at->diffForHumans() : 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $history->updated_at }}"
                                            title="{{ $history->updated_at }}"
                                        >
                                            {{ $history->updated_at ? $history->updated_at->diffForHumans() : 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $history->completed_at }}"
                                            title="{{ $history->completed_at }}"
                                        >
                                            {{ $history->completed_at ? $history->completed_at->diffForHumans() : 'N/A' }}
                                        </time>
                                    </td>
                                    <td>
                                        <time
                                            datetime="{{ $history->prewarned_at }}"
                                            title="{{ $history->prewarned_at }}"
                                        >
                                            {{ $history->prewarned_at ? $history->prewarned_at->diffForHumans() : 'N/A' }}
                                        </time>
                                    </td>

                                    @if ($history->seedtime < config('hitrun.seedtime'))
                                        <td
                                            class="text-red"
                                            title="{{ App\Helpers\StringHelper::timeElapsed($history->seedtime) }}"
                                        >
                                            {{ $weeks = intdiv($history->seedtime ?? 0, 3600 * 24 * 7) }}w
                                            {{ intdiv(($history->seedtime ?? 0) - $weeks * 3600 * 24 * 7, 3600) }}h
                                        </td>
                                    @else
                                        <td
                                            class="text-green"
                                            title="{{ App\Helpers\StringHelper::timeElapsed($history->seedtime) }}"
                                        >
                                            {{ $weeks = intdiv($history->seedtime ?? 0, 3600 * 24 * 7) }}w
                                            {{ intdiv(($history->seedtime ?? 0) - $weeks * 3600 * 24 * 7, 3600) }}h
                                        </td>
                                    @endif
                                    <td>
                                        @if ($history->active)
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
                                    <td>
                                        @if ($history->seeder)
                                            <i
                                                class="{{ config('other.font-awesome') }} text-green fa-check"
                                                title="Seeder"
                                            ></i>
                                        @else
                                            <i
                                                class="{{ config('other.font-awesome') }} text-red fa-times"
                                                title="Leecher"
                                            ></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($history->immune)
                                            <i
                                                class="{{ config('other.font-awesome') }} text-green fa-check"
                                                title="Immune"
                                            ></i>
                                        @else
                                            <i
                                                class="{{ config('other.font-awesome') }} text-red fa-times"
                                                title="Not immune"
                                            ></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($history->hitrun)
                                            <i
                                                class="{{ config('other.font-awesome') }} text-green fa-check"
                                                title="Warned"
                                            ></i>
                                        @else
                                            <i
                                                class="{{ config('other.font-awesome') }} text-red fa-times"
                                                title="Not warned"
                                            ></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            @endswitch
            {{ $histories->links('partials.pagination') }}
        </div>
    </section>
</div>
