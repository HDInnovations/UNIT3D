@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ $user->username }} {{ __('user.achievements') }} - {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.achievements') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-achievement--index')

@if (auth()->user()->isAllowed($user, 'achievement', 'show_achievement'))
    @section('main')
        <section class="panelV2 achievements__unlocked">
            <h2 class="panel__heading">{{ __('user.unlocked-achievements') }}</h2>
            @foreach ($achievements->load('details') as $achievement)
                <article
                    class="achievement"
                    title="{{ $achievement->points }}/{{ $achievement->details->points }}"
                >
                    <figure class="achievement__badge">
                        <img
                            src="/img/badges/{{ $achievement->details->name }}.png"
                            alt="{{ $achievement->details->name }}"
                            title="{{ $achievement->details->name }}"
                        />
                        <figcaption class="achievement__description">
                            {{ $achievement->details->description }}
                        </figcaption>
                    </figure>
                    <progress
                        class="achievement__progress"
                        max="{{ $achievement->details->points }}"
                        value="{{ $achievement->points }}"
                    ></progress>
                </article>
            @endforeach
        </section>
        <section class="panelV2 achievements__pending">
            <h2 class="panel__heading">{{ __('user.pending-achievements') }}</h2>
            @foreach ($pending->load('details') as $achievement)
                <article
                    class="achievement"
                    title="{{ $achievement->points }}/{{ $achievement->details->points }}"
                >
                    <figure class="achievement__badge">
                        <img
                            src="/img/badges/{{ $achievement->details->name }}.png"
                            alt="{{ $achievement->details->name }}"
                            title="{{ $achievement->details->name }}"
                        />
                        <figcaption class="achievement__description">
                            {{ $achievement->details->description }}
                        </figcaption>
                    </figure>
                    <progress
                        class="achievement__progress"
                        max="{{ $achievement->details->points }}"
                        value="{{ $achievement->points }}"
                    ></progress>
                </article>
            @endforeach
        </section>
    @endsection

    @section('sidebar')
        <section class="panelV2 achievement__statistics">
            <h2 class="panel__heading">{{ __('user.statistics') }}</h2>
            <dl class="key-value">
                <div class="key-value__group">
                    <dt>{{ __('user.unlocked-achievements') }}:</dt>
                </div>
                <div class="key-value__group">
                    <dd>{{ auth()->user()->unlockedAchievements()->count() }}</dd>
                </div>
                <div class="key-value__group">
                    <dt>{{ __('user.locked-achievements') }}</dt>
                    <dd>{{ auth()->user()->lockedAchievements()->count() }}</dd>
                </div>
            </dl>
        </section>
    @endsection
@else
    @section('main')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.private-profile') }}</h2>
            <div class="panel__body">{{ __('user.not-authorized') }}</div>
        </section>
    @endsection
@endif
