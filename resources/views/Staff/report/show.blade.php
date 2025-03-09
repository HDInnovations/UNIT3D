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
        <a href="{{ route('staff.reports.index') }}" class="breadcrumb__link">
            {{ __('staff.reports-log') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('common.report') }} Details</li>
@endsection

@section('page', 'page__poll--show')

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
            <h2 class="panel__heading">Referenced Links:</h2>
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

    <livewire:comments :model="$report" />

    @if ($report->solved)
        <section class="panelV2">
            <h2 class="panel__heading">Verdict</h2>
            {{-- format-ignore-start --}}<div class="panel__body" style="white-space: pre-wrap">{{ $report->verdict }}</div>{{-- format-ignore-end --}}
        </section>
    @else
        <section class="panelV2">
            <h2 class="panel__heading">Resolve {{ __('common.report') }}</h2>
            <div class="panel__body">
                <form
                    class="form"
                    method="POST"
                    action="{{ route('staff.reports.update', ['report' => $report]) }}"
                >
                    @csrf
                    @method('PATCH')
                    @livewire('bbcode-input', ['name' => 'verdict', 'label' => 'Verdict', 'required' => true])
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
        <h2 class="panel__heading">Reported {{ __('common.user') }}</h2>
        <div class="panel__body">
            <x-user_tag :anon="false" :user="$report->reported" />
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.reporter') }}</h2>
        <div class="panel__body">
            <x-user_tag :anon="false" :user="$report->reporter" />
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">Solved by</h2>
        <div class="panel__body">
            @if ($report->solved)
                <x-user_tag :anon="false" :user="$report->staff" />
            @else
                <span class="text-red">
                    <i class="{{ config('other.font-awesome') }} fa-times"></i>
                    UNSOLVED
                </span>
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
                            Custom Days
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
                    action="{{ route('staff.snoozed_reports.destroy', ['report' => $report]) }}"
                    method="POST"
                >
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="snoozed_until" value="" />
                    <p class="form__group form__group--horizontal">
                        <button class="form__button form__button--centered form__button--filled">
                            <i class="{{ config('other.font-awesome') }} fa-clock"></i>
                            Unsnooze
                        </button>
                    </p>
                </form>
            @endif
        </div>
    </section>
@endsection
