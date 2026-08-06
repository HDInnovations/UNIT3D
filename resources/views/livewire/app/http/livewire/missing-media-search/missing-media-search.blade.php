<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">Missing media</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        wire:model.live="name"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('torrent.title') }}
                    </label>
                </div>
            </div>
            <div class="panel__action">
                <div class="form__group">
                    <input
                        type="text"
                        name="year"
                        id="year"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        wire:model.live="year"
                        placeholder=" "
                    />
                    <label class="form__label form__label--floating" for="year">
                        {{ __('common.year') }}
                    </label>
                </div>
            </div>
        </div>
    </header>
    <table class="data-table" id="missing-media-table">
        <thead>
            <tr>
                <th wire:click="sortBy('title')" role="columnheader button">
                    {{ __('torrent.title') }}
                    @include('livewire.includes._sort-icon', ['field' => 'title'])
                </th>
                <th wire:click="sortBy('requests_count')" role="columnheader button">
                    {{ __('request.requests') }}
                    @include('livewire.includes._sort-icon', ['field' => 'requests_count'])
                </th>
                @foreach ($types as $type)
                    <th>{{ $type->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($medias as $media)
                <tr>
                    <td>
                        @if ($media->torrents_min_category_id === null)
                            {{ $media->title }} ({{ $media->release_date?->format('Y') }})
                        @else
                            <a
                                href="{{ route('torrents.similar', ['category_id' => $media->torrents_min_category_id, 'tmdb' => $media->id]) }}"
                            >
                                {{ $media->title }} ({{ $media->release_date?->format('Y') }})
                            </a>
                        @endif
                    </td>
                    <td>
                        <a
                            href="{{ route('requests.index', ['categories' => [1], 'tmdbId' => $media->id, 'unfilled' => 1]) }}"
                        >
                            {{ $media->requests_count }}
                        </a>
                    </td>
                    @foreach ($types as $type)
                        @if ($media->torrents->where('type_id', '=', $type->id)->isEmpty())
                            <td
                                style="
                                    color: #f05555 !important;
                                    background: rgba(107, 6, 6, 0.58) !important;
                                    font-weight: bold;
                                "
                            >
                                Missing
                            </td>
                        @else
                            <td
                                style="
                                    color: #55b160 !important;
                                    background: rgba(1, 70, 10, 0.53) !important;
                                    font-weight: bold;
                                "
                            >
                                {{ $media->torrents->where('type_id', '=', $type->id)->implode('resolution.name', ' | ') }}
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $medias->links('partials.pagination') }}
</section>
