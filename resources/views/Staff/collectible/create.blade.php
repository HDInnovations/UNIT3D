@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        <a href="{{ route('staff.collectibles.index') }}" class="breadcrumb__link">
            {{ __('user.badges') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__badge--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">Add a new Item</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('staff.collectibles.store') }}"
                x-data="{ requirements: 0 }"
            >
                @csrf
                <p class="form__group">
                    <input class="form__file" type="file" name="collectible[icon]" id="icon" />
                    <label for="icon" class="form__label">{{ __('common.image') }}</label>
                </p>
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="collectible[name]"
                        required
                    />
                    <label class="form__label form__label--floating" for="name">Name</label>
                </p>
                <p class="form__group">
                    <input
                        id="description"
                        class="form__text"
                        type="text"
                        name="collectible[description]"
                        required
                    />
                    <label class="form__label form__label--floating" for="description">
                        Description
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="category_id"
                        name="collectible[category_id]"
                        class="form__select"
                        x-data="{ selected: {{ $collectibleCategoryId }} || '' }"
                        x-model="selected"
                        x-bind:class="selected === '' ? 'form__selected--default' : ''"
                        required
                    >
                        <option disabled hidden></option>
                        @foreach ($collectibleCategories as $collectibleCategory)
                            <option
                                value="{{ $collectibleCategory->id }}"
                                @selected($collectibleCategory->id === $collectibleCategoryId)
                            >
                                {{ $collectibleCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="forum_category_id">
                        {{ __('common.category') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="price"
                        class="form__text"
                        inputmode="numeric"
                        name="collectible[price]"
                        pattern="[0-9]*"
                        required
                        type="text"
                    />
                    <label class="form__label form__label--floating" for="price">
                        BON per item
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="max_amount"
                        class="form__text"
                        inputmode="numeric"
                        name="collectible[max_amount]"
                        pattern="[0-9]*"
                        required
                        type="text"
                    />
                    <label class="form__label form__label--floating" for="max_amount">
                        Amount of pieces for this item
                    </label>
                </p>
                <p class="form__group">
                    <input type="hidden" name="resell" value="0" />
                    <input
                        type="checkbox"
                        class="form__checkbox"
                        id="resell"
                        name="collectible[resell]"
                        value="1"
                    />
                    <label class="form__label" for="resell">Allow resell?</label>
                </p>
                <h3>Requirements</h3>
                <div class="form__group--horizontal">
                    <p class="form__group">
                        <input
                            id="min_uploaded"
                            class="form__text"
                            inputmode="numeric"
                            name="requirements[min_uploaded]"
                            pattern="[0-9]*"
                            type="text"
                        />
                        <label class="form__label" for="min_uploaded">Min. Upload</label>
                    </p>
                    <p class="form__group">
                        <input
                            id="min_seedsize"
                            class="form__text"
                            inputmode="numeric"
                            name="requirements[min_seedsize]"
                            pattern="[0-9]*"
                            type="text"
                        />
                        <label class="form__label" for="min_seedsize">Min. Seedsize</label>
                    </p>
                    <p class="form__group">
                        <input
                            id="min_avg_seedtime"
                            class="form__text"
                            inputmode="numeric"
                            name="requirements[min_avg_seedtime]"
                            pattern="[0-9]*"
                            type="text"
                        />
                        <label class="form__label" for="min_avg_seedtime">
                            Min. Average Seedtime
                        </label>
                    </p>
                    <p class="form__group">
                        <input
                            id="min_ratio"
                            class="form__text"
                            inputmode="numeric"
                            name="requirements[min_ratio]"
                            type="text"
                        />
                        <label class="form__label" for="min_ratio">Min. Ratio</label>
                    </p>
                    <p class="form__group">
                        <input
                            id="min_age"
                            class="form__text"
                            inputmode="numeric"
                            name="requirements[min_age]"
                            pattern="[0-9]*"
                            type="text"
                        />
                        <label class="form__label" for="min_age">Min. Age</label>
                    </p>
                </div>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
