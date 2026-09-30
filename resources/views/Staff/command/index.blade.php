@extends('layout.with-main')

@section('title')
    <title>
        {{ __('staff-interface.commands') }} - {{ __('staff.staff-dashboard') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="{{ __('staff-interface.commands') }} - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('staff-interface.commands') }}</li>
@endsection

@section('page', 'page__staff-command--index')

@section('main')
    <div
        style="
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 2rem;
        "
    >
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('staff-interface.maintenance-mode') }}</h2>
            <div class="panel__body">
                <div class="form__group form__group--horizontal">
                    <form
                        role="form"
                        method="POST"
                        action="{{ url('/dashboard/commands/maintenance-enable') }}"
                    >
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.maintenance-enable-hint') }}"
                        >
                            {{ __('staff-interface.enable-maintenance-mode') }}
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form
                        role="form"
                        method="POST"
                        action="{{ url('/dashboard/commands/maintenance-disable') }}"
                    >
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.maintenance-disable-hint') }}"
                        >
                            {{ __('staff-interface.disable-maintenance-mode') }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('staff-interface.caching') }}</h2>
            <div class="panel__body">
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/clear-cache') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.clear-cache-hint') }}"
                        >
                            {{ __('staff-interface.clear-cache') }}
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/clear-view-cache') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.clear-view-cache-hint') }}"
                        >
                            {{ __('staff-interface.clear-view-cache') }}
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form
                        method="POST"
                        action="{{ url('/dashboard/commands/clear-route-cache') }}"
                    >
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.clear-route-cache-hint') }}"
                        >
                            {{ __('staff-interface.clear-route-cache') }}
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form
                        method="POST"
                        action="{{ url('/dashboard/commands/clear-config-cache') }}"
                    >
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.clear-config-cache-hint') }}"
                        >
                            {{ __('staff-interface.clear-config-cache') }}
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/clear-all-cache') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.clear-all-cache-hint') }}"
                        >
                            {{ __('staff-interface.clear-all-cache') }}
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/set-all-cache') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.set-all-cache-hint') }}"
                        >
                            {{ __('staff-interface.set-all-cache') }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('common.email') }}</h2>
            <div class="panel__body">
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/test-email') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="{{ __('staff-interface.test-email-hint') }}"
                        >
                            {{ __('staff-interface.send-test-email') }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
