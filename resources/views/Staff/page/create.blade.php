@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.pages.index') }}" class="breadcrumb__link">
            {{ __('staff.pages') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__staff-page--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.add') }}
            {{ trans_choice('common.a-an-art', false) }}
            {{ __('common.new-adj') }}
            {{ __('staff.page') }}
        </h2>
        <div class="panel__body">
            <form class="form" method="POST" action="{{ route('staff.pages.store') }}">
                @csrf
                <p class="form__group">
                    <input id="name" class="form__text" type="text" name="name" required />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('staff.page') }} {{ __('common.name') }}
                    </label>
                </p>
                @livewire('bbcode-input', ['name' => 'content', 'label' => __('common.content'), 'required' => true])
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
