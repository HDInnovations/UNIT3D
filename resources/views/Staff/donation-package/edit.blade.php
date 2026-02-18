@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.packages.index') }}" class="breadcrumb__link">Packages</a>
    </li>
    <li class="breadcrumb--active">Edit package</li>
@endsection

@section('page', 'page__staff-donation-package--edit')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Edit: {{ $package->name }}</h2>
        </header>
        <div class="data-table-wrapper">
            <form
                role="form"
                method="POST"
                action="{{ route('staff.packages.update', ['package' => $package]) }}"
            >
                @csrf
                @method('PATCH')
                <div class="table-responsive">
                    <table class="table table-condensed table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Cost ({{ config('donation.currency') }})</th>
                                <th>Upload (Bytes)</th>
                                <th>Invite (#)</th>
                                <th>Bonus (#)</th>
                                <th>Supporter (Days)</th>
                                <th>Active</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <input
                                        type="number"
                                        name="position"
                                        value="{{ $package->position }}"
                                        class="form__text"
                                    />
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ $package->name }}"
                                        class="form__text"
                                    />
                                </td>
                                <td>
                                    <textarea name="description" class="form__textarea">
{{ $package->description }}</textarea
                                    >
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        step="1.00"
                                        name="cost"
                                        value="{{ $package->cost }}"
                                        class="form__text"
                                    />
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="upload_value"
                                        value="{{ $package->upload_value }}"
                                        class="form__text"
                                    />
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="invite_value"
                                        value="{{ $package->invite_value }}"
                                        class="form__text"
                                    />
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="bonus_value"
                                        value="{{ $package->bonus_value }}"
                                        class="form__text"
                                    />
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="donor_value"
                                        value="{{ $package->donor_value }}"
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
                                        @checked($package->is_active)
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="form__button form__button--filled">Update</button>
            </form>
        </div>
    </section>
@endsection
