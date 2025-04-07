<template>
  <div class="chatroom__messages--wrapper">
    <ul class="chatroom__messages">
      <li v-for="message in messages">
        <article class="chatbox-message">
          <header class="chatbox-message__header">
            <address
              class="chatbox-message__address user-tag"
              :style="`${message.user?.is_lifetime ? 'background-image: url(/img/sparkels.gif);' : 'background-image:' + message.user?.group?.effect};`"
            >
              <a
                class="user-tag__link"
                :class="message.user?.group?.icon"
                :href="`/users/${message.user?.username}`"
                :style="`color: ${message.user?.group?.color}`"
                :title="message.user?.group?.name"
              >
                <span v-if="message.user && message.user.id > 1" :style="`padding-right: 5px;`">{{
                  message.user?.username ?? 'Unknown'
                }}</span>
                <span
                  v-if="
                    message.bot && message.bot.id >= 1 && (!message.user || message.user.id < 2)
                  "
                  >{{ message.bot?.name ?? 'Unknown' }}</span
                >
                <i v-if="message.user?.icon !== null">
                  <img
                    :style="`max-height: 16px; vertical-align: text-bottom;`"
                    title="Custom User Icon"
                    :src="`/authenticated-images/user-icons/${message.user.username}`"
                  />
                </i>
                <i
                  v-if="message.user?.is_lifetime == 1"
                  class="fal fa-star"
                  id="lifeline"
                  title="Lifetime Donor"
                ></i>
                <i
                  v-if="message.user?.is_donor == 1 && message.user?.is_lifetime == 0"
                  class="fal fa-star text-gold"
                  title="Donor"
                ></i>
              </a>
            </address>
            <div
              v-if="message.bot && message.bot.id >= 1 && (!message.user || message.user.id < 2)"
              :style="`font-style: italic; white-space: nowrap;`"
              v-html="message.message"
            ></div>
            <time
              v-if="message.bot && message.bot.id >= 1 && (!message.user || message.user.id < 2)"
              :style="`margin-left: 10px; white-space: nowrap;`"
              class="chatbox-message__time"
              :datetime="message.created_at"
              :title="message.created_at"
            >
              {{ message.created_at | diffForHumans }}
            </time>
            <time
              v-else
              class="chatbox-message__time"
              :datetime="message.created_at"
              :title="message.created_at"
            >
              {{ message.created_at | diffForHumans }}
            </time>
          </header>
          <aside class="chatbox-message__aside">
            <figure class="chatbox-message__figure">
              <i
                class="fa fa-bell"
                title="System Notification"
                v-if="message.bot && message.bot.id >= 1 && (!message.user || message.user.id < 2)"
              ></i>
              <a
                v-if="message.user?.id != 1"
                :href="`/users/${message.user?.username}`"
                class="chatbox-message__avatar-link"
              >
                <img
                  v-if="message.user?.id != 1"
                  class="chatbox-message__avatar"
                  :src="
                    message.user?.image
                      ? `/authenticated-images/user-avatars/${message.user.username}`
                      : '/img/profile.png'
                  "
                  :style="`border: 2px solid ${message.user?.chat_status?.color};`"
                  :title="message.user?.chat_status?.name"
                />
              </a>
            </figure>
          </aside>
          <menu class="chatbox-message__menu">
            <li class="chatbox-message__menu-item">
              <button
                class="chatbox-message__delete-button"
                v-if="message.user?.id != 1 && canMod(message)"
                @click="deleteMessage(message.id)"
                title="Delete message"
              >
                <i class="fa fa-trash"></i>
              </button>
            </li>
          </menu>
          <section
            v-if="message.user && message.user.id > 1"
            class="chatbox-message__content bbcode-rendered"
            v-html="message.message"
          ></section>
        </article>
      </li>
      <li v-if="messages.length === 0">There is no chat history here. Send a message!</li>
    </ul>
  </div>
</template>
<script>
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

export default {
  props: {
    messages: { required: true },
  },
  data() {
    return {
      editor: null,
    };
  },
  methods: {
    checkBot(e, message) {
      if (e.target.hasAttribute('trigger') && e.target.getAttribute('trigger') == 'bot') {
        e.preventDefault();
        let target = e.target.hash;
        const tmp = target.split('/');
        document.getElementById('chat-message').value = '/' + tmp[1] + ' ' + tmp[2] + ' ';
        document.getElementById('chat-message').value = '/' + tmp[1] + ' ' + tmp[2] + ' ';
      }
    },
    canMod(message) {
      /*
          A user can Mod his own messages
          A user in an is_modo group can Mod messages
          A is_modo CAN NOT Mod another is_modo message
      */

      return (
        /* Owner can mod all */
        this.$parent.auth.group.is_owner ||
        /* User can mod his own message */
        message.user.id === this.$parent.auth.id ||
        /* is_admin can mod messages except for Owner messages */
        (this.$parent.auth.group.is_admin && !message.user.group.is_owner) ||
        /* Mods CAN NOT mod other mods messages */
        (this.$parent.auth.group.is_modo && !message.user.group.is_modo)
      );
    },
    editMessage(message) {},
    deleteMessage(id) {
      axios.post(`/api/chat/message/${id}/delete`);
    },
    userStyles(user) {
      return `cursor: pointer; color: ${user.group.color}; background-image: ${user.group.effect};`;
    },
    groupColor(user) {
      return user && user.group && user.group.hasOwnProperty('color')
        ? `color: ${user.group.color};`
        : `cursor: pointer;`;
    },
  },
  created() {
    dayjs.extend(relativeTime);
    this.interval = setInterval(() => this.$forceUpdate(), 30000);
  },

  filters: {
    diffForHumans: (date) => {
      if (!date) {
        return null;
      }

      return dayjs(date).fromNow();
    },
  },
  beforeDestroy() {
    clearInterval(this.interval);
  },
};
</script>
