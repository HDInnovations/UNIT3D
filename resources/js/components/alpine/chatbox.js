// AlpineJS Chatbox Component for UNIT3D
// Handles all chat logic, state, and Echo events

import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

// Initialize dayjs plugins
dayjs.extend(relativeTime);

// Utility functions
const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

// Message handler module
const messageHandler = {
    create(message, context) {
        if (!message || message.trim() === '') return;

        return axios
            .post('/api/chat/messages', {
                receiver_id: context.state.chat.conversation.bot
                    ? 1
                    : context.state.chat.conversation.target?.id,
                bot_id: context.state.chat.conversation.bot?.id,
                chatroom_id: context.state.chat.conversation.room?.id,
                message: message,
            })
            .then((response) => {
                if (
                    context.state.chat.conversation.bot !== null ||
                    context.state.chat.conversation.target !== null
                ) {
                    context.messages.set(response.data.data.id, response.data.data);
                }
                if (context.$refs && context.$refs.message) {
                    context.$refs.message.value = '';
                }
            });
    },

    delete(id, context) {
        if (!id) return;

        return axios
            .post(`/api/chat/message/${id}/delete`)
            .then(() => {
                context.messages.delete(id);
            })
            .catch((error) => {
                console.error('Error deleting message:', error);
            });
    },
};

// Channel handler module
const channelHandler = {
    setupRoom(id, context) {
        if (context.channel) {
            window.Echo.leave(`chatroom.${context.state.chat.room}`);
        }

        context.channel = window.Echo.join(`chatroom.${id}`);

        this.setupListeners(context);
    },

    setupListeners(context) {
        if (!context.channel) return;

        context.channel
            .here((users) => {
                context.users = new Map(users.map((user) => [user.id, user]));
            })
            .joining((user) => {
                context.users.set(user.id, user);
            })
            .leaving((user) => {
                context.users.delete(user.id);
            })
            .listen('.new.message', (e) => {
                if (context.state.chat.conversation.room === null) return;
                const message = context.processMessageCanMod(e.message);
                context.messages.set(message.id, message);
            })
            .listen('.new.ping', (e) => {
                context.handlePing('room', e.ping.id);
            })
            .listen('.delete.message', (e) => {
                if (
                    context.state.chat.conversation.target !== null ||
                    context.state.chat.conversation.bot !== null
                )
                    return;
                context.messages.delete(e.message.id);
            })
            .listenForWhisper('typing', (e) => {
                if (
                    context.state.chat.conversation.target !== null ||
                    context.state.chat.conversation.bot !== null
                )
                    return;
                const username = e.username;
                clearTimeout(context.activePeer.get(username));
                const messageTimeout = setTimeout(() => context.activePeer.delete(username), 15000);
                context.activePeer.set(username, messageTimeout);
            });
    },
};

document.addEventListener('alpine:init', () => {
    Alpine.data('chatbox', () => ({
        state: {
            ui: {
                loading: true,
                fullscreen: false,
                error: null,
            },
            chat: {
                conversation: null,
                room: null,
                bot: null,
                showWhispers: true,
                showUserList: false,
            },
        },

        auth: null,
        statuses: [],
        conversations: [],
        chatrooms: [],
        messages: new Map(),
        users: new Map(),
        pings: [],
        activePeer: new Map(),
        channel: null,
        chatter: null,
        config: {},
        typingTimeout: null,
        timestampTick: 0,

        init() {
            this.auth = JSON.parse(atob(this.$root.dataset.user));

            Promise.all([
                this.fetchStatuses(),
                this.fetchConversations(),
                this.fetchBots(),
                this.fetchRooms(),
            ])
                .then(() => {
                    this.state.chat.conversation =
                        this.conversations.find(
                            (conversation) => conversation.room?.id === this.auth.chatroom_id,
                        ) ?? this.conversations[0].room.id;
                    this.changeRoom(this.state.chat.conversation.room.id);
                    this.state.ui.loading = false;
                    this.listenForChatter();

                    this.$watch('auth.chat_status_id', (status, oldStatus) => {
                        if (status === oldStatus) return; // Closing a chatbox tab triggers this (alpinejs bug)
                        this.syncStatus();
                    });

                    this.$watch('state.chat.room', (chatroom, oldChatroom) => {
                        if (chatroom === oldChatroom) return;
                        this.changeRoom(chatroom);
                    });

                    this.$cleanup = () => {
                        if (this.channel) {
                            window.Echo.leave(`chatroom.${this.state.chat.room}`);
                        }
                        if (this.chatter) {
                            this.chatter.stopListening('Chatter');
                        }
                        clearTimeout(this.typingTimeout);
                    };

                    setInterval(() => {
                        this.timestampTick++;
                    }, 30000);
                })
                .catch((error) => {
                    console.error('Error initializing chat:', error);
                    this.state.ui.error = 'Error loading chat. Please try again.';
                    this.state.ui.loading = false;
                });
        },

        // Fetchers
        async fetchConversations() {
            try {
                const response = await axios.get('/api/chat/conversations');
                this.conversations = this.sortConversations(response.data.data);
            } catch (error) {
                console.error('Error fetching conversations:', error);
                throw error;
            }
        },

        async fetchBots() {
            try {
                const response = await axios.get('/api/chat/bots');
                const bots = response.data.data;
                if (bots.length > 0) {
                    console.log('here');
                    this.state.chat.bot = bots[0];
                }
            } catch (error) {
                console.error('Error fetching bots:', error);
                throw error;
            }
        },

        async fetchRooms() {
            try {
                const response = await axios.get('/api/chat/rooms');
                this.chatrooms = response.data.data;
                if (this.chatrooms.length > 0) {
                    this.state.chat.room = this.auth.chatroom.id;
                }
            } catch (error) {
                console.error('Error fetching rooms:', error);
                throw error;
            }
        },

        async fetchConfiguration() {
            try {
                const response = await axios.get(`/api/chat/config`);
                this.config = response.data;
            } catch (error) {
                console.error('Error fetching configuration:', error);
                throw error;
            }
        },

        async fetchBotMessages(id) {
            try {
                const response = await axios.get(`/api/chat/bot/${id}`);
                // Process messages to add canMod property for each message and sanitize content
                this.messages = new Map(
                    response.data.data
                        .map((message) => [message.id, this.processMessageCanMod(message)])
                        .reverse(),
                );
            } catch (error) {
                console.error('Error fetching bot messages:', error);
                throw error;
            }
        },

        async fetchPrivateMessages(id) {
            try {
                const response = await axios.get(`/api/chat/private/messages/${id}`);
                // Process messages to add canMod property for each message and sanitize content
                this.messages = new Map(
                    response.data.data
                        .map((message) => [message.id, this.processMessageCanMod(message)])
                        .reverse(),
                );
            } catch (error) {
                console.error('Error fetching private messages:', error);
                throw error;
            }
        },

        async fetchRoomMessages(id) {
            try {
                const response = await axios.get(`/api/chat/messages/${id}`);
                // Process messages to add canMod property for each message and sanitize content
                this.messages = new Map(
                    response.data.data
                        .map((message) => [message.id, this.processMessageCanMod(message)])
                        .reverse(),
                );
            } catch (error) {
                console.error('Error fetching messages:', error);
                throw error;
            }
        },

        // Process messages for display
        processMessageCanMod(message) {
            if (!message) return message;

            // Check if the user can moderate this message
            message.canMod = this.canMod(message);

            return message;
        },

        // Permission checking
        canMod(message) {
            if (!message || !message.user || !this.auth || !this.auth.group) return false;

            return (
                // Owner can mod all messages
                this.auth.group.is_owner ||
                // User can mod their own messages
                message.user.id === this.auth.id ||
                // Admins can mod messages except for Owner messages
                (this.auth.group.is_admin && message.user?.group && !message.user.group.is_owner) ||
                // Mods cannot mod other mods' messages
                (this.auth.group.is_modo && message.user?.group && !message.user.group.is_modo)
            );
        },

        async fetchStatuses() {
            try {
                const response = await axios.get('/api/chat/statuses');
                this.statuses = response.data;
            } catch (error) {
                console.error('Error fetching statuses:', error);
                throw error;
            }
        },

        // Tab/Room/Target/Bot switching
        changeConversation(id) {
            let conversation = this.conversations.find((o) => o.id === id);

            this.state.chat.conversation = conversation;

            if (conversation.room !== null) {
                this.fetchRoomMessages(conversation.room.id);
            }

            if (conversation.target !== null) {
                this.deletePing('target', conversation.target.id);
                this.fetchPrivateMessages(conversation.target.id);
            }

            if (conversation.bot !== null) {
                this.deletePing('bot', conversation.bot.id);
                this.fetchBotMessages(conversation.bot.id);
            }
        },

        toggleUserList() {
            this.state.chat.showUserList = !this.state.chat.showUserList;
        },

        changeRoom(id) {
            axios
                .post(`/api/chat/user/chatroom`, { room_id: id })
                .then((response) => {
                    this.auth = response.data;
                    this.state.chat.conversation = this.conversations.find(
                        (conversation) => conversation.room?.id === id,
                    );
                    this.fetchRoomMessages(id);
                })
                .catch((error) => {
                    console.error('Error changing room:', error);
                });

            // Set up room channel with improved connection handling
            channelHandler.setupRoom(id, this);
        },

        leaveConversation(id) {
            let conversation = this.conversations.find((o) => o.id === id);

            if (conversation.room !== null) {
                axios
                    .post('/api/chat/conversations/delete/chatroom', {
                        room_id: conversation.room.id,
                    })
                    .then((response) => {
                        this.auth = response.data;
                        this.changeRoom(this.auth.chatroom_id);
                    })
                    .catch((error) => {
                        console.error('Error leaving room:', error);
                    });
            }

            if (conversation.target !== null) {
                axios
                    .post('/api/chat/conversations/delete/target', {
                        target_id: conversation.target.id,
                    })
                    .then((response) => {
                        this.auth = response.data;
                        this.changeRoom(this.auth.chatroom_id);
                    })
                    .catch((error) => {
                        console.error('Error leaving target:', error);
                    });
            }
        },

        // Delegate message operations to messageHandler
        createMessage(event) {
            if (event.shiftKey) {
                return;
            }

            event.preventDefault();

            return messageHandler.create(this.$el.value, this);
        },

        deleteMessage(id) {
            return messageHandler.delete(id, this);
        },

        isTyping(e) {
            const self = this;

            if (!this._debouncedIsTyping) {
                this._debouncedIsTyping = debounce(function (e) {
                    if (self.state.chat.target < 1 && self.channel) {
                        self.channel.whisper('typing', { username: e.username });
                    }
                }, 300);
            }

            this._debouncedIsTyping(e);
        },

        // Sound management
        playSound() {
            if (window.sounds && window.sounds['alert.mp3']) {
                window.sounds['alert.mp3'].pause();
                window.sounds['alert.mp3'].currentTime = 0;
                window.sounds['alert.mp3'].play();
            }
        },

        // Event listeners
        listenForChatter() {
            this.chatter = window.Echo.private(`chatter.${this.auth.id}`);

            this.chatter.listen('Chatter', (e) => {
                if (e.type == 'conversations') {
                    this.conversations = this.sortConversations(e.conversations);
                } else if (e.type == 'new.message') {
                    if (this.state.chat.conversation.room !== null) return;

                    if (e.message.bot && e.message.bot.id != this.state.chat.bot) return;
                    if (e.message.user && e.message.user.id != this.state.chat.target) return;

                    // Process and sanitize new message
                    const message = this.processMessageCanMod(e.message);
                    this.messages.set(message.id, message);
                } else if (e.type == 'new.bot') {
                    // Process and sanitize new bot message
                    const message = this.processMessageCanMod(e.message);
                    this.messages.set(message.id, message);
                } else if (e.type == 'new.ping') {
                    if (e.ping.type == 'bot') {
                        this.handlePing('bot', e.ping.id);
                    } else {
                        this.handlePing('target', e.ping.id);
                    }
                } else if (e.type == 'delete.message') {
                    if (this.state.chat.target < 1 && this.state.chat.bot < 1) return;
                    this.messages.delete(e.message.id);
                } else if (e.type == 'typing') {
                    if (this.state.chat.target < 1) return;
                    const username = e.username;
                    clearTimeout(this.activePeer.get(username));
                    const messageTimeout = setTimeout(
                        () => this.activePeer.delete(username),
                        15000,
                    );
                    this.activePeer.set(username, messageTimeout);
                }
            });
        },

        listenForEvents() {
            channelHandler.setupListeners(this);
        },

        // Utility
        sortConversations(conversations) {
            if (!conversations || !Array.isArray(conversations)) return [];

            let conversationsSorted = conversations.sort((a, b) => {
                let nv1 = a.room?.name || a.target?.username || a.bot?.name || '';
                let nv2 = b.room?.name || b.target?.username || b.bot?.name || '';
                return nv1.localeCompare(nv2);
            });

            return conversationsSorted.sort((a, b) => {
                const priority = (conversation) =>
                    conversation.room !== null
                        ? 0
                        : conversation.target !== null
                          ? 1
                          : conversation.bot !== null
                            ? 2
                            : 3;
                return priority(a) - priority(b);
            });
        },

        deletePing(type, id) {
            let idx = this.pings.findIndex((p) => p.type === type && p.id === id);
            if (idx !== -1) this.pings.splice(idx, 1);
        },

        handlePing(type, id) {
            if (!this.pings.some((p) => p.type === type && p.id === id)) {
                this.pings.push({ type, id, count: 0 });
            }

            let conversation = this.conversations.find(
                (conversation) => conversation[type]?.id == id,
            );

            if (conversation.audible && !this.$root.matches(':focus-within')) {
                this.playSound();
            }
        },

        checkPings(conversation) {
            if (conversation.target !== null) {
                return this.pings.some(
                    (p) => p.type === 'target' && p.id === conversation.target.id,
                );
            }

            if (conversation.bot !== null) {
                return this.pings.some((p) => p.type === 'bot' && p.id === conversation.bot.id);
            }

            return false;
        },

        // UI actions
        changeFullscreen() {
            this.state.ui.fullscreen = !this.state.ui.fullscreen;
        },

        changeWhispers() {
            this.state.chat.showWhispers = !this.state.chat.showWhispers;
        },

        syncStatus() {
            axios
                .post(`/api/chat/user/status`, { status_id: this.auth.chat_status_id })
                .catch((error) => {
                    console.error('Error changing status:', error);
                });
        },

        changeBot(id) {
            axios
                .post(`/api/chat/user/bot`, { bot_id: id })
                .then((response) => {
                    this.auth = response.data;
                    this.state.chat.conversation = this.conversations.find(
                        (conversation) => conversation.bot?.id === id,
                    );
                    this.fetchBotMessages(id);
                })
                .catch((error) => {
                    console.error('Error changing bot:', error);
                });
        },

        forceMessage(name) {
            const messageInput = document.getElementById('chatbox__messages-create');
            if (messageInput) {
                messageInput.value = '/msg ' + name + ' ';
                messageInput.focus();
            }
        },

        forceGift(name) {
            const messageInput = document.getElementById('chatbox__messages-create');
            if (messageInput) {
                messageInput.value = '/gift ' + name + ' ';
                messageInput.focus();
            }
        },

        formatTime(timestamp) {
            if (!timestamp) return '';
            this.timestampTick;
            return dayjs(timestamp).fromNow();
        },

        whispers() {
            return this.activePeer.size > 3
                ? 'Several people are typing...'
                : this.activePeer.size === 1
                  ? [...this.activePeer.keys()][0] + ' is typing...'
                  : [...this.activePeer.keys()].slice(0, -1).join(', ') +
                    ' and ' +
                    [...this.activePeer.keys()][this.activePeer.size - 1] +
                    ' are typing...';
        },

        renderMessage(html) {
            this.$el.innerHTML = html;
        },
    }));
});
