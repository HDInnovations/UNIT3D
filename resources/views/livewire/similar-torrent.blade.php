<div class="similar-torrents-list">
    <div style="display: flex; flex-direction: column; gap: 16px">
        <search class="compact-search similar-torrents__filters" x-data="toggle">
            <div class="compact-search__visible-default">
                <p class="form__group">
                    <input
                        id="name"
                        wire:model.live="name"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                        @if (auth()->user()->settings?->torrent_search_autofocus)
                            autofocus
                        @endif
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.search') }}
                    </label>
                </p>
                <button class="form__button form__standard-icon-button" x-on:click="toggle">
                    <i class="{{ config('other.font-awesome') }} fa-sliders"></i>
                </button>
            </div>
            <form class="form" x-cloak x-show="isToggledOn">
                <div class="form__group--short-horizontal">
                    <p class="form__group">
                        <input
                            id="description"
                            wire:model.live="description"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="description">
                            {{ __('torrent.description') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="mediainfo"
                            wire:model.live="mediainfo"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="mediainfo">
                            {{ __('torrent.media-info') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="keywords"
                            wire:model.live="keywords"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="keywords">
                            {{ __('torrent.keywords') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="uploader"
                            wire:model.live="uploader"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="uploader">
                            {{ __('torrent.uploader') }}
                        </label>
                    </p>
                </div>
                <div class="form__group--short-horizontal">
                    <div class="form__group--short-horizontal">
                        <p class="form__group">
                            <input
                                id="episodeNumber"
                                wire:model.live="episodeNumber"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="episodeNumber">
                                {{ __('torrent.episode-number') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                id="seasonNumber"
                                wire:model.live="seasonNumber"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="seasonNumber">
                                {{ __('torrent.season-number') }}
                            </label>
                        </p>
                    </div>
                    <div class="form__group--short-horizontal">
                        <p class="form__group">
                            <input
                                id="minSize"
                                wire:model.live="minSize"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="minSize">
                                Minimum Size
                            </label>
                        </p>
                        <p class="form__group">
                            <select
                                id="minSizeMultiplier"
                                wire:model.live="minSizeMultiplier"
                                class="form__select"
                                placeholder=" "
                            >
                                <option value="1" selected>Bytes</option>
                                <option value="1000">KB</option>
                                <option value="1024">KiB</option>
                                <option value="1000000">MB</option>
                                <option value="1048576">MiB</option>
                                <option value="1000000000">GB</option>
                                <option value="1073741824">GiB</option>
                                <option value="1000000000000">TB</option>
                                <option value="1099511627776">TiB</option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                for="minSizeMultiplier"
                            >
                                Unit
                            </label>
                        </p>
                    </div>
                    <div class="form__group--short-horizontal">
                        <p class="form__group">
                            <input
                                id="maxSize"
                                wire:model.live="maxSize"
                                class="form__text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                placeholder=" "
                            />
                            <label class="form__label form__label--floating" for="maxSize">
                                Maximum Size
                            </label>
                        </p>
                        <p class="form__group">
                            <select
                                id="maxSizeMultiplier"
                                wire:model.live="maxSizeMultiplier"
                                class="form__select"
                                placeholder=" "
                            >
                                <option value="1" selected>Bytes</option>
                                <option value="1000">KB</option>
                                <option value="1024">KiB</option>
                                <option value="1000000">MB</option>
                                <option value="1048576">MiB</option>
                                <option value="1000000000">GB</option>
                                <option value="1073741824">GiB</option>
                                <option value="1000000000000">TB</option>
                                <option value="1099511627776">TiB</option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                for="maxSizeMultiplier"
                            >
                                Unit
                            </label>
                        </p>
                    </div>
                </div>
                <div class="form__group--short-horizontal">
                    <p class="form__group">
                        <input
                            id="playlistId"
                            wire:model.live="playlistId"
                            class="form__text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="playlistId">
                            Playlist ID
                        </label>
                    </p>
                </div>
                <div class="form__group--short-horizontal">
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.type') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($types as $type)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                class="form__checkbox"
                                                type="checkbox"
                                                value="{{ $type->id }}"
                                                wire:model.live="typeIds"
                                            />
                                            {{ $type->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">
                                {{ __('torrent.resolution') }}
                            </legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach ($resolutions as $resolution)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                class="form__checkbox"
                                                type="checkbox"
                                                value="{{ $resolution->id }}"
                                                wire:model.live="resolutionIds"
                                            />
                                            {{ $resolution->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">Buff</legend>
                            <div class="form__fieldset-checkbox-container">
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="0"
                                            wire:model.live="free"
                                        />
                                        0% Freeleech
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="25"
                                            wire:model.live="free"
                                        />
                                        25% Freeleech
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="50"
                                            wire:model.live="free"
                                        />
                                        50% Freeleech
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="75"
                                            wire:model.live="free"
                                        />
                                        75% Freeleech
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="100"
                                            wire:model.live="free"
                                        />
                                        100% Freeleech
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="doubleup"
                                        />
                                        Double Upload
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="featured"
                                        />
                                        Featured
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="refundable"
                                        />
                                        Refundable
                                    </label>
                                </p>
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">Tags</legend>
                            <div class="form__fieldset-checkbox-container">
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="internal"
                                        />
                                        {{ __('torrent.internal') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="personalRelease"
                                        />
                                        {{ __('torrent.personal-release') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="trumpable"
                                        />
                                        Trumpable
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="highspeed"
                                        />
                                        {{ __('common.high-speeds') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="bookmarked"
                                        />
                                        {{ __('common.bookmarked') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="wished"
                                        />
                                        {{ __('common.wished') }}
                                    </label>
                                </p>
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.health') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="alive"
                                        />
                                        {{ __('torrent.alive') }}
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="dying"
                                        />
                                        Dying
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="dead"
                                        />
                                        Dead
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="graveyard"
                                        />
                                        {{ __('graveyard.graveyard') }}
                                    </label>
                                </p>
                            </div>
                        </fieldset>
                    </div>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.history') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="notDownloaded"
                                        />
                                        Not Downloaded
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="downloaded"
                                        />
                                        Downloaded
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="seeding"
                                        />
                                        Seeding
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="leeching"
                                        />
                                        Leeching
                                    </label>
                                </p>
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="1"
                                            wire:model.live="incomplete"
                                        />
                                        Incomplete
                                    </label>
                                </p>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </form>
        </search>
        <section class="panelV2" x-data="torrentGroup">
            <header class="panel__header">
                <h2 class="panel__heading">{{ __('torrent.torrents') }}</h2>
                <div class="panel__actions">
                    @if ($checked && $user->group->is_modo)
                        <div class="panel__action">
                            <button
                                class="form__button form__button--filled"
                                wire:click="alertConfirm()"
                            >
                                Delete ({{ count($checked) }})
                            </button>
                        </div>
                    @endif

                    @if ($user->group->is_modo)
                        <div class="panel__action" title="{{ __('common.select') }}">
                            <label class="form__label">
                                Select All
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live="selectPage"
                                />
                            </label>
                        </div>
                    @endif

                    <div class="panel__action">
                        <button class="form__button form__button--text" x-bind="all">
                            Expand all
                        </button>
                    </div>
                </div>
            </header>
            <div class="data-table-wrapper">
                @if ($category->tv_meta)
                    <section>
                        @if ($similarTorrents->has('Complete Pack'))
                            <details class="torrent-search--grouped__dropdown" open>
                                <summary x-bind="complete">Complete Pack</summary>
                                <table class="similar-torrents__torrents">
                                    <tbody>
                                        @foreach ($similarTorrents['Complete Pack'] as $type => $torrents)
                                            @foreach ($torrents as $torrent)
                                                <tr>
                                                    @if ($loop->first)
                                                        <th
                                                            class="similar-torrents__type"
                                                            scope="rowgroup"
                                                            rowspan="{{ $loop->count }}"
                                                        >
                                                            {{ $type }}
                                                        </th>
                                                    @endif

                                                    @if ($user->group->is_modo)
                                                        <td
                                                            class="similar-torrents__checkbox"
                                                            x-on:click.self="$el.firstElementChild.click()"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                value="{{ $torrent->id }}"
                                                                wire:model.live="checked"
                                                            />
                                                        </td>
                                                    @endif

                                                    @include('components.partials._torrent-group-row')
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </details>
                        @endif

                        @if ($similarTorrents->has('Specials'))
                            <details
                                class="torrent-search--grouped__dropdown"
                                @if ($checked || (! $similarTorrents->has('Complete Pack') && ! $similarTorrents->has('Seasons')))
                                    open
                                @endif
                            >
                                <summary x-bind="specials">Specials</summary>
                                @foreach ($similarTorrents['Specials'] as $specialName => $special)
                                    <details
                                        class="torrent-search--grouped__dropdown"
                                        @if ($checked || $loop->first)
                                            open
                                        @endif
                                    >
                                        <summary x-bind="special">{{ $specialName }}</summary>
                                        <table class="similar-torrents__torrents">
                                            @foreach ($special as $type => $torrents)
                                                <tbody>
                                                    @foreach ($torrents as $torrent)
                                                        <tr>
                                                            @if ($loop->first)
                                                                <th
                                                                    class="similar-torrents__type"
                                                                    scope="rowgroup"
                                                                    rowspan="{{ $loop->count }}"
                                                                >
                                                                    {{ $type }}
                                                                </th>
                                                            @endif

                                                            @if ($user->group->is_modo)
                                                                <td
                                                                    class="similar-torrents__checkbox"
                                                                    x-on:click.self="$el.firstElementChild.click()"
                                                                >
                                                                    <input
                                                                        type="checkbox"
                                                                        value="{{ $torrent->id }}"
                                                                        wire:model.live="checked"
                                                                    />
                                                                </td>
                                                            @endif

                                                            @include('components.partials._torrent-group-row')
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endforeach
                                        </table>
                                    </details>
                                @endforeach
                            </details>
                        @endif

                        @foreach ($similarTorrents['Seasons'] ?? [] as $seasonName => $season)
                            <details
                                class="torrent-search--grouped__dropdown"
                                @if ($checked || $loop->first)
                                    open
                                @endif
                            >
                                <summary x-bind="season">{{ $seasonName }}</summary>
                                @if ($season->has('Season Pack') && ! $season->has('Episodes'))
                                    <table class="similar-torrents__torrents">
                                        @foreach ($season['Season Pack'] as $type => $torrents)
                                            <tbody>
                                                @foreach ($torrents as $torrent)
                                                    <tr>
                                                        @if ($loop->first)
                                                            <th
                                                                class="similar-torrents__type"
                                                                scope="rowgroup"
                                                                rowspan="{{ $loop->count }}"
                                                            >
                                                                {{ $type }}
                                                            </th>
                                                        @endif

                                                        @if ($user->group->is_modo)
                                                            <td
                                                                class="similar-torrents__checkbox"
                                                                x-on:click.self="$el.firstElementChild.click()"
                                                            >
                                                                <input
                                                                    type="checkbox"
                                                                    value="{{ $torrent->id }}"
                                                                    wire:model.live="checked"
                                                                />
                                                            </td>
                                                        @endif

                                                        @include('components.partials._torrent-group-row')
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        @endforeach
                                    </table>
                                @elseif ($season->has('Season Pack'))
                                    <details open class="torrent-search--grouped__dropdown">
                                        <summary x-bind="pack">Season Pack</summary>
                                        <table class="similar-torrents__torrents">
                                            @foreach ($season['Season Pack'] as $type => $torrents)
                                                <tbody>
                                                    @foreach ($torrents as $torrent)
                                                        <tr>
                                                            @if ($loop->first)
                                                                <th
                                                                    class="similar-torrents__type"
                                                                    scope="rowgroup"
                                                                    rowspan="{{ $loop->count }}"
                                                                >
                                                                    {{ $type }}
                                                                </th>
                                                            @endif

                                                            @if ($user->group->is_modo)
                                                                <td
                                                                    class="similar-torrents__checkbox"
                                                                    x-on:click.self="$el.firstElementChild.click()"
                                                                >
                                                                    <input
                                                                        type="checkbox"
                                                                        value="{{ $torrent->id }}"
                                                                        wire:model.live="checked"
                                                                    />
                                                                </td>
                                                            @endif

                                                            @include('components.partials._torrent-group-row')
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endforeach
                                        </table>
                                    </details>
                                @endif

                                @foreach ($season['Episodes'] ?? [] as $episodeName => $episode)
                                    <details
                                        class="torrent-search--grouped__dropdown"
                                        @if ($checked || ($loop->first && ! $season->has('Season Pack')))
                                            open
                                        @endif
                                    >
                                        <summary x-bind="episode">{{ $episodeName }}</summary>
                                        <table class="similar-torrents__torrents">
                                            @foreach ($episode as $type => $torrents)
                                                <tbody>
                                                    @foreach ($torrents as $torrent)
                                                        <tr>
                                                            @if ($loop->first)
                                                                <th
                                                                    class="similar-torrents__type"
                                                                    scope="rowgroup"
                                                                    rowspan="{{ $loop->count }}"
                                                                >
                                                                    {{ $type }}
                                                                </th>
                                                            @endif

                                                            @if ($user->group->is_modo)
                                                                <td
                                                                    class="similar-torrents__checkbox"
                                                                    x-on:click.self="$el.firstElementChild.click()"
                                                                >
                                                                    <input
                                                                        type="checkbox"
                                                                        value="{{ $torrent->id }}"
                                                                        wire:model.live="checked"
                                                                    />
                                                                </td>
                                                            @endif

                                                            @include('components.partials._torrent-group-row')
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            @endforeach
                                        </table>
                                    </details>
                                @endforeach
                            </details>
                        @endforeach
                    </section>
                @elseif ($category->movie_meta)
                    <table class="similar-torrents__torrents">
                        <thead>
                            <tr class="similar-torrents__headers">
                                <th class="similar-torrents__type-header">
                                    {{ __('torrent.type') }}
                                </th>
                                @if ($user->group->is_modo)
                                    <th
                                        class="similar-torrents__checkbox-header"
                                        title="{{ __('common.select') }}"
                                    >
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectPage"
                                            style="vertical-align: middle"
                                        />
                                    </th>
                                @endif

                                <th class="similar-torrents__name-header">
                                    {{ __('torrent.name') }}
                                </th>
                                <th class="similar-torrents__actions-header" colspan="3">
                                    {{ __('common.actions') }}
                                </th>
                                <th class="similar-torrents__size-header">
                                    {{ __('torrent.size') }}
                                </th>
                                <th
                                    class="similar-torrents__seeders-header"
                                    title="{{ __('torrent.seeders') }}"
                                >
                                    <i class="fas fa-arrow-alt-circle-up"></i>
                                </th>
                                <th
                                    class="similar-torrents__leechers-header"
                                    title="{{ __('torrent.leechers') }}"
                                >
                                    <i class="fas fa-arrow-alt-circle-down"></i>
                                </th>
                                <th
                                    class="similar-torrents__completed-header"
                                    title="{{ __('torrent.completed') }}"
                                >
                                    <i class="fas fa-check-circle"></i>
                                </th>
                                <th class="similar-torrents__age-header">
                                    {{ __('torrent.age') }}
                                </th>
                            </tr>
                        </thead>
                        @foreach ($similarTorrents as $type => $torrents)
                            <tbody>
                                @foreach ($torrents as $torrent)
                                    <tr>
                                        @if ($loop->first)
                                            <th
                                                class="similar-torrents__type"
                                                scope="rowgroup"
                                                rowspan="{{ $loop->count }}"
                                            >
                                                {{ $type }}
                                            </th>
                                        @endif

                                        @if ($user->group->is_modo)
                                            <td
                                                class="similar-torrents__checkbox"
                                                x-on:click.self="$el.firstElementChild.click()"
                                            >
                                                <input
                                                    type="checkbox"
                                                    value="{{ $torrent->id }}"
                                                    wire:model.live="checked"
                                                />
                                            </td>
                                        @endif

                                        @include('components.partials._torrent-group-row')
                                    </tr>
                                @endforeach
                            </tbody>
                        @endforeach
                    </table>
                @elseif ($category->game_meta)
                    @foreach ($similarTorrents->sortBy('type.position')->values()->groupBy('type.name') as $type => $torrents)
                        <section class="panelV2" x-data>
                            <h2 class="panel__heading">{{ $type }}</h2>
                            <div class="data-table-wrapper">
                                <table class="data-table">
                                    @foreach ($torrents->sortBy('resolution.position')->values()->groupBy('resolution.name') as $resolution => $torrents)
                                        <tbody>
                                            <tr>
                                                <th colspan="100">{{ $resolution }}</th>
                                            </tr>
                                            @foreach ($torrents as $torrent)
                                                @if ($user->group->is_modo)
                                                    <tr>
                                                        <td
                                                            colspan="0"
                                                            rowspan="2"
                                                            x-on:click.self="$el.firstElementChild.click()"
                                                        >
                                                            <input
                                                                type="checkbox"
                                                                value="{{ $torrent->id }}"
                                                                wire:model.live="checked"
                                                            />
                                                        </td>
                                                    </tr>
                                                @endif

                                                <x-torrent.row
                                                    :torrent="$torrent"
                                                    :meta="$work"
                                                    :personal_freeleech="$personalFreeleech"
                                                />
                                            @endforeach
                                        </tbody>
                                    @endforeach
                                </table>
                            </div>
                        </section>
                    @endforeach
                @endif
            </div>
        </section>

        <section class="panelV2">
            <header style="cursor: pointer" class="panel__header">
                <h2 class="panel__heading">
                    {{ __('request.requests') }}
                </h2>
                <div class="panel__actions">
                    <div class="panel__action">
                        <label class="form__label">
                            Hide Filled Requests
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                wire:model.live="hideFilledRequests"
                            />
                        </label>
                    </div>
                    <div class="panel__action">
                        <a
                            href="{{
                                route('requests.create', [
                                    'category_id' => $category->id,
                                    'title' => rawurlencode(
                                        $category->movie_meta
                                            ? ($work?->title ?? '') . ' ' . substr($work->release_date ?? '', 0, 4)
                                            : ($work?->name ?? '') . ' ' . substr($work->first_air_date ?? '', 0, 4)
                                    ),
                                    'imdb' => $work?->imdb_id ?? '',
                                    'tmdb' => $tmdbId ?? '',
                                    'tvdb' => $work->tvdb_id ?? '',
                                    'igdb' => $igdb ?? '',
                                ])
                            }}"
                            class="form__button form__button--text"
                        >
                            {{ __('request.add-request') }}
                        </a>
                    </div>
                </div>
            </header>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <tbody>
                        @forelse ($torrentRequests as $torrentRequest)
                            <tr>
                                <td>
                                    <a
                                        href="{{ route('requests.show', ['torrentRequest' => $torrentRequest]) }}"
                                    >
                                        {{ $torrentRequest->name }}
                                    </a>
                                </td>
                                <td>{{ $torrentRequest->category->name }}</td>
                                <td>{{ $torrentRequest->type->name }}</td>
                                <td>{{ $torrentRequest->resolution->name ?? 'Unknown' }}</td>
                                <td>
                                    <x-user_tag
                                        :user="$torrentRequest->user"
                                        :anon="$torrentRequest->anon"
                                    />
                                </td>
                                <td>{{ $torrentRequest->votes }}</td>
                                <td>{{ $torrentRequest->comments_count }}</td>
                                <td>{{ number_format($torrentRequest->bounty) }}</td>
                                <td>
                                    <time
                                        datetime="{{ $torrentRequest->created_at }}"
                                        title="{{ $torrentRequest->created_at }}"
                                    >
                                        {{ $torrentRequest->created_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td>
                                    @switch(true)
                                        @case($torrentRequest->claimed && $torrentRequest->torrent_id === null)
                                            <i class="fas fa-circle text-blue"></i>
                                            {{ __('request.claimed') }}

                                            @break
                                        @case($torrentRequest->torrent_id !== null && $torrentRequest->approved_by === null)
                                            <i class="fas fa-circle text-purple"></i>
                                            {{ __('request.pending') }}

                                            @break
                                        @case($torrentRequest->torrent_id === null)
                                            <i class="fas fa-circle text-red"></i>
                                            {{ __('request.unfilled') }}

                                            @break
                                        @default
                                            <i class="fas fa-circle text-green"></i>
                                            {{ __('request.filled') }}

                                            @break
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">{{ __('common.no-result') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

@section('javascripts')
    @if ($user->group->is_modo)
        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            window.addEventListener('swal:modal', event => {
              Swal.fire({
                title: event.detail.message,
                text: event.detail.text,
                icon: event.detail.type,
              })
            })

            window.addEventListener('swal:confirm', event => {
              const { value: text } = Swal.fire({
                input: 'textarea',
                inputLabel: 'Delete Reason',
                inputPlaceholder: 'Type your reason here...',
                inputAttributes: {
                  'aria-label': 'Type your reason here'
                },
                inputValidator: (value) => {
                  if (!value) {
                    return 'You need to write something!'
                  }
                },
                title: event.detail.message,
                html: event.detail.body,
                icon: event.detail.type,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
              }).then((result) => {
                if (result.isConfirmed) {
                @this.set('reason', result.value);
                  Livewire.dispatch('destroy')
                }
              })
            })
        </script>
    @endif
@endsection
