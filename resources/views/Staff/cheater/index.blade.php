@extends('layout.with-main')

@section('title')
    <title>
        Possible leech cheaters - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}
    </title>
@endsection

@section('meta')
    <meta
        name="description"
        content="Possible leech cheaters - {{ __('staff.staff-dashboard') }}"
    />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('staff.possible-leech-cheaters') }}
    </li>
@endsection

@section('page', 'page__staff-cheater--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff.possible-leech-cheaters') }} (Ghost leechers)</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.user') }}</th>
                        <th>{{ __('user.member-since') }}</th>
                        <th>{{ __('user.last-login') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cheaters as $cheater)
                        <tr>
                            <td>
                                <x-user-tag :anon="false" :user="$cheater" />
                            </td>
                            <td>
                                <time
                                    datetime="{{ $cheater->created_at }}"
                                    title="{{ $cheater->created_at }}"
                                >
                                    {{ $cheater->created_at ?? 'N/A' }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $cheater->last_login }}"
                                    title="{{ $cheater->last_login }}"
                                >
                                    {{ $cheater->last_login ?? 'N/A' }}
                                </time>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $cheaters->links('partials.pagination') }}
    </section>
@endsection
