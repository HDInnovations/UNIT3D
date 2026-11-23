@extends('layout.with-main-and-sidebar')

@section('title')
    <title>{{ __('torrent.download-check') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.index') }}" class="breadcrumb__link">
            {{ __('torrent.torrents') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('torrents.show', ['id' => $torrent->id]) }}" class="breadcrumb__link">
            {{ $torrent->name }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('torrent.download-check') }}
    </li>
@endsection

@section('page', 'page__torrent-download-check--show')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('common.name') }}</dt>
                <dd class>{{ $torrent->name }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.size') }} :</dt>
                <dd class="text-blue">
                    {{ $torrent->getSize() }}
                    <i class="{{ config('other.font-awesome') }} fa-database"></i>
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.released') }}</dt>
                <dd class="text-blue">
                    {{ $torrent->created_at->diffForHumans() }}
                    <i class="{{ config('other.font-awesome') }} fa-fw fa-clock"></i>
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.seeders') }}</dt>
                <dd class="text-green">
                    {{ $torrent->seeders }}
                    <i class="{{ config('other.font-awesome') }} fa-arrow-up"></i>
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.leechers') }}</dt>
                <dd class="text-red">
                    {{ $torrent->leechers }}
                    <i class="{{ config('other.font-awesome') }} fa-arrow-down"></i>
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.completed') }}</dt>
                <dd class="text-orange">
                    {{ $torrent->times_completed }}
                    <i class="{{ config('other.font-awesome') }} fa-check-square"></i>
                </dd>
            </div>
        </dl>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('torrent.download-check') }}</h2>
        <div class="panel__body">
            @if (($user->ratio < config('other.ratio') || $user->can_download == 0) && $torrent->user_id !== $user->id)
                <h4>{{ __('torrent.no-privileges') }}</h4>
            @else
                <h4>{{ __('torrent.ready') }}</h4>
            @endif
        </div>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>
                    {{ __('common.ratio') }} {{ strtolower(__('torrent.greater-than')) }}
                    {{ config('other.ratio') }} :
                </dt>
                <dd>
                    @if ($user->ratio < config('other.ratio'))
                        <span class="text-red">
                            <i class="{{ config('other.font-awesome') }} fa-times"></i>
                            {{ strtoupper(__('torrent.failed')) }}
                        </span>
                    @else
                        <span class="text-green">
                            <i class="{{ config('other.font-awesome') }} fa-check"></i>
                            {{ strtoupper(__('torrent.passed')) }}
                        </span>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.download-rights-active') }}</dt>
                <dd>
                    @if ($user->can_download == 0 && $torrent->user_id != $user->id)
                        <span class="text-red">
                            <i class="{{ config('other.font-awesome') }} fa-times"></i>
                            {{ strtoupper(__('torrent.failed')) }}
                        </span>
                    @else
                        <span class="text-green">
                            <i class="{{ config('other.font-awesome') }} fa-check"></i>
                            {{ strtoupper(__('torrent.passed')) }}
                        </span>
                    @endif
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('torrent.moderation') }}</dt>
                <dd>
                    @if ($torrent->status === \App\Enums\ModerationStatus::REJECTED)
                        <span class="text-red">
                            <i class="{{ config('other.font-awesome') }} fa-times"></i>
                            {{ strtoupper(__('torrent.rejected')) }}
                        </span>
                    @elseif ($torrent->status === \App\Enums\ModerationStatus::PENDING)
                        <span class="text-orange">
                            <i class="{{ config('other.font-awesome') }} fa-times"></i>
                            {{ strtoupper(__('torrent.pending')) }}
                        </span>
                    @elseif ($torrent->status === \App\Enums\ModerationStatus::POSTPONED)
                        <span class="text-red">
                            <i class="{{ config('other.font-awesome') }} fa-times"></i>
                            {{ strtoupper(__('torrent.postponed')) }}
                        </span>
                    @else
                        <span class="text-green">
                            <i class="{{ config('other.font-awesome') }} fa-check"></i>
                            {{ strtoupper(__('torrent.approved')) }}
                        </span>
                    @endif
                </dd>
            </div>
        </dl>
        <div class="panel__body">
            @if (($user->ratio < config('other.ratio') || $user->can_download == 0) && $torrent->user_id != $user->id)
                <span class="text-red text-bold">{{ __('torrent.no-privileges-desc') }}</span>
            @else
                <p class="form__group form__group--horizontal">
                    <a
                        href="{{ route('download', ['id' => $torrent->id]) }}"
                        role="button"
                        class="form__button form__button--filled form__button--centered"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-download"></i>
                        {{ __('common.download') }}
                    </a>
                </p>
            @endif
        </div>
    </section>
@endsection
