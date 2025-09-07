@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('ticket.helpdesk') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('tickets.index') }}" class="breadcrumb__link">
            {{ __('ticket.helpdesk') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $ticket->subject }}
    </li>
@endsection

@section('page', 'page__ticket--show')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ $ticket->subject }}</h2>
        {{-- format-ignore-start --}}<div class="panel__body" style="white-space: pre-wrap">{{ $ticket->body }}</div>{{-- format-ignore-end --}}
    </section>
    @if ($user->group->is_modo)
        <section class="panelV2">
            <header class="panel__header">
                <h2 class="panel__heading">{{ __('ticket.staff-notes') }}</h2>
                <div class="panel__actions">
                    <div class="panel__action" x-data="dialog">
                        <button class="form__button form__button--text" x-bind="showDialog">
                            {{ __('common.add') }}
                        </button>
                        <dialog class="dialog" x-bind="dialogElement">
                            <h4 class="dialog__heading">Add Staff Note</h4>
                            <form
                                class="dialog__form"
                                method="POST"
                                action="{{ route('tickets.note.store', ['ticket' => $ticket]) }}"
                                x-bind="dialogForm"
                            >
                                @csrf
                                <p class="form__group">
                                    <textarea
                                        id="message"
                                        class="form__textarea"
                                        name="message"
                                        type="text"
                                        required
                                    ></textarea>
                                    <label class="form__label form__label--floating" for="message">
                                        Message
                                    </label>
                                </p>
                                <p class="form__group">
                                    <button class="form__button form__button--filled">
                                        {{ __('common.add') }}
                                    </button>
                                    <button
                                        formmethod="dialog"
                                        formnovalidate
                                        class="form__button form__button--outlined"
                                    >
                                        {{ __('common.cancel') }}
                                    </button>
                                </p>
                            </form>
                        </dialog>
                    </div>
                </div>
            </header>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.staff') }}</th>
                            <th>{{ __('user.note') }}</th>
                            <th>{{ __('user.created-on') }}</th>
                            <th>{{ __('common.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ticket->notes as $note)
                            <tr>
                                <td>
                                    <x-user-tag :anon="false" :user="$note->user" />
                                </td>
                                {{-- format-ignore-start --}}<td style="white-space: pre-wrap">@linkify($note->message)</td>{{-- format-ignore-end --}}
                                <td>
                                    <time
                                        datetime="{{ $note->created_at }}"
                                        title="{{ $note->created_at }}"
                                    >
                                        {{ $note->created_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('tickets.note.destroy', ['ticket' => $ticket]) }}"
                                                method="POST"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <p class="form__group form__group--horizontal">
                                                    <button
                                                        x-on:click.prevent="confirmAction"
                                                        data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this note: ' . $note->message . '?') }}"
                                                        class="form__button form__button--text form__button--centered"
                                                    >
                                                        {{ __('ticket.delete') }}
                                                    </button>
                                                </p>
                                            </form>
                                        </li>
                                    </menu>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No notes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <livewire:comments :model="$ticket" />
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>ID</dt>
                <dd>{{ $ticket->id }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('common.created_at') }}</dt>
                <dd>{{ $ticket->created_at->format('Y-m-d') }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('ticket.opened-by') }}</dt>
                <dd>
                    <x-user-tag :user="$ticket->user" :anon="false" />
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('ticket.category') }}</dt>
                <dd>{{ $ticket->category->name }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('ticket.priority') }}</dt>
                <dd>
                    <i
                        class="{{ config('other.font-awesome') }} {{ $ticket->priority->icon }}"
                        style="color: {{ $ticket->priority->color }}"
                    ></i>
                    {{ $ticket->priority->name }}
                </dd>
            </div>
            @if ($ticket->closed_at !== null)
                <div class="key-value__group">
                    <dt>{{ __('ticket.closed') }}</dt>
                    <dd>
                        <time
                            datetime="{{ $ticket->closed_at }}"
                            title="{{ $ticket->closed_at }}"
                        >
                            {{ $ticket->closed_at->format('Y-m-d') }}
                        </time>
                    </dd>
                </div>
            @endif
        </dl>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.actions') }}</h2>
        <div class="panel__body">
            @if ($user->group->is_modo)
                <form
                    class="form form--horizontal"
                    action="{{ route('tickets.assignee.store', ['ticket' => $ticket]) }}"
                    method="POST"
                    x-data
                >
                    @csrf
                    <p class="form__group">
                        <select
                            id="staff_id"
                            name="staff_id"
                            class="form__select"
                            x-on:change="$root.submit()"
                        >
                            <option hidden disabled selected value=""></option>
                            @foreach (App\Models\User::select(['id', 'username'])->whereIn('group_id', App\Models\Group::where('is_modo', 1)->whereNotIn('id', [9])->pluck('id')->toArray())->get() as $user)
                                <option
                                    value="{{ $user->id }}"
                                    @selected($user->id === $ticket->staff_id)
                                >
                                    {{ $user->username }}
                                </option>
                            @endforeach
                        </select>
                        <label class="form__label form__label--floating" for="staff_id">
                            {{ __('ticket.assign') }}
                        </label>
                    </p>
                </form>

                @if ($ticket->staff_id !== null)
                    <form
                        action="{{ route('tickets.assignee.destroy', ['ticket' => $ticket]) }}"
                        method="POST"
                    >
                        @csrf
                        @method('DELETE')
                        <p class="form__group form__group--horizontal">
                            <button
                                class="form__button form__button--filled form__button--centered"
                            >
                                {{ __('ticket.unassign') }}
                            </button>
                        </p>
                    </form>
                @endif

                <form action="{{ route('tickets.destroy', ['ticket' => $ticket]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <p class="form__group form__group--horizontal">
                        <button class="form__button form__button--filled form__button--centered">
                            {{ __('ticket.delete') }}
                        </button>
                    </p>
                </form>
            @endif

            @if ($ticket->closed_at === null)
                <form action="{{ route('tickets.close', ['ticket' => $ticket]) }}" method="POST">
                    <p class="form__group form__group--horizontal">
                        @csrf
                        <button class="form__button form__button--filled form__button--centered">
                            {{ __('ticket.close') }}
                        </button>
                    </p>
                </form>
            @else
                <form action="{{ route('tickets.reopen', ['ticket' => $ticket]) }}" method="POST">
                    <p class="form__group form__group--horizontal">
                        @csrf
                        <button class="form__button form__button--filled form__button--centered">
                            {{ __('ticket.reopen') }}
                        </button>
                    </p>
                </form>
            @endif
        </div>
    </section>
    @livewire('attachment-upload', ['id' => $ticket->id])
    @if ($pastUserTickets->count() > 0)
        <section class="panelV2">
            <h2 class="panel__heading">Other Tickets</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('ticket.created') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pastUserTickets as $ticket)
                            <tr>
                                <td>
                                    <i
                                        class="{{ config('other.font-awesome') }} {{ $ticket->priority->icon }}"
                                        style="color: {{ $ticket->priority->color }}"
                                    ></i>
                                    <a href="{{ route('tickets.show', ['ticket' => $ticket]) }}">
                                        {{ $ticket->subject }}
                                    </a>
                                </td>
                                <td>
                                    <time
                                        title="{{ $ticket->created_at }}"
                                        datetime="{{ $ticket->created_at }}"
                                    >
                                        {{ $ticket->created_at->format('Y-m') }}
                                    </time>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
@endsection
