@extends('layout.with-main-and-sidebar')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('giveaways.index') }}" class="breadcrumb__link">
            {{ __('event.giveaways') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $giveaway->name }}
    </li>
@endsection

@section('page', 'page__giveaway--show')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $giveaway->name }}</h2>
        <div class="panel__body">
            <ol class="events__list">
                @foreach (\Carbon\CarbonPeriod::create($giveaway->starts_at, $giveaway->ends_at) as $i => $date)
                    <li class="events__list-item">
                        <article class="events__prize">
                            <h3 class="events__prize-heading">
                                {{ $date->format('M j') }}
                            </h3>

                            @if ($prize = $userPrizes->get($i)?->first())
                                <i
                                    class="events__prize-icon events__prize-icon--today-claimed fad {{ $giveaway->icon }}"
                                ></i>
                                @if ($prize->bon > 0 || $prize->fl_tokens > 0)
                                    <ul class="events__prize-winnings-list">
                                        @if ($prize->bon > 0)
                                            <li class="events_prize-winnings-list-item">
                                                <i class="events__prize-message">
                                                    {{ $prize->bon }} {{ __('bon.bon') }}
                                                </i>
                                            </li>
                                        @endif

                                        @if ($prize->fl_tokens > 0)
                                            <li>
                                                <i class="events__prize-message">
                                                    @if ($prize->fl_tokens === 1)
                                                        {{ $prize->fl_tokens }}
                                                        {{ __('torrent.freeleech-token') }}
                                                    @else
                                                        {{ $prize->fl_tokens }}
                                                        {{ __('common.fl_tokens') }}
                                                    @endif
                                                </i>
                                            </li>
                                        @endif
                                    </ul>
                                @else
                                    <i class="events__prize-message">No winnings :(</i>
                                @endif
                            @else
                                @if (now()->isBefore($date))
                                    <i
                                        class="events__prize-icon events__prize-icon--future fad {{ $giveaway->icon }}"
                                    ></i>
                                    <i class="events__prize-message">Check back later!</i>
                                @elseif (now()->isAfter($date->addDay(1)))
                                    <i
                                        class="events__prize-icon events__prize-icon--past fad {{ $giveaway->icon }}"
                                    ></i>
                                    <i class="events__prize-message">{{ __('common.expired') }}</i>
                                @else
                                    <i
                                        class="events__prize-icon events__prize-icon--today-unclaimed fad {{ $giveaway->icon }} fa-beat-fade"
                                    ></i>
                                    <form
                                        class="form"
                                        action="{{ route('giveaways.claims.store', ['giveaway' => $giveaway]) }}"
                                        method="POST"
                                        style="display: contents"
                                    >
                                        @csrf
                                        <p class="form__group form__group--short-horizontal">
                                            <button
                                                class="form__button form__button--text form__button--centered"
                                            >
                                                {{ __('request.claim') }}
                                            </button>
                                        </p>
                                    </form>
                                @endif
                            @endif
                        </article>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <div class="panel__body">
            {{ $giveaway->description }}
        </div>
    </section>
@endsection
