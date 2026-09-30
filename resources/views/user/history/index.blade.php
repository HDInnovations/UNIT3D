@extends('layout.with-main')

@section('title')
    <title>{{ $user->username }} {{ __('user.torrents') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('users.show', ['user' => $user]) }}" class="breadcrumb__link">
            {{ $user->username }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('user.torrents-history') }}
    </li>
@endsection

@section('nav-tabs')
    @include('user.buttons.user')
@endsection

@section('page', 'page__user-history--index')

@section('main')
    @php
        $totalUpload = $history->upload ?? 0;
        $totalDownload = $history->download ?? 0;
        $overallRatio = $totalDownload > 0 ? $totalUpload / $totalDownload : null;
    @endphp

    <header class="history-page__heading">
        <h1>{{ __('user.torrents-history') }}</h1>
        <p>{{ __('livewire-interface.history.subtitle', ['username' => $user->username]) }}</p>
    </header>

    <section class="history-summary" aria-label="{{ __('user.statistics') }}">
        <article class="history-summary__card">
            <span class="history-summary__label">
                {{ __('livewire-interface.history.upload-total') }}
            </span>
            <span class="history-summary__value">
                {{ App\Helpers\StringHelper::formatBytes($totalUpload, 2) }}
            </span>
            <span class="history-summary__caption">
                {{ __('livewire-interface.history.credited', ['value' => App\Helpers\StringHelper::formatBytes($history->credited_upload ?? 0, 2)]) }}
            </span>
        </article>
        <article class="history-summary__card">
            <span class="history-summary__label">
                {{ __('livewire-interface.history.download-total') }}
            </span>
            <span class="history-summary__value">
                {{ App\Helpers\StringHelper::formatBytes($totalDownload, 2) }}
            </span>
            <span class="history-summary__caption">
                {{ __('livewire-interface.history.credited', ['value' => App\Helpers\StringHelper::formatBytes($history->credited_download ?? 0, 2)]) }}
            </span>
        </article>
        <article class="history-summary__card">
            <span class="history-summary__label">
                {{ __('livewire-interface.history.total-ratio') }}
            </span>
            <span class="history-summary__value">
                {{ $overallRatio === null ? '∞' : \number_format($overallRatio, 2) }}
            </span>
            <span class="history-summary__caption">
                {{ __('livewire-interface.history.summary-scope') }}
            </span>
        </article>
        <article class="history-summary__card">
            <span class="history-summary__label">
                {{ __('livewire-interface.history.active-seeding') }}
            </span>
            <span class="history-summary__value">{{ $history->seeding ?? 0 }}</span>
            <span class="history-summary__caption">
                {{ __('livewire-interface.history.seeding') }}
            </span>
        </article>
    </section>

    @livewire('user-torrents', ['userId' => $user->id])
@endsection
