<article class="sidebar2">
    <div>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('notification.notifications') }}</h2>
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('notification.title') }}</th>
                            <th>{{ __('notification.message') }}</th>
                            <th>{{ __('notification.date') }}</th>
                            <th>{{ __('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notification)
                            <tr>
                                <td
                                    class="{{ $notification->read_at === null ? 'notification--unread' : 'notification--read' }}"
                                >
                                    <a
                                        href="{{ route('users.notifications.show', ['user' => $user, 'notification' => $notification]) }}"
                                    >
                                        {{ $notification->data['title'] }}
                                    </a>
                                </td>
                                <td style="word-break: break-all">
                                    {{ $notification->data['body'] }}
                                </td>
                                <td>
                                    <time
                                        datetime="{{ $notification->created_at }}"
                                        title="{{ $notification->created_at }}"
                                    >
                                        {{ $notification->created_at->diffForHumans() }}
                                    </time>
                                </td>
                                <td>
                                    <menu class="data-table__actions">
                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('users.notifications.update', ['user' => $user, 'notification' => $notification]) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    class="form__button form__button--text"
                                                    @disabled($notification->read_at !== null)
                                                >
                                                    {{ __('notification.mark-read') }}
                                                </button>
                                            </form>
                                        </li>
                                        <li class="data-table__action">
                                            <form
                                                action="{{ route('users.notifications.destroy', ['user' => $user, 'notification' => $notification]) }}"
                                                method="POST"
                                                x-data="confirmation"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    x-on:click.prevent="confirmAction"
                                                    data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete this notification: ' . $notification->data['body'] . '?') }}"
                                                    class="form__button form__button--text"
                                                >
                                                    {{ __('notification.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </menu>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">{{ __('notification.no-notifications') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $notifications->links('partials.pagination') }}
            </div>
        </section>
    </div>
    <aside>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('notification.filter-by-type') }}</h2>
            <div class="panel__body">
                <fieldset class="form__fieldset">
                    <legend class="form__legend">{{ __('torrent.filters') }}</legend>
                    <div class="form__fieldset-checkbox-container">
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="bon_gifts"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-coins text-success"
                                ></i>
                                {{ __('notification.bon-gifts') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="comment"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-comments text-success"
                                ></i>
                                {{ __('common.comments') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="comment_tags"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-tag text-success"
                                ></i>
                                {{ __('notification.comment-tags') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="followers"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-smile-plus text-success"
                                ></i>
                                {{ __('user.followers') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="playlist_suggestions"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-comment-dots text-success"
                                ></i>
                                {{ __('playlist.playlist-suggestions') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="playlist_suggestion_rejections"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-comment-dots text-success"
                                ></i>
                                {{ __('playlist.playlist-suggestion-rejections') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="posts"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-comment-dots text-success"
                                ></i>
                                {{ __('common.posts') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="post_tags"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-tag text-success"
                                ></i>
                                {{ __('notification.post-tags') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="post_tips"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-coins text-success"
                                ></i>
                                {{ __('notification.post-tips') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="request_bounties"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-crosshairs text-success"
                                ></i>
                                {{ __('notification.request-bounties') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="request_claims"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check-circle text-success"
                                ></i>
                                {{ __('notification.request-claims') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="request_fills"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-check-square text-success"
                                ></i>
                                {{ __('notification.request-fills') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="request_approvals"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-clipboard-check text-success"
                                ></i>
                                {{ __('notification.request-approvals') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="request_rejections"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times text-success"
                                ></i>
                                {{ __('notification.request-rejections') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="request_unclaims"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-times-square text-success"
                                ></i>
                                {{ __('notification.request-unclaims') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="reseed_requests"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-question text-success"
                                ></i>
                                {{ __('notification.reseed-requests') }}
                            </label>
                        </p>
                        @if (config('other.thanks-system.is-enabled'))
                            <p class="form__group">
                                <label class="form__label">
                                    <input
                                        class="form__checkbox"
                                        type="checkbox"
                                        wire:model.live.prefetch="thanks"
                                        value="1"
                                    />
                                    <i
                                        class="{{ config('other.font-awesome') }} fa-heart text-success"
                                    ></i>
                                    {{ __('torrent.thanks') }}
                                </label>
                            </p>
                        @endif

                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="upload_tips"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-coins text-success"
                                ></i>
                                {{ __('bon.tips') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="topics"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-comment-alt-check text-success"
                                ></i>
                                {{ __('common.topics') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="unfollows"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-frown text-success"
                                ></i>
                                {{ __('notification.unfollows') }}
                            </label>
                        </p>
                        <p class="form__group">
                            <label class="form__label">
                                <input
                                    class="form__checkbox"
                                    type="checkbox"
                                    wire:model.live.prefetch="uploads"
                                    value="1"
                                />
                                <i
                                    class="{{ config('other.font-awesome') }} fa-upload text-success"
                                ></i>
                                {{ __('user.uploads') }}
                            </label>
                        </p>
                    </div>
                </fieldset>
            </div>
        </section>
        <section class="panelV2">
            <h2 class="panel__heading">{{ __('common.actions') }}</h2>
            <div class="panel__body">
                <form
                    action="{{ route('users.notifications.mass_update', ['user' => $user]) }}"
                    method="POST"
                    x-data="confirmation"
                >
                    @csrf
                    @method('PATCH')
                    <p class="form__group form__group--horizontal">
                        <button
                            x-on:click.prevent="confirmAction"
                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to mark all notifications as read?') }}"
                            class="form__button form__button--filled form__button--centered"
                        >
                            {{ __('notification.mark-all-read') }}
                        </button>
                    </p>
                </form>
                <form
                    action="{{ route('users.notifications.mass_destroy', ['user' => $user]) }}"
                    method="POST"
                    x-data="confirmation"
                >
                    @csrf
                    @method('DELETE')
                    <p class="form__group form__group--horizontal">
                        <button
                            x-on:click.prevent="confirmAction"
                            data-b64-deletion-message="{{ base64_encode('Are you sure you want to delete all notifications?') }}"
                            class="form__button form__button--filled form__button--centered"
                        >
                            {{ __('notification.delete-all') }}
                        </button>
                    </p>
                </form>
            </div>
        </section>
    </aside>
</article>
