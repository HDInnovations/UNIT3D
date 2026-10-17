<section class="panelV2">
    <header class="panel__header">
        <h2 class="panel__heading">{{ __('user.bans') }}</h2>
        <div class="panel__actions">
            <div class="panel__action">
                @if ($user->group->slug === 'banned')
                    <button class="form__button form__button--text" popovertarget="unban-add">
                        {{ __('user.unban') }}
                    </button>
                    <dialog id="unban-add" class="dialog" popover>
                        <h3 class="dialog__heading">Unban user: {{ $user->username }}</h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('staff.unbans.store') }}"
                        >
                            @csrf
                            <input type="hidden" name="owned_by" value="{{ $user->id }}" />
                            <p class="form__group">
                                <textarea
                                    id="unban_reason"
                                    class="form__textarea"
                                    name="unban_reason"
                                    required
                                ></textarea>
                                <label class="form__label form__label--floating" for="unban_reason">
                                    Reason
                                </label>
                                <span class="form__hint">
                                    The reason is only visible for staff.
                                </span>
                            </p>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('user.unban') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="unban-add"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                @else
                    <button class="form__button form__button--text" popovertarget="unban-add">
                        {{ __('user.ban') }}
                    </button>
                    <dialog id="unban-add" class="dialog" popover>
                        <h3 class="dialog__heading">Ban user: {{ $user->username }}</h3>
                        <form
                            class="dialog__form"
                            method="POST"
                            action="{{ route('staff.bans.store') }}"
                        >
                            @csrf
                            <p class="form__group">
                                <textarea
                                    id="ban_reason"
                                    class="form__textarea"
                                    name="ban_reason"
                                    required
                                ></textarea>
                                <label class="form__label form__label--floating" for="ban_reason">
                                    Reason
                                </label>
                                <span class="form__hint">
                                    The reason will be emailed to the user.
                                </span>
                            </p>
                            <input type="hidden" name="owned_by" value="{{ $user->id }}" />
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('user.ban') }}
                                </button>
                                <button
                                    class="form__button form__button--outlined"
                                    type="button"
                                    popovertarget="unban-add"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                @endif
            </div>
        </div>
    </header>
    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ __('common.user') }}</th>
                    <th>{{ __('user.judge') }}</th>
                    <th>{{ __('user.reason-ban') }}</th>
                    <th>{{ __('user.reason-unban') }}</th>
                    <th>{{ __('user.created') }}</th>
                    <th>{{ __('user.removed') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bans as $ban)
                    <tr>
                        <td>
                            <x-user-tag :user="$ban->user" :anon="false" />
                        </td>
                        <td>
                            <x-user-tag :user="$ban->staff" :anon="false" />
                        </td>
                        <td>{{ $ban->ban_reason }}</td>
                        <td>{{ $ban->unban_reason }}</td>
                        <td>
                            <time
                                datetime="{{ $ban->created_at }}"
                                title="{{ $ban->created_at }}"
                            >
                                {{ $ban->created_at }}
                            </time>
                        </td>
                        <td>
                            <time
                                datetime="{{ $ban->removed_at }}"
                                title="{{ $ban->removed_at }}"
                            >
                                {{ $ban->removed_at }}
                            </time>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">{{ __('user.no-ban') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
