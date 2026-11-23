@extends('layout.with-main')

@section('title')
    <title>{{ __('poll.results') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('polls.index') }}" class="breadcrumb__link">
            {{ __('poll.polls') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('polls.show', ['poll' => $poll]) }}" class="breadcrumb__link">
            {{ $poll->title }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.results') }}
    </li>
@endsection

@section('page', 'page__poll--result')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('poll.results') }}: {{ $poll->title }}</h2>
        <div class="panel__body">
            @php($total = $poll->options->sum('votes'))
            @foreach ($poll->options as $option)
                <p class="form__group">
                    <label class="form__label" for="option{{ $loop->iteration }}">
                        {{ $option->name }}
                        ({{ \number_format($total === 0 ? 0 : (100 * $option->votes) / $total, 2) }}%)
                    </label>
                    <meter
                        id="option{{ $loop->iteration }}"
                        class="form__meter"
                        min="0"
                        max="{{ $total }}"
                        value="{{ $option->votes }}"
                    >
                        {{ \number_format($total === 0 ? 0 : (100 * $option->votes) / $total, 1) }}%
                        - {{ $option->votes }}
                        {{ $option->votes === 1 ? __('poll.vote') : __('poll.votes') }}
                    </meter>
                </p>
            @endforeach
        </div>
    </section>
@endsection
