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
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-bon-earning--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }}
            {{ trans_choice('common.a-an-art', false) }}
            {{ __('bon.bon') }} {{ __('bon.earning') }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.bon_earnings.update', ['bonEarning' => $bonEarning]) }}"
                x-data="conditions"
            >
                @csrf
                @method('patch')
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="bon_earning[name]"
                        required
                        type="text"
                        maxlength="255"
                        value="{{ $bonEarning->name }}"
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
                        value="{{ $bonEarning->description }}"
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
                        value="{{ $bonEarning->position }}"
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
                        <option
                            class="form__option"
                            value="1"
                            @selected($bonEarning->variable === '1')
                        >
                            1 (Constant)
                        </option>
                        <option
                            class="form__option"
                            value="age"
                            @selected($bonEarning->variable === 'age')
                        >
                            {{ __('torrent.age') }} (seconds)
                        </option>
                        <option
                            class="form__option"
                            value="size"
                            @selected($bonEarning->variable === 'size')
                        >
                            {{ __('torrent.size') }} (bytes)
                        </option>
                        <option
                            class="form__option"
                            value="seeders"
                            @selected($bonEarning->variable === 'seeders')
                        >
                            {{ __('torrent.seeders') }}
                        </option>
                        <option
                            class="form__option"
                            value="leechers"
                            @selected($bonEarning->variable === 'leechers')
                        >
                            {{ __('torrent.leechers') }}
                        </option>
                        <option
                            class="form__option"
                            value="times_completed"
                            @selected($bonEarning->variable === 'times_completed')
                        >
                            {{ __('torrent.completed-times') }}
                        </option>
                        <option
                            class="form__option"
                            value="seedtime"
                            @selected($bonEarning->variable === 'seedtime')
                        >
                            {{ __('torrent.seedtime') }} (seconds)
                        </option>
                        <option
                            class="form__option"
                            value="personal_release"
                            @selected($bonEarning->variable === 'personal_release')
                        >
                            {{ __('torrent.personal-release') }} (1 (true) or 0 (false))
                        </option>
                        <option
                            class="form__option"
                            value="internal"
                            @selected($bonEarning->variable === 'internal')
                        >
                            {{ __('common.internal') }} (1 (true) or 0 (false))
                        </option>
                    </select>
                    <label class="form__label form__label--floating" for="variable">Variable</label>
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
                        value="{{ preg_replace('/(\.\d+?)0+$/', '$1', $bonEarning->multiplier) }}"
                    />
                    <label class="form__label form__label--floating" for="multiplier">
                        Multiplier
                    </label>
                </p>
                <p class="form__group">
                    <select
                        id="operation"
                        class="form__select"
                        name="bon_earning[operation]"
                        required
                        value="{{ old('operation') }}"
                    >
                        <option hidden selected disabled value=""></option>
                        <option
                            class="form__option"
                            value="append"
                            @selected($bonEarning->operation === 'append')
                        >
                            Append
                        </option>
                        <option
                            class="form__option"
                            value="multiply"
                            @selected($bonEarning->operation === 'multiply')
                        >
                            Multiply
                        </option>
                    </select>
                    <label class="form__label form__label--floating" for="operation">
                        Operation
                    </label>
                </p>
                <h3>Conditions</h3>
                <template x-for="(condition, i) in conditions">
                    <div class="form__group--horizontal">
                        <input
                            type="hidden"
                            x-bind:name="'conditions[' + i + '][id]'"
                            x-bind:value="condition['id']"
                        />
                        <p class="form__group">
                            <select
                                x-bind:id="'condition' + i + 'operand1'"
                                class="form__select"
                                x-bind:name="'conditions[' + i + '][operand1]'"
                                required
                            >
                                <option hidden selected disabled value=""></option>
                                <option
                                    class="form__option"
                                    value="1"
                                    x-bind:selected="condition['operand1'] === '1'"
                                >
                                    1 (Constant)
                                </option>
                                <option
                                    class="form__option"
                                    value="age"
                                    x-bind:selected="condition['operand1'] === 'age'"
                                >
                                    {{ __('torrent.age') }} (seconds)
                                </option>
                                <option
                                    class="form__option"
                                    value="size"
                                    x-bind:selected="condition['operand1'] === 'size'"
                                >
                                    {{ __('torrent.size') }} (bytes)
                                </option>
                                <option
                                    class="form__option"
                                    value="seeders"
                                    x-bind:selected="condition['operand1'] === 'seeders'"
                                >
                                    {{ __('torrent.seeders') }}
                                </option>
                                <option
                                    class="form__option"
                                    value="leechers"
                                    x-bind:selected="condition['operand1'] === 'leechers'"
                                >
                                    {{ __('torrent.leechers') }}
                                </option>
                                <option
                                    class="form__option"
                                    value="times_completed"
                                    x-bind:selected="condition['operand1'] === 'times_completed'"
                                >
                                    {{ __('torrent.completed-times') }}
                                </option>
                                <option
                                    class="form__option"
                                    value="internal"
                                    x-bind:selected="condition['operand1'] === 'internal'"
                                >
                                    {{ __('common.internal') }} (1 (true) or 0 (false))
                                </option>
                                <option
                                    class="form__option"
                                    value="personal_release"
                                    x-bind:selected="condition['operand1'] === 'personal_release'"
                                >
                                    {{ __('torrent.personal-release') }} (1 (true) or 0 (false))
                                </option>
                                <option
                                    class="form__option"
                                    value="type_id"
                                    x-bind:selected="condition['operand1'] === 'type_id'"
                                >
                                    {{ __('torrent.type') }} (id)
                                </option>
                                <option
                                    class="form__option"
                                    value="seedtime"
                                    x-bind:selected="condition['operand1'] === 'seedtime'"
                                >
                                    {{ __('torrent.seedtime') }} (seconds)
                                </option>
                                <option
                                    class="form__option"
                                    value="connectable"
                                    x-bind:selected="condition['operand1'] === 'connectable'"
                                >
                                    Connectable (1 (true) or 0 (false))
                                </option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'condition' + i + 'operand1'"
                            >
                                Operand 1
                            </label>
                        </p>
                        <p class="form__group">
                            <select
                                x-bind:id="'condition' + i + 'operator'"
                                class="form__select"
                                x-bind:name="'conditions[' + i + '][operator]'"
                                required
                            >
                                <option hidden selected disabled value=""></option>
                                <option
                                    class="form__option"
                                    value="<"
                                    x-bind:selected="condition['operator'] === '<'"
                                >
                                    &lt;
                                </option>
                                <option
                                    class="form__option"
                                    value=">"
                                    x-bind:selected="condition['operator'] === '>'"
                                >
                                    &gt;
                                </option>
                                <option
                                    class="form__option"
                                    value="<="
                                    x-bind:selected="condition['operator'] === '<='"
                                >
                                    &leq;
                                </option>
                                <option
                                    class="form__option"
                                    value=">="
                                    x-bind:selected="condition['operator'] === '>='"
                                >
                                    &geq;
                                </option>
                                <option
                                    class="form__option"
                                    value="="
                                    x-bind:selected="condition['operator'] === '='"
                                >
                                    &equals;
                                </option>
                                <option
                                    class="form__option"
                                    value="!="
                                    x-bind:selected="condition['operator'] === '!='"
                                >
                                    &ne;
                                </option>
                            </select>
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'condition' + i + 'operator'"
                            >
                                Operator
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                x-bind:id="'condition' + i + 'operand2'"
                                class="form__text"
                                x-bind:name="'conditions[' + i + '][operand2]'"
                                required
                                type="text"
                                x-bind:value="removeZeroes(condition['operand2'])"
                            />
                            <label
                                class="form__label form__label--floating"
                                x-bind:for="'condition' + i + 'operand2'"
                            >
                                Operand 2
                            </label>
                        </p>
                    </div>
                </template>
                <p class="form__group">
                    <button
                        x-on:click.prevent="conditions.push({ 'id': 0, 'operand1': '', 'operator': '', 'operand2': '' })"
                        class="form__button form__button--outlined"
                    >
                        Add condition
                    </button>
                    <button
                        class="form__button form__button--outlined"
                        x-on:click.prevent="conditions.length > 0 ? conditions.pop() : null"
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
        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            document.addEventListener('alpine:init', () => {
                Alpine.data('conditions', () => ({
                    conditions: {{ Js::from($bonEarning->conditions) }},
                    removeZeroes(value) {
                        return value.replace(/(\.\d+?)0+$/, '$1');
                    },
                }));
            });
        </script>
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
