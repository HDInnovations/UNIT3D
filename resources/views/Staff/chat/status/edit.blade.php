@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.statuses.index') }}" class="breadcrumb__link">
            {{ __('staff.chat') }} {{ __('staff.statuses') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $chatstatus->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-chat-status--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }} {{ __('staff.chat') }} {{ __('staff.status') }}:
            {{ $chatstatus->name }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.statuses.update', ['chatStatus' => $chatstatus]) }}"
                enctype="multipart/form-data"
            >
                @csrf
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="name"
                        required
                        type="text"
                        value="{{ $chatstatus->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="color"
                        class="form__text"
                        name="color"
                        required
                        type="text"
                        value="{{ $chatstatus->color }}"
                    />
                    <label class="form__label form__label--floating" for="color">
                        {{ __('common.color') }} (e.g. #ff0000)
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="icon"
                        class="form__text"
                        name="icon"
                        placeholder="Enter Font Awesome code here..."
                        required
                        type="text"
                        value="{{ $chatstatus->icon }}"
                    />
                    <label class="form__label form__label--floating" for="icon">
                        Font Awesome icon code (e.g. fas fa-comment-smile)
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
