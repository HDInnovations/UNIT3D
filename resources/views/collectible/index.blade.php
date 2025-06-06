@extends('layout.with-main')

@section('title')
    <title>{{ __('user.badges') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('collectibles.index') }}" class="breadcrumb__link">
            {{ __('user.badges') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.badges') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link nav-tab--active__link" href="{{ route('collectibles.index') }}">
            {{ __('common.view-all') }} {{ __('user.badges') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a
            class="nav-tab__link"
            href="{{ route('users.collectibles.index', ['user' => auth()->user()]) }}"
        >
            {{ __('common.my') }} {{ __('user.badges') }}
        </a>
    </li>
@endsection

@section('page', 'page__stats--overview')

@section('main')
    @foreach ($collectibles as $collectibleCategory)
        <section class="panelV2">
            <h2 class="panel__heading">{{ $collectibleCategory->name }}</h2>
            <div class="panel__body">
                <ul class="collectible-card__list">
                    @forelse ($collectibleCategory->collectibles as $collectible)
                        <li class="collectible-card__list-item">
                            <a
                                href="{{ route('collectibles.show', ['collectible' => $collectible]) }}"
                                class="collectible-card"
                            >
                                <h2 class="collectible-card__heading">
                                    <img
                                        src="{{ $collectible->icon === null ? '' : route('authenticated_images.collectible_icon', ['collectible' => $collectible]) }}"
                                        alt="No icon"
                                    />
                                </h2>
                                <h3 class="collectible-card__subheading">
                                    {{ $collectible->name }}
                                </h3>
                                <h4 class="collectible-card__subheading">
                                    {{ $collectible->description }}
                                </h4>
                                @if ($collectible->items()->count() -
                                    $collectible
                                        ->items()
                                        ->whereNotNull('user_id')
                                        ->count() <
                                    5 &&
                                    $collectible->items()->count() -
                                        $collectible
                                            ->items()
                                            ->whereNotNull('user_id')
                                            ->count() >
                                        0)
                                    <h4
                                        class="collectible-card__subheading collectible-card__banner--last"
                                        title="Last chance!"
                                    >
                                        Last
                                    </h4>
                                @elseif ($collectible->items()->whereNull('user_id')->count() > 0)
                                    <h4
                                        class="collectible-card__subheading collectible-card__banner--available"
                                    >
                                        In Stock
                                    </h4>
                                @endif
                            </a>
                        </li>
                    @empty
                        <p>No items in this {{ __('common.category') }}.</p>
                    @endforelse
                </ul>
            </div>
        </section>
    @endforeach
@endsection
