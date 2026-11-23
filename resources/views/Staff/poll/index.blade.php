@extends('layout.with-main')

@section('title')
    <title>Polls - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('poll.polls') }}
    </li>
@endsection

@section('page', 'page__staff-poll--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('poll.poll') }}</h2>
            <a href="{{ route('staff.polls.create') }}" class="form__button form__button--text">
                {{ __('common.add') }}
            </a>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('poll.title') }}</th>
                        <th>{{ __('common.date') }}</th>
                        <th>{{ __('forum.state') }}</th>
                        <th>{{ __('common.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($polls as $poll)
                        <tr>
                            <td>
                                <a href="{{ route('staff.polls.show', ['poll' => $poll]) }}">
                                    {{ $poll->title }}
                                </a>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $poll->created_at }}"
                                    title="{{ $poll->created_at }}"
                                >
                                    {{ date('d M Y', $poll->created_at->getTimestamp()) }}
                                </time>
                            </td>
                            <td>
                                @if ($poll->expires_at?->isPast())
                                    {{ __('forum.closed') }}
                                @else
                                    {{ __('forum.open') }}
                                @endif
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('polls.show', ['poll' => $poll]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.view') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <a
                                            href="{{ route('staff.polls.edit', ['poll' => $poll]) }}"
                                            class="form__button form__button--text"
                                        >
                                            {{ __('common.edit') }}
                                        </a>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('staff.polls.destroy', ['poll' => $poll]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this poll: ' . $poll->title . '?') }}"
                                                class="form__button form__button--text"
                                            >
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
