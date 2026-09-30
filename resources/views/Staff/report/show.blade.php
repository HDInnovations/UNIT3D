@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ __('staff.reports-log') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('staff.reports-log') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.reports.index') }}" class="breadcrumb__link">
            {{ __('staff.reports-log') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('staff-interface.report-details') }}</li>
@endsection

@section('page', 'page__staff-report--show')

@section('main')
    @if ($report->torrent)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.torrent') }} {{ __('torrent.title') }}</h2>
            <div class="panel__body">
                <a href="{{ route('torrents.show', ['id' => $report->torrent->id]) }}">
                    {{ $report->title }}
                </a>
            </div>
        </section>
    @endif

    @if ($report->request)
        <section class="panelV2">
            <h2 class="panel__heading">
                {{ __('torrent.torrent-request') }} {{ __('request.title') }}
            </h2>
            <div class="panel__body">
                <a href="{{ route('requests.show', ['torrentRequest' => $report->request]) }}">
                    {{ $report->title }}
                </a>
            </div>
        </section>
    @endif

    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.message') }}</h2>
        {{-- format-ignore-start --}}<div class="panel__body" style="white-space: pre-wrap">{{ $report->message }}</div>{{-- format-ignore-end --}}
    </section>
    @if (count($urls) > 0)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('staff-interface.referenced-links') }}:</h2>
            <div class="panel__body">
                <ul style="margin: 0; padding-left: 20px">
                    @foreach ($urls as $url)
                        <li>
                            <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($report->solved)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('staff-interface.verdict') }}</h2>
            {{-- format-ignore-start --}}<div class="panel__body" style="white-space: pre-wrap">{{ $report->verdict }}</div>{{-- format-ignore-end --}}
        </section>
    @else
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('staff-interface.resolve-report') }}</h2>
            <div class="panel__body">
                <form
                    class="form"
                    method="POST"
                    action="{{ route('staff.reports.update', ['report' => $report]) }}"
                >
                    @csrf
                    @method('PATCH')
                    @livewire('bbcode-input', ['name' => 'verdict', 'label' => __('staff-interface.verdict'), 'required' => true])
                    <p class="form__group">
                        <button class="form__button form__button--filled">
                            {{ __('common.submit') }}
                        </button>
                    </p>
                </form>
            </div>
        </section>
    @endif
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>ID</dt>
                <dd>{{ $report->id }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('ticket.category') }}</dt>
                <dd>{{ $report->type }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('common.created_at') }}</dt>
                <dd>{{ $report->created_at->toDisplayTimezone()->format('Y-m-d') }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('common.reporter') }}</dt>
                <dd>
                    <x-user-tag :anon="false" :user="$report->reporter" />
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('vltava.torrent.reported') }}</dt>
                <dd>
                    <x-user-tag :anon="false" :user="$report->reported" />
                </dd>
            </div>
            @if ($report->solved_by !== null)
                <div class="key-value__group">
                    <dt>{{ __('staff-interface.solved-by') }}</dt>
                    <dd>
                        <x-user-tag :anon="false" :user="$report->judge" />
                    </dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('ticket.closed') }}</dt>
                    <dd>
                        <time
                            datetime="{{ $report->solved_at }}"
                            title="{{ $report->solved_at }}"
                        >
                            {{ $report->solved_at?->toDisplayTimezone()->format('Y-m-d') }}
                        </time>
                    </dd>
                </div>
            @endif
        </dl>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.actions') }}</h2>
        <div class="panel__body">
            <form
                class="form form--horizontal"
                action="{{ route('staff.reports.assignee.store', ['report' => $report]) }}"
                method="POST"
                x-data
            >
                @csrf
                <p class="form__group">
                    <select
                        id="assigned_to"
                        name="assigned_to"
                        class="form__select"
                        x-on:change="$root.submit()"
                    >
                        <option hidden disabled selected value=""></option>
                        @foreach ($staff as $staffUser)
                            <option
                                value="{{ $staffUser->id }}"
                                @selected($staffUser->id === $report->assigned_to)
                            >
                                {{ $staffUser->username }}
                            </option>
                        @endforeach
                    </select>
                    <label class="form__label form__label--floating" for="assigned_to">
                        {{ __('ticket.assign') }}
                    </label>
                </p>
            </form>

            @if ($report->assigned_to !== null)
                <form
                    action="{{ route('staff.reports.assignee.destroy', ['report' => $report]) }}"
                    method="POST"
                >
                    @csrf
                    @method('DELETE')
                    <p class="form__group form__group--horizontal">
                        <button class="form__button form__button--filled form__button--centered">
                            {{ __('ticket.unassign') }}
                        </button>
                    </p>
                </form>
            @endif
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff-interface.snooze') }}</h2>
        @if ($report->snoozed_until !== null)
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>{{ __('staff-interface.snoozed-until') }}</dt>
                    <dd>{{ $report->snoozed_until }}</dd>
                </div>
            </dl>
        @endif

        <div class="panel__body">
            @if ($report->snoozed_until === null)
                <form
                    class="form"
                    action="{{ route('staff.snoozed_reports.store', ['report' => $report]) }}"
                    method="POST"
                    x-data
                    x-on:change="$root.submit()"
                >
                    @csrf
                    <p class="form__group">
                        <input
                            id="snoozed_days"
                            class="form__text"
                            name="snoozed_days"
                            placeholder=" "
                            inputmode="numeric"
                            pattern="[0-9]*"
                            type="text"
                        />
                        <label for="snoozed_days" class="form__label form__label--floating">
                            {{ __('staff-interface.custom-days') }}
                        </label>
                    </p>
                    <div class="form__group--short-horizontal">
                        <p class="form__group form__group--short-horizontal">
                            <button
                                name="snoozed_until"
                                value="{{ now()->addDays(1) }}"
                                class="form__button form__button--outlined form__button--centered"
                            >
                                {{ __('staff-interface.snooze-1-day') }}
                            </button>
                        </p>
                        <p class="form__group form__group--short-horizontal">
                            <button
                                name="snoozed_until"
                                value="{{ now()->addDays(3) }}"
                                class="form__button form__button--outlined form__button--centered"
                            >
                                {{ __('staff-interface.snooze-3-days') }}
                            </button>
                        </p>
                        <p class="form__group form__group--short-horizontal">
                            <button
                                name="snoozed_until"
                                value="{{ now()->addDays(7) }}"
                                class="form__button form__button--outlined form__button--centered"
                            >
                                {{ __('staff-interface.snooze-1-week') }}
                            </button>
                        </p>
                        <p class="form__group form__group--short-horizontal">
                            <button
                                name="snoozed_until"
                                value="{{ now()->addDays(14) }}"
                                class="form__button form__button--outlined form__button--centered"
                            >
                                {{ __('staff-interface.snooze-2-weeks') }}
                            </button>
                        </p>
                        <p class="form__group form__group--short-horizontal">
                            <button
                                name="snoozed_until"
                                value="{{ now()->addDays(28) }}"
                                class="form__button form__button--outlined form__button--centered"
                            >
                                {{ __('staff-interface.snooze-4-weeks') }}
                            </button>
                        </p>
                        <p class="form__group form__group--short-horizontal">
                            <button
                                name="snoozed_until"
                                value="{{ now()->addDays(56) }}"
                                class="form__button form__button--outlined form__button--centered"
                            >
                                {{ __('staff-interface.snooze-8-weeks') }}
                            </button>
                        </p>
                    </div>
                </form>
            @else
                <form
                    class="form"
                    action="{{ route('staff.snoozed_reports.destroy', ['report' => $report]) }}"
                    method="POST"
                >
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="snoozed_until" value="" />
                    <p class="form__group form__group--horizontal">
                        <button class="form__button form__button--centered form__button--filled">
                            <i class="{{ config('other.font-awesome') }} fa-clock"></i>
                            {{ __('staff-interface.unsnooze') }}
                        </button>
                    </p>
                </form>
            @endif
        </div>
    </section>
@endsection
