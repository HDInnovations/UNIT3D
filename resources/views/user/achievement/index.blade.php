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
        <section class="panelV2">
            <header class="panel__header">
                <h2 class="panel__heading">{{ __('user.achievements') }}</h2>
                <div class="achievement-summary">
                    <span class="achievement-summary__item">
                        <strong>{{ $earnedCount }}</strong> {{ __('user.earned') }}
                    </span>
                    <span class="achievement-summary__item">
                        <strong>{{ $completedCount }}</strong> {{ __('user.completed') }}
                    </span>
                    <span class="achievement-summary__item">
                        <strong>{{ $availableCount }}</strong> {{ __('user.available') }}
                    </span>
                </div>
            </header>
            <div class="panel__body">
                @foreach ($grouped as $category => $categoryAchievements)
                    <section class="achievement-group">
                        <h3 class="achievement-group__title">{{ $category }}</h3>
                        <div class="achievement-showcase">
                            @foreach ($categoryAchievements as $row)
                                <article
                                    @class([
                                        'achievement-tile',
                                        'achievement-tile--earned' => $row['currentTier'] > 0,
                                        'achievement-tile--maxed' => $row['isCompleted'],
                                        'achievement-tile--locked' => $row['currentTier'] === 0 && ! $row['isHidden'],
                                        'achievement-tile--hidden' => $row['isHidden'],
                                    ])
                                    @unless ($row['isHidden'])
                                        title="{{ $row['achievement']->name }}{{ $row['currentTierDetails']?->description ? ' — '.$row['currentTierDetails']->description : '' }}"
                                    @endunless
                                >
                                    <figure class="achievement-tile__medal">
                                        @if ($row['isHidden'])
                                            <i class="{{ config('other.font-awesome') }} fa-question" aria-hidden="true"></i>
                                        @elseif ($row['iconRoute'])
                                            <img src="{{ $row['iconRoute'] }}" alt="" />
                                        @else
                                            <span class="achievement-tile__initial">{{ mb_substr($row['achievement']->name, 0, 1) }}</span>
                                        @endif
                                        @if ($row['isCompleted'])
                                            <span class="achievement-tile__crest" aria-hidden="true">
                                                <i class="{{ config('other.font-awesome') }} fa-check"></i>
                                            </span>
                                        @endif
                                    </figure>

                                    <h4 class="achievement-tile__title">
                                        {{ $row['isHidden'] ? '???' : $row['achievement']->name }}
                                    </h4>

                                    @if ($row['isHidden'])
                                        <p class="achievement-tile__tier">{{ __('user.hidden-achievement') }}</p>
                                    @else
                                        <p class="achievement-tile__tier">
                                            @if ($row['currentTier'] > 0)
                                                {{ $row['currentTierDetails']?->name }} &middot;
                                                {{ __('user.tier') }} {{ $row['currentTier'] }}/{{ $row['maxTier'] }}
                                            @else
                                                {{ __('user.locked') }}
                                            @endif
                                        </p>

                                        @if ($row['maxTier'] > 1)
                                            <div class="achievement-tile__track" aria-hidden="true">
                                                @for ($pip = 1; $pip <= $row['maxTier']; $pip++)
                                                    <span @class([
                                                        'achievement-tile__pip',
                                                        'achievement-tile__pip--on' => $pip <= $row['currentTier'],
                                                    ])></span>
                                                @endfor
                                            </div>
                                        @endif

                                        <p class="achievement-tile__status">
                                            @if ($row['isCompleted'])
                                                <i class="{{ config('other.font-awesome') }} fa-trophy" aria-hidden="true"></i>
                                                {{ __('user.completed') }}
                                            @elseif ($row['nextTierDetails'])
                                                {{ __('user.next-tier') }}:
                                                {{ $row['nextTierDetails']->name }}
                                                ({{ number_format((float) $row['nextTierDetails']->threshold) }})
                                            @else
                                                {{ __('user.not-yet-earned') }}
                                            @endif
                                        </p>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </section>
    @endsection

    @section('sidebar')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.statistics') }}</h2>
            <div class="panel__body achievement-stats">
                <div class="achievement-stats__meter">
                    <progress
                        class="achievement-meter"
                        value="{{ $earnedCount }}"
                        max="{{ max($availableCount, 1) }}"
                    ></progress>
                    <span class="achievement-meter__label">{{ $earnedCount }} / {{ $availableCount }}</span>
                </div>
                <dl class="key-value">
                    <div class="key-value__group">
                        <dt>{{ __('user.earned') }}:</dt>
                        <dd>{{ $earnedCount }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.completed') }}:</dt>
                        <dd>{{ $completedCount }}</dd>
                    </div>
                    <div class="key-value__group">
                        <dt>{{ __('user.available') }}:</dt>
                        <dd>{{ $availableCount }}</dd>
                    </div>
                </dl>
            </div>
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
