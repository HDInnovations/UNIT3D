<article class="sidebar2">
    <div>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('mediahub.persons') }}</h2>
            {{ $persons->links('partials.pagination') }}
            <div
                class="panel__body"
                style="
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                    gap: 2rem;
                "
            >
                @forelse ($persons as $person)
                    <figure style="display: flex; flex-direction: column; align-items: center">
                        <a href="{{ route('mediahub.persons.show', ['id' => $person->id]) }}">
                            @if ($person->still === null)
                                <div class="person--no-still">
                                    {{ $person->name[0] ?? '' }}{{ str($person->name)->explode(' ')->last()[0] ?? '' }}
                                </div>
                            @else
                                <img
                                    alt="{{ $person->name }}"
                                    src="{{ tmdb_image('cast_mid', $person->still) }}"
                                    class="person--still"
                                />
                            @endif
                        </a>
                        <figcaption>{{ $person->name }}</figcaption>
                    </figure>
                @empty
                    No persons.
                @endforelse
            </div>
            {{ $persons->links('partials.pagination') }}
        </section>
    </div>
    <aside>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.filters') }}</h2>
            <div class="panel__body">
                <form class="form" x-data x-on:submit.prevent>
                    <p class="form__group">
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
                    </p>
                    <p class="form__group">
                        <select
                            id="firstCharacter"
                            class="form__select"
                            wire:model.live="firstCharacter"
                            x-data="{ firstCharacter: '' }"
                            x-model="firstCharacter"
                            x-bind:class="firstCharacter === '' ? 'form__select--default' : ''"
                        >
                            <option hidden disabled selected value=""></option>
                            @foreach ($firstCharacters as $firstCharacter)
                                <option class="form__option" value="{{ $firstCharacter->alpha }}">
                                    {{ $firstCharacter->alpha }}
                                    ({{ $firstCharacter->count }})
                                </option>
                            @endforeach
                        </select>
                        <label for="firstCharacter" class="form__label form__label--floating">
                            Starts with
                        </label>
                    </p>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('torrent.category') }}</legend>
                            <div class="form__fieldset-checkbox-container">
                                @foreach (App\Models\Occupation::query()->select(['id', 'name'])->orderBy('position')->get() as $occupation)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                class="form__checkbox"
                                                type="checkbox"
                                                value="{{ $occupation->id }}"
                                                wire:model.live="occupationIds"
                                            />
                                            {{ $occupation->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </section>
    </aside>
</article>
