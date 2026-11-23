@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('common.blacklist') }}
    </li>
@endsection

@section('page', 'page__blacklist--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('page.blacklist-clients') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Client name</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                @foreach ($clients as $client)
                    <tr>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->reason }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <div class="panel__body">
            {{ __('page.blacklist-desc', ['title' => config('other.title')]) }}
        </div>
    </section>
@endsection
