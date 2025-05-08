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
    <li class="breadcrumbV2">
        {{ $collectible->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__badge--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.edit') }}: {{ $collectible->name }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                enctype="multipart/form-data"
                action="{{ route('staff.collectibles.update', ['collectible' => $collectible]) }}"
                x-data="{ requirements: {{ Js::from($collectible->requirements) }} }"
            >
                @csrf
                <p class="form__group">
                    <label for="icon" class="form__label">{{ __('common.image') }}</label>
                    <input class="form__file" type="file" name="icon" id="icon" />
                </p>
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        type="text"
                        name="collectible[name]"
                        value="{{ $collectible->name }}"
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
                        value="{{ $collectible->description }}"
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
                        x-data="{ selected: {{ $collectible->category_id }} || '' }"
                        x-model="selected"
                        x-bind:class="selected === '' ? 'form__selected--default' : ''"
                        required
                    >
                        <option disabled hidden></option>
                        @foreach ($collectibleCategories as $collectibleCategory)
                            <option
                                value="{{ $collectibleCategory->id }}"
                                @selected($collectibleCategory->id === $collectible->category_id)
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
                        value="{{ $collectible->price }}"
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
                        value="{{ $max_amount }}"
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
                        @if ($collectible->resell)
                            checked
                        @endif
                    />
                    <label class="form__label" for="resell">Allow resell?</label>
                </p>
                <h3>Requirements</h3>
                <template x-for="(requirement, i) in requirements">
                    <div class="form__group--horizontal">
                        <input
                            type="hidden"
                            x-bind:name="'requirements[' + i + '][id]'"
                            x-bind:value="requirement['id']"
                        />
                        <p class="form__group">
                            <select
                                x-bind:id="'requirement' + i + 'operand1'"
                                class="form__select"
                                x-bind:name="'requirements[' + i + '][operand1]'"
                                required
                            >
                                <option hidden selected disabled value=""></option>
                                <option
                                    class="form__option"
                                    value="uploaded"
                                    x-bind:selected="requirement['operand1'] === 'uploaded'"
                                >
                                    {{ __('common.account') }} {{ __('user.total-upload') }}
                                    (bytes)
                                </option>
                                <option
                                    class="form__option"
                                    value="seedsize"
                                    x-bind:selected="requirement['operand1'] === 'seedsize'"
                                >
                                    {{ __('common.account') }} {{ __('torrent.seedsize') }}
                                    (bytes)
                                </option>
                                <option
                                    class="form__option"
                                    value="average_seedtime"
                                    x-bind:selected="requirement['operand1'] === 'average_seedtime'"
                                >
                                    {{ __('common.account') }} {{ __('user.avg-seedtime') }}
                                    (seconds)
                                </option>
                                <option
                                    class="form__option"
                                    value="ratio"
                                    x-bind:selected="requirement['operand1'] === 'ratio'"
                                >
                                    {{ __('common.account') }} {{ __('common.ratio') }}
                                </option>
                                <option
                                    class="form__option"
                                    value="age"
                                    x-bind:selected="requirement['operand1'] === 'age'"
                                >
                                    {{ __('common.account') }} Age (seconds)
                                </option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'requirement' + i + 'operand1'"
                            >
                                Operand 1
                            </label>
                        </p>
                        <p class="form__group">
                            <select
                                x-bind:id="'requirement' + i + 'operator'"
                                class="form__select"
                                x-bind:name="'requirements[' + i + '][operator]'"
                                required
                            >
                                <option hidden selected disabled value=""></option>
                                <option
                                    class="form__option"
                                    value="<"
                                    x-bind:selected="requirement['operator'] === '<'"
                                >
                                    &lt;
                                </option>
                                <option
                                    class="form__option"
                                    value=">"
                                    x-bind:selected="requirement['operator'] === '>'"
                                >
                                    &gt;
                                </option>
                                <option
                                    class="form__option"
                                    value="<="
                                    x-bind:selected="requirement['operator'] === '<='"
                                >
                                    &leq;
                                </option>
                                <option
                                    class="form__option"
                                    value=">="
                                    x-bind:selected="requirement['operator'] === '>='"
                                >
                                    &geq;
                                </option>
                                <option
                                    class="form__option"
                                    value="="
                                    x-bind:selected="requirement['operator'] === '='"
                                >
                                    &equals;
                                </option>
                                <option
                                    class="form__option"
                                    value="!="
                                    x-bind:selected="requirement['operator'] === '!='"
                                >
                                    &ne;
                                </option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'requirement' + i + 'operator'"
                            >
                                Operator
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                x-bind:id="'requirement' + i + 'operand2'"
                                class="form__text"
                                x-bind:name="'requirements[' + i + '][operand2]'"
                                required
                                type="text"
                                x-bind:value="requirement['operand2'].replace(/(\.\d+?)0+$/, '$1')"
                            />
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'requirement' + i + 'operand2'"
                            >
                                Operand 2
                            </label>
                        </p>
                    </div>
                </template>
                <p class="form__group">
                    <button
                        x-on:click.prevent="requirements.push({ 'id': 0, 'operand1': '', 'operator': '', 'operand2': '' })"
                        class="form__button form__button--outlined"
                    >
                        Add Requirement
                    </button>
                    <button
                        class="form__button form__button--outlined"
                        x-on:click.prevent="requirements.length > 0 ? requirements.pop() : null"
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
