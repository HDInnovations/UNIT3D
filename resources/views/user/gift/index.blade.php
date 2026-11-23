@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ $user->username }} {{ __('user.gifts') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('users.earnings.index', ['user' => $user]) }}" class="breadcrumb__link">
            {{ __('bon.bonus') }} {{ __('bon.points') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('bon.gifts') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-gift--index')

@section('main')
    <section class="panelV2">
        <header class="panel__header">
            <h2 class="panel__heading">{{ __('bon.gifts') }}</h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <a
                        class="form__button form__button--text"
                        href="{{ route('users.gifts.create', ['user' => $user]) }}"
                    >
                        {{ __('bon.send-gift') }}
                    </a>
                </div>
            </div>
        </header>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('bon.sender') }}</th>
                        <th>{{ __('bon.receiver') }}</th>
                        <th>{{ __('bon.points') }}</th>
                        <th>{{ __('common.message') }}</th>
                        <th>{{ __('bon.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gifts as $gift)
                        <tr>
                            <td>
                                @if ($gift->sender === null)
                                    Deleted user
                                @else
                                    <x-user-tag :user="$gift->sender" :anon="false" />
                                @endif
                            </td>
                            <td>
                                @if ($gift->recipient === null)
                                    Deleted user
                                @else
                                    <x-user-tag :user="$gift->recipient" :anon="false" />
                                @endif
                            </td>
                            <td>{{ $gift->bon }}</td>
                            <td>{{ $gift->message }}</td>
                            <td>
                                <time
                                    datetime="{{ $gift->created_at }}"
                                    title="{{ $gift->created_at }}"
                                >
                                    {{ $gift->created_at->format('Y-m-d') }}
                                </time>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $gifts->links('partials.pagination') }}
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bon.your-points') }}</h2>
        <div class="panel__body">{{ $bon }}</div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bon.total-gifts') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('bon.you-have-received-gifts') }}</dt>
                <dd>{{ $receivedGifts }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('bon.you-have-sent-gifts') }}</dt>
                <dd>{{ $sentGifts }}</dd>
            </div>
        </dl>
    </section>
@endsection
