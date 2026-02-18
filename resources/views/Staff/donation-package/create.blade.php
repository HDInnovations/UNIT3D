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
    <li class="breadcrumb--active">Create package</li>
@endsection

@section('page', 'page__staff-donation-package--create')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Add new package</h2>
        </header>
        <div class="data-table-wrapper">
            <form role="form" method="POST" action="{{ route('staff.packages.store') }}">
                @csrf
                <table class="data-table">
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
                                    placeholder="Name"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <textarea
                                    name="description"
                                    placeholder="Description"
                                    class="form__textarea"
                                ></textarea>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    step="1.00"
                                    name="cost"
                                    value=""
                                    placeholder="Cost"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="upload_value"
                                    value=""
                                    placeholder="nullable"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="invite_value"
                                    value=""
                                    placeholder="nullable"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="bonus_value"
                                    value=""
                                    placeholder="nullable"
                                    class="form__text"
                                />
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="donor_value"
                                    value=""
                                    placeholder="(empty for lifetime)"
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
                <button type="submit" class="form__button form__button--filled">Create</button>
            </form>
        </div>
    </section>
@endsection
