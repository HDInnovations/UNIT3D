@extends('layout.with-main')

@section('title')
    <title>Collectibles - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('collectibles.index') }}" class="breadcrumb__link">
            {{ __('user.badges') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        <a
            href="{{ route('collectibles.show', ['collectible' => $collectibleItem->collectible]) }}"
            class="breadcrumb__link"
        >
            {{ $collectibleItem->collectible->name }}
        </a>
    </li>
    <li class="breadcrumb--active">Add Sell Offer</li>
@endsection

@section('page', 'page__stats--overview')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">Add Sell Offer</h2>
        </header>
        <div class="data-table-wrapper">
            <form
                role="form"
                method="POST"
                action="{{ route('collectibles.offer.store', ['collectible' => $collectibleItem->collectible]) }}"
            >
                @csrf
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('bon.item') }}</th>
                            <th>Bought for</th>
                            <th>Sell for</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $collectibleItem->collectible->name }}</td>
                            <td>{{ $collectibleItem->userTransaction->price ?? 'N/A' }}</td>
                            <td>
                                <input
                                    type="number"
                                    name="offer[price]"
                                    value=""
                                    placeholder="1000"
                                    class="form__text"
                                    required
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" class="form__button form__button--filled">Sell</button>
            </form>
        </div>
    </section>
@endsection
