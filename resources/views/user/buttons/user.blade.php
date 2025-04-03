@php
    $isModo = auth()->user()->group->is_modo;
    $isProfileOwner = auth()
        ->user()
        ->is($user);
@endphp

<li class="nav-tab-menu">
    <a
        class="{{ Route::is('users.show', 'users.edit') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        href="{{ route('users.show', ['user' => $user]) }}"
    >
        {{ __('user.profile') }}
    </a>
    <ul class="nav-tab-menu__items">
        <li class="{{ Route::is('users.show') ? 'nav-tab--active' : 'nav-tavV2' }}">
            <a
                class="{{ Route::is('users.show') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                href="{{ route('users.show', ['user' => $user]) }}"
            >
                {{ __('user.profile') }}
            </a>
        </li>
        @if ($isProfileOwner)
            <li class="{{ Route::is('users.edit') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.edit') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.edit', ['user' => $user]) }}"
                >
                    {{ __('user.edit-profile') }}
                </a>
            </li>
        @else
            <li class="nav-tabV2">
                @if ($user->followers()->where('users.id', '=', auth()->id())->exists())
                    <form
                        action="{{ route('users.followers.destroy', ['user' => $user]) }}"
                        method="POST"
                        style="display: contents"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            class="nav-tab__link"
                            type="submit"
                            id="delete-follow-{{ $user->target_id }}"
                        >
                            {{ __('user.unfollow') }}
                        </button>
                    </form>
                @else
                    <form
                        action="{{ route('users.followers.store', ['user' => $user]) }}"
                        method="POST"
                        style="display: contents"
                    >
                        @csrf
                        <button
                            class="nav-tab__link"
                            type="submit"
                            id="follow-user-{{ $user->id }}"
                        >
                            {{ __('user.follow') }}
                        </button>
                    </form>
                @endif
            </li>
            <li class="nav-tabV2" x-data="dialog">
                <button class="nav-tab__link" x-bind="showDialog">
                    {{ __('user.report') }}
                </button>
                <dialog class="dialog" x-bind="dialogElement">
                    <h3 class="dialog__heading">Report user: {{ $user->username }}</h3>
                    <form
                        class="dialog__form"
                        method="POST"
                        action="{{ route('report_user', ['username' => $user->username]) }}"
                        x-bind="dialogForm"
                    >
                        @csrf
                        <p class="form__group">
                            <textarea
                                id="report_reason"
                                class="form__textarea"
                                name="message"
                                required
                            ></textarea>
                            <label class="form__label form__label--floating" for="report_reason">
                                Reason
                            </label>
                        </p>
                        <p class="form__group">
                            <button class="form__button form__button--filled">
                                {{ __('common.save') }}
                            </button>
                            <button
                                formmethod="dialog"
                                formnovalidate
                                class="form__button form__button--outlined"
                            >
                                {{ __('common.cancel') }}
                            </button>
                        </p>
                    </form>
                </dialog>
            </li>
        @endif
    </ul>
</li>
@if ($isProfileOwner || $isModo)
    <li class="nav-tab-menu">
        <a
            class="{{ Route::is('users.general_settings.edit', 'user_security', 'user_privacy', 'user_notification') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
            href="{{ route('users.general_settings.edit', ['user' => $user]) }}"
        >
            {{ __('user.settings') }}
        </a>
        <ul class="nav-tab-menu__items">
            @if ($isProfileOwner)
                <li
                    class="{{ Route::is('users.general_settings.edit') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.general_settings.edit') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.general_settings.edit', ['user' => $user]) }}"
                    >
                        {{ __('user.general') }}
                    </a>
                </li>
            @endif

            <li class="{{ Route::is('users.email.edit') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.email.edit') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.email.edit', ['user' => $user]) }}"
                >
                    {{ __('common.email') }}
                </a>
            </li>
            <li class="{{ Route::is('users.password.edit') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.password.edit') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.password.edit', ['user' => $user]) }}"
                >
                    {{ __('common.password') }}
                </a>
            </li>
            <li class="{{ Route::is('users.passkeys.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.passkeys.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.passkeys.index', ['user' => $user]) }}"
                >
                    Passkey
                </a>
            </li>
            <li class="{{ Route::is('users.rsskeys.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.rsskeys.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.rsskeys.index', ['user' => $user]) }}"
                >
                    {{ __('user.rsskey') }}
                </a>
            </li>
            <li class="{{ Route::is('users.apikeys.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.apikeys.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.apikeys.index', ['user' => $user]) }}"
                >
                    {{ __('user.apikey') }}
                </a>
            </li>
            <li
                class="{{ Route::is('users.two_factor_auth.edit') ? 'nav-tab--active' : 'nav-tavV2' }}"
            >
                <a
                    class="{{ Route::is('users.two_factor_auth.edit') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.two_factor_auth.edit', ['user' => $user]) }}"
                >
                    {{ __('user.two-step-auth.title') }}
                </a>
            </li>
            @if ($isProfileOwner)
                <li
                    class="{{ Route::is('users.privacy_settings.edit') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.privacy_settings.edit') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.privacy_settings.edit', ['user' => $user]) }}"
                    >
                        {{ __('user.privacy') }}
                    </a>
                </li>
                <li
                    class="{{ Route::is('users.notification_settings.edit') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.notification_settings.edit') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.notification_settings.edit', ['user' => $user]) }}"
                    >
                        {{ __('user.notification') }}
                    </a>
                </li>
            @endif
        </ul>
    </li>
@endif

@if ($isProfileOwner || $isModo)
    <li class="nav-tab-menu">
        @if ($isProfileOwner || $isModo)
            <a
                class="{{ Route::is('users.history.index', 'users.torrents.index', 'users.peers.index', 'users.resurrections.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                href="{{ route('users.history.index', ['user' => $user]) }}"
            >
                {{ __('torrent.torrents') }}
            </a>
        @else
            <span tabindex="-1" class="nav-tab__link">
                {{ __('torrent.torrents') }}
            </span>
        @endif
        <ul class="nav-tab-menu__items">
            @if ($isProfileOwner || $isModo)
                <li
                    class="{{ Route::is('users.history.index') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.history.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.history.index', ['user' => $user]) }}"
                    >
                        {{ __('torrent.history') }}
                    </a>
                </li>
                <li class="nav-tabV2">
                    <a
                        class="nav-tab__link"
                        href="{{ route('users.torrents.index', ['user' => $user]) }}"
                    >
                        {{ __('user.uploads') }}
                    </a>
                </li>
                <li class="{{ Route::is('users.peers.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                    <a
                        class="{{ Route::is('users.peers.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.peers.index', ['user' => $user]) }}"
                    >
                        {{ __('user.active') }}
                    </a>
                </li>
                <li
                    class="{{ Route::is('users.resurrections.index') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.resurrections.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.resurrections.index', ['user' => $user]) }}"
                    >
                        {{ __('user.resurrections') }}
                    </a>
                </li>
            @endif

            @if (auth()->user()->isAllowed($user, '', 'show_requested'))
                <li class="nav-tavV2">
                    <a
                        class="nav-tab__link"
                        href="{{ route('requests.index', ['requestor' => $user->username]) }}"
                    >
                        {{ __('user.requested') }}
                    </a>
                </li>
            @endif

            @if ($isProfileOwner || $isModo)
                <li class="nav-tabV2">
                    <a
                        class="nav-tab__link"
                        href="{{ route('users.bookmarks.index', ['user' => $user]) }}"
                    >
                        {{ __('user.bookmarks') }}
                    </a>
                </li>
            @endif

            @if ($isProfileOwner)
                @if (! config('announce.external_tracker.is_enabled'))
                    <form
                        action="{{ route('users.peers.mass_destroy', ['user' => $user]) }}"
                        method="POST"
                        style="display: contents"
                    >
                        @csrf()
                        @method('DELETE')
                        <button class="nav-tab__link" type="submit">
                            {{ __('staff.flush-ghost-peers') }}
                        </button>
                    </form>
                @endif

                <li class="nav-tabV2" x-data="dialog">
                    <a class="nav-tab__link" x-bind="showDialog">Download Torrent Files</a>

                    <dialog class="dialog" x-bind="dialogElement">
                        <h3 class="dialog__heading">Download Torrent Files</h3>
                        <form
                            class="dialog__form"
                            action="{{ route('users.torrent_zip.show', ['user' => $user]) }}"
                            x-bind="dialogForm"
                        >
                            <fieldset>
                                <legend>Select download type:</legend>

                                <div>
                                    <input
                                        class="form__radio"
                                        type="radio"
                                        id="history"
                                        name="type"
                                        value="false"
                                        checked
                                    />
                                    <label for="history">All History</label>
                                </div>

                                <div>
                                    <input
                                        class="form__radio"
                                        type="radio"
                                        id="peer"
                                        name="type"
                                        value="true"
                                    />
                                    <label for="peer">Active Peers</label>
                                </div>
                            </fieldset>
                            <p class="form__group">
                                <button class="form__button form__button--filled">
                                    {{ __('common.download') }}
                                </button>
                                <button
                                    formmethod="dialog"
                                    formnovalidate
                                    class="form__button form__button--outlined"
                                >
                                    {{ __('common.cancel') }}
                                </button>
                            </p>
                        </form>
                    </dialog>
                </li>
            @endif
        </ul>
    </li>
@endif

<li class="nav-tab-menu">
    <span
        tabindex="-1"
        class="{{ Route::is('users.achievements.*', 'users.topics.index', 'users.posts.index', 'users.followers.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
    >
        {{ __('forum.activity') }}
    </span>
    <ul class="nav-tab-menu__items">
        @if (auth()->user()->isAllowed($user, 'achievement', 'show_achievement'))
            <li
                class="{{ Route::is('users.achievements.index') ? 'nav-tab--active' : 'nav-tavV2' }}"
            >
                <a
                    class="{{ Route::is('users.achievements.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.achievements.index', ['user' => $user]) }}"
                >
                    {{ __('user.achievements') }}
                </a>
            </li>
        @endif

        <li class="nav-tavV2">
            <a
                class="nav-tab__link"
                href="{{ route('playlists.index', ['username' => $user->username]) }}"
            >
                {{ __('playlist.playlists') }}
            </a>
        </li>

        @if (auth()->user()->isAllowed($user, 'forum', 'show_topic'))
            <li class="{{ Route::is('users.topics.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.topics.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.topics.index', ['user' => $user]) }}"
                >
                    {{ __('user.topics') }}
                </a>
            </li>
        @endif

        @if (auth()->user()->isAllowed($user, 'forum', 'show_post'))
            <li class="{{ Route::is('users.posts.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.posts.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.posts.index', ['user' => $user]) }}"
                >
                    {{ __('user.posts') }}
                </a>
            </li>
        @endif

        @if (auth()->user()->isAllowed($user, 'follower', 'show_follower'))
            <li class="{{ Route::is('users.followers.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.followers.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.followers.index', ['user' => $user]) }}"
                >
                    {{ __('user.followers') }}
                </a>
            </li>
        @endif

        @if (auth()->user()->group->is_modo || auth()->id() === $user->id)
            <li class="{{ Route::is('users.following.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.following.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.following.index', ['user' => $user]) }}"
                >
                    {{ __('user.following') }}
                </a>
            </li>
        @endif
    </ul>
</li>

@if ($isProfileOwner || $isModo)
    <li class="nav-tab-menu">
        <a
            class="{{ Route::is('users.earnings.index', 'users.transactions.create', 'users.gifts.index', 'users.gifts.create', 'users.post_tips.index', 'users.torrent_tips.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
            href="{{ route('users.earnings.index', ['user' => $user]) }}"
        >
            {{ __('bon.bonus') }} {{ __('bon.points') }}
        </a>
        <ul class="nav-tab-menu__items">
            <li class="{{ Route::is('users.earnings.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.earnings.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.earnings.index', ['user' => $user]) }}"
                >
                    {{ __('bon.earnings') }}
                </a>
            </li>
            @if ($isProfileOwner)
                <li
                    class="{{ Route::is('users.transactions.create') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.transactions.create') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.transactions.create', ['user' => $user]) }}"
                    >
                        {{ __('bon.store') }}
                    </a>
                </li>
            @endif

            <li class="{{ Route::is('users.gifts.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.gifts.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.gifts.index', ['user' => $user]) }}"
                >
                    {{ __('bon.gifts') }}
                </a>
            </li>
            <li class="{{ Route::is('users.post_tips.index') ? 'nav-tab--active' : 'nav-tavV2' }}">
                <a
                    class="{{ Route::is('users.post_tips.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.post_tips.index', ['user' => $user]) }}"
                >
                    {{ __('forum.post') }} {{ __('bon.tips') }}
                </a>
            </li>
            <li
                class="{{ Route::is('users.torrent_tips.index') ? 'nav-tab--active' : 'nav-tavV2' }}"
            >
                <a
                    class="{{ Route::is('users.torrent_tips.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                    href="{{ route('users.torrent_tips.index', ['user' => $user]) }}"
                >
                    {{ __('torrent.torrent') }} {{ __('bon.tips') }}
                </a>
            </li>
        </ul>
    </li>
@endif

@if ($isProfileOwner || $isModo)
    <li class="nav-tab-menu">
        <span
            tabindex="-1"
            class="{{ Route::is('users.wishes.*', 'users.seedboxes.*', 'users.invites.*') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
        >
            {{ __('common.other') }}
        </span>
        <ul class="nav-tab-menu__items">
            @if ($isProfileOwner || $isModo)
                <li
                    class="{{ Route::is('users.wishes.index') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.wishes.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.wishes.index', ['user' => $user]) }}"
                    >
                        {{ __('user.wishlist') }}
                    </a>
                </li>
                <li
                    class="{{ Route::is('users.seedboxes.index') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.seedboxes.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.seedboxes.index', ['user' => $user]) }}"
                    >
                        {{ __('user.seedboxes') }}
                    </a>
                </li>
                <li
                    class="{{ Route::is('users.invites.index') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.invites.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.invites.index', ['user' => $user]) }}"
                    >
                        {{ __('user.invites') }}
                    </a>
                </li>
                <li
                    class="{{ Route::is('users.invite_tree.index') ? 'nav-tab--active' : 'nav-tavV2' }}"
                >
                    <a
                        class="{{ Route::is('users.invite_tree.index') ? 'nav-tab--active__link' : 'nav-tab__link' }}"
                        href="{{ route('users.invite_tree.index', ['user' => $user]) }}"
                    >
                        {{ __('user.invite-tree') }}
                    </a>
                </li>
            @endif
        </ul>
    </li>
@endif
