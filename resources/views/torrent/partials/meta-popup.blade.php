<div x-cloak x-show="metaPopup" class="meta__poster-popup">
    <div class="meta__poster-popup-card">
        <div class="meta__poster-popup-backdrop">
            <img
                src="{{ isset($meta->backdrop) ? tmdb_image('back_mid', $meta->backdrop) : (isset($meta->poster) ? tmdb_image('poster_mid', $meta->poster) : 'https://via.placeholder.com/500x280') }}"
                alt="{{ $meta->title ?? ($meta->name ?? 'No Title') }}"
            />
            <div class="meta__poster-popup-backdrop-overlay"></div>
        </div>
        <div class="meta__poster-popup-content">
            <div class="meta__poster-popup-header">
                <h3 class="meta__poster-popup-title">
                    {{ $meta->title ?? ($meta->name ?? 'No Meta Found') }}
                    <span class="meta__poster-popup-year">
                        ({{ substr($meta->release_date ?? ($meta->first_air_date ?? ''), 0, 4) ?? 'Unknown' }})
                    </span>
                </h3>
            </div>
            <p class="meta__poster-popup-overview">
                {{ $meta?->overview ?? 'No overview available.' }}
            </p>

            <div class="meta__poster-popup-details">
                @if ($meta?->vote_average)
                    <div class="meta__poster-popup-detail">
                        <span class="detail-label">Rating</span>
                        <span class="detail-value">
                            {{ round($meta?->vote_average ?? 0, 1) }}/10
                            ({{ $meta?->vote_count ?? 0 }} votes)
                        </span>
                    </div>
                @endif

                @if ($meta?->runtime || $meta?->episode_run_time)
                    <div class="meta__poster-popup-detail">
                        <span class="detail-label">Runtime</span>
                        <span class="detail-value">
                            {{ \Carbon\CarbonInterval::minutes($meta->runtime ?? ($meta->episode_run_time ?? 0))->cascade()->forHumans(null, true) }}
                        </span>
                    </div>
                @endif

                @if ($meta?->genres?->isNotEmpty())
                    <div class="meta__poster-popup-detail">
                        <span class="detail-label">Genres</span>
                        <span class="detail-value">
                            {{ $meta->genres->pluck('name')->join(', ') }}
                        </span>
                    </div>
                @endif

                @if ($meta instanceof \App\Models\TmdbMovie && $meta->directors->isNotEmpty())
                    <div class="meta__poster-popup-detail">
                        <span class="detail-label">Directors</span>
                        <span class="detail-value">
                            {{ $meta->directors->pluck('name')->join(', ') }}
                        </span>
                    </div>
                @endif

                @if ($meta instanceof \App\Models\TmdbTv && $meta->creators->isNotEmpty())
                    <div class="meta__poster-popup-detail">
                        <span class="detail-label">Creators</span>
                        <span class="detail-value">
                            {{ $meta->creators->pluck('name')->join(', ') }}
                        </span>
                    </div>
                @endif

                @if ($meta?->actors?->isNotEmpty())
                    <div class="meta__poster-popup-detail">
                        <span class="detail-label">Actors</span>
                        <span class="detail-value">
                            {{ $meta->actors->pluck('name')->join(', ') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
