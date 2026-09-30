@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.packages.index') }}" class="breadcrumb__link">
            {{ __('staff-interface.packages') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('staff-interface.create-package') }}</li>
@endsection

@section('page', 'page__staff-donation-package--create')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('staff-interface.add-new-package') }}</h2>
        </header>
        <div class="data-table-wrapper">
            <form role="form" method="POST" action="{{ route('staff.packages.store') }}">
                @csrf
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.position') }}</th>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('common.description') }}</th>
                            <th>{{ __('staff-interface.cost') }}</th>
                            <th>{{ __('staff-interface.upload-bytes-header') }}</th>
                            <th>{{ __('staff-interface.invite-count-header') }}</th>
                            <th>{{ __('staff-interface.bonus-count-header') }}</th>
                            <th>{{ __('staff-interface.supporter-days-header') }}</th>
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
                                <textarea
                                    name="description"
                                    placeholder="{{ __('common.description') }}"
                                    class="form__textarea"
                                ></textarea>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    step="1.00"
                                    name="cost"
                                    value=""
                                    placeholder="{{ __('staff-interface.cost') }}"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="upload_value"
                                    value=""
                                    placeholder="{{ __('staff-interface.nullable-placeholder') }}"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="invite_value"
                                    value=""
                                    placeholder="{{ __('staff-interface.nullable-placeholder') }}"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="bonus_value"
                                    value=""
                                    placeholder="{{ __('staff-interface.nullable-placeholder') }}"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="donor_value"
                                    value=""
                                    placeholder="{{ __('staff-interface.empty-for-lifetime-placeholder') }}"
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
