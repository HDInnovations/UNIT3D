@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Torrent distributors</li>
@endsection

@section('page', 'page__staff-distributor--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Torrent distributors</h2>
            <div class="panel__actions">
                <a
                    href="{{ route('staff.distributors.create') }}"
                    class="panel__action form__button form__button--text"
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
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($distributors as $distributor)
                        <tr>
                            <td>
                                <a
                                    href="{{ route('staff.distributors.edit', ['distributor' => $distributor]) }}"
                                >
                                    {{ $distributor->name }}
                                </a>
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.distributors.edit', ['distributor' => $distributor]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.distributors.delete', ['distributor' => $distributor]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.delete') }}
                                        </a>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">No distributors</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
