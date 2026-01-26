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
        {{ __('user.irckeys') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-irckey--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.irckeys') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('user.irckey') }}</th>
                        <th>{{ __('common.created_at') }}</th>
                        <th>{{ __('user.deleted-on') }}</th>
                        <th>{{ __('common.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($irckeys as $irckey)
                        <tr>
                            <td>{{ $irckey->content }}</td>
                            <td>
                                <time
                                    datetime="{{ $irckey->created_at }}"
                                    title="{{ $irckey->created_at }}"
                                >
                                    {{ $irckey->created_at }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $irckey->deleted_at }}"
                                    title="{{ $irckey->deleted_at }}"
                                >
                                    {{ $irckey->deleted_at ?? 'Currently in use' }}
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
                            <td colspan="4">No irckey history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.reset-irc-token') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                action="{{ route('users.irckeys.update', ['user' => $user]) }}"
                method="POST"
            >
                @csrf
                @method('PATCH')
                <p>{{ __('user.reset-irc-help') }}.</p>
                @if ($user->irckey === null)
                    <p>You currently do not have an IRC key.</p>
                    <p class="form__group--horizontal">
                        <button class="form__button form__button--filled form__button--centered">
                            Generate IRC key
                        </button>
                    </p>
                @else
                    <p class="form__group--horizontal">
                        <button class="form__button form__button--filled form__button--centered">
                            Reset
                        </button>
                    </p>
                @endif
            </form>
        </div>
    </section>
@endsection
