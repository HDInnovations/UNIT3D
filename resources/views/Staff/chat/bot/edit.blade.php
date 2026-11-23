@extends('layout.with-main')

@section('title')
    <title>{{ __('bot.edit-bot') }} - {{ config('other.title') }}</title>
@endsection

@section('breadcrumbs')
    <li class="breadcrumbV2">
        <a href="{{ route('staff.dashboard.index') }}" class="breadcrumb__link">
            {{ __('staff.staff-dashboard') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        <a href="{{ route('staff.bots.index') }}" class="breadcrumb__link">
            {{ __('bot.bots') }}
        </a>
    </li>
    <li class="breadcrumbV2">
        {{ $bot->name }}
    </li>
    <li class="breadcrumb--active">
        {{ __('common.edit') }}
    </li>
@endsection

@section('nav-tabs')
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('staff.statuses.index') }}">
            {{ __('staff.statuses') }}
        </a>
    </li>
    <li class="nav-tabV2">
        <a class="nav-tab__link" href="{{ route('staff.chatrooms.index') }}">
            {{ __('staff.rooms') }}
        </a>
    </li>
    <li class="nav-tab--active">
        <a class="nav-tab--active__link" href="{{ route('staff.bots.index') }}">
            {{ __('staff.bots') }}
        </a>
    </li>
@endsection

@section('page', 'page__staff-bot--edit')

@section('main')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('bot.edit-bot') }}: {{ $bot->name }}</h2>
        <div class="panel__body">
            <form
                class="form"
                method="POST"
                action="{{ route('staff.bots.update', ['bot' => $bot]) }}"
            >
                @csrf
                @method('PATCH')
                <p class="form__group">
                    <input
                        id="name"
                        class="form__text"
                        name="name"
                        placeholder=" "
                        type="text"
                        value="{{ $bot->name }}"
                    />
                    <label class="form__label form__label--floating" for="name">
                        {{ __('bot.name') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="position"
                        class="form__text"
                        inputmode="numeric"
                        name="position"
                        pattern="[0-9]*"
                        placeholder=" "
                        type="text"
                        value="{{ $bot->position }}"
                    />
                    <label class="form__label form__label--floating" for="position">
                        {{ __('common.position') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="command"
                        class="form__text"
                        name="command"
                        placeholder=" "
                        type="text"
                        value="{{ $bot->command }}"
                    />
                    <label class="form__label form__label--floating" for="command">
                        {{ __('bot.command') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="emoji"
                        class="form__text"
                        name="emoji"
                        placeholder=" "
                        type="text"
                        value="{{ $bot->emoji }}"
                    />
                    <label class="form__label form__label--floating" for="emoji">
                        {{ __('bot.emoji-code') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="icon"
                        class="form__text"
                        name="icon"
                        placeholder=" "
                        type="text"
                        value="{{ $bot->icon }}"
                    />
                    <label class="form__label form__label--floating" for="icon">
                        {{ __('bot.icon') }}
                    </label>
                </p>
                <p class="form__group">
                    <input
                        id="color"
                        class="form__text"
                        name="color"
                        placeholder=" "
                        type="text"
                        value="{{ $bot->color }}"
                    />
                    <label class="form__label form__label--floating" for="color">
                        {{ __('bot.color') }}
                    </label>
                </p>
                <p class="form__group">
                    <textarea id="help" class="form__textarea" name="help" placeholder=" ">
{{ $bot->help }}</textarea
                    >
                    <label class="form__label form__label--floating" for="help">
                        {{ __('bot.help') }}
                    </label>
                </p>
                <p class="form__group">
                    <button class="form__button form__button--filled">
                        {{ __('common.edit') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
