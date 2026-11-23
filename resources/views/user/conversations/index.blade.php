@extends('layout.with-main-and-sidebar')

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
        {{ __('pm.inbox') }}
    </li>
@endsection

@section('page', 'page__user-conversation--index')

@section('main')
    @livewire('conversation-search')
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.actions') }}</h2>
        <div class="panel__body">
            <p class="form__group form__group--horizontal">
                <a
                    href="{{ route('users.conversations.create', ['user' => $user]) }}"
                    class="form__button form__button--filled form__button--centered"
                >
                    {{ __('pm.new') }}
                </a>
            </p>
            <form
                action="{{ route('users.conversations.mass_update', ['user' => $user]) }}"
                method="POST"
            >
                <p class="form__group form__group--horizontal">
                    @csrf
                    @method('PATCH')
                    <button class="form__button form__button--filled form__button--centered">
                        <i class="{{ config('other.font-awesome') }} fa-eye"></i>
                        {{ __('pm.mark-all-read') }}
                    </button>
                </p>
            </form>
            <form
                method="POST"
                action="{{ route('users.conversations.mass_destroy', ['user' => $user]) }}"
                x-data="confirmation"
            >
                @csrf
                @method('DELETE')
                <p class="form__group form__group--horizontal">
                    <button
                        x-on:click.prevent="confirmAction"
                        data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete all private messages?') }}"
                        class="form__button form__button--filled form__button--centered"
                    >
                        <i class="{{ config('other.font-awesome') }} fa-trash"></i>
                        {{ __('pm.empty-inbox') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
