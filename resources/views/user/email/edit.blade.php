@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ $user->username }} - Security - {{ __('common.members') }} -
        {{ config('other.title') }}
    </title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a
            href="{{ route('users.general_settings.edit', ['user' => $user]) }}"
            class="breadcrumb__link"
        >
            {{ __('user.settings') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.email') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-email--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.change-email') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                action="{{ route('users.email.update', ['user' => $user]) }}"
                method="POST"
            >
                @csrf
                @method('PATCH')
                <p>{{ __('user.change-email-help') }}.</p>
                <p class="form__group">
                    <input
                        id="current_email"
                        class="form__text"
                        name="current_email"
                        readonly
                        type="email"
                        value="{{ $user->email }}"
                    />
                    <label class="form__label form__label--floating" for="current_email">
                        Current email
                    </label>
                </p>
                <p class="form__group">
                    <input id="email" class="form__text" name="email" required type="email" />
                    <label class="form__label form__label--floating" for="email">New email</label>
                </p>
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
        <h2 class="panel__heading">{{ __('user.email-updates') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('common.created_at') }}</th>
                        <th>{{ __('user.deleted-on') }}</th>
                        <th>{{ __('common.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($emailUpdates as $emailUpdate)
                        <tr>
                            <td>
                                <time
                                    datetime="{{ $emailUpdate->created_at }}"
                                    title="{{ $emailUpdate->created_at }}"
                                >
                                    {{ $emailUpdate->created_at->format('Y-m-d') }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $emailUpdate->deleted_at }}"
                                    title="{{ $emailUpdate->deleted_at }}"
                                >
                                    {{ $emailUpdate->deleted_at?->format('Y-m-d') ?? 'Currently in use' }}
                                </time>
                            </td>
                            <td>
                                @if ($loop->first)
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-check text-green"
                                    ></i>
                                    {{ __('common.active') }}
                                @else
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-times text-red"
                                    ></i>
                                    {{ __('stat.disabled') }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">No email update history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
