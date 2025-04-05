@extends('layout.with-main')

@section('title')
    <title>{{ __('stat.stats') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('stats') }}" class="breadcrumb__link">
            {{ __('stat.stats') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('stat.groups') }}
    </li>
@endsection

@section('nav-tabs')
    @include('partials.statsgroupmenu')
@endsection

@section('page', 'page__stats--groups')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('stat.groups') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.group') }}</th>
                        <th>{{ __('common.users') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groups as $group)
                        <tr>
                            <td>
                                <span style="color: {{ $group->color }}">
                                    <i class="{{ $group->icon }}"></i>
                                    {{ $group->name }}
                                </span>
                            </td>
                            <td>{{ $group->users_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
