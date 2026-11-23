@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('users.earnings.index', ['user' => $user]) }}" class="breadcrumb__link">
            {{ __('bon.bonus') }} {{ __('bon.points') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('users.gifts.index', ['user' => $user]) }}" class="breadcrumb__link">
            {{ __('bon.gifts') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('bon.send-gift') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-gift--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bon.gift-to') }}</h2>
        <div class="panel__body">
            <form
                id="send_bonus"
                class="form"
                method="POST"
                action="{{ route('users.gifts.store', ['user' => $user]) }}"
            >
                @csrf
                <p class="form__group">
                    <input
                        id="recipient_username"
                        class="form__text"
                        name="recipient_username"
                        placeholder=" "
                        required
                        type="text"
                    />
                    <label class="form__label form__label--floating" for="recipient_username">
                        {{ __('pm.select') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="bon"
                        class="form__text"
                        inputmode="numeric"
                        name="bon"
                        pattern="[0-9]*"
                        placeholder=" "
                        required
                        type="text"
                    />
                    <label class="form__label form__label--floating" for="bon">
                        {{ __('bon.amount') }}
                    </label>
                </p>
                <p class="form__group">
                    <textarea
                        id="message"
                        class="form__textarea"
                        name="message"
                        placeholder=" "
                        required
                    ></textarea>
                    <label class="form__label form__label--floating" for="message">
                        {{ __('pm.message') }}
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

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bon.your-points') }}</h2>
        <div class="panel__body">{{ $bon }}</div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bon.no-refund') }}</h2>
        <div class="panel__body">{{ __('bon.exchange-warning') }}</div>
    </section>
@endsection
