@extends('layout.with-main-and-sidebar')

@section('title')
    <title>
        {{ $user->username }} - Security - {{ __('common.members') }} - {{ config('other.title') }}
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
    <li class="breadcrumb--active">{{ __('user.apikeys') }}</li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-apikey--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.apikeys') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('user.apikey') }}</th>
                        <th>{{ __('common.created_at') }}</th>
                        <th>{{ __('user.deleted-on') }}</th>
                        <th>{{ __('common.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($apikeys as $apikey)
                        <tr>
                            <td>{{ $apikey->content }}</td>
                            <td>
                                <time
                                    datetime="{{ $apikey->created_at }}"
                                    title="{{ $apikey->created_at }}"
                                >
                                    {{ $apikey->created_at }}
                                </time>
                            </td>
                            <td>
                                <time
                                    datetime="{{ $apikey->deleted_at }}"
                                    title="{{ $apikey->deleted_at }}"
                                >
                                    {{
                                        $apikey->deleted_at ??
                                            'Currently in use'
                                    }}
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
                            <td colspan="4">No apikey history</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.reset-api-token') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                action="{{ route('users.apikeys.update', ['user' => $user]) }}"
                method="POST"
            >
                @csrf
                @method('PATCH')
                <p>{{ __('user.reset-api-help') }}.</p>
                @if ($user->api_token === null)
                    <p>You currently do not have an API key.</p>
                    <p class="form__group--horizontal">
                        <button class="form__button form__button--filled form__button--centered">
                            Generate API key
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
