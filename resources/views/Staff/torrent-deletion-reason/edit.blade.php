@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.types.index') }}" class="breadcrumb__link">
            {{ __('staff.torrent-deletion-reasons') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $torrentDeletionReason->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('page', 'page__staff-torrent-deletion-reason--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.edit') }}
            {{ __('staff.torrent-deletion-reason') }}
            {{ $torrentDeletionReason->name }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.torrent_deletion_reasons.update', ['torrentDeletionReason' => $torrentDeletionReason]) }}"
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
                        value="{{ $torrentDeletionReason->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('common.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="description"
                        class="form__text"
                        name="description"
                        required
                        type="text"
                        value="{{ $torrentDeletionReason->description }}"
                    />
                    <label class="form__label form__label--floating" for="description">
                        {{ __('common.description') }}
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
