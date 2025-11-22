@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('common.upload') }} {{ __('common.contests') }}</li>
@endsection

@section('page', 'page__staff-upload-contest--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('common.upload') }} {{ __('common.contests') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('staff.upload_contests.create') }}"
                    >
                        {{ __('common.add') }}
                    </a>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.starts-at') }}</th>
                        <th>{{ __('common.ends-at') }}</th>
                        <th>{{ __('common.active') }}</th>
                        <th>{{ __('common.awarded') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($uploadContests as $uploadContest)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('staff.upload_contests.edit', ['uploadContest' => $uploadContest]) }}"
                                >
                                    {{ $uploadContest->name }}
                                </a>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $uploadContest->starts_at }}"
                                    title="{{ $uploadContest->starts_at }}"
                                >
                                    {{ $uploadContest->starts_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $uploadContest->ends_at }}"
                                    title="{{ $uploadContest->ends_at }}"
                                >
                                    {{ $uploadContest->ends_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                @if ($uploadContest->active)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                @if ($uploadContest->awarded)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                @endif
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.upload_contests.edit', ['uploadContest' => $uploadContest]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.upload_contests.destroy', ['uploadContest' => $uploadContest]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this upload contest: ' . $uploadContest->name . '?') }}"
                                                class="form__button form__button--text"
                                            >
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
