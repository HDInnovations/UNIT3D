<section class="panelV2 blocks__random-media">
    <header class="panel__header">
        <h2 class="panel__heading">
            <i class="{{ config('other.font-awesome') }} fa-user-astronaut"></i>
            Random media
        </h2>
        <div class="panel__actions">
            <div class="panel__action">
                <button class="form__button form__button--text" wire:click="$refresh">
                    {{ __('pm.refresh') }}
                    <i class="{{ config('other.font-awesome') }} fa-redo"></i>
                </button>
            </div>
        </div>
    </header>
    <div class="panel__body" style="padding: 0">
        <div class="random__media">
            @foreach ($movies as $key => $movie)
                <a
                    target="_blank"
                    href="{{ route('torrents.similar', ['category_id' => $movie->torrents_min_category_id ?? 1, 'tmdb' => $movie->id]) }}"
                    class="@if ($loop->iteration == 1) media @elseif ($loop->iteration == 2)media1 @else media2 @endif"
                    style="
                        background-image: url('{{ \tmdb_image('back_small', $movie->backdrop) }}');
                        background-repeat: no-repeat;
                        background-position: center center;
                        background-size: cover;
                    "
                >
                    <span style="padding-left: 6px">MOVIE</span>
                    <div class="media__title">
                        {{ $movie->title }} ({{ $movie->release_date?->format('Y') }})
                    </div>
                </a>
            @endforeach

            @foreach ($tvs as $key => $tv)
                <a
                    target="_blank"
                    href="{{ route('torrents.similar', ['category_id' => $tv->torrents_min_category_id ?? 2, 'tmdb' => $tv->id]) }}"
                    class="@if ($loop->iteration == 1) media3 @elseif ($loop->iteration == 2) media4 @else media5 @endif"
                    style="
                        background-image: url('{{ \tmdb_image('back_small', $tv->backdrop) }}');
                        background-repeat: no-repeat;
                        background-position: center center;
                        background-size: cover;
                    "
                >
                    <span style="padding-left: 6px">TV</span>
                    <div class="media__title">
                        {{ $tv->name }} ({{ $tv->first_air_date?->format('Y') }})
                    </div>
                </a>
            @endforeach

            @foreach ($movies2 as $key => $movie)
                <a
                    target="_blank"
                    href="{{ route('torrents.similar', ['category_id' => $movie->torrents_min_category_id ?? 1, 'tmdb' => $movie->id]) }}"
                    class="@if ($loop->iteration == 1) media6 @elseif ($loop->iteration == 2) media7 @else media8 @endif"
                    style="
                        background-image: url('{{ \tmdb_image('back_small', $movie->backdrop) }}');
                        background-repeat: no-repeat;
                        background-position: center center;
                        background-size: cover;
                    "
                >
                    <span style="padding-left: 6px">MOVIE</span>
                    <div class="media__title">
                        {{ $movie->title }} ({{ $movie->release_date?->format('Y') }})
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
