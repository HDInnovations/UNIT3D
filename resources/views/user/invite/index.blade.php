@extends('layout.with-main')

@section('title')
    <title>{{ $user->username }} - {{ __('user.invites') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.invites') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-invite--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('user.invites') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('users.invites.create', ['user' => $user]) }}"
                    >
                        {{ __('user.send-invite') }}
                    </a>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('user.sender') }}</th>
                        <th>{{ __('common.email') }}</th>
                        @if (auth()->user()->group->is_modo)
                            <th>{{ __('user.code') }}</th>
                            <th>{{ __('common.message') }}</th>
                        @endif

                        <th>{{ __('user.created-on') }}</th>
                        <th>{{ __('user.expires-on') }}</th>
                        <th>{{ __('user.accepted-by') }}</th>
                        <th>{{ __('user.accepted-at') }}</th>
                        <th>{{ __('user.deleted-on') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invites as $invite)
                        <tr>
                            <td>
                                <x-user-tag :user="$invite->sender" :anon="false" />
                            </td>
                            <td>{{ $invite->email }}</td>
                            @if (auth()->user()->group->is_modo)
                                <td>{{ $invite->code }}</td>
                                {{-- format-ignore-start --}}<td style="white-space: pre-wrap">{{ $invite->custom }}</td>{{-- format-ignore-end --}}
                            @endif

                            <td>
                                <time
                                    datetime="{{ $invite->created_at }}"
                                    title="{{ $invite->created_at }}"
                                >
                                    {{ $invite->created_at }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $invite->expires_on }}"
                                    title="{{ $invite->expires_on }}"
                                >
                                    {{ $invite->expires_on }}
                                </time>
                            </td>
                            <td>
                                @if ($invite->accepted_by !== null && $invite->accepted_by !== 1)
                                    <x-user-tag :user="$invite->receiver" :anon="false" />
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <time
                                    datetime="{{ $invite->accepted_at }}"
                                    title="{{ $invite->accepted_at }}"
                                >
                                    {{ $invite->accepted_at ?? 'N/A' }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $invite->deleted_at }}"
                                    title="{{ $invite->deleted_at }}"
                                >
                                    {{ $invite->deleted_at ?? 'N/A' }}
                                </time>
                            </td>
                            <td>
                                <menu class="data-table__actions">
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('users.invites.send', ['user' => $user, 'sentInvite' => $invite]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to resend the email to: ' . $invite->email . '?') }}"
                                                class="form__button form__button--text"
                                                @disabled($invite->accepted_at !== null || $invite->expires_on < now())
                                            >
                                                {{ __('common.resend') }}
                                            </button>
                                        </form>
                                    </li>
                                    <li class="data-table__action">
                                        <form
                                            action="{{ route('users.invites.destroy', ['user' => $user, 'sentInvite' => $invite]) }}"
                                            method="POST"
                                            x-data="confirmation"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                x-on:click.prevent="confirmAction"
                                                data-b64-deletion-message="{{ base64_encode('Are you sure you want to retract the invite to: ' . $invite->email . '?') }}"
                                                class="form__button form__button--text"
                                                @disabled($invite->accepted_at !== null || $invite->expires_on < now() || $invite->deleted_at !== null)
                                            >
                                                {{ __('common.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                </menu>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->group->is_modo ? 10 : 8 }}">
                                No invitees
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $invites->links('partials.pagination') }}
    </section>
@endsection
