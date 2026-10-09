<section class="panelV2" x-data="tabs($wire.entangle('tab').live)">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('pm.inbox') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                <p class="form__group">
                    <input
                        id="subject"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        name="subject"
                        placeholder=" "
                        wire:model.live="subject"
                    />
                    <label class="form__label form__label--floating" for="subject">
                        {{ __('pm.subject') }}
                    </label>
                </p>
            </div>
            <div class="panel__action">
                <p class="form__group">
                    <input
                        id="username"
                        class="form__text"
                        name="username"
                        placeholder=" "
                        wire:model.live="username"
                    />
                    <label class="form__label form__label--floating" for="username">
                        {{ __('common.username') }}
                    </label>
                </p>
            </div>
            <div class="panel__action">
                <p class="form__group">
                    <input
                        id="message"
                        class="form__text"
                        type="search"
                        autocomplete="off"
                        name="message"
                        placeholder=" "
                        wire:model.live="message"
                    />
                    <label class="form__label form__label--floating" for="message">
                        {{ __('common.message') }}
                    </label>
                </p>
            </div>
        </div>
    </header>
    <menu class="panel__tabs panel__tabs--centered">
        <li class="panel__tab--full-width" x-bind="tabButton" data-tab="all">
            {{ __('stat.all') }}
        </li>
        <li class="panel__tab--full-width" x-bind="tabButton" data-tab="unread">
            {{ __('pm.unread') }}
        </li>
        <li class="panel__tab--full-width" x-bind="tabButton" data-tab="inbox">
            {{ __('pm.inbox') }}
        </li>
        <li class="panel__tab--full-width" x-bind="tabButton" data-tab="outbox">
            {{ __('pm.outbox') }}
        </li>
    </menu>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('user.user') }}</th>
                    <th wire:click="sortBy('subject')" role="columnheader button">
                        {{ __('pm.subject') }}
                        @include('livewire.includes._sort-icon', ['field' => 'subject'])
                    </th>
                    <th wire:click="sortBy('updated_at')" role="columnheader button">
                        {{ __('common.date') }}
                        @include('livewire.includes._sort-icon', ['field' => 'updated_at'])
                    </th>
                    <th
                        wire:click="sortBy('messages_count')"
                        role="columnheader button"
                        style="white-space: nowrap"
                    >
                        {{ __('forum.replies') }}
                        @include('livewire.includes._sort-icon', ['field' => 'messages_count'])
                    </th>
                    <th>{{ __('pm.read') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($conversations as $conversation)
                    <tr>
                        <td>
                            @foreach ($conversation->users as $sender)
                                <x-user-tag :user="$sender" :anon="false" />
                            @endforeach
                        </td>
                        <td>
                            <a
                                href="{{ route('users.conversations.show', ['user' => $user, 'conversation' => $conversation]) }}"
                            >
                                {{ $conversation->subject }}
                            </a>
                        </td>
                        <td>
                            <time
                                style="white-space: nowrap"
                                datetime="{{ $conversation->updated_at }}"
                                title="{{ $conversation->updated_at }}"
                            >
                                {{ $conversation->updated_at->diffForHumans() }}
                            </time>
                        </td>
                        <td>
                            {{ $conversation->messages_count }}
                        </td>
                        <td>
                            @if ($conversation->participants->first()->read)
                                <i
                                    class="{{ \config('other.font-awesome') }} fa-check text-green"
                                    title="{{ __('pm.read') }}"
                                ></i>
                            @else
                                <i
                                    class="{{ \config('other.font-awesome') }} fa-times text-red"
                                    title="{{ __('pm.unread') }}"
                                ></i>
                            @endif
                        </td>
                        <td>
                            <menu class="data-table__actions">
                                <li class="data-table__action">
                                    @if ($conversation->participants->first()->read)
                                        <button
                                            class="form__button form__button--text"
                                            wire:click="markUnread({{ $conversation->id }})"
                                        >
                                            {{ __('pm.unread') }}
                                        </button>
                                    @else
                                        <button
                                            class="form__button form__button--text"
                                            wire:click="markRead({{ $conversation->id }})"
                                        >
                                            {{ __('pm.read') }}
                                        </button>
                                    @endif
                                </li>
                                <li class="data-table__action">
                                    <button
                                        class="form__button form__button--text"
                                        wire:click="destroy({{ $conversation->id }})"
                                    >
                                        {{ __('common.delete') }}
                                    </button>
                                </li>
                            </menu>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $conversations->links('partials.pagination') }}
</section>
