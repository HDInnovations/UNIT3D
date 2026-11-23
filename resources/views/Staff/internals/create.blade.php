@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.internals.index') }}" class="breadcrumb__link">Internals</a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-internal--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.add') }} internal group</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.internals.store') }}"
                enctype="multipart/form-data"
            >
                @csrf
                <p class="form__group">
                    <input id="name" class="form__text" name="name" required type="text" />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="icon"
                        class="form__text"
                        name="icon"
                        required
                        type="text"
                        value="fa-magic"
                    />
                    <label class="form__label form__label--floating" for="icon">Icon</label>
                </p>
                <p class="form__group">
                    <input
                        id="effect"
                        class="form__text"
                        name="effect"
                        required
                        type="text"
                        value="none"
                    />
                    <label class="form__label form__label--floating" for="effect">Effect</label>
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
