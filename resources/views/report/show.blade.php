@extends('layout.with-main-and-sidebar')

@section('title')
    <title>Reports - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Reports - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('reports.index') }}" class="breadcrumb__link">
            {{ __('common.reports') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('common.report') }} details</li>
@endsection

@section('page', 'page__staff-report--show')

@section('main')
    @if ($report->torrent)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('torrent.torrent') }}</h2>
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
                {{ __('torrent.torrent-request') }}
            </h2>
            <div class="panel__body">
                <a href="{{ route('requests.show', ['torrentRequest' => $report->request]) }}">
                    {{ $report->title }}
                </a>
            </div>
        </section>
    @endif

    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">
                <x-user-tag :user="$report->reporter" :anon="false" />
            </h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <time datetime="{{ $report->created_at }}" title="{{ $report->created_at }}">
                        {{ $report->created_at?->diffForHumans() }}
                    </time>
                </div>
            </div>
        </header>

        <div class="panel__body bbcode-rendered">
            @bbcode($report->message)
        </div>
    </section>

    @foreach ($report->replies as $reply)
        <section class="panelV2">
            <header class="panel__header">
                <h2 class="panel__heading">
                    <x-user-tag :user="$reply->user" :anon="false" />
                </h2>
                <div class="panel__actions">
                    <div class="panel__action">
                        <time
                            datetime="{{ $reply->created_at }}"
                            title="{{ $reply->created_at }}"
                        >
                            {{ $reply->created_at?->diffForHumans() }}
                        </time>
                    </div>
                </div>
            </header>

            <div class="panel__body bbcode-rendered">
                @bbcode($reply->content)
            </div>
        </section>
    @endforeach

    <section class="panelV2">
        <h2 class="panel__heading">Reply</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('reports.replies.store', ['report' => $report]) }}"
            >
                @csrf
                @livewire('bbcode-input', ['name' => 'content', 'label' => 'Reply', 'required' => true])
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.submit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
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
                <dd>{{ $report->created_at->format('Y-m-d') }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('common.reporter') }}</dt>
                <dd>
                    <x-user-tag :anon="false" :user="$report->reporter" />
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Reported</dt>
                <dd>
                    <x-user-tag
                        :anon="$report->torrent?->anon ?? $report->request?->anon ?? false"
                        :user="$report->reported"
                    />
                </dd>
            </div>
            @if ($report->solved_at !== null)
                <div class="key-value__group">
                    <dt>{{ __('ticket.closed') }}</dt>
                    <dd>
                        <time
                            datetime="{{ $report->solved_at }}"
                            title="{{ $report->solved_at }}"
                        >
                            {{ $report->solved_at?->format('Y-m-d') }}
                        </time>
                    </dd>
                </div>
            @endif
        </dl>
    </section>
    @if ($user->group->is_modo)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('common.actions') }}</h2>
            <div class="panel__body">
                <form
                    class="form form--horizontal"
                    action="{{ route('reports.assignee.store', ['report' => $report]) }}"
                    method="POST"
                    x-data
                >
                    @csrf
                    <p class="form__group">
                        <select
                            id="staff_id"
                            name="staff_id"
                            class="form__select"
                            x-on:change="$root.submit()"
                        >
                            <option hidden disabled selected value=""></option>
                            @foreach ($staff as $staffUser)
                                <option
                                    value="{{ $staffUser->id }}"
                                    @selected($staffUser->id === $report->staff_id)
                                >
                                    {{ $staffUser->username }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="staff_id">
                            {{ __('ticket.assign') }}
                        </label>
                    </p>
                </form>

                @if ($report->staff_id !== null)
                    <form
                        action="{{ route('reports.assignee.destroy', ['report' => $report]) }}"
                        method="POST"
                    >
                        @csrf
                        @method('DELETE')
                        <p class="form__group form__group--horizontal">
                            <button
                                class="form__button form__button--filled form__button--centered"
                            >
                                {{ __('ticket.unassign') }}
                            </button>
                        </p>
                    </form>
                @endif
            </div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">Snooze</h2>
            @if ($report->snoozed_until !== null)
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>Snoozed until</dt>
                        <dd>{{ $report->snoozed_until }}</dd>
                    </div>
                </dl>
            @endif

            <div class="panel__body">
                @if ($report->snoozed_until === null)
                    <form
                        class="form"
                        action="{{ route('reports.snooze.store', ['report' => $report]) }}"
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
                                Custom days
                            </label>
                        </p>
                        <div class="form__group--short-horizontal">
                            <p class="form__group form__group--short-horizontal">
                                <button
                                    name="snoozed_until"
                                    value="{{ now()->addDays(1) }}"
                                    class="form__button form__button--outlined form__button--centered"
                                >
                                    1 day
                                </button>
                            </p>
                            <p class="form__group form__group--short-horizontal">
                                <button
                                    name="snoozed_until"
                                    value="{{ now()->addDays(3) }}"
                                    class="form__button form__button--outlined form__button--centered"
                                >
                                    3 days
                                </button>
                            </p>
                            <p class="form__group form__group--short-horizontal">
                                <button
                                    name="snoozed_until"
                                    value="{{ now()->addDays(7) }}"
                                    class="form__button form__button--outlined form__button--centered"
                                >
                                    1 week
                                </button>
                            </p>
                            <p class="form__group form__group--short-horizontal">
                                <button
                                    name="snoozed_until"
                                    value="{{ now()->addDays(14) }}"
                                    class="form__button form__button--outlined form__button--centered"
                                >
                                    2 weeks
                                </button>
                            </p>
                            <p class="form__group form__group--short-horizontal">
                                <button
                                    name="snoozed_until"
                                    value="{{ now()->addDays(28) }}"
                                    class="form__button form__button--outlined form__button--centered"
                                >
                                    4 weeks
                                </button>
                            </p>
                            <p class="form__group form__group--short-horizontal">
                                <button
                                    name="snoozed_until"
                                    value="{{ now()->addDays(56) }}"
                                    class="form__button form__button--outlined form__button--centered"
                                >
                                    8 weeks
                                </button>
                            </p>
                        </div>
                    </form>
                @else
                    <form
                        class="form"
                        action="{{ route('reports.snooze.destroy', ['report' => $report]) }}"
                        method="POST"
                    >
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="snoozed_until" value="" />
                        <p class="form__group form__group--horizontal">
                            <button
                                class="form__button form__button--centered form__button--filled"
                            >
                                <i class="{{ config('other.font-awesome') }} fa-clock"></i>
                                Unsnooze
                            </button>
                        </p>
                    </form>
                @endif
            </div>
        </section>
    @endif
@endsection
