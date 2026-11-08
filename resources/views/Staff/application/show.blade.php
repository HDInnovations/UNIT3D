@extends('layout.with-main-and-sidebar')

@section('title')
    <title>Application - {{ __('staff.staff-dashboard') }} - {{ config('other.title') }}</title>
@endsection

@section('meta')
    <meta name="description" content="Application - {{ __('staff.staff-dashboard') }}" />
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.applications.index') }}" class="breadcrumb__link">
            {{ __('staff.applications') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ $application->id }}
    </li>
@endsection

@section('page', 'page__staff-application--show')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.image') }}</h2>
        <div class="panel__body">
            <ul>
                @foreach ($application->imageProofs as $img_proof)
                    <li>
                        <a href="{{ $img_proof->image }}" target="_blank">
                            {{ $img_proof->image }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('user.profile') }} {{ __('staff.links') }}</h2>
        <div class="panel__body">
            <ul>
                @foreach ($application->urlProofs as $url_proof)
                    <li>
                        <a href="{{ $url_proof->url }}" target="_blank">
                            {{ $url_proof->url }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">
            {{ __('staff.application-referrer') }}
        </h2>
        <div class="panel__body">{{ $application->referrer }}</div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.info') }}</h2>
        <dl class="key-value">
            <div class="key-value__group">
                <dt>{{ __('common.email') }}</dt>
                <dd>{{ $application->email }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('staff.application-type') }}</dt>
                <dd>{{ $application->type }}</dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('common.created_at') }}</dt>
                <dd>
                    <time
                        datetime="{{ $application->created_at }}"
                        title="{{ $application->created_at }}"
                    >
                        {{ $application->created_at->diffForHumans() }}
                    </time>
                </dd>
            </div>
            <div class="key-value__group">
                <dt>{{ __('common.status') }}</dt>
                <dd>
                    @switch($application->status)
                        @case(\App\Enums\ModerationStatus::PENDING)
                            <span class="application--pending">Pending</span>

                            @break
                        @case(\App\Enums\ModerationStatus::APPROVED)
                            <span class="application--approved">Approved</span>

                            @break
                        @case(\App\Enums\ModerationStatus::REJECTED)
                            <span class="application--rejected">Rejected</span>

                            @break
                        @default
                            <span class="application--unknown">Unknown</span>
                    @endswitch
                </dd>
            </div>
        </dl>
    </section>
    <section class="panelV2">
        @if ($application->status !== \App\Enums\ModerationStatus::PENDING)
            <h2 class="panel__heading">{{ __('common.moderated-by') }}</h2>
            <div class="panel__body">
                <x-user-tag :anon="false" :user="$application->moderated" />
            </div>
        @else
            <h2 class="panel__heading">{{ __('common.action') }}</h2>
            <div class="panel__body">
                <div>
                    <p class="form__group form__group--horizontal">
                        <button
                            class="form__button form__button--filled form__button--centered"
                            popovertarget="application-approve"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-check"></i>
                            {{ __('request.approve') }}
                        </button>
                    </p>
                    <dialog id="application-show" class="dialog" popover>
                        <h3 class="dialog__heading">
                            {{ __('request.approve') }}
                            {{ __('common.this') }}
                            {{ __('staff.application') }}
                        </h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('staff.applications.approve', ['id' => $application->id]) }}"
                        >
                            @csrf
                            <input
                                id="email"
                                name="email"
                                type="hidden"
                                value="{{ $application->email }}"
                            />
                            <p class="form__group">
                                <textarea
                                    id="approve"
                                    class="form__textarea"
                                    name="approve"
                                    placeholder=" "
                                >
Application approved!</textarea
                                >
                                <label class="form__label form__label--floating" for="approve">
                                    Invitation message
                                </label>
                            </p>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('request.approve') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="application-approve"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                </div>
                <div>
                    <p class="form__group form__group--horizontal">
                        <button
                            class="form__button form__button--filled form__button--centered"
                            popovertarget="application-reject"
                        >
                            <i class="{{ config('other.font-awesome') }} fa-times"></i>
                            {{ __('request.reject') }}
                        </button>
                    </p>
                    <dialog id="application-reject" class="dialog" popover>
                        <h3 class="dialog__heading">
                            {{ __('request.reject') }}
                            {{ __('common.this') }}
                            {{ __('staff.application') }}
                        </h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('staff.applications.reject', ['id' => $application->id]) }}"
                        >
                            @csrf
                            <input
                                id="email"
                                name="email"
                                type="hidden"
                                value="{{ $application->email }}"
                            />
                            <p class="form__group">
                                <textarea id="message" class="form__textarea" name="deny" required>
Insufficient proofs.</textarea
                                >
                                <label class="form__label form__label--floating" for="message">
                                    Rejection message
                                </label>
                            </p>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('request.reject') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="application-reject"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                </div>
            </div>
        @endif
    </section>
@endsection
