@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.ticket_priorities.index') }}" class="breadcrumb__link">
            {{ __('staff.ticket-priorities') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $ticketPriority->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-ticket-priority--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }}
            {{ __('staff.ticket-priority') }}:
            {{ $ticketPriority->name }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.ticket_priorities.update', ['ticketPriority' => $ticketPriority]) }}"
            >
                @method('PATCH')
                @csrf
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="name"
                        required
                        type="text"
                        value="{{ $ticketPriority->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        inputmode="numeric"
                        name="position"
                        pattern="[0-9]+"
                        required
                        type="text"
                        value="{{ $ticketPriority->position }}"
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="color"
                        class="form__text"
                        name="color"
                        required
                        type="text"
                        value="{{ $ticketPriority->color }}"
                    />
                    <label class="form__label form__label--floating" for="color">
                        {{ __('common.color') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="icon"
                        class="form__text"
                        name="icon"
                        type="text"
                        value="{{ $ticketPriority->icon }}"
                        placeholder="fa-circle"
                    />
                    <label class="form__label form__label--floating" for="icon">
                        {{ __('common.icon') }}
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
