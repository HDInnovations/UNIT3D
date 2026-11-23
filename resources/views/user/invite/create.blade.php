@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ $user->username }} - {{ __('user.send-invite') }} - {{ config('other.title') }}
    </title>
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

@section('page', 'page__user-invite--create')

@if (config('other.invite-only') === false)
    @section('main')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.invites-disabled') }}</h2>
            <div class="panel__body">{{ __('user.invites-disabled-desc') }}</div>
        </section>
    @endsection
@elseif (! ($user->can_invite ?? $user->group->can_invite))
    @section('main')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.invites-banned') }}</h2>
            <div class="panel__body">{{ __('user.invites-banned-desc') }}</div>
        </section>
    @endsection
@else
    @section('main')
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('user.invite-friend') }}</h2>
            <div class="panel__body">
                <form
                    class="form"
                    action="{{ route('users.invites.store', ['user' => $user]) }}"
                    method="post"
                >
                    @csrf
                    <p class="form__group">
                        <input
                            id="email"
                            class="form__text"
                            type="email"
                            name="email"
                            placeholder=" "
                        />
                        <label class="form__label form__label--floating" for="email">
                            {{ __('common.email') }}
                        </label>
                    </p>
                    <p class="form__group">
                        <textarea
                            id="message"
                            class="form__textarea"
                            name="message"
                            required
                        ></textarea>
                        <label class="form__label form__label--floating" for="message">
                            {{ __('common.message') }}
                        </label>
                    </p>
                    @if ($user->group->is_modo)
                        <p class="form__group">
                            <textarea
                                id="internal_note"
                                class="form__textarea"
                                name="internal_note"
                                placeholder=" "
                            ></textarea>
                            <label class="form__label form__label--floating" for="internal_note">
                                {{ __('ticket.staff-notes') }}
                            </label>
                        </p>
                    @endif

                    <p class="form__group">
                        <button class="form__button form__button--filled">
                            {{ __('common.submit') }}
                        </button>
                    </p>
                </form>
            </div>
        </section>
    @endsection

    @section('sidebar')
        <section class="panelV2">
            <h2 class="panel__heading">
                {{ __('user.invites-count', ['count' => $user->invites]) }}
            </h2>
            <div class="panel__body">
                <ul style="padding-left: 0; list-style-position: inside">
                    @foreach (__('user.invites-rules') as $rule)
                        <li>{{ $rule }}</li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endsection
@endif
