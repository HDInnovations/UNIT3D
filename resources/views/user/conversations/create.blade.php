@extends('layout.with-main')

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a
            href="{{ route('users.conversations.index', ['user' => $user]) }}"
            class="breadcrumb__link"
        >
            {{ __('pm.messages') }}
        </a>
    </li>
    <li class="breadcrumb--active">
        {{ __('common.new-adj') }}
    </li>
@endsection

@section('page', 'page__user-conversation--create')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('pm.new') }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('users.conversations.store', ['user' => $user]) }}"
            >
                @csrf
                <p class="form__group">
                    <input
                        id="receiver_username"
                        class="form__text"
                        name="receiver_username"
                        required
                        @if ($username !== null)
                            readonly
                            value="{{ $username }}"
                        @endif
                    />
                    <label for="receiver_username" class="form__label form__label--floating">
                        {{ __('common.username') }}
                    </label>
                </p>
                <p class="form__group">
                    <input id="subject" class="form__text" name="conversation[subject]" required />
                    <label for="subject" class="form__label form__label--floating">
                        {{ __('pm.enter-subject') }}
                    </label>
                </p>
                @livewire('bbcode-input', ['name' => 'message', 'label' => __('pm.reply'), 'required' => true])
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('pm.send') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
