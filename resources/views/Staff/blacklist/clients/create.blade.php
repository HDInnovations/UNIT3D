@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.blacklisted_clients.index') }}" class="breadcrumb__link">
            Blacklisted clients
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-blacklisted-client--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.add') }}
        </h2>
        <div class="panel__body">
            <form
                name="upload"
                class="upload-form form"
                id="upload-form"
                method="POST"
                action="{{ route('staff.blacklisted_clients.store') }}"
            >
                @csrf
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="name"
                        required
                        type="text"
                        value="{{ old('name') }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="peer_id_prefix"
                        class="form__text"
                        name="peer_id_prefix"
                        placeholder=" "
                        type="text"
                        value="{{ old('peer_id_prefix') }}"
                    />
                    <label class="form__label form__label--floating" for="peer_id_prefix">
                        Peer ID prefix
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="reason"
                        class="form__text"
                        name="reason"
                        required
                        type="text"
                        value="{{ old('reason') }}"
                    />
                    <label class="form__label form__label--floating" for="reason">
                        {{ __('common.reason') }}
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
