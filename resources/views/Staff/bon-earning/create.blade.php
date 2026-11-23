@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.bon_earnings.index') }}" class="breadcrumb__link">
            {{ __('bon.bon') }} {{ __('bon.earning') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-bon-earning--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.add') }}
            {{ trans_choice('common.a-an-art', false) }}
            {{ __('bon.bon') }} {{ __('bon.earning') }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.bon_earnings.store') }}"
                x-data="{ conditions: 0 }"
            >
                @csrf
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="bon_earning[name]"
                        required
                        type="text"
                        maxlength="255"
                        value="{{ old('name') }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="description"
                        class="form__text"
                        name="bon_earning[description]"
                        required
                        type="text"
                        maxlength="255"
                        value="{{ old('description') }}"
                    />
                    <label class="form__label form__label--floating" for="description">
                        {{ __('common.description') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        inputmode="numeric"
                        name="bon_earning[position]"
                        pattern="[0-9]*"
                        required
                        type="text"
                        value="{{ old('position') }}"
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="variable"
                        class="form__select"
                        name="bon_earning[variable]"
                        required
                    >
                        <option hidden selected disabled value=""></option>
                        <option class="form__option" value="1">1 (Constant)</option>
                        <option class="form__option" value="age">
                            {{ __('torrent.age') }} (seconds)
                        </option>
                        <option class="form__option" value="size">
                            {{ __('torrent.size') }} (bytes)
                        </option>
                        <option class="form__option" value="seeders">
                            {{ __('torrent.seeders') }}
                        </option>
                        <option class="form__option" value="leechers">
                            {{ __('torrent.leechers') }}
                        </option>
                        <option class="form__option" value="times_completed">
                            {{ __('torrent.completed-times') }}
                        </option>
                        <option class="form__option" value="internal">
                            {{ __('common.internal') }} (1 (true) or 0 (false))
                        </option>
                        <option class="form__option" value="personal_release">
                            {{ __('torrent.personal-release') }} (1 (true) or 0 (false))
                        </option>
                        <option class="form__option" value="seedtime">
                            {{ __('torrent.seedtime') }} (seconds)
                        </option>
                        <option class="form__option" value="connectable">
                            Connectable (1 (true) or 0 (false))
                        </option>
                    </select>
                    <label class="form__label form__label--floating" for="autocat">Variable</label>
                </p>
                <p class="form__group">
                    <input
                        id="multiplier"
                        class="form__text"
                        inputmode="numeric"
                        name="bon_earning[multiplier]"
                        pattern="[0-9.]*"
                        required
                        type="text"
                        value="{{ old('multiplier') }}"
                    />
                    <label class="form__label form__label--floating" for="multiplier">
                        Multiplier
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="type"
                        class="form__select"
                        name="bon_earning[operation]"
                        required
                        value="{{ old('operation') }}"
                    >
                        <option hidden selected disabled value=""></option>
                        <option class="form__option" value="append">Append</option>
                        <option class="form__option" value="multiply">Multiply</option>
                    </select>
                    <label class="form__label form__label--floating" for="operation">
                        Operation
                    </label>
                </p>
                <h3>Conditions</h3>
                <template x-for="condition in conditions">
                    <div class="form__group--horizontal">
                        <p class="form__group">
                            <select
                                x-bind:id="'condition' + condition + 'operand1'"
                                class="form__select"
                                x-bind:name="'conditions[' + condition + '][operand1]'"
                                required
                            >
                                <option hidden selected disabled value=""></option>
                                <option class="form__option" value="1">1 (Constant)</option>
                                <option class="form__option" value="age">
                                    {{ __('torrent.age') }} (seconds)
                                </option>
                                <option class="form__option" value="size">
                                    {{ __('torrent.size') }} (bytes)
                                </option>
                                <option class="form__option" value="seeders">
                                    {{ __('torrent.seeders') }}
                                </option>
                                <option class="form__option" value="leechers">
                                    {{ __('torrent.leechers') }}
                                </option>
                                <option class="form__option" value="times_completed">
                                    {{ __('torrent.completed-times') }}
                                </option>
                                <option class="form__option" value="internal">
                                    {{ __('common.internal') }} (1 (true) or 0 (false))
                                </option>
                                <option class="form__option" value="personal_release">
                                    {{ __('torrent.personal-release') }} (1 (true) or 0 (false))
                                </option>
                                <option class="form__option" value="type_id">
                                    {{ __('torrent.type') }} (id)
                                </option>
                                <option class="form__option" value="seedtime">
                                    {{ __('torrent.seedtime') }} (seconds)
                                </option>
                                <option class="form__option" value="connectable">
                                    Connectable (1 (true) or 0 (false))
                                </option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'condition' + condition + 'operand1'"
                            >
                                Operand 1
                            </label>
                        </p>
                        <p class="form__group">
                            <select
                                x-bind:id="'condition' + condition + 'operator'"
                                class="form__select"
                                x-bind:name="'conditions[' + condition + '][operator]'"
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
                                x-bind:for="'condition' + condition + 'operator'"
                            >
                                Operator
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                x-bind:id="'condition' + condition + 'operand2'"
                                class="form__text"
                                x-bind:name="'conditions[' + condition + '][operand2]'"
                                required
                                type="text"
                            />
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'condition' + condition + 'operand2'"
                            >
                                Operand 2
                            </label>
                        </p>
                    </div>
                </template>
                <p class="form__group">
                    <button
                        x-on:click.prevent="conditions++"
                        class="form__button form__button--outlined"
                    >
                        Add condition
                    </button>
                    <button
                        class="form__button form__button--outlined"
                        x-on:click.prevent="conditions = Math.max(0, conditions - 1)"
                    >
                        Delete condition
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
            Every hour, earnings are calculated and distributed to each user. Each earning is
            calculated as "variable * multiplier". There exist two types of earnings: "append", and
            "multiply". If the earning is of the type "append", then it is added onto previous
            earnings. If the earning is of the type "multiply", then it multiplies all previous
            earnings (denoted by a position lower than this earning). For example, if the order of
            the earnings was "append", "append", "multiply", "append", then the sum of the first two
            earnings will be multiplied by the third earning before being added to the fourth
            earning. Conditions can also be added to specify if an earning should be calculated or
            not.
        </div>
    </section>
@endsection
