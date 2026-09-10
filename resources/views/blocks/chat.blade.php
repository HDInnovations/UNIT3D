@php
    $user = App\Models\User::query()
    ->with(['chatroom', 'group', 'settings'])
    ->find(auth()->id());
@endphp

<section
    id="chatbody"
    class="panelV2 chatbox"
    x-data="chatbox"
    data-user="{{ base64_encode(Js::encode($user)) }}"
    :class="state.ui.fullscreen && 'chatbox--fullscreen'"
>
    <div class="loading__spinner" x-show="state.ui.loading">
        <div class="spinner__dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
        <div class="spinner__text">Chatbox loading</div>
    </div>

    <div x-show="!state.ui.loading">
        <header class="panel__header" id="chatbox_header">
            <h2 class="panel__heading">
                <i class="fas fa-comment-dots"></i>
                Chatbox
            </h2>
            <div class="panel__actions">
                <div class="panel__action">
                    <button
                        class="form__button form__button--text"
                        @click.prevent="changeBot(state.chat.bot.id)"
                    >
                        <i class="fa fa-robot"></i>
                        <span
                            x-text="state.chat && state.chat.bot && state.chat.bot.name"
                        ></span>
                    </button>
                </div>
                <div
                    class="panel__action"
                    x-show="state.chat.conversation && state.chat.conversation.room !== null"
                >
                    <button class="form__button form__button--text" @click.prevent="toggleUserList">
                        <i class="fa fa-users"></i>
                        Users:
                        <span x-text="users.size"></span>
                    </button>
                </div>
                <div class="panel__action">
                    <button
                        class="form__button form__standard-icon-button form__standard-icon-button--skinny"
                        @click.prevent="changeAudible(state.chat.conversation && state.chat.conversation.id)"
                        :style="'color: ' + (state.chat.conversation && state.chat.conversation.audible ? 'rgb(0,102,0)' : 'rgb(204,0,0)')"
                    >
                        <i
                            :class="state.chat.conversation && state.chat.conversation.audible ? 'fa fa-bell' : 'fa fa-bell-slash'"
                        ></i>
                    </button>
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
                        title="Toggle fullscreen"
                        @click.prevent="changeFullscreen()"
                    >
                        <i :class="state.ui.fullscreen ? 'fas fa-compress' : 'fas fa-expand'"></i>
                    </button>
                </div>
            </div>
        </header>
        <menu id="chatbox_tabs" class="panel__tabs" role="tablist">
            <template x-for="conversation in conversations" :key="conversation.id">
                <li
                    class="panel__tab chatbox__tab"
                    :class="state.chat.conversation && state.chat.conversation.id === conversation.id && 'panel__tab--active'"
                    role="tab"
                    @click.prevent="changeConversation(conversation.id)"
                >
                    <i
                        class="fa fa-comment"
                        :class="checkPings(conversation) ? 'fa-beat text-success' : 'text-danger'"
                    ></i>
                    <span
                        x-text="
                            (conversation.room && conversation.room.name) ||
                                (conversation.target && conversation.target.username) ||
                                (conversation.bot && conversation.bot.name) ||
                                ''
                        "
                    ></span>
                    <button
                        x-show="state.chat.conversation && state.chat.conversation.id === conversation.id"
                        class="chatbox__tab-delete-button"
                        @click.prevent="leaveConversation(conversation.id)"
                    >
                        <i class="fa fa-times chatbox__tab-delete-icon"></i>
                    </button>
                </li>
            </template>
        </menu>
        <div class="chatbox__chatroom">
            <template x-if="state.chat.conversation !== null">
                <div class="chatroom__messages--wrapper" x-ref="messagesWrapper">
                    <ul class="chatroom__messages">
                        <template x-for="message in messages" :key="message[1].id">
                            <li>
                                <article class="chatbox-message">
                                    <header class="chatbox-message__header">
                                        <address
                                            class="chatbox-message__address user-tag"
                                            :style="(message[1].user.is_donor ? 'background-image: url(/img/sparkels.gif);' : (message[1].user.group.effect ? 'background-image:' + message[1].user.group.effect + ';' : ''))"
                                        >
                                            <a
                                                class="user-tag__link"
                                                :class="message[1].user.group.icon"
                                                :href="message[1].user.username ? '/users/' + message[1].user.username : ''"
                                                :style="message[1].user.group.color ? 'color:' + message[1].user.group.color : ''"
                                                :title="message[1].user.group.name"
                                            >
                                                <span
                                                    x-show="message[1].user && message[1].user.id > 1"
                                                    style="padding-right: 5px"
                                                    x-text="message[1].user.username || 'Unknown'"
                                                ></span>
                                                <span
                                                    x-show="message[1].bot && message[1].bot.id >= 1 && (! message[1].user || message[1].user.id < 2)"
                                                    x-text="message[1].bot && message[1].bot.name || 'Unknown'"
                                                ></span>
                                                <template x-if="message[1].user.icon">
                                                    <i>
                                                        <img
                                                            :style="'max-height: 16px; vertical-align: text-bottom;'"
                                                            title="Custom user icon"
                                                            :src="'/authenticated-images/user-icons/' + message[1].user.username"
                                                            loading="lazy"
                                                        />
                                                    </i>
                                                </template>
                                                <i
                                                    x-show="message[1].user.is_lifetime == 1"
                                                    class="fal fa-star"
                                                    id="lifeline"
                                                    title="Lifetime donor"
                                                ></i>
                                                <i
                                                    x-show="message[1].user.is_donor == 1 && message[1].user.is_lifetime == 0"
                                                    class="fal fa-star text-gold"
                                                    title="Donor"
                                                ></i>
                                            </a>
                                        </address>
                                        <div
                                            x-show="message[1].bot && message[1].bot.id >= 1 && (! message[1].user || message[1].user.id < 2)"
                                            class="bbcode-rendered bot-message"
                                            style="
                                                font-style: italic;
                                                white-space: nowrap;
                                                display: inline;
                                            "
                                            x-effect="renderMessage(message[1].message)"
                                        ></div>
                                        <time
                                            x-show="message[1].bot && message[1].bot.id >= 1 && (! message[1].user || message[1].user.id < 2)"
                                            style="
                                                margin-left: 10px;
                                                white-space: nowrap;
                                                display: inline;
                                            "
                                            class="chatbox-message__time"
                                            :datetime="message[1].created_at"
                                            :title="message[1].created_at"
                                            x-text="formatTime(message[1].created_at)"
                                        ></time>
                                        <time
                                            x-show="! (message[1].bot && message[1].bot.id >= 1 && (! message[1].user || message[1].user.id < 2))"
                                            class="chatbox-message__time"
                                            :datetime="message[1].created_at"
                                            :title="message[1].created_at"
                                            x-text="formatTime(message[1].created_at)"
                                        ></time>
                                    </header>
                                    <aside class="chatbox-message__aside">
                                        <figure class="chatbox-message__figure">
                                            <i
                                                class="fa fa-bell"
                                                title="System notification"
                                                x-show="message[1].bot && message[1].bot.id >= 1 && (! message[1].user || message[1].user.id < 2)"
                                            ></i>
                                            <a
                                                x-show="message[1].user && message[1].user.id != 1"
                                                :href="'/users/' + message[1].user.username"
                                                class="chatbox-message__avatar-link"
                                            >
                                                <img
                                                    x-show="message[1].user && message[1].user.id != 1"
                                                    class="chatbox-message__avatar"
                                                    :src="message[1].user.image ? '/authenticated-images/user-avatars/' + message[1].user.username : '/img/profile.png'"
                                                    :style="'border: 2px solid ' + (message[1].user.chat_status.color || '#ccc')"
                                                    :title="message[1].user.chat_status.name"
                                                    loading="lazy"
                                                />
                                            </a>
                                        </figure>
                                    </aside>
                                    <section
                                        @class([
                                            'bbcode-rendered',
                                            'chatbox-message__content',
                                            'bbcode-rendered__censor' => $user->settings->censor,
                                        ])
                                        x-show="! (message[1].bot && message[1].bot.id >= 1 && (! message[1].user || message[1].user.id < 2))"
                                        x-effect="renderMessage(message[1].message)"
                                    ></section>
                                    <!-- Move menu back to original position after timestamp -->
                                    <menu
                                        class="chatbox-message__menu"
                                        x-show="message[1].canMod === true || message[1].canMod === 1"
                                    >
                                        <li class="chatbox-message__menu-item">
                                            <button
                                                class="chatbox-message__delete-button"
                                                title="Delete message"
                                                @click.prevent="deleteMessage(message[1].id)"
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
                    <template x-for="user in users" :key="user.id">
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
                    x-text="whispers"
                ></span>
            </section>
            <form class="form chatroom__new-message">
                <p class="form__group">
                    <textarea
                        id="chatbox__messages-create"
                        class="form__textarea"
                        name="message"
                        placeholder=" "
                        x-ref="message"
                        x-on:keydown.enter="createMessage"
                        x-on:keyup="isTyping(auth)"
                    ></textarea>
                    <label class="form__label form__label--floating" for="chatbox__messages-create">
                        Write your message...
                    </label>
                </p>
            </form>
        </div>
    </div>
</section>
