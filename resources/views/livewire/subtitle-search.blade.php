<article class="sidebar2 sidebar--inverse">
    <div>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('common.subtitles') }}</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="category__icon"></th>
                            <th wire:click="sortBy('title')" role="columnheader button">
                                {{ __('torrent.torrent') }}
                                @include('livewire.includes._sort-icon', ['field' => 'title'])
                            </th>
                            <th wire:click="sortBy('language_id')" role="columnheader button">
                                {{ __('common.language') }}
                                @include('livewire.includes._sort-icon', ['field' => 'language_id'])
                            </th>
                            <th wire:click="sortBy('extension')" role="columnheader button">
                                {{ __('subtitle.extension') }}
                                @include('livewire.includes._sort-icon', ['field' => 'extension'])
                            </th>
                            <th wire:click="sortBy('file_size')" role="columnheader button">
                                {{ __('subtitle.size') }}
                                @include('livewire.includes._sort-icon', ['field' => 'file_size'])
                            </th>
                            <th wire:click="sortBy('downloads')" role="columnheader button">
                                {{ __('subtitle.downloads') }}
                                @include('livewire.includes._sort-icon', ['field' => 'downloads'])
                            </th>
                            <th wire:click="sortBy('created_at')" role="columnheader button">
                                {{ __('subtitle.uploaded') }}
                                @include('livewire.includes._sort-icon', ['field' => 'created_at'])
                            </th>
                            <th wire:click="sortBy('user_id')" role="columnheader button">
                                {{ __('subtitle.uploader') }}
                                @include('livewire.includes._sort-icon', ['field' => 'user_id'])
                            </th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subtitles as $subtitle)
                            <tr>
                                <td>
                                    @if ($subtitle->torrent->category->image === null)
                                        <i
                                            class="{{ $subtitle->torrent->category->icon }} category__icon"
                                            title="{{ $subtitle->torrent->category->name }} {{ strtolower(__('torrent.torrent')) }}"
                                        ></i>
                                    @else
                                        <img
                                            src="{{ route('authenticated_images.category_image', ['category' => $subtitle->torrent->category]) }}"
                                            title="{{ $subtitle->torrent->category->name }} {{ strtolower(__('torrent.torrent')) }}"
                                            alt="{{ $subtitle->torrent->category->name }}"
                                        />
                                    @endif
                                </td>
                                <td>
                                    <a
                                        href="{{ route('torrents.show', ['id' => $subtitle->torrent->id]) }}"
                                    >
                                        {{ $subtitle->torrent->name }}
                                    </a>
                                </td>
                                <td>
                                    {{ $subtitle->language->name }}
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-closed-captioning"
                                        title="{{ $subtitle->note }}"
                                    ></i>
                                </td>
                                <td>{{ $subtitle->extension }}</td>
                                <td>{{ $subtitle->getSize() }}</td>
                                <td>{{ $subtitle->downloads }}</td>
                                <td>
                                    <time
                                        datetime="{{ $subtitle->created_at }}"
                                        title="{{ $subtitle->created_at }}"
                                    >
                                        {{ $subtitle->created_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td>
                                    <x-user-tag :user="$subtitle->user" :anon="$subtitle->anon" />
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <a
                                                class="form__button form__button--text"
                                                href="{{ route('subtitles.download', ['subtitle' => $subtitle]) }}"
                                            >
                                                {{ __('common.download') }}
                                            </a>
                                        </li>
                                    </menu>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $subtitles->links('partials.pagination') }}
        </section>
    </div>
    <aside>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.filters') }}</h2>
            <div class="panel__body">
                <form class="form">
                    <p class="form__group">
                        <input
                            id="search"
                            wire:model.live="search"
                            type="search"
                            class="form__text"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label for="search" class="form__label form__label--floating">
                            {{ __('torrent.name') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="language_id"
                            class="form__select"
                            wire:model.live="language"
                            x-data="{ language: '' }"
                            x-model="language"
                            x-bind:class="language === '' ? 'form__select--default' : ''"
                        >
                            <option hidden disabled selected value=""></option>
                            @foreach (App\Models\MediaLanguage::query()->orderBy('name')->get() as $media_language)
                                <option class="form__option" value="{{ $media_language->id }}">
                                    {{ $media_language->name }} ({{ $media_language->code }})
                                </option>
                            @endforeach
                        </select>
                        <label for="language_id" class="form__label form__label--floating">
                            {{ __('common.language') }}
                        </label>
                    </p>
                    <div class="form__group">
                        <fieldset class="form__fieldset">
                            <legend class="form__legend">{{ __('common.category') }}</legend>
                            <div class="form__fieldset-checkbox-container--shrink">
                                @foreach (App\Models\Category::query()->orderBy('position')->get() as $category)
                                    <p class="form__group">
                                        <label class="form__label">
                                            <input
                                                class="form__checkbox"
                                                type="checkbox"
                                                value="{{ $category->id }}"
                                                wire:model.live="categories"
                                            />
                                            {{ $category->name }}
                                        </label>
                                    </p>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>
                    <p class="form__group">
                        <input
                            id="username"
                            wire:model.live="username"
                            class="form__text"
                            type="search"
                            autocomplete="off"
                            placeholder=" "
                        />
                        <label for="username" class="form__label form__label--floating">
                            {{ __('subtitle.uploader') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <select
                            id="quantity"
                            class="form__select"
                            wire:model.live="perPage"
                            required
                        >
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <label class="form__label form__label--floating" for="quantity">
                            {{ __('common.quantity') }}
                        </label>
                    </p>
                </form>
            </div>
        </section>
    </aside>
</article>
