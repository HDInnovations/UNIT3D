@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.bon_exchanges.index') }}" class="breadcrumb__link">
            {{ __('bon.bon') }} {{ __('bon.exchange') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-bon-exchange--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }}
            {{ trans_choice('common.a-an-art', false) }}
            {{ __('bon.bon') }} {{ __('bon.exchange') }}
        </h2>
        <div class="panel__body">
            <form
                name="upload"
                class="upload-form form"
                id="upload-form"
                method="POST"
                action="{{ route('staff.bon_exchanges.update', ['bonExchange' => $bonExchange->id]) }}"
            >
                @csrf
                @method('patch')
                <p class="form__group">
                    <input
                        type="text"
                        name="description"
                        id="description"
                        class="form__text"
                        value="{{ $bonExchange->description }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="description">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        type="text"
                        name="value"
                        id="value"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        value="{{ $bonExchange->value }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="value">
                        {{ __('value') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        type="text"
                        name="cost"
                        id="cost"
                        class="form__text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        value="{{ $bonExchange->cost }}"
                        required
                    />
                    <label class="form__label form__label--floating" for="cost">
                        {{ __('bon.points') }}
                    </label>
                </p>
                <p class="form__group">
                    <select name="type" id="type" class="form__select" required>
                        <option hidden selected disabled value=""></option>
                        <option
                            class="form__option"
                            value="upload"
                            @selected($bonExchange->upload)
                        >
                            {{ __('common.add') }} {{ __('common.upload') }}
                        </option>
                        <option
                            class="form__option"
                            value="download"
                            @selected($bonExchange->download)
                        >
                            {{ __('common.remove') }} {{ __('common.download') }}
                        </option>
                        <option
                            class="form__option"
                            value="personal_freeleech"
                            @selected($bonExchange->personal_freeleech)
                        >
                            {{ __('torrent.personal-freeleech') }}
                        </option>
                        <option
                            class="form__option"
                            value="invite"
                            @selected($bonExchange->invite)
                        >
                            {{ __('user.invites') }}
                        </option>
                    </select>
                    <label class="form__label form__label--floating" for="type">
                        {{ __('common.type') }}
                    </label>
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
