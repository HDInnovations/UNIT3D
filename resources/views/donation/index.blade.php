@extends('layout.with-main')

@section('title')
    <title>{{ __('member-interface.misc.donation.donate') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="{{ __('member-interface.misc.donation.donate') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">{{ __('member-interface.misc.donation.donate') }}</li>
@endsection

@section('page', 'page__donation--index')

@section('main')
    <section x-data class="panelV2">
        <h2 class="panel__heading">
            {{ __('member-interface.misc.donation.support-heading', ['title' => config('other.title')]) }}
        </h2>
        <div class="panel__body">
            <p>{{ config('donation.description') }}</p>
            <div class="donation-packages">
                @foreach ($packages as $package)
                    <div class="donation-package__wrapper">
                        <div class="donation-package">
                            <div class="donation-package__header">
                                <div class="donation-package__name">{{ $package->name }}</div>
                                <div class="donation-package__price-days">
                                    <span class="donation-package__price">
                                        {{ $package->cost }} {{ config('donation.currency') }}
                                    </span>
                                    <span class="donation-package__separator">-</span>
                                    <span class="donation-package__days">
                                        @if ($package->donor_value === null)
                                            {{ __('member-interface.misc.donation.lifetime') }}
                                        @else
                                            {{ trans_choice('member-interface.misc.donation.days', $package->donor_value, ['count' => $package->donor_value]) }}
                                        @endif
                                    </span>
                                </div>
                                <div class="donation-package__description">
                                    {{ $package->description }}
                                </div>
                            </div>
                            <div class="donation-package__benefits-list">
                                <ol class="benefits-list">
                                    @if ($package->donor_value === null)
                                        <li>
                                            {{ __('member-interface.misc.donation.unlimited-download-slots') }}
                                        </li>
                                    @endif

                                    @if ($package->donor_value === null)
                                        <li>{{ __('member-interface.misc.donation.custom-user-icon') }}</li>
                                    @endif

                                    <li>{{ __('member-interface.misc.donation.global-freeleech') }}</li>
                                    <li>{{ __('member-interface.misc.donation.immunity-warnings') }}</li>
                                    <li
                                        style="
                                            background-image: url(/img/sparkels.gif);
                                            width: auto;
                                        "
                                    >
                                        {{ __('member-interface.misc.donation.sparkle-effect') }}
                                    </li>
                                    <li>
                                        {{ __('member-interface.misc.donation.donor-star') }}
                                        @if ($package->donor_value === null)
                                            <i
                                                id="lifeline"
                                                class="fal fa-star"
                                                title="{{ __('member-interface.misc.donation.lifetime-donor') }}"
                                            ></i>
                                        @else
                                            <i
                                                class="fal fa-star text-gold"
                                                title="{{ __('member-interface.misc.donation.donor') }}"
                                            ></i>
                                        @endif
                                    </li>
                                    <li>
                                        {{ __('member-interface.misc.donation.warm-fuzzy', ['title' => config('other.title')]) }}
                                    </li>
                                    @if ($package->upload_value !== null)
                                        <li>
                                            {{ __('member-interface.misc.donation.upload-credit', ['amount' => App\Helpers\StringHelper::formatBytes($package->upload_value)]) }}
                                        </li>
                                    @endif

                                    @if ($package->bonus_value !== null)
                                        <li>
                                            {{ trans_choice('member-interface.misc.donation.bonus-points', $package->bonus_value, ['count' => number_format($package->bonus_value)]) }}
                                        </li>
                                    @endif

                                    @if ($package->invite_value !== null)
                                        <li>
                                            {{ trans_choice('member-interface.misc.donation.invites', $package->invite_value, ['count' => $package->invite_value]) }}
                                        </li>
                                    @endif
                                </ol>
                            </div>
                            <div class="donation-package__footer">
                                <p class="form__group form__group--horizontal">
                                    <button
                                        class="form__button form__button--filled form__button--centered"
                                        x-on:click.stop="$refs.dialog{{ $package->id }}.showModal()"
                                    >
                                        <i class="fas fa-handshake"></i>
                                        {{ __('member-interface.misc.donation.donate') }}
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @foreach ($packages as $package)
            <dialog class="dialog" x-ref="dialog{{ $package->id }}">
                <h4 class="dialog__heading">
                    {{ __('member-interface.misc.donation.modal-heading', ['cost' => $package->cost]) }}
                </h4>
                <form
                    class="dialog__form"
                    method="POST"
                    action="{{ route('donations.store') }}"
                    x-on:click.outside="$refs.dialog{{ $package->id }}.close()"
                >
                    @csrf
                    <span class="text-success text-center">
                        {{ __('member-interface.misc.donation.steps-intro') }}
                    </span>
                    <div class="form__group--horizontal">
                        @foreach ($gateways->sortBy('position') as $gateway)
                            <p class="form__group">
                                <input
                                    class="form__text"
                                    type="text"
                                    disabled
                                    value="{{ $gateway->address }}"
                                    id="{{ 'gateway-' . $gateway->id }}"
                                />
                                <label
                                    for="{{ 'gateway-' . $gateway->id }}"
                                    class="form__label form__label--floating"
                                >
                                    {{ $gateway->name }}
                                </label>
                            </p>
                        @endforeach

                        <p class="text-info">
                            {{ __('member-interface.misc.donation.send-instructions', ['amount' => '$ ' . $package->cost . ' ' . config('donation.currency')]) }}
                        </p>
                    </div>
                    <div class="form__group--horizontal">
                        <p class="form__group">
                            <input
                                class="form__text"
                                type="text"
                                disabled
                                value="{{ $package->cost }}"
                                id="package-cost"
                            />
                            <label for="package-cost" class="form__label form__label--floating">
                                {{ __('member-interface.misc.donation.cost-label') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <input
                                class="form__text"
                                type="text"
                                value=""
                                id="proof"
                                name="transaction"
                            />
                            <label for="proof" class="form__label form__label--floating">
                                {{ __('member-interface.misc.donation.tx-hash-label') }}
                            </label>
                        </p>
                    </div>
                    <span class="text-warning">
                        {{ __('member-interface.misc.donation.processing-warning') }}
                    </span>
                    <p class="form__group">
                        <input type="hidden" name="package_id" value="{{ $package->id }}" />
                        <button class="form__button form__button--filled">
                            {{ __('member-interface.misc.donation.donate') }}
                        </button>
                        <button
                            formmethod="dialog"
                            formnovalidate
                            class="form__button form__button--outlined"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </p>
                </form>
            </dialog>
        @endforeach
    </section>
@endsection
