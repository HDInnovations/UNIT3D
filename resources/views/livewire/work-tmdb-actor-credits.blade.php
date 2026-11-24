<section class="meta__chip-container">
    <h2 class="meta__heading">Cast</h2>
    @island('actors')
        @foreach ($this->credits ?? [] as $credit)
            <article class="meta-chip-wrapper">
                <a
                    href="{{ route('mediahub.persons.show', ['id' => $credit->person->id, 'occupationId' => $credit->occupation_id]) }}"
                    class="meta-chip"
                >
                    @if ($credit->person->still)
                        <img
                            class="meta-chip__image"
                            src="{{ tmdb_image('cast_face', $credit->person->still) }}"
                            alt=""
                            loading="lazy"
                        />
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-user meta-chip__icon"></i>
                    @endif
                    <h2 class="meta-chip__name">{{ $credit->person->name }}</h2>
                    <h3 class="meta-chip__value">{{ $credit->character }}</h3>
                </a>
            </article>
        @endforeach
    @endisland

    <div wire:intersect.margin.200px="loadMore" wire:island.append="actors"></div>
</section>
