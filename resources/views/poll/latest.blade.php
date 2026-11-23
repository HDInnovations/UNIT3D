@extends('layout.with-main')

@section('title')
    <title>{{ __('poll.polls') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb--active">
        {{ __('poll.polls') }}
    </li>
@endsection

@section('page', 'page__poll--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('poll.polls') }}: {{ __('poll.vote-now') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                @foreach ($polls as $poll)
                    <tr>
                        <td>
                            <a href="{{ route('polls.show', ['poll' => $poll]) }}">
                                <i class="{{ config('other.font-awesome') }} fa-pie-chart"></i>
                                {{ $poll->title }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </section>
@endsection
