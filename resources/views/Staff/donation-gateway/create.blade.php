@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.gateways.index') }}" class="breadcrumb__link">
            {{ __('staff-interface.gateways') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('staff-interface.create-gateway') }}</li>
@endsection

@section('page', 'page__staff-donation-gateway--create')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('staff-interface.add-new-gateway') }}</h2>
        </header>
        <div class="data-table-wrapper">
            <form role="form" method="POST" action="{{ route('staff.gateways.store') }}">
                @csrf
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.position') }}</th>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('staff-interface.address') }}</th>
                            <th>{{ __('common.active') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input
                                    type="number"
                                    name="position"
                                    value=""
                                    placeholder="0"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    name="name"
                                    value=""
                                    placeholder="{{ __('common.name') }}"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    name="address"
                                    value=""
                                    placeholder="{{ __('staff-interface.address') }}"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input name="is_active" type="hidden" value="0" />
                                <input
                                    id="is_active"
                                    class="form__checkbox"
                                    name="is_active"
                                    type="checkbox"
                                    value="1"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" class="form__button form__button--filled">
                    {{ __('common.create') }}
                </button>
            </form>
        </div>
    </section>
@endsection
