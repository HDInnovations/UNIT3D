@extends('layout.with-main')

@section('title')
    <title>{{ __('common.my') }} {{ __('user.badges') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('collectibles.index') }}" class="breadcrumb__link">
            {{ __('user.badges') }}
        </a>
    </li>
    <li class="breadcrumb--active">{{ __('common.my') }} {{ __('user.badges') }}</li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('collectibles.index') }}">
            {{ __('common.view-all') }} {{ __('user.badges') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a
            class="nav-tab__link nav-tab--active__link"
            href="{{ route('users.collectibles.index', ['user' => $user]) }}"
        >
            {{ __('common.my') }} {{ __('user.badges') }}
        </a>
    </li>
@endsection

@section('page', 'page__stats--overview')

@section('main')
    @foreach ($collectibleCategories as $collectibleCategory)
        <section class="panelV2">
            <h2 class="panel__heading">{{ $collectibleCategory->name }}</h2>
            <div class="panel__body">
                <ul class="mediahub-card__list">
                    @php
                        // Retrieve collectibles for this category
                        $categoryCollectibles = $collectiblesByCategory->get($collectibleCategory->id, collect());
                    @endphp

                    @if ($categoryCollectibles->isEmpty())
                        <p>No items in this {{ __('common.category') }}.</p>
                    @else
                        @foreach ($categoryCollectibles as $collectible)
                            <li class="mediahub-card__list-item">
                                <a
                                    href="{{ route('collectibles.show', ['collectible' => $collectible]) }}"
                                    class="mediahub-card"
                                >
                                    <h2 class="mediahub-card__heading">
                                        <img
                                            src="{{ $collectible->icon === null ? '' : route('authenticated_images.collectible_icon', ['collectible' => $collectible]) }}"
                                        />
                                    </h2>
                                    <h3 class="mediahub-card__subheading">
                                        {{ $collectible->name }}
                                    </h3>
                                    <h4 class="mediahub-card__subheading">
                                        {{ $collectible->description }}
                                    </h4>
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </section>
    @endforeach
@endsection
