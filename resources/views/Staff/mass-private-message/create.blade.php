@extends('layout.with-main')

@section('title')
    <title>
        {{ __('staff.mass-pm') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('staff.mass-pm') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.mass-pm') }}
    </li>
@endsection

@section('page', 'page__staff-mass-private-message--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff.mass-pm') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                action="{{ route('staff.mass_private_message.store') }}"
                method="POST"
                x-data="confirmation"
            >
                @csrf
                <div class="form__group">
                    <fieldset class="form__fieldset">
                        <legend class="form__legend">Groups</legend>
                        <div class="form__fieldset-checkbox-container">
                            @foreach ($groups as $group)
                                <p class="form__group">
                                    <label class="form__label">
                                        <input
                                            class="form__checkbox"
                                            type="checkbox"
                                            id="group_id{{ $group->id }}"
                                            name="group_ids[]"
                                            value="{{ $group->id }}"
                                        />
                                        {{ $group->name }}
                                    </label>
                                </p>
                            @endforeach
                        </div>
                    </fieldset>
                </div>
                <p class="form__group">
                    <input
                        id="subject"
                        class="form__text"
                        minlength="5"
                        name="subject"
                        type="text"
                        required
                    />
                    <label class="form__label form__label--floating" for="subject">
                        {{ __('pm.subject') }}
                    </label>
                </p>
                @livewire('bbcode-input', ['name' => 'message', 'label' => __('pm.message'), 'required' => true])
                <p class="form__group">
                    <button
                        x-on:click.prevent="confirmAction"
                        class="form__button form__button--filled"
                        data-b64-deletion-message="{{ base64_encode('Are you sure you want to send this private message?') }}"
                    >
                        {{ __('pm.send') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
