@extends('layout.with-main')

@section('title')
    <title>Commands - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Commands - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Commands</li>
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
            <h2 class="panel__heading">Maintenance mode</h2>
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
                            title="This commands enables maintenance mode while whitelisting only your IP Address."
                        >
                            Enable maintenance mode
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
                            title="This commands disables maintenance mode. Bringing the site backup for all to access."
                        >
                            Disable maintenance mode
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">Caching</h2>
            <div class="panel__body">
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/clear-cache') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="This commands clears your sites cache. This cache depends on what driver you are using."
                        >
                            Clear cache
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/clear-view-cache') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="This commands clears your sites compiled views cache."
                        >
                            Clear view cache
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
                            title="This commands clears your sites compiled routes cache."
                        >
                            Clear route cache
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
                            title="This commands clears your sites compiled configs cache."
                        >
                            Clear config cache
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/clear-all-cache') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="This commands clears ALL of your sites cache."
                        >
                            Clear all cache
                        </button>
                    </form>
                </div>
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/set-all-cache') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="This commands sets ALL of your sites cache."
                        >
                            Set all cache
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">Email</h2>
            <div class="panel__body">
                <div class="form__group form__group--horizontal">
                    <form method="POST" action="{{ url('/dashboard/commands/test-email') }}">
                        @csrf
                        <button
                            class="form__button form__button--text"
                            title="This commands tests your email configuration."
                        >
                            Send test email
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
