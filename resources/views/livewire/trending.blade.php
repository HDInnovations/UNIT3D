<section
    @class([
        'panelV2',
        'trending',
        'trending--weekly' => in_array($this->interval, ['weekly', 'monthly']),
    ])
>
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('common.trending') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <select
                        id="interval"
                        class="form__select"
                        type="date"
                        name="interval"
                        wire:model.live="interval"
                    >
                        <option value="day">{{ __('livewire-interface.past-day') }}</option>
                        <option value="week">{{ __('livewire-interface.past-week') }}</option>
                        <option value="month">{{ __('livewire-interface.past-month') }}</option>
                        <option value="year">{{ __('livewire-interface.past-year') }}</option>
                        <option value="all">{{ __('livewire-interface.all-time') }}</option>
                        <option value="weekly">{{ __('livewire-interface.weekly') }}</option>
                        <option value="monthly">{{ __('livewire-interface.monthly') }}</option>
                        <option value="release_year">{{ __('livewire-interface.release-year') }}</option>
                        <option value="custom">{{ __('livewire-interface.custom') }}</option>
                    </select>
                    <label class="form__label form__label--floating" for="interval">
                        {{ __('livewire-interface.interval') }}
                    </label>
                </div>
            </div>
            @if ($this->interval === 'custom')
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="from"
                            class="form__text"
                            name="from"
                            type="date"
                            wire:model.live="from"
                        />
                        <label class="form__label form__label--floating" for="from">
                            {{ __('livewire-interface.from') }}
                        </label>
                    </div>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <input
                            id="until"
                            class="form__text"
                            name="until"
                            type="date"
                            wire:model.live="until"
                        />
                        <label class="form__label form__label--floating" for="until">
                            {{ __('livewire-interface.until') }}
                        </label>
                    </div>
                </div>
            @endif

            <div class="panel__action">
                <div class="form__group">
                    <select
                        id="metaType"
                        class="form__select"
                        name="metaType"
                        wire:model.live="metaType"
                    >
                        @foreach ($metaTypes as $name => $type)
                            <option value="{{ $type }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="metaType">
                        {{ __('torrent.category') }}
                    </label>
                </div>
            </div>
        </div>
    </header>
    @if ($this->interval === 'weekly')
        <div class="data-table-wrapper">
            <div wire:loading.delay class="panel__body">{{ __('livewire-interface.computing') }}</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('livewire-interface.week') }}</th>
                        <th>{{ __('livewire-interface.rankings') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($works as $weeklyRankings)
                        <tr>
                            <th>
                                {{ $weeklyRankings->first()?->week_start?->format('Y-m-d') }}
                            </th>
                            <td class="panel__body trending-weekly__row">
                                @foreach ($weeklyRankings as $ranking)
                                    <figure class="trending-poster">
                                        @switch($this->metaType)
                                            @case('movie_meta')
                                                <x-movie.poster
                                                    :movie="$ranking->movie"
                                                    :categoryId="$ranking->category_id"
                                                    :tmdb="$ranking->tmdb_movie_id"
                                                />

                                                @break
                                            @case('tv_meta')
                                                <x-tv.poster
                                                    :tv="$ranking->tv"
                                                    :categoryId="$ranking->category_id"
                                                    :tmdb="$ranking->tmdb_tv_id"
                                                />

                                                @break
                                        @endswitch
                                        <figcaption
                                            class="trending-poster__download-count"
                                            title="{{ __('torrent.completed-times') }}"
                                        >
                                            {{ $ranking->download_count }}
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($this->interval === 'monthly')
        <div class="data-table-wrapper">
            <div wire:loading.delay class="panel__body">{{ __('livewire-interface.computing') }}</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.month') }}</th>
                        <th>{{ __('livewire-interface.rankings') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($works as $monthlyRankings)
                        <tr>
                            <th>
                                {{ substr($monthlyRankings->first()?->the_year_month, 0, 4) }}-{{ substr($monthlyRankings->first()?->the_year_month, 4) }}
                            </th>
                            <td class="panel__body trending-weekly__row">
                                @foreach ($monthlyRankings as $ranking)
                                    <figure class="trending-poster">
                                        @switch($this->metaType)
                                            @case('movie_meta')
                                                <x-movie.poster
                                                    :movie="$ranking->movie"
                                                    :categoryId="$ranking->category_id"
                                                    :tmdb="$ranking->tmdb_movie_id"
                                                />

                                                @break
                                            @case('tv_meta')
                                                <x-tv.poster
                                                    :tv="$ranking->tv"
                                                    :categoryId="$ranking->category_id"
                                                    :tmdb="$ranking->tmdb_tv_id"
                                                />

                                                @break
                                        @endswitch
                                        <figcaption
                                            class="trending-poster__download-count"
                                            title="{{ __('torrent.completed-times') }}"
                                        >
                                            {{ $ranking->download_count }}
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($this->interval === 'release_year')
        <div class="data-table-wrapper">
            <div wire:loading.delay class="panel__body">{{ __('livewire-interface.computing') }}</div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.year') }}</th>
                        <th>{{ __('livewire-interface.rankings') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($works as $releaseYearRankings)
                        <tr>
                            <th>{{ $releaseYearRankings->first()?->the_year }}</th>
                            <td class="panel__body trending-weekly__row">
                                @foreach ($releaseYearRankings as $ranking)
                                    <figure class="trending-poster">
                                        @switch($this->metaType)
                                            @case('movie_meta')
                                                <x-movie.poster
                                                    :movie="$ranking->movie"
                                                    :categoryId="$ranking->category_id"
                                                    :tmdb="$ranking->tmdb_movie_id"
                                                />

                                                @break
                                            @case('tv_meta')
                                                <x-tv.poster
                                                    :tv="$ranking->tv"
                                                    :categoryId="$ranking->category_id"
                                                    :tmdb="$ranking->tmdb_tv_id"
                                                />

                                                @break
                                        @endswitch
                                        <figcaption
                                            class="trending-poster__download-count"
                                            title="{{ __('torrent.completed-times') }}"
                                        >
                                            {{ $ranking->download_count }}
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="panel__body torrent-search--poster__results">
            <div wire:loading.delay>{{ __('livewire-interface.computing') }}</div>

            @switch($this->metaType)
                @case('movie_meta')
                    @foreach ($works as $work)
                        <figure class="trending-poster">
                            <x-movie.poster
                                :movie="$work->movie"
                                :categoryId="$work->category_id"
                                :tmdb="$work->tmdb_movie_id"
                            />
                            <figcaption
                                class="trending-poster__download-count"
                                title="{{ __('torrent.completed-times') }}"
                            >
                                {{ $work->download_count }}
                            </figcaption>
                        </figure>
                    @endforeach

                    @break
                @case('tv_meta')
                    @foreach ($works as $work)
                        <figure class="trending-poster">
                            <x-tv.poster
                                :tv="$work->tv"
                                :categoryId="$work->category_id"
                                :tmdb="$work->tmdb_tv_id"
                            />
                            <figcaption
                                class="trending-poster__download-count"
                                title="{{ __('torrent.completed-times') }}"
                            >
                                {{ $work->download_count }}
                            </figcaption>
                        </figure>
                    @endforeach

                    @break
            @endswitch
        </div>
    @endif
</section>
