@php
    $user = App\Models\User::with(['chatroom', 'group'])->find(auth()->id());
@endphp

<section
    id="chatbody"
    class="panelV2 chatbox"
    x-data="chatbox(@js($user))"
    :class="state.ui.fullscreen && 'chatbox--fullscreen'"
    audio="false"
>
    <div class="loading__spinner" x-show="state.ui.loading">
        <div class="spinner__dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
        <div class="spinner__text">Chatbox Loading</div>
    </div>

    <div x-show="!state.ui.loading">
        <header class="panel__header" id="chatbox_header">
            <h2 class="panel__heading">
                <i class="fas fa-comment-dots"></i>
                Chatbox
            </h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <button class="form__button form__button--text" @click.prevent="startBot()">
                        <i class="fa fa-robot"></i>
                        <span x-text="state.message.helpName"></span>
                    </button>
                </div>
                <div class="panel__action" x-show="state.chat.target < 1 && state.chat.bot < 1">
                    <button class="form__button form__button--text" @click.prevent="toggleUserList">
                        <i class="fa fa-users"></i>
                        Users:
                        <span x-text="users.size"></span>
                    </button>
                </div>
                <div class="panel__action">
                    <template
                        x-if="state.chat.room && state.chat.room > 0 && state.chat.bot < 1 && state.chat.target < 1"
                    >
                        <button
                            class="form__button form__standard-icon-button form__standard-icon-button--skinny"
                            @click.prevent="changeAudible('room', state.chat.room, state.chat.listening ? 0 : 1)"
                            :style="'color: ' + (state.chat.listening ? 'rgb(0,102,0)' : 'rgb(204,0,0)')"
                        >
                            <i
                                :class="state.chat.listening ? 'fa fa-bell' : 'fa fa-bell-slash'"
                            ></i>
                        </button>
                    </template>
                    <template
                        x-if="state.chat.bot && state.chat.bot >= 1 && state.chat.target < 1"
                    >
                        <button
                            class="form__button form__standard-icon-button form__standard-icon-button--skinny"
                            @click.prevent="changeAudible('bot', state.chat.bot, state.chat.listening ? 0 : 1)"
                            :style="'color: ' + (state.chat.listening ? 'rgb(0,102,0)' : 'rgb(204,0,0)')"
                        >
                            <i
                                :class="state.chat.listening ? 'fa fa-bell' : 'fa fa-bell-slash'"
                            ></i>
                        </button>
                    </template>
                    <template
                        x-if="state.chat.target && state.chat.target >= 1 && state.chat.bot < 1"
                    >
                        <button
                            class="form__button form__standard-icon-button form__standard-icon-button--skinny"
                            @click.prevent="changeAudible('target', state.chat.target, state.chat.listening ? 0 : 1)"
                            :style="'color: ' + (state.chat.listening ? 'rgb(0,102,0)' : 'rgb(204,0,0)')"
                        >
                            <i
                                :class="state.chat.listening ? 'fa fa-bell' : 'fa fa-bell-slash'"
                            ></i>
                        </button>
                    </template>
                </div>
                <div class="panel__action">
                    <button
                        class="form__button form__standard-icon-button form__standard-icon-button--skinny"
                        title="Toggle typing notifications"
                        @click.prevent="changeWhispers()"
                        :style="'color: ' + (state.chat.showWhispers ? 'rgb(0,102,0)' : 'rgb(204,0,0)')"
                    >
                        <i
                            :class="state.chat.showWhispers ? 'fas fa-keyboard' : 'fa fa-keyboard'"
                        ></i>
                    </button>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <select
                            id="currentChatroom"
                            class="form__select"
                            x-model.number="state.chat.room"
                        >
                            <template x-for="chatroom in chatrooms" :key="chatroom.id">
                                <option :value="chatroom.id" x-text="chatroom.name"></option>
                            </template>
                        </select>
                        <label class="form__label form__label--floating" for="currentChatroom">
                            Room
                        </label>
                    </div>
                </div>
                <div class="panel__action">
                    <div class="form__group">
                        <select
                            id="currentChatstatus"
                            class="form__select"
                            x-model.number="auth.chat_status_id"
                        >
                            <template x-for="chatstatus in statuses" :key="chatstatus.id">
                                <option
                                    :value="chatstatus.id"
                                    :selected="chatstatus.id === auth.chat_status_id"
                                    x-text="chatstatus.name"
                                ></option>
                            </template>
                        </select>
                        <label class="form__label form__label--floating" for="currentChatstatus">
                            Status
                        </label>
                    </div>
                </div>
                <div class="panel__action">
                    <button
                        id="panel-fullscreen"
                        class="form__button form__standard-icon-button"
                        title="Toggle Fullscreen"
                        @click.prevent="changeFullscreen()"
                    >
                        <i :class="state.ui.fullscreen ? 'fas fa-compress' : 'fas fa-expand'"></i>
                    </button>
                </div>
            </div>
        </header>
        <menu id="chatbox_tabs" class="panel__tabs" role="tablist">
            <template x-for="echo in echoes" :key="echo.id">
                <li
                    x-show="echo.room && echo.room.name && echo.room.name.length > 0"
                    class="panel__tab chatbox__tab"
                    :class="state.chat.tab && echo.room && state.chat.tab === echo.room.name && 'panel__tab--active'"
                    role="tab"
                    @click.prevent="changeTab('room', echo.room.id)"
                >
                    <i
                        class="fa fa-comment"
                        :class="checkPings('room', echo.room && echo.room.id ? echo.room.id : 0) ? 'fa-beat text-success' : 'text-danger'"
                    ></i>
                    <span x-text="echo.room && echo.room.name ? echo.room.name : ''"></span>
                    <button
                        x-show="state.chat.tab && echo.room && state.chat.tab === echo.room.name"
                        class="chatbox__tab-delete-button"
                        @click.prevent="leaveRoom(state.chat.room)"
                    >
                        <i class="fa fa-times chatbox__tab-delete-icon"></i>
                    </button>
                </li>
            </template>
            <template x-for="echo in echoes" :key="echo.id">
                <li
                    x-show="echo.target && echo.target.id >= 3 && echo.target.username && echo.target.username.length > 0"
                    class="panel__tab chatbox__tab"
                    :class="state.chat.target >= 3 && echo.target && state.chat.target === echo.target.id && 'panel__tab--active'"
                    role="tab"
                    @click.prevent="changeTab('target', echo.target.id)"
                >
                    <i
                        class="fa fa-comment"
                        :class="checkPings('target', echo.target && echo.target.id ? echo.target.id : 0) ? 'fa-beat text-success' : 'text-danger'"
                    ></i>
                    @
                    <span
                        x-text="echo.target && echo.target.username ? echo.target.username : ''"
                    ></span>
                    <button
                        x-show="state.chat.target >= 3 && echo.target && state.chat.target === echo.target.id"
                        class="chatbox__tab-delete-button"
                        @click.prevent="leaveTarget(state.chat.target)"
                    >
                        <i class="fa fa-times chatbox__tab-delete-icon"></i>
                    </button>
                </li>
            </template>
            <template x-for="echo in echoes" :key="echo.id">
                <li
                    x-show="echo.bot && echo.bot.id >= 1 && echo.bot.name && echo.bot.name.length > 0"
                    class="panel__tab chatbox__tab"
                    :class="state.chat.bot > 0 && echo.bot && state.chat.bot === echo.bot.id && 'panel__tab--active'"
                    role="tab"
                    @click.prevent="changeTab('bot', echo.bot.id)"
                >
                    <i
                        class="fa fa-comment"
                        :class="checkPings('bot', echo.bot && echo.bot.id ? echo.bot.id : 0) ? 'fa-beat text-success' : 'text-danger'"
                    ></i>
                    @
                    <span x-text="echo.bot && echo.bot.name ? echo.bot.name : ''"></span>
                    <button
                        x-show="state.chat.bot > 0 && echo.bot && state.chat.bot === echo.bot.id"
                        class="chatbox__tab-delete-button"
                        @click.prevent="leaveBot(state.chat.bot)"
                    >
                        <i class="fa fa-times chatbox__tab-delete-icon"></i>
                    </button>
                </li>
            </template>
        </menu>
        <div class="chatbox__chatroom">
            <template x-if="state.chat.tab !== ''">
                <div class="chatroom__messages--wrapper" x-ref="messagesWrapper">
                    <ul class="chatroom__messages">
                        <template x-for="message in [...messages.values()]" :key="message.id">
                            <li>
                                <article class="chatbox-message">
                                    <header class="chatbox-message__header">
                                        <address
                                            class="chatbox-message__address user-tag"
                                            :style="(message.user?.is_donor ? 'background-image: url(/img/sparkels.gif);' : (message.user?.group?.effect ? 'background-image:' + message.user.group.effect + ';' : ''))"
                                        >
                                            <a
                                                class="user-tag__link"
                                                :class="message.user?.group?.icon"
                                                :href="message.user?.username ? '/users/' + message.user.username : ''"
                                                :style="message.user?.group?.color ? 'color:' + message.user.group.color : ''"
                                                :title="message.user?.group?.name"
                                            >
                                                <span
                                                    x-show="message.user && message.user.id > 1"
                                                    style="padding-right: 5px"
                                                    x-text="message.user?.username || 'Unknown'"
                                                ></span>
                                                <span
                                                    x-show="message.bot && message.bot.id >= 1 && (! message.user || message.user.id < 2)"
                                                    x-text="message.bot?.name || 'Unknown'"
                                                ></span>
                                                <template x-if="message.user?.icon">
                                                    <i>
                                                        <img
                                                            :style="'max-height: 16px; vertical-align: text-bottom;'"
                                                            title="Custom User Icon"
                                                            :src="'/authenticated-images/user-icons/' + message.user.username"
                                                            loading="lazy"
                                                        />
                                                    </i>
                                                </template>
                                                <i
                                                    x-show="message.user?.is_lifetime == 1"
                                                    class="fal fa-star"
                                                    id="lifeline"
                                                    title="Lifetime Donor"
                                                ></i>
                                                <i
                                                    x-show="message.user?.is_donor == 1 && message.user?.is_lifetime == 0"
                                                    class="fal fa-star text-gold"
                                                    title="Donor"
                                                ></i>
                                            </a>
                                        </address>
                                        <div
                                            x-show="message.bot && message.bot.id >= 1 && (! message.user || message.user.id < 2)"
                                            class="bbcode-rendered bot-message"
                                            style="
                                                font-style: italic;
                                                white-space: nowrap;
                                                display: inline;
                                            "
                                            x-html="message.message"
                                        ></div>
                                        <time
                                            x-show="message.bot && message.bot.id >= 1 && (! message.user || message.user.id < 2)"
                                            style="
                                                margin-left: 10px;
                                                white-space: nowrap;
                                                display: inline;
                                            "
                                            class="chatbox-message__time"
                                            :datetime="message.created_at"
                                            :title="message.created_at"
                                            x-text="formatTime(message.created_at)"
                                        ></time>
                                        <time
                                            x-show="! (message.bot && message.bot.id >= 1 && (! message.user || message.user.id < 2))"
                                            class="chatbox-message__time"
                                            :datetime="message.created_at"
                                            :title="message.created_at"
                                            x-text="formatTime(message.created_at)"
                                        ></time>
                                    </header>
                                    <aside class="chatbox-message__aside">
                                        <figure class="chatbox-message__figure">
                                            <i
                                                class="fa fa-bell"
                                                title="System Notification"
                                                x-show="message.bot && message.bot.id >= 1 && (! message.user || message.user.id < 2)"
                                            ></i>
                                            <a
                                                x-show="message.user && message.user.id != 1"
                                                :href="'/users/' + message.user.username"
                                                class="chatbox-message__avatar-link"
                                            >
                                                <img
                                                    x-show="message.user && message.user.id != 1"
                                                    class="chatbox-message__avatar"
                                                    :src="message.user?.image ? '/authenticated-images/user-avatars/' + message.user.username : '/img/profile.png'"
                                                    :style="'border: 2px solid ' + (message.user?.chat_status?.color || '#ccc')"
                                                    :title="message.user?.chat_status?.name"
                                                    loading="lazy"
                                                />
                                            </a>
                                        </figure>
                                    </aside>
                                    <section
                                        class="chatbox-message__content bbcode-rendered"
                                        x-show="! (message.bot && message.bot.id >= 1 && (! message.user || message.user.id < 2))"
                                        x-html="message.message"
                                    ></section>
                                    <!-- Move menu back to original position after timestamp -->
                                    <menu
                                        class="chatbox-message__menu"
                                        x-show="message.canMod === true || message.canMod === 1"
                                    >
                                        <li class="chatbox-message__menu-item">
                                            <button
                                                class="chatbox-message__delete-button"
                                                title="Delete Message"
                                                @click.prevent="deleteMessage(message.id)"
                                                style="
                                                    cursor: pointer;
                                                    padding: 0;
                                                    margin-left: 8px;
                                                "
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </li>
                                    </menu>
                                </article>
                            </li>
                        </template>
                        <li x-show="messages.size === 0">
                            There is no chat history here. Send a message!
                        </li>
                    </ul>
                </div>
            </template>
            <section
                class="chatroom__users"
                x-show="state.chat.showUserList && state.chat.target < 1 && state.chat.bot < 1"
            >
                <h2 class="chatroom-users__heading">Users</h2>
                <ul class="chatroom-users__list">
                    <template x-for="user in [...users.values()]" :key="user.id">
                        <li class="chatroom-users__list-item">
                            <span class="chatroom-users__user user-tag">
                                <a
                                    class="chatroom-users__user-link user-tag__link"
                                    :href="'/users/' + user.username"
                                >
                                    <span x-text="user.username"></span>
                                </a>
                            </span>
                            <menu class="chatroom-users__buttons" x-show="auth.id !== user.id">
                                <li>
                                    <button
                                        class="chatroom-users__button"
                                        title="Gift user bon"
                                        @click.prevent="forceGift(user.username)"
                                    >
                                        <i class="fas fa-gift"></i>
                                    </button>
                                </li>
                                <li>
                                    <button
                                        class="chatroom-users__button"
                                        title="Send chat PM"
                                        @click.prevent="forceMessage(user.username)"
                                    >
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </li>
                            </menu>
                        </li>
                    </template>
                </ul>
            </section>
            <section class="chatroom__whispers" x-show="state.chat.showWhispers">
                <span
                    x-show="state.chat.target < 1 && state.chat.bot < 1 && activePeer && activePeer.size > 0"
                    x-text="
                        activePeer.size > 3
                            ? 'Several people are typing...'
                            : activePeer.size === 1
                              ? [...activePeer.keys()][0] + ' is typing...'
                              : [...activePeer.keys()].slice(0, -1).join(', ') +
                                ' and ' +
                                [...activePeer.keys()][activePeer.size - 1] +
                                ' are typing...'
                    "
                ></span>
            </section>
            <form
                class="form chatroom__new-message"
                @submit.prevent="createMessage($refs.message.value, auth.id, state.message.receiver_id, state.message.bot_id)"
            >
                <p class="form__group">
                    <textarea
                        id="chatbox__messages-create"
                        class="form__textarea"
                        name="message"
                        placeholder=" "
                        x-ref="message"
                        @keydown.enter="!$event.shiftKey && ($event.preventDefault(), createMessage($refs.message.value, auth.id, state.message.receiver_id, state.message.bot_id), $refs.message.value = '')"
                        @keyup="isTyping(auth)"
                    ></textarea>
                    <label class="form__label form__label--floating" for="chatbox__messages-create">
                        Write your message...
                    </label>
                </p>
            </form>
        </div>
    </div>
</section>
