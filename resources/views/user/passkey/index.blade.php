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
        {{ __('staff.passkeys') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-passkey--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('staff.passkeys') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <td>{{ __('user.passkey') }}</td>
                        <td>{{ __('common.created_at') }}</td>
                        <td>{{ __('user.deleted-on') }}</td>
                        <td>{{ __('common.status') }}</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($passkeys as $passkey)
                        <tr>
                            <td>{{ $passkey->content }}</td>
                            <td>
                                <time
                                    datetime="{{ $passkey->created_at }}"
                                    title="{{ $passkey->created_at }}"
                                >
                                    {{ $passkey->created_at }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $passkey->deleted_at }}"
                                    title="{{ $passkey->deleted_at }}"
                                >
                                    {{ $passkey->deleted_at ?? 'Currently in use' }}
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
                            <td colspan="4">No passkey history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.reset-passkey') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                action="{{ route('users.passkeys.update', ['user' => $user]) }}"
                method="POST"
            >
                @csrf
                @method('PATCH')
                <p>{{ __('user.reset-passkey-help') }}.</p>
                <p class="form__group--horizontal">
                    <button class="form__button form__button--filled form__button--centered">
                        Reset
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
