<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('mediahub.networks') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <div class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        placeholder=" "
                        type="search"
                        autocomplete="off"
                        wire:model.live.debounce.250ms="search"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('torrent.search-by-name') }}
                    </label>
                </div>
            </div>
        </div>
    </header>
    {{ $networks->links('partials.pagination') }}
    <div class="panel__body">
        <ul class="mediahub-card__list">
            @forelse ($networks as $network)
                <li class="mediahub-card__list-item">
                    <a
                        href="{{ route('torrents.index', ['view' => 'group', 'networkId' => $network->id]) }}"
                        class="mediahub-card"
                    >
                        <h2 class="mediahub-card__heading">
                            @isset($network->logo)
                                <img
                                    class="mediahub-card__image"
                                    src="{{ tmdb_image('logo_mid', $network->logo) }}"
                                    alt="{{ $network->name }}"
                                />
                            @else
                                {{ $network->name }}
                            @endisset
                        </h2>
                        <h3 class="mediahub-card__subheading">
                            <i class="{{ config('other.font-awesome') }} fa-tv-retro"></i>
                            {{ $network->tv_count }} shows
                        </h3>
                    </a>
                </li>
            @empty
                No {{ __('mediahub.networks') }}
            @endforelse
        </ul>
    </div>
    {{ $networks->links('partials.pagination') }}
</section>
