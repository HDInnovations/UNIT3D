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
                <template x-for="requirement in requirements">
                    <div class="form__group--horizontal">
                        <p class="form__group">
                            <select
                                x-bind:id="'requirement' + requirement + 'operand1'"
                                class="form__select"
                                x-bind:name="'requirements[' + requirement + '][operand1]'"
                                required
                            >
                                <option hidden selected disabled value=""></option>
                                <option class="form__option" value="uploaded">
                                    {{ __('common.account') }} {{ __('user.total-upload') }}
                                    (bytes)
                                </option>
                                <option class="form__option" value="seedsize">
                                    {{ __('common.account') }} {{ __('torrent.seedsize') }}
                                    (bytes)
                                </option>
                                <option class="form__option" value="average_seedtime">
                                    {{ __('common.account') }} {{ __('user.avg-seedtime') }}
                                    (seconds)
                                </option>
                                <option class="form__option" value="ratio">
                                    {{ __('common.account') }} {{ __('common.ratio') }}
                                </option>
                                <option class="form__option" value="age">
                                    {{ __('common.account') }} Age (seconds)
                                </option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'requirement' + requirement + 'operand1'"
                            >
                                Operand 1
                            </label>
                        </p>
                        <p class="form__group">
                            <select
                                x-bind:id="'requirement' + requirement + 'operator'"
                                class="form__select"
                                x-bind:name="'requirements[' + requirement + '][operator]'"
                                required
                            >
                                <option hidden selected disabled value=""></option>
                                <option class="form__option" value="<">&lt;</option>
                                <option class="form__option" value=">">&gt;</option>
                                <option class="form__option" value="<=">&leq;</option>
                                <option class="form__option" value=">=">&geq;</option>
                                <option class="form__option" value="=">&equals;</option>
                                <option class="form__option" value="!=">&ne;</option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'requirement' + requirement + 'operator'"
                            >
                                Operator
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                x-bind:id="'requirement' + requirement + 'operand2'"
                                class="form__text"
                                x-bind:name="'requirements[' + requirement + '][operand2]'"
                                required
                                type="text"
                            />
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'requirement' + requirement + 'operand2'"
                            >
                                Operand 2
                            </label>
                        </p>
                    </div>
                </template>
                <p class="form__group">
                    <button
                        x-on:click.prevent="requirements++"
                        class="form__button form__button--outlined"
                    >
                        Add Requirement
                    </button>
                    <button
                        class="form__button form__button--outlined"
                        x-on:click.prevent="requirements = Math.max(0, requirements - 1)"
                    >
                        Delete Requirement
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
