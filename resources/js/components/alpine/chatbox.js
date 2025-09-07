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
    format(message) {
        return message;
    },

    create(message, context, user_id = 1, receiver_id = null, bot_id = null) {
        if (!message || message.trim() === '') return;

        return axios
            .post('/api/chat/messages', {
                user_id,
                receiver_id,
                bot_id,
                chatroom_id: context.state.chat.room,
                message: message,
                targeted: context.state.chat.target,
            })
            .then((response) => {
                if (
                    context.state.chat.activeTab.startsWith('bot') ||
                    context.state.chat.activeTab.startsWith('target')
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
                if (!context.state.chat.activeTab.startsWith('room')) return;
                const message = context.processMessageCanMod(e.message);
                context.messages.set(message.id, message);
            })
            .listen('.new.ping', (e) => {
                context.handlePing('room', e.ping.id);
            })
            .listen('.delete.message', (e) => {
                if (context.state.chat.target > 0 || context.state.chat.bot > 0) return;
                context.messages.delete(e.message.id);
            })
            .listenForWhisper('typing', (e) => {
                if (context.state.chat.target > 0 || context.state.chat.bot > 0) return;
                const username = e.username;
                clearTimeout(context.activePeer.get(username));
                const messageTimeout = setTimeout(() => context.activePeer.delete(username), 15000);
                context.activePeer.set(username, messageTimeout);
            });

        context.channel.error((error) => {
            console.error('Socket error:', error);
            context.state.ui.error = 'Connection lost. Trying to reconnect...';

            setTimeout(() => {
                this.setupRoom(context.state.chat.room, context);
            }, 5000);
        });
    },
};

document.addEventListener('alpine:init', () => {
    Alpine.data('chatbox', (user) => ({
        state: {
            ui: {
                loading: true,
                fullscreen: false,
                error: null,
            },
            chat: {
                tab: '',
                room: 0,
                target: 0,
                bot: 0,
                activeTab: '',
                activeRoom: '',
                activeTarget: '',
                activeBot: '',
                listening: 1,
                showWhispers: true,
                showUserList: false,
            },
            message: {
                helpName: '',
                helpCommand: '',
                helpId: 0,
                receiver_id: null,
                bot_id: null,
            },
        },

        auth: user,
        statuses: [],
        echoes: [],
        chatrooms: [],
        messages: new Map(),
        users: new Map(),
        pings: [],
        audibles: [],
        activePeer: new Map(),
        scroll: true,
        channel: null,
        chatter: null,
        config: {},
        typingTimeout: null,
        blurHandler: null,
        focusHandler: null,
        timestampTick: 0,

        init() {
            this.state.chat.activeRoom = this.auth.chatroom.name;

            this.blurHandler = () => {
                document.getElementById('chatbody').setAttribute('audio', true);
            };

            this.focusHandler = () => {
                document.getElementById('chatbody').setAttribute('audio', false);
            };

            Promise.all([
                this.fetchStatuses(),
                this.fetchEchoes(),
                this.fetchBots(),
                this.fetchAudibles(),
                this.fetchRooms(),
            ])
                .then(() => {
                    this.state.ui.loading = false;
                    this.listenForChatter();
                    this.attachAudible();

                    setInterval(() => {
                        this.timestampTick++;
                    }, 30000);
                })
                .catch((error) => {
                    console.error('Error initializing chat:', error);
                    this.state.ui.error = 'Error loading chat. Please try again.';
                    this.state.ui.loading = false;
                });

            this.$watch('auth.chat_status_id', (status, oldStatus) => {
                if (status === oldStatus) return; // Closing a chatbox tab triggers this (alpinejs bug)
                this.syncStatus();
            });

            this.$cleanup = () => {
                if (this.channel) {
                    window.Echo.leave(`chatroom.${this.state.chat.room}`);
                }
                if (this.chatter) {
                    this.chatter.stopListening('Chatter');
                }
                window.removeEventListener('blur', this.blurHandler);
                window.removeEventListener('focus', this.focusHandler);
                clearTimeout(this.typingTimeout);
            };
        },

        // Fetchers
        async fetchAudibles() {
            try {
                const response = await axios.get('/api/chat/audibles');
                this.audibles = response.data.data;
                return this.fetchConfiguration();
            } catch (error) {
                console.error('Error fetching audibles:', error);
                throw error;
            }
        },

        async fetchEchoes() {
            try {
                const response = await axios.get('/api/chat/echoes');
                this.echoes = this.sortEchoes(response.data.data);
            } catch (error) {
                console.error('Error fetching echoes:', error);
                throw error;
            }
        },

        async fetchBots() {
            try {
                const response = await axios.get('/api/chat/bots');
                const bots = response.data.data;
                if (bots.length > 0) {
                    this.state.message.helpId = bots[0].id;
                    this.state.message.helpName = bots[0].name;
                    this.state.message.helpCommand = bots[0].command;
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
                    this.state.chat.tab = this.auth.chatroom.name;
                    this.state.chat.activeTab = 'room' + this.state.chat.room;

                    // Immediately load messages for the user's current chatroom
                    await this.changeRoom(this.auth.chatroom.id);
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

        async fetchPrivateMessages() {
            try {
                const response = await axios.get(
                    `/api/chat/private/messages/${this.state.chat.target}`,
                );
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

        async fetchMessages() {
            try {
                const response = await axios.get(`/api/chat/messages/${this.state.chat.room}`);
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

            // Sanitize message content if it exists
            if (message.message) {
                message.originalMessage = message.message;
                message.message = messageHandler.format(message.message);
            }

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
        changeTab(typeVal, newVal) {
            if (typeVal == 'room') {
                this.state.chat.bot = 0;
                this.state.chat.target = 0;
                this.state.message.bot_id = 0;
                this.state.message.receiver_id = 0;
                this.state.chat.tab = newVal;
                this.state.chat.activeTab = 'room' + newVal;
                this.state.chat.activeRoom = newVal;
                this.deletePing('room', newVal);

                let currentRoom = this.echoes.find((o) => o.room && o.room.id == newVal);
                if (currentRoom) {
                    this.changeRoom(currentRoom.room.id);
                    this.state.message.receiver_id = null;
                    this.state.message.bot_id = null;
                }

                let currentAudio = this.audibles.find((o) => o.room && o.room.id == newVal);
                this.state.chat.listening = currentAudio && currentAudio.status == 1 ? 1 : 0;
            } else if (typeVal == 'target') {
                this.state.chat.bot = 0;
                this.state.chat.tab = newVal;
                this.state.chat.activeTab = 'target' + newVal;
                this.state.chat.activeTarget = newVal;
                this.deletePing('target', newVal);

                let currentTarget = this.echoes.find((o) => o.target && o.target.id == newVal);
                if (currentTarget) {
                    this.changeTarget(currentTarget.target.id);
                    this.state.message.receiver_id = currentTarget.target.id;
                    this.state.message.bot_id = null;
                }

                let currentAudio = this.audibles.find((o) => o.target && o.target.id == newVal);
                this.state.chat.listening = currentAudio && currentAudio.status == 1 ? 1 : 0;
            } else if (typeVal == 'bot') {
                this.state.chat.target = 0;
                this.state.chat.tab = newVal;
                this.state.chat.activeTab = 'bot' + newVal;
                this.state.chat.activeBot = newVal;
                this.deletePing('bot', newVal);

                let currentBot = this.echoes.find((o) => o.bot && o.bot.id == newVal);
                if (currentBot) {
                    this.changeBot(currentBot.bot.id);
                    this.state.message.receiver_id = 1;
                    this.state.message.bot_id = currentBot.bot.id;
                }

                let currentAudio = this.audibles.find((o) => o.bot && o.bot.id == newVal);
                this.state.chat.listening = currentAudio && currentAudio.status == 1 ? 1 : 0;
            }
        },

        toggleUserList() {
            this.state.chat.showUserList = !this.state.chat.showUserList;
        },

        changeRoom(id) {
            this.state.chat.bot = 0;
            this.state.chat.target = 0;
            this.state.message.bot_id = null;
            this.state.message.receiver_id = null;

            if (this.auth.chatroom.id === id) {
                this.state.chat.tab = this.auth.chatroom.name;
                this.state.chat.activeRoom = this.auth.chatroom.name;
                this.fetchMessages();
            } else {
                axios
                    .post(`/api/chat/user/chatroom`, { room_id: id })
                    .then((response) => {
                        this.auth = response.data;
                        this.state.chat.tab = this.auth.chatroom.name;
                        this.state.chat.activeRoom = this.auth.chatroom.name;
                        this.fetchMessages();
                    })
                    .catch((error) => {
                        console.error('Error changing room:', error);
                    });
            }

            // Set up room channel with improved connection handling
            channelHandler.setupRoom(id, this);

            this.state.chat.room = id;
        },

        leaveRoom(id) {
            if (id !== 1) {
                // Update the user's chatroom in the database
                axios
                    .post(`/api/chat/echoes/delete/chatroom`, { room_id: id })
                    .then((response) => {
                        // Reassign the auth variable to the response data
                        this.auth = response.data;
                        document.getElementById('currentChatroom').value = '1';
                        this.fetchRooms().then(() => {
                            // Check if there are other chat tabs available
                            if (this.state.chat.tab) {
                                // Switch to the first chat tab
                                const firstTab = this.state.chat.tab;
                                this.changeTab('room', firstTab);
                            } else if (this.chatrooms.length > 0) {
                                // Default to the first chatroom from the dropdown
                                const firstChatroom = this.chatrooms[0];
                                this.changeRoom(firstChatroom.id);
                            } else {
                                console.warn('No chat tabs or chatrooms available.');
                            }
                        });
                    })
                    .catch((error) => {
                        console.error('Error leaving room:', error);
                    });
            }
        },

        changeTarget(id) {
            if (this.state.chat.target !== id && id != 0) {
                this.state.chat.target = id;
                this.fetchPrivateMessages();
            }
        },

        leaveTarget(id) {
            if (id !== 1) {
                // Update the user's chatroom in the database
                axios
                    .post(`/api/chat/echoes/delete/target`, { target_id: id })
                    .then((response) => {
                        // Reassign the auth variable to the response data
                        this.auth = response.data;
                        document.getElementById('currentChatroom').value = '1';
                        this.fetchRooms().then(() => {
                            // Check if there are other chat tabs available
                            if (this.state.chat.tab) {
                                // Switch to the first chat tab
                                const firstTab = this.state.chat.tab;
                                this.changeTab('room', firstTab);
                            } else if (this.chatrooms.length > 0) {
                                // Default to the first chatroom from the dropdown
                                const firstChatroom = this.chatrooms[0];
                                this.changeRoom(firstChatroom.id);
                            } else {
                                console.warn('No chat tabs or chatrooms available.');
                            }
                        });
                    })
                    .catch((error) => {
                        console.error('Error leaving room:', error);
                    });
            }
        },

        changeBot(id) {
            if (this.state.chat.bot !== id && id != 0) {
                this.state.chat.bot = id;
                this.state.message.bot_id = id;
                this.state.message.receiver_id = 1;
                this.fetchBotMessages(this.state.chat.bot);
            }
        },

        // Delegate message operations to messageHandler
        createMessage(message, user_id = 1, receiver_id = null, bot_id = null) {
            return messageHandler.create(
                message,
                this,
                user_id,
                receiver_id || this.state.message.receiver_id,
                bot_id || this.state.message.bot_id,
            );
        },

        deleteMessage(id) {
            return messageHandler.delete(id, this);
        },

        isTyping(e) {
            const self = this;

            if (!this._debouncedIsTyping) {
                this._debouncedIsTyping = debounce(function (e) {
                    if (self.state.chat.target < 1 && self.channel && self.state.chat.tab != '') {
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
                if (e.type == 'echo') {
                    this.echoes = this.sortEchoes(e.echoes);
                } else if (e.type == 'audible') {
                    this.audibles = e.audibles;
                } else if (e.type == 'new.message') {
                    if (
                        !this.state.chat.activeTab.startsWith('bot') &&
                        !this.state.chat.activeTab.startsWith('target')
                    )
                        return;

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

            this.chatter.error((error) => {
                console.error('Chatter connection error:', error);
                setTimeout(() => {
                    this.listenForChatter();
                }, 5000);
            });
        },

        listenForEvents() {
            channelHandler.setupListeners(this);
        },

        // Utility
        sortEchoes(obj) {
            if (!obj || !Array.isArray(obj)) return [];

            return obj.sort((a, b) => {
                let nv1 = a.room?.name || a.target?.username || a.bot?.name || '';
                let nv2 = b.room?.name || b.target?.username || b.bot?.name || '';
                return nv1.localeCompare(nv2);
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
            this.playSound();
        },

        checkPings(type, id) {
            return this.pings.some((p) => p.type === type && p.id === id);
        },

        attachAudible() {
            // Use the stored handlers for consistency and cleanup
            window.addEventListener('blur', this.blurHandler);
            window.addEventListener('focus', this.focusHandler);
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

        startBot() {
            if (this.state.chat.bot == 9999) return;

            this.state.chat.tab = '@' + this.state.message.helpName;
            this.state.chat.bot = this.state.message.helpId;
            this.state.message.bot_id = this.state.message.helpId;
            this.state.message.receiver_id = 1;

            this.fetchBotMessages(this.state.chat.bot);
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
    }));
});
