@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('staff-interface.packages') }}</li>
@endsection

@section('page', 'page__staff-donation-package--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('staff-interface.packages') }}</h2>
            <div class="panel__actions">
                <a
                    class="panel__action form__button form__button--text"
                    href="{{ route('staff.packages.create') }}"
                >
                    {{ __('common.add') }}
                </a>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.position') }}</th>
                        <th>{{ __('common.name') }}</th>
                        <th>{{ __('staff-interface.cost') }}</th>
                        <th>{{ __('staff-interface.upload-gib-header') }}</th>
                        <th>{{ __('staff-interface.invite-count-header') }}</th>
                        <th>{{ __('staff-interface.bonus-count-header') }}</th>
                        <th>{{ __('staff-interface.supporter-days-header') }}</th>
                        <th>{{ __('common.active') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages as $package)
                        <tr>
                            <td>{{ $package->position }}</td>
                            <td>
                                <a
                                    href="{{ route('staff.packages.edit', ['package' => $package]) }}"
                                >
                                    {{ $package->name }}
                                </a>
                            </td>
                            <td>$ {{ $package->cost }}</td>
                            <td>
                                {{ App\Helpers\StringHelper::formatBytes($package->upload_value ?? 0) }}
                            </td>
                            <td>{{ $package->invite_value ?? 0 }}</td>
                            <td>{{ $package->bonus_value ?? 0 }}</td>
                            <td>
                                @if ($package->donor_value === null)
                                    {{ __('staff-interface.lifetime') }}
                                @else
                                    {{ $package->donor_value }} {{ __('vltava.torrent.days') }}
                                @endif
                            </td>
                            <td class="{{ $package->is_active ? 'text-green' : 'text-red' }}">
                                @if ($package->is_active)
                                    {{ __('common.yes') }}
                                @else
                                    {{ __('common.no') }}
                                @endif
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.packages.edit', ['package' => $package]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.packages.destroy', ['package' => $package]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode(__('staff-interface.delete-package-confirmation', ['name' => $package->name])) }}"
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
        {{ $packages->links('partials.pagination') }}
    </section>
@endsection
