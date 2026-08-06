<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('mediahub.companies') }}</h2>
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
    {{ $companies->links('partials.pagination') }}
    <div class="panel__body">
        <ul class="mediahub-card__list">
            @forelse ($companies as $company)
                <li class="mediahub-card__list-item">
                    <a
                        href="{{ route('torrents.index', ['view' => 'group', 'companyId' => $company->id]) }}"
                        class="mediahub-card"
                    >
                        <h2 class="mediahub-card__heading">
                            @isset($company->logo)
                                <img
                                    class="mediahub-card__image"
                                    src="{{ tmdb_image('logo_mid', $company->logo) }}"
                                    alt="{{ $company->name }}"
                                />
                            @else
                                {{ $company->name }}
                            @endisset
                        </h2>
                        <h3 class="mediahub-card__subheading">
                            <i class="{{ config('other.font-awesome') }} fa-tv-retro"></i>
                            {{ $company->tv_count }} shows | {{ $company->movie_count }} movies
                        </h3>
                    </a>
                </li>
            @empty
                No {{ __('mediahub.companies') }}
            @endforelse
        </ul>
    </div>
    {{ $companies->links('partials.pagination') }}
</section>
