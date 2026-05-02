@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.achievements.index') }}" class="breadcrumb__link">
            {{ __('user.achievements') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-achievement--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }}: {{ $achievement->name }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.achievements.update', ['achievement' => $achievement]) }}"
                enctype="multipart/form-data"
                x-data="{ tiers: {{ Js::from($achievement->tiers->map(fn (\App\Models\AchievementTier $tier) => ['id' => $tier->id, 'name' => $tier->name, 'description' => $tier->description, 'threshold' => rtrim(rtrim($tier->threshold, '0'), '.')])) }} }"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="achievement[name]"
                        required
                        type="text"
                        maxlength="255"
                        value="{{ $achievement->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="category"
                        class="form__text"
                        name="achievement[category]"
                        required
                        type="text"
                        maxlength="255"
                        value="{{ $achievement->category }}"
                    />
                    <label class="form__label form__label--floating" for="category">
                        {{ __('common.category') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="type"
                        class="form__select"
                        name="achievement[type]"
                        required
                    >
                        @foreach ($conditionTypes as $type)
                            <option
                                class="form__option"
                                value="{{ $type->value }}"
                                @selected($achievement->type === $type)
                            >
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="type">
                        {{ __('user.condition-type') }}
                    </label>
                </p>
                <p class="form__group">
                    @if ($achievement->icon_path)
                        <img
                            class="achievement-icon-preview"
                            src="{{ route('authenticated_images.achievement_image', ['achievement' => $achievement]) }}"
                            alt="{{ $achievement->name }}"
                        />
                    @endif
                    <input
                        id="icon"
                        class="form__file"
                        name="achievement[icon]"
                        type="file"
                        accept="image/*"
                    />
                    <label class="form__label form__label--floating" for="icon">
                        {{ __('common.icon') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="positions"
                        class="form__text"
                        inputmode="numeric"
                        name="achievement[positions]"
                        pattern="[0-9]*"
                        required
                        type="text"
                        value="{{ $achievement->positions }}"
                    />
                    <label class="form__label form__label--floating" for="positions">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="filter_type_id" class="form__select" name="achievement[filter_type_id]">
                        <option value="">{{ __('common.no-filter') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected($achievement->filter_type_id == $type->id)>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="filter_type_id">
                        Filter: {{ __('torrent.type') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="filter_category_id" class="form__select" name="achievement[filter_category_id]">
                        <option value="">{{ __('common.no-filter') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($achievement->filter_category_id == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="filter_category_id">
                        Filter: {{ __('torrent.category') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="filter_resolution_id" class="form__select" name="achievement[filter_resolution_id]">
                        <option value="">{{ __('common.no-filter') }}</option>
                        @foreach ($resolutions as $resolution)
                            <option value="{{ $resolution->id }}" @selected($achievement->filter_resolution_id == $resolution->id)>
                                {{ $resolution->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="filter_resolution_id">
                        Filter: {{ __('torrent.resolution') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="filter_playlist_id" class="form__select" name="achievement[filter_playlist_id]">
                        <option value="">{{ __('common.no-filter') }}</option>
                        @foreach ($playlists as $playlist)
                            <option value="{{ $playlist->id }}" @selected($achievement->filter_playlist_id == $playlist->id)>
                                {{ $playlist->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="filter_playlist_id">
                        {{ __('user.filter-playlist') }}
                    </label>
                </p>
                <p class="form__group">
                    <label class="form__label">
                        <input type="hidden" name="achievement[is_hidden]" value="0" />
                        <input
                            class="form__checkbox"
                            type="checkbox"
                            name="achievement[is_hidden]"
                            value="1"
                            @checked($achievement->is_hidden)
                        />
                        {{ __('common.hidden') }}
                    </label>
                </p>
                <p class="form__group">
                    <label class="form__label">
                        <input type="hidden" name="achievement[enabled]" value="0" />
                        <input
                            class="form__checkbox"
                            type="checkbox"
                            name="achievement[enabled]"
                            value="1"
                            @checked($achievement->enabled)
                        />
                        {{ __('common.enabled') }}
                    </label>
                </p>
                <h3>{{ __('user.tiers') }}</h3>
                <template x-for="(tier, i) in tiers" :key="i">
                    <div class="form__group--horizontal">
                        <input type="hidden" x-bind:name="'tiers[' + i + '][id]'" x-bind:value="tier.id" />
                        <p class="form__group">
                            <span
                                class="form__text achievement-tier-number"
                                x-text="i + 1"
                            ></span>
                            <label class="form__label form__label--floating">{{ __('user.tier') }}</label>
                        </p>
                        <p class="form__group">
                            <input
                                x-bind:id="'tier_name_' + i"
                                class="form__text"
                                x-bind:name="'tiers[' + i + '][name]'"
                                required
                                type="text"
                                maxlength="255"
                                x-model="tier.name"
                            />
                            <label class="form__label form__label--floating" x-bind:for="'tier_name_' + i">
                                {{ __('common.name') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                x-bind:id="'tier_description_' + i"
                                class="form__text"
                                x-bind:name="'tiers[' + i + '][description]'"
                                required
                                type="text"
                                maxlength="1000"
                                x-model="tier.description"
                            />
                            <label class="form__label form__label--floating" x-bind:for="'tier_description_' + i">
                                {{ __('common.description') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                x-bind:id="'tier_threshold_' + i"
                                class="form__text"
                                x-bind:name="'tiers[' + i + '][threshold]'"
                                required
                                type="text"
                                inputmode="numeric"
                                x-model="tier.threshold"
                            />
                            <label class="form__label form__label--floating" x-bind:for="'tier_threshold_' + i">
                                {{ __('user.threshold') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                x-bind:id="'tier_icon_' + i"
                                class="form__file"
                                x-bind:name="'tiers[' + i + '][icon]'"
                                type="file"
                                accept="image/*"
                            />
                            <label class="form__label form__label--floating" x-bind:for="'tier_icon_' + i">
                                {{ __('common.icon') }}
                            </label>
                        </p>
                    </div>
                </template>
                <p class="form__group">
                    <button
                        x-on:click.prevent="tiers.push({ id: null, name: '', description: '', threshold: '' })"
                        class="form__button form__button--outlined"
                    >
                        {{ __('user.add-tier') }}
                    </button>
                    <button
                        class="form__button form__button--outlined"
                        x-on:click.prevent="tiers.length > 1 ? tiers.pop() : null"
                    >
                        {{ __('user.remove-tier') }}
                    </button>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.information') }}</h2>
        <div class="panel__body">
            Edit this achievement and its tiers. Users who have already earned tiers will keep
            them. Removing a tier does not revoke awards. Changing the condition type or filters
            will affect future evaluations only.
        </div>
    </section>
@endsection
