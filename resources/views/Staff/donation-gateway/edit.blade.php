@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.gateways.index') }}" class="breadcrumb__link">Gateways</a>
    </li>
    <li class="breadcrumb--active">Edit gateway</li>
@endsection

@section('page', 'page__staff-donation-gateway--edit')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Edit: {{ $gateway->name }}</h2>
        </header>
        <div class="data-table-wrapper">
            <form
                role="form"
                method="POST"
                action="{{ route('staff.gateways.update', ['gateway' => $gateway]) }}"
            >
                @csrf
                @method('PATCH')
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Active</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    pattern="[0-9]+"
                                    name="position"
                                    required
                                    value="{{ $gateway->position }}"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    name="name"
                                    required
                                    value="{{ $gateway->name }}"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    name="address"
                                    required
                                    value="{{ $gateway->address }}"
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
                                    @checked($gateway->is_active)
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" class="form__button form__button--filled">Update</button>
            </form>
        </div>
    </section>
@endsection
