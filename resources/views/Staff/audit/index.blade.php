@extends('layout.with-main')

@section('title')
    <title>Audits log - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Audits log - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.audit-log') }}
    </li>
@endsection

@section('page', 'page__staff-audit--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.staff') }} {{ __('common.stats') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('stat.last30days') }}</th>
                        <th>{{ __('stat.last60days') }}</th>
                        <th>{{ __('stat.all-time') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($staffUsers as $staffUser)
                        <tr>
                            <td>
                                <x-user-tag :anon="false" :user="$staffUser" />
                            </td>
                            <td>{{ $staffUser->last_30_days }}</td>
                            <td>{{ $staffUser->last_60_days }}</td>
                            <td>{{ $staffUser->total_actions }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @livewire('audit-log-search')
@endsection
