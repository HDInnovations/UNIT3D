@extends('layout.with-main')

@section('title')
    <title>Polls - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.polls.index') }}" class="breadcrumb__link">
            {{ __('poll.polls') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-poll--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('poll.edit-poll') }}: {{ $poll->title }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.polls.update', ['poll' => $poll]) }}"
                x-data="extraOptions"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="title"
                        class="form__text"
                        minlength="10"
                        name="title"
                        required
                        type="text"
                        value="{{ $poll->title }}"
                    />
                    <label class="form__label form__label--floating" for="title">
                        {{ __('poll.title') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="expires_at"
                        class="form__text"
                        name="expires_at"
                        type="dateTime-local"
                        value="{{ $poll->expires_at }}"
                    />
                    <label class="form__label form__label--floating" for="expires_at">
                        {{ __('poll.close-date') }}
                    </label>
                </p>
                <template x-for="(option, i) in extraOptions">
                    <p class="form__group">
                        <input
                            type="hidden"
                            x-bind:name="'options[' + i + '][id]'"
                            x-bind:value="option['id']"
                        />
                        <input
                            x-bind:id="'option' + i"
                            class="form__text"
                            type="text"
                            x-bind:name="'options[' + i + '][name]'"
                            x-bind:value="option['name']"
                            required
                        />
                        <label
                            class="form__label form__label--floating"
                            x-bind:for="'option' + i"
                        >
                            {{ __('poll.option') }}
                        </label>
                    </p>
                </template>
                <p class="form__group">
                    <button
                        x-on:click.prevent="extraOptions.push({ 'id': 0, 'name': '' })"
                        class="form__button form__button--outlined"
                    >
                        {{ __('poll.add-option') }}
                    </button>
                    <button
                        x-on:click.prevent="extraOptions.length > 2 ? extraOptions.pop() : null"
                        id="del"
                        class="form__button form__button--outlined"
                    >
                        {{ __('poll.delete-option') }}
                    </button>
                </p>
                <p class="form__group">
                    <input type="hidden" name="multiple_choice" value="0" />
                    <input
                        id="multiple_choice"
                        class="form__checkbox"
                        type="checkbox"
                        name="multiple_choice"
                        value="1"
                        @checked($poll->multiple_choice)
                    />
                    <label class="form__label" for="multiple_choice">
                        {{ __('poll.multiple-choice') }}
                    </label>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('poll.edit-poll') }}
                    </button>
                </p>
            </form>
        </div>
        <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
            document.addEventListener('alpine:init', () => {
                Alpine.data('extraOptions', () => ({
                    extraOptions: {{ Js::from($poll->options->select('id', 'name')) }},
                }));
            });
        </script>
    </section>
@endsection
