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
        {{ __('common.create') }}
    </li>
@endsection

@section('page', 'page__staff-achievement--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.create') }}
            {{ __('user.achievements') }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.achievements.store') }}"
                enctype="multipart/form-data"
                x-data="{ tiers: [{ name: '', description: '', threshold: '' }] }"
            >
                @csrf
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="achievement[name]"
                        required
                        type="text"
                        maxlength="255"
                        value="{{ old('achievement.name') }}"
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
                        value="{{ old('achievement.category') }}"
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
                        <option hidden selected disabled value=""></option>
                        @foreach ($conditionTypes as $type)
                            <option
                                class="form__option"
                                value="{{ $type->value }}"
                                @selected(old('achievement.type') === $type->value)
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
                        id="position"
                        class="form__text"
                        inputmode="numeric"
                        name="achievement[position]"
                        pattern="[0-9]*"
                        required
                        type="text"
                        value="{{ old('achievement.position', '0') }}"
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <select id="filter_type_id" class="form__select" name="achievement[filter_type_id]">
                        <option value="">{{ __('common.no-filter') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
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
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                            <option value="{{ $resolution->id }}">{{ $resolution->name }}</option>
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
                            <option value="{{ $playlist->id }}">{{ $playlist->name }}</option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="filter_playlist_id">
                        {{ __('user.filter-playlist') }}
                    </label>
                </p>
                <p class="form__group">
                    <label class="form__label">
                        <input
                            type="hidden"
                            name="achievement[is_hidden]"
                            value="0"
                        />
                        <input
                            class="form__checkbox"
                            type="checkbox"
                            name="achievement[is_hidden]"
                            value="1"
                        />
                        {{ __('common.hidden') }}
                    </label>
                </p>
                <p class="form__group">
                    <label class="form__label">
                        <input
                            type="hidden"
                            name="achievement[enabled]"
                            value="0"
                        />
                        <input
                            class="form__checkbox"
                            type="checkbox"
                            name="achievement[enabled]"
                            value="1"
                            checked
                        />
                        {{ __('common.enabled') }}
                    </label>
                </p>
                <h3>{{ __('user.tiers') }}</h3>
                <template x-for="(tier, i) in tiers" :key="i">
                    <div class="form__group--horizontal">
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
                        x-on:click.prevent="tiers.push({ name: '', description: '', threshold: '' })"
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
            Create an achievement with one or more tiers. Each tier represents a level within
            the achievement. Users progress through tiers by meeting the threshold for the selected
            condition type. Achievements are evaluated daily by a scheduled command. Filters narrow
            the condition to specific torrent types, categories, resolutions, or playlists.
        </div>
    </section>
@endsection
