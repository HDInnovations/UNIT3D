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
        {{ $conversation->subject }}
    </li>
@endsection

@section('page', 'page__user-conversation--show')

@section('main')
    @foreach ($conversation->messages as $message)
        <section class="panelV2">
            <header class="panel__header">
                <h2 class="panel__heading">
                    <x-user-tag :user="$message->sender" :anon="false" />
                </h2>
                <div class="panel__actions">
                    <div class="panel__action">
                        <time
                            datetime="{{ $message->created_at }}"
                            title="{{ $message->created_at }}"
                        >
                            {{ $message->created_at?->diffForHumans() }}
                        </time>
                    </div>
                </div>
            </header>

            <div class="panel__body bbcode-rendered">
                @bbcode($message->message)
            </div>
        </section>
    @endforeach

    <section class="panelV2">
        <h2 class="panel__heading">{{ __('pm.reply') }}</h2>
        <div class="panel__body">
            @if ($conversation->users->contains(fn ($user) => $user->id === \App\Models\User::SYSTEM_USER_ID))
                You can not reply to the system user.
            @else
                <form
                    method="POST"
                    action="{{ route('users.conversations.update', ['user' => $user, 'conversation' => $conversation]) }}"
                >
                    @csrf
                    @method('PATCH')
                    @livewire('bbcode-input', ['name' => 'message', 'label' => __('pm.reply'), 'required' => true])
                    <p class="form__group">
                        <button class="form__button form__button--filled">
                            {{ __('pm.reply') }}
                        </button>
                    </p>
                </form>
            @endif
        </div>
    </section>
@endsection

@section('sidebar')
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.users') }}</h2>
        <div class="panel__body">
            <ul>
                @foreach ($conversation->users as $conversationUser)
                    <li>
                        <x-user-tag :user="$conversationUser" :anon="false" />
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
    <section class="panelV2">
        <h2 class="panel__heading">{{ __('common.actions') }}</h2>
        <div class="panel__body">
            <form
                action="{{ route('users.conversations.destroy', ['user' => $user, 'conversation' => $conversation]) }}"
                method="POST"
            >
                @csrf
                @method('DELETE')
                <p class="form__group form__group--horizontal">
                    <button class="form__button form__button--filled form__button--centered">
                        <i class="{{ config('other.font-awesome') }} fa-trash"></i>
                        {{ __('pm.delete') }}
                    </button>
                </p>
            </form>
        </div>
    </section>
@endsection
