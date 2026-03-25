@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.torrent_deletion_reasons.index') }}" class="breadcrumb__link">
            {{ __('staff.torrent-deletion-reasons') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $torrentDeletionReason->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.delete') }}
    </li>
@endsection

@section('page', 'page__staff-torrent-deletion-reason--delete')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('common.delete') }} {{ __('staff.torrent-deletion-reason') }}:
            {{ $torrentDeletionReason->name }}
        </h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.torrent_deletion_reasons.destroy', ['torrentDeletionReason' => $torrentDeletionReason]) }}"
                x-data="confirmation"
            >
                @csrf
                @method('DELETE')
                <p class="form__group">
                    An existing torrent on site may already use this deletion reason. Would you like
                    to change it?
                </p>
                <p class="form__group">
                    <select
                        name="deletion_reason_id"
                        class="form__select"
                        x-data="{ torrentDeletionReason: '' }"
                        x-model="torrentDeletionReason"
                        x-bind:class="torrentDeletionReason === '' ? 'form__select--default' : ''"
                    >
                        <option hidden disabled selected value=""></option>
                        @foreach ($otherTorrentDeletionReasons as $otherTorrentDeletionReason)
                            <option value="{{ $otherTorrentDeletionReason->id }}">
                                {{ $otherTorrentDeletionReason->name }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="autoreg">
                        Replacement
                    </label>
                </p>
                <p class="form__group">
                    <button
                        x-on:click.prevent="confirmAction"
                        data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this torrent deletion reason: ' . $torrentDeletionReason->name . '?') }}"
                        class="form__button form__button--filled"
                    >
                        {{ __('common.delete') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
