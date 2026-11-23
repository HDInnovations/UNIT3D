@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ $user->username }} {{ __('user.tips') }} - {{ config('other.title') }}</title>
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
        {{ __('bon.tips') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-torrent-tip--index')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bon.tips') }}</h2>
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('bon.sender') }}</th>
                        <th>{{ __('bon.receiver') }}</th>
                        <th>{{ __('bon.points') }}</th>
                        <th>{{ __('torrent.torrent') }}</th>
                        <th>{{ __('bon.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tips as $tip)
                        <tr>
                            <td>
                                @if ($tip->sender === null)
                                    Deleted user
                                @else
                                    <x-user-tag :user="$tip->sender" :anon="false" />
                                @endif
                            </td>
                            <td>
                                @if ($tip->torrent === null)
                                    Torrent deleted
                                @else
                                    <x-user-tag
                                        :user="$tip->recipient"
                                        :anon="$tip->torrent->anon"
                                    />
                                @endif
                            </td>
                            <td>{{ $tip->bon }}</td>
                            <td>
                                @if ($tip->torrent === null)
                                    Torrent deleted
                                @else
                                    <a
                                        href="{{ route('torrents.show', ['id' => $tip->torrent->id]) }}"
                                    >
                                        {{ $tip->torrent->name }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                <time
                                    datetime="{{ $tip->created_at }}"
                                    title="{{ $tip->created_at }}"
                                >
                                    {{ $tip->created_at->format('Y-m-d') }}
                                </time>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $tips->links('partials.pagination') }}
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bon.your-points') }}</h2>
        <div class="panel__body">{{ $bon }}</div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bon.total-tips') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('bon.you-have-received-tips') }}</dt>
                <dd>{{ $receivedTips }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('bon.you-have-sent-tips') }}</dt>
                <dd>{{ $sentTips }}</dd>
            </div>
        </dl>
    </section>
@endsection
