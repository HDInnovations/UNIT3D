<div class="page__requests request-search__component">
    <search class="compact-search request-search__filters" x-data="toggle">
        <div class="compact-search__visible-default">
            <p class="form__group">
                <input
                    id="name"
                    wire:model.live="name"
                    type="search"
                    autocomplete="off"
                    class="form__text"
                    placeholder=" "
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
            <div class="form__group--horizontal">
                <p class="form__group">
                    <input
                        id="requester"
                        wire:model.live="requestor"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="requester">
                        {{ __('common.author') }}
                    </label>
                </p>
            </div>
            <div class="form__group--short-horizontal">
                <p class="form__group">
                    <input
                        id="tmdbId"
                        wire:model.live="tmdbId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="tmdbId">TMDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="imdbId"
                        wire:model.live="imdbId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        pattern="[0-9]+|tt0*\d{7,}"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="imdbId">IMDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="tvdbId"
                        wire:model.live="tvdbId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="tvdbId">TVDb ID</label>
                </p>
                <p class="form__group">
                    <input
                        id="malId"
                        wire:model.live="malId"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="malId">MAL ID</label>
                </p>
            </div>
            <div class="form__group--short-horizontal">
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('torrent.category') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($categories as $category)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $category->id }}"
                                            wire:model.live="categoryIds"
                                        />
                                        {{ $category->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('common.type') }}</legend>
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
                        <legend class="form__legend">{{ __('common.resolution') }}</legend>
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
                        <legend class="form__legend">{{ __('torrent.genre') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($genres as $genre)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $genre->id }}"
                                            wire:model.live="genreIds"
                                        />
                                        {{ $genre->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Primary language</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($primaryLanguages as $primaryLanguage)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            value="{{ $primaryLanguage }}"
                                            wire:model.live="primaryLanguageNames"
                                        />
                                        {{ $primaryLanguage }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('common.status') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="unfilled"
                                    />
                                    {{ __('request.unfilled') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="claimed"
                                    />
                                    {{ __('request.claimed') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="pending"
                                    />
                                    {{ __('request.pending') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="filled"
                                    />
                                    {{ __('request.filled') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">{{ __('common.extra') }}</legend>
                        <div class="form__fieldset-checkbox-container">
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="myRequests"
                                    />
                                    {{ __('request.my-requests') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="myClaims"
                                    />
                                    {{ __('request.my-claims') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="myVoted"
                                    />
                                    {{ __('request.my-voted') }}
                                </label>
                            </p>
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        value="1"
                                        wire:model.live="myFilled"
                                    />
                                    {{ __('request.my-filled') }}
                                </label>
                            </p>
                        </div>
                    </fieldset>
                </div>
            </div>
        </form>
    </search>
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('request.requests') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        href="{{ route('requests.create') }}"
                        class="form__button form__button--text"
                    >
                        {{ __('request.add-request') }}
                    </a>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" role="columnheader button">
                            {{ __('common.name') }}
                            @include('livewire.includes._sort-icon', ['field' => 'name'])
                        </th>
                        <th wire:click="sortBy('category_id')" role="columnheader button">
                            {{ __('common.category') }}
                            @include('livewire.includes._sort-icon', ['field' => 'category_id'])
                        </th>
                        <th wire:click="sortBy('type_id')" role="columnheader button">
                            {{ __('common.type') }}
                            @include('livewire.includes._sort-icon', ['field' => 'type_id'])
                        </th>
                        <th wire:click="sortBy('resolution_id')" role="columnheader button">
                            {{ __('common.resolution') }}
                            @include('livewire.includes._sort-icon', ['field' => 'resolution_id'])
                        </th>
                        <th wire:click="sortBy('user_id')" role="columnheader button">
                            {{ __('common.author') }}
                            @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                        </th>
                        <th wire:click="sortBy('bounties_count')" role="columnheader button">
                            <i class="{{ config('other.font-awesome') }} fa-thumbs-up"></i>
                            @include('livewire.includes._sort-icon', ['field' => 'bounties_count'])
                        </th>
                        <th>
                            <i class="{{ config('other.font-awesome') }} fa-comment-alt-lines"></i>
                        </th>
                        <th wire:click="sortBy('bounty')" role="columnheader button">
                            <i class="{{ config('other.font-awesome') }} fa-coins"></i>
                            @include('livewire.includes._sort-icon', ['field' => 'bounty'])
                        </th>
                        <th wire:click="sortBy('last_bountied_at')" role="columnheader button">
                            {{ __('common.created_at') }}
                            @include('livewire.includes._sort-icon', ['field' => 'last_bountied_at'])
                        </th>
                        <th>{{ __('common.status') }}</th>
                    </tr>
                </thead>
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
                            <td>{{ $torrentRequest->type->name ?? 'Any' }}</td>
                            <td>{{ $torrentRequest->resolution->name ?? 'Any' }}</td>
                            <td>
                                <x-user-tag
                                    :user="$torrentRequest->user"
                                    :anon="$torrentRequest->anon"
                                />
                            </td>
                            <td>{{ $torrentRequest->bounties_count }}</td>
                            <td>{{ $torrentRequest->comments_count }}</td>
                            <td>{{ number_format($torrentRequest->bounty) }}</td>
                            <td>
                                <time
                                    datetime="{{ $torrentRequest->last_bountied_at }}"
                                    title="{{ $torrentRequest->last_bountied_at }}"
                                >
                                    {{ $torrentRequest->last_bountied_at->diffForHumans() }}
                                </time>
                            </td>
                            <td>
                                @switch(true)
                                    @case($torrentRequest->claim_exists && $torrentRequest->torrent_id === null)
                                        <i class="fas fa-circle text-blue"></i>
                                        {{ __('request.claimed') }}

                                        @break
                                    @case($torrentRequest->torrent_id !== null && $torrentRequest->approved_when === null)
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
            {{ $torrentRequests->links('partials.pagination') }}
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.stats') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('request.requests') }}:</dt>
                <dd>{{ number_format($torrentRequestStat->total) }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.filled') }}:</dt>
                <dd>{{ number_format($torrentRequestStat->filled) }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.unfilled') }}:</dt>
                <dd>{{ number_format($torrentRequestStat->unfilled) }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.total-bounty') }}:</dt>
                <dd>{{ number_format($torrentRequestBountyStat->total) }} {{ __('bon.bon') }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.bounty-claimed') }}:</dt>
                <dd>
                    {{ number_format($torrentRequestBountyStat->claimed) }}
                    {{ __('bon.bon') }}
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('request.bounty-unclaimed') }}:</dt>
                <dd>
                    {{ number_format($torrentRequestBountyStat->unclaimed) }}
                    {{ __('bon.bon') }}
                </dd>
            </div>
        </dl>
    </section>
</div>
