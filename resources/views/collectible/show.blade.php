@extends('layout.with-main-and-sidebar')

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
        {{ $collectible->name }}
    </li>
@endsection

@section('page', 'page__stats--overview')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $collectible->name }}</h2>
        <img
            class="collectible__icon--centered"
            src="{{ $collectible->icon === null ? '' : route('authenticated_images.collectible_icon', ['collectible' => $collectible]) }}"
        />
        <dl class="key-value">
            <div class="key-value__group">
                <dt>Part of</dt>
                <dd>{{ $collectible->category->name }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Description</dt>
                <dd>{{ $collectible->description }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Total quantity limit</dt>
                <dd>{{ $collectible->items->count() }}</dd>
            </div>
            <div class="key-value__group">
                <dt>Available in stock</dt>
                <dd>
                    @if ($availableCount > 0)
                        {{ $availableCount }}
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-times text-red"></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Initial Price</dt>
                <dd>{{ $collectible->price }} {{ __('bon.bon') }}</dd>
            </div>
        </dl>

        @if (! $userOwns && $collectible->in_stock && $userMeetsAllRequirements)
            <form
                role="form"
                method="POST"
                action="{{ route('collectibles.transaction.create', ['collectible' => $collectible]) }}"
            >
                @csrf
                <p class="form__group form__group--horizontal">
                    <button class="form__button form__button--filled form__button--centered">
                        Buy from Store for
                        {{ \number_format($collectible->price, 0, null, "\u{202F}") }}
                        {{ __('bon.bon') }}
                    </button>
                </p>
            </form>
        @endif

        @if ($userOwns)
            <p class="form__group form__group--horizontal">
                <a
                    class="form__button form__button--filled form__button--centered"
                    href="{{ route('collectibles.offer.create', ['collectible' => $collectible]) }}"
                    role="button"
                    @if (! $userOwns || $collectible->in_stock || $userIsSelling || ! $collectible->resell)
                        disabled
                    @endif
                >
                    Sell
                </a>
            </p>
        @endif
    </section>

    @if ($userOwns)
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.statistics') }}</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>Bought at</dt>
                    <dd>
                        <time
                            datetime="{{ $userTransaction->created_at }}"
                            title="{{ $userTransaction->created_at }}"
                        >
                            {{ $userTransaction->created_at->diffForHumans() }}
                        </time>
                    </dd>
                </div>
                <div class="key-value__group">
                    <dt>Bought price</dt>
                    <dd>{{ $userTransaction->price }} {{ __('bon.bon') }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>Bought from</dt>
                    <dd>
                        <x-user-tag
                            :user="$userTransaction->seller"
                            :anon="$userTransaction->seller->privacy?->private_profile"
                        />
                    </dd>
                </div>
            </dl>
        </section>
    @endif

    <section class="panelV2" x-data="toggle">
        <h2 class="panel__heading" style="cursor: pointer" x-on:click="toggle">
            Price History
            <i
                class="{{ config('other.font-awesome') }} fa-plus-circle fa-pull-right"
                x-show="isToggledOff"
            ></i>
            <i
                class="{{ config('other.font-awesome') }} fa-minus-circle fa-pull-right"
                x-show="isToggledOn"
                x-cloak
            ></i>
        </h2>
        <div class="chart-wrapper" x-show="isToggledOn" x-cloak>
            <div style="padding: 25px">
                <canvas id="dailyPriceHistory"></canvas>
            </div>
        </div>
    </section>

    <section class="panelV2">
        <h2 class="panel__heading">Offers</h2>
        <div class="panel__body">
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>{{ __('user.user') }}</th>
                            <th>Price</th>
                            <th>{{ __('common.created_at') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($offers as $offer)
                            <tr>
                                <td>{{ $offer->collectible->name }}</td>
                                <td>
                                    <x-user-tag
                                        :user="$offer->seller"
                                        :anon="$offer->seller->privacy?->private_profile"
                                    />
                                </td>
                                <td>{{ $offer->price }}</td>
                                <td>{{ $offer->created_at }}</td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('collectibles.offer.update', ['collectibleOffer' => $offer]) }}"
                                                method="POST"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    x-on:click.prevent="confirmAction"
                                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to accept this offer: ' . $offer->collectible->name . '?') }}"
                                                    class="form__button form__button--filled"
                                                    @if (auth()->id() === $offer->seller->id || $userOwns || ! $userMeetsAllRequirements)
                                                        disabled
                                                    @endif
                                                >
                                                    Buy
                                                </button>
                                            </form>
                                        </li>
                                        @if (auth()->id() === $offer->seller->id || auth()->user()->group->is_modo)
                                            <li class="data-table__action">
                                                <form
                                                    action="{{ route('collectibles.offer.destroy', ['collectibleOffer' => $offer]) }}"
                                                    method="POST"
                                                    x-data="confirmation"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        x-on:click.prevent="confirmAction"
                                                        data-b64-deletion-message="{{ base64_encode('Are you sure you want to cancel this offer: ' . $offer->collectible->name . '?') }}"
                                                        class="form__button form__button--filled"
                                                    >
                                                        {{ __('common.cancel') }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </menu>
                                </td>
                            </tr>
                        @empty
                            <p>There are no offers for this item.</p>
                            <p>
                                User-to-user offers are only permitted when this item is out of
                                stock.
                            </p>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">Buy requirements</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>Is not possessed?</dt>
                <dd>
                    @if (! $userOwns)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-times text-red"></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Min. {{ __('common.account') }} {{ __('common.upload') }}</dt>
                <dd>
                    @if ($user->uploaded >= $requirements->min_uploaded ?? 0)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-x text-red"></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Min. {{ __('torrent.seedsize') }}</dt>
                <dd>
                    @if ($userSeedSize >= $requirements->min_seedsize ?? 0)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-x text-red"></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Min. Average {{ __('torrent.seedtime') }}</dt>
                <dd>
                    @if ($userAvgSeedtime >= $requirements->min_avg_seedtime ?? 0)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-x text-red"></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Min. {{ __('common.ratio') }}</dt>
                <dd>
                    @if ($user->ratio >= $requirements->min_ratio ?? 0)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-x text-red"></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Min. {{ __('common.account') }} {{ __('torrent.age') }}</dt>
                <dd>
                    @if ($user->created_at->diffInSeconds(now()) >= $requirements->min_age ?? 0)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-x text-red"></i>
                    @endif
                </dd>
            </div>
        </dl>
    </section>

    <section class="panelV2">
        <h2 class="panel__heading">Sell requirements</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>Is possessed?</dt>
                <dd>
                    @if ($userOwns)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-times text-red"></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Reselling is allowed?</dt>
                <dd>
                    @if ($collectible->resell)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i class="{{ config('other.font-awesome') }} fa-times text-red"></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Is not in stock?</dt>
                <dd>
                    @if (! $collectible->in_stock)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i
                            class="{{ config('other.font-awesome') }} fa-times text-red"
                            title="The item must not be in stock in order to resell it"
                        ></i>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>Not already selling?</dt>
                <dd>
                    @if (! $userIsSelling)
                        <i class="{{ config('other.font-awesome') }} fa-check text-green"></i>
                    @else
                        <i
                            class="{{ config('other.font-awesome') }} fa-times text-red"
                            title="This item already has an active offer to sell."
                        ></i>
                    @endif
                </dd>
            </div>
        </dl>
    </section>
@endsection

@section('scripts')
    @vite('resources/js/vendor/chart.js')
    <script nonce="{{ HDVinnie\SecureHeaders\SecureHeaders::nonce('script') }}">
        document.addEventListener('DOMContentLoaded', function () {
            const transactions = {!! Js::from($transactions) !!};

            // Transaction History Chart
            const dailyCtx = document.getElementById('dailyPriceHistory').getContext('2d');
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: transactions.map((transaction) => transaction.created_at),
                    datasets: [
                        {
                            label: 'Price History last 50 Transactions',
                            data: transactions.map((transaction) => transaction.price),
                            borderWidth: 1,
                            fill: false,
                        },
                    ],
                },
                options: {
                    scales: {
                        x: { type: 'time', time: { unit: 'month' } },
                        y: { beginAtZero: true },
                    },
                },
            });
        });
    </script>
@endsection
