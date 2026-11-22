@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">Gateways</li>
@endsection

@section('page', 'page__staff-donation-gateway--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Gateways</h2>
            <div class="panel__actions">
                <a
                    class="panel__action form__button form__button--text"
                    href="{{ route('staff.gateways.create') }}"
                >
                    {{ __('common.add') }}
                </a>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Active</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gateways as $gateway)
                        <tr>
                            <td>{{ $gateway->position }}</td>
                            <td>
                                <a
                                    href="{{ route('staff.gateways.edit', ['gateway' => $gateway]) }}"
                                >
                                    {{ $gateway->name }}
                                </a>
                            </td>
                            <td>{{ $gateway->address }}</td>
                            <td class="{{ $gateway->is_active ? 'text-green' : 'text-red' }}">
                                @if ($gateway->is_active)
                                    {{ __('common.yes') }}
                                @else
                                    {{ __('common.no') }}
                                @endif
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.gateways.edit', ['gateway' => $gateway]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.gateways.destroy', ['gateway' => $gateway]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this page: ' . $gateway->name . '?') }}"
                                                class="form__button form__button--text"
                                            >
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
        {{ $gateways->links('partials.pagination') }}
    </section>
@endsection
