@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.achievements') }}
    </li>
@endsection

@section('page', 'page__staff-achievement--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('user.achievements') }}</h2>
            <div class="panel__actions">
                <a
                    href="{{ route('staff.achievements.create') }}"
                    class="panel__action"
                >
                    {{ __('common.add') }}
                </a>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('common.category') }}</th>
                        <th>{{ __('common.type') }}</th>
                        <th>{{ __('user.tiers') }}</th>
                        <th>{{ __('common.hidden') }}</th>
                        <th>{{ __('common.enabled') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($achievements as $achievement)
                        <tr>
                            <td>
                                @if ($achievement->icon_path)
                                    <img
                                        class="achievement-icon-thumb"
                                        src="{{ route('authenticated_images.achievement_image', ['achievement' => $achievement]) }}"
                                        alt="{{ $achievement->name }}"
                                    />
                                @endif
                                {{ $achievement->name }}
                            </td>
                            <td>{{ $achievement->category }}</td>
                            <td>{{ $achievement->type }}</td>
                            <td>{{ $achievement->tiers->count() }}</td>
                            <td>{{ $achievement->is_hidden ? __('common.yes') : __('common.no') }}</td>
                            <td>{{ $achievement->enabled ? __('common.yes') : __('common.no') }}</td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.achievements.edit', ['achievement' => $achievement]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.achievements.destroy', ['achievement' => $achievement]) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button class="form__button form__button--text">
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
