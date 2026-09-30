@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.distributors.index') }}" class="breadcrumb__link">
            {{ __('staff-interface.torrent-distributors') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $distributor->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.delete') }}
    </li>
@endsection

@section('page', 'page__staff-distributor--delete')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('staff-interface.delete-torrent-distributor-heading', ['name' => $distributor->name]) }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.distributors.destroy', ['distributor' => $distributor]) }}"
                x-data="confirmation"
            >
                @csrf
                @method('DELETE')
                <p class="form__group">
                    {{ __('staff-interface.distributor-delete-warning') }}
                </p>
                <p class="form__group">
                    <select
                        name="distributor_id"
                        id="autoreg"
                        class="form__select"
                        x-data="{ distributor: '' }"
                        x-model="distributor"
                        x-bind:class="distributor === '' ? 'form__select--default' : ''"
                    >
                        <option hidden disabled selected value=""></option>
                        @foreach ($distributors as $distributor)
                            <option value="{{ $distributor->id }}">
                                {{ $distributor->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="autoreg">
                        {{ __('staff-interface.replacement-distributor') }}
                    </label>
                </p>
                <p class="form__group">
                    <button
                        x-on:click.prevent="confirmAction"
                        data-b64-deletion-message="{{ base64_encode(__('staff-interface.delete-distributor-confirmation', ['name' => $distributor->name])) }}"
                        class="form__button form__button--filled"
                    >
                        {{ __('common.delete') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
