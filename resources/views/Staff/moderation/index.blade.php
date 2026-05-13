@extends('layout.with-main')

@section('title')
    <title>Moderation - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.torrent-moderation') }}
    </li>
@endsection

@section('page', 'page__staff-moderation--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.pending-torrents') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('torrent.uploaded') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.category') }}</th>
                        <th>{{ __('common.type') }}</th>
                        <th>{{ __('common.resolution') }}</th>
                        <th>{{ __('torrent.size') }}</th>
                        <th>{{ __('torrent.uploader') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pending as $torrent)
                        <tr>
                            <td>
                                <time
                                    datetime="{{ $torrent->created_at }}"
                                    title="{{ $torrent->created_at }}"
                                >
                                    {{ $torrent->created_at->diffForHumans() }}
                                </time>
                            </td>
                            <td>
                                <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}">
                                    {{ $torrent->name }}
                                </a>
                            </td>
                            <td>
                                <i
                                    class="{{ $torrent->category->icon }} category__icon"
                                    data-original-title="{{ $torrent->category->name }} torrent"
                                ></i>
                            </td>
                            <td>{{ $torrent->type->name }}</td>
                            <td>{{ $torrent->resolution->name ?? 'No res' }}</td>
                            <td>{{ $torrent->getSize() }}</td>
                            <td>
                                <x-user-tag :anon="false" :user="$torrent->user" />
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <form
                                            method="POST"
                                            action="{{ route('staff.moderation.update', ['id' => $torrent->id]) }}"
                                        >
                                            @csrf
                                            <input
                                                type="hidden"
                                                name="old_status"
                                                value="{{ $torrent->status }}"
                                            />
                                            <input
                                                type="hidden"
                                                name="status"
                                                value="{{ \App\Enums\ModerationStatus::APPROVED }}"
                                            />
                                            <button class="form__button form__button--filled">
                                                <i
                                                    class="{{ config('other.font-awesome') }} fa-thumbs-up"
                                                ></i>
                                                {{ __('common.moderation-approve') }}
                                            </button>
                                        </form>
                                    </li>
                                    @include('Staff.moderation.partials._postpone-dialog', ['torrent' => $torrent])
                                    @include('Staff.moderation.partials._reject-dialog', ['torrent' => $torrent])
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No pending torrents</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('torrent.postponed-torrents') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('staff.moderation-since') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.category') }}</th>
                        <th>{{ __('common.type') }}</th>
                        <th>{{ __('common.resolution') }}</th>
                        <th>{{ __('torrent.size') }}</th>
                        <th>{{ __('torrent.uploader') }}</th>
                        <th>{{ __('common.staff') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($postponed as $torrent)
                        <tr>
                            <td>
                                <time
                                    datetime="{{ $torrent->moderated_at }}"
                                    title="{{ $torrent->moderated_at }}"
                                >
                                    {{ $torrent->moderated_at->diffForHumans() }}
                                </time>
                            </td>
                            <td>
                                <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}">
                                    {{ $torrent->name }}
                                </a>
                            </td>
                            <td>
                                <i
                                    class="{{ $torrent->category->icon }} category__icon"
                                    title="{{ $torrent->category->name }} torrent"
                                ></i>
                            </td>
                            <td>{{ $torrent->type->name }}</td>
                            <td>{{ $torrent->resolution->name ?? 'No res' }}</td>
                            <td>{{ $torrent->getSize() }}</td>
                            <td>
                                <x-user-tag :anon="false" :user="$torrent->user" />
                            </td>
                            <td>
                                <x-user-tag :anon="false" :user="$torrent->moderated" />
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <form
                                            method="POST"
                                            action="{{ route('staff.moderation.update', ['id' => $torrent->id]) }}"
                                        >
                                            @csrf
                                            <input
                                                type="hidden"
                                                name="old_status"
                                                value="{{ $torrent->status }}"
                                            />
                                            <input
                                                type="hidden"
                                                name="status"
                                                value="{{ \App\Enums\ModerationStatus::APPROVED }}"
                                            />
                                            <button class="form__button form__button--filled">
                                                <i
                                                    class="{{ config('other.font-awesome') }} fa-thumbs-up"
                                                ></i>
                                                {{ __('common.moderation-approve') }}
                                            </button>
                                        </form>
                                    </li>
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('torrents.edit', ['id' => $torrent->id]) }}"
                                            class="form__button form__button--filled"
                                        >
                                            <i
                                                class="{{ config('other.font-awesome') }} fa-pencil"
                                            ></i>
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    @include('Staff.moderation.partials._delete-dialog', ['torrent' => $torrent])
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No postponed torrents</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section
        class="panelV2"
        x-data="{
            selectedRejected: [],
            rejectedTorrentIds:
                {{ Js::from($rejected->pluck('id')->map(fn ($id) => (string) $id)->values()) }},
        }"
    >
        <h2 class="panel__heading">{{ __('torrent.rejected') }}</h2>
        @if ($rejected->isNotEmpty())
            <menu class="data-table__actions">
                <li class="data-table__action">
                    <button
                        class="form__button form__button--filled"
                        type="button"
                        popovertarget="rejected-torrents-delete"
                        x-bind:disabled="selectedRejected.length === 0"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-thumbs-down"></i>
                        {{ __('common.delete') }} selected
                    </button>
                </li>
            </menu>
            <dialog id="rejected-torrents-delete" class="dialog" popover>
                <h4 class="dialog__heading">
                    {{ __('common.delete') }} selected rejected torrents
                </h4>
                <form
                    id="bulk-delete-rejected-torrents"
                    class="dialog__form"
                    method="POST"
                    action="{{ route('staff.moderation.destroy_rejected') }}"
                >
                    @csrf
                    @method('DELETE')
                    <p class="form__group">
                        <textarea
                            class="form__textarea"
                            name="message"
                            id="bulk-deletion-message"
                            required
                        ></textarea>
                        <label
                            class="form__label form__label--floating"
                            for="bulk-deletion-message"
                        >
                            Deletion reason
                        </label>
                    </p>
                    <p class="form__group">
                        <button class="form__button form__button--filled">
                            {{ __('common.delete') }}
                        </button>
                        <button
                            class="form__button form__button--outlined"
                            type="button"
                            popovertarget="rejected-torrents-delete"
                        >
                            {{ __('common.cancel') }}
                        </button>
                    </p>
                </form>
            </dialog>
        @endif

        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>
                            <input
                                class="form__checkbox"
                                type="checkbox"
                                aria-label="Select all rejected torrents"
                                x-bind:checked="rejectedTorrentIds.length > 0 && selectedRejected.length === rejectedTorrentIds.length"
                                x-on:change="selectedRejected = $event.target.checked ? rejectedTorrentIds.slice() : []"
                            />
                        </th>
                        <th>{{ __('staff.moderation-since') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.category') }}</th>
                        <th>{{ __('common.type') }}</th>
                        <th>{{ __('common.resolution') }}</th>
                        <th>{{ __('torrent.size') }}</th>
                        <th>{{ __('torrent.uploader') }}</th>
                        <th>{{ __('common.staff') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rejected as $torrent)
                        <tr>
                            <td>
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    name="torrent_ids[]"
                                    value="{{ $torrent->id }}"
                                    form="bulk-delete-rejected-torrents"
                                    x-model="selectedRejected"
                                    aria-label="Select {{ $torrent->name }}"
                                />
                            </td>
                            <td>
                                <time
                                    datetime="{{ $torrent->moderated_at }}"
                                    title="{{ $torrent->moderated_at }}"
                                >
                                    {{ $torrent->moderated_at->diffForHumans() }}
                                </time>
                            </td>
                            <td>
                                <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}">
                                    {{ $torrent->name }}
                                </a>
                            </td>
                            <td>
                                <i
                                    class="{{ $torrent->category->icon }} category__icon"
                                    title="{{ $torrent->category->name }} torrent"
                                ></i>
                            </td>
                            <td>{{ $torrent->type->name }}</td>
                            <td>{{ $torrent->resolution->name ?? 'No res' }}</td>
                            <td>{{ $torrent->getSize() }}</td>
                            <td>
                                <x-user-tag :anon="false" :user="$torrent->user" />
                            </td>
                            <td>
                                <x-user-tag :anon="false" :user="$torrent->moderated" />
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <form
                                            method="POST"
                                            action="{{ route('staff.moderation.update', ['id' => $torrent->id]) }}"
                                        >
                                            @csrf
                                            <input
                                                type="hidden"
                                                name="old_status"
                                                value="{{ $torrent->status }}"
                                            />
                                            <input
                                                type="hidden"
                                                name="status"
                                                value="{{ \App\Enums\ModerationStatus::APPROVED }}"
                                            />
                                            <button class="form__button form__button--filled">
                                                <i
                                                    class="{{ config('other.font-awesome') }} fa-thumbs-up"
                                                ></i>
                                                {{ __('common.moderation-approve') }}
                                            </button>
                                        </form>
                                    </li>
                                    @include('Staff.moderation.partials._postpone-dialog', ['torrent' => $torrent])
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('torrents.edit', ['id' => $torrent->id]) }}"
                                            class="form__button form__button--filled"
                                        >
                                            <i
                                                class="{{ config('other.font-awesome') }} fa-pencil"
                                            ></i>
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    @include('Staff.moderation.partials._delete-dialog', ['torrent' => $torrent])
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">No rejected torrents</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
