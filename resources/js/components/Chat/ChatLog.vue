<template>
  <div
    id="nav-scroller"
    ref="content"
    class="chat-log"
    style="position:relative; height:500px; overflow-y:scroll;"
  >

    <div class="container">
      <Message
        v-for="(message, index) in room.messages"
        :key="index"
        :index="index"
        :message="message"
        :room="room"
      />

      <div
        v-if="!room.messages || (room.messages && !room.messages.length)"
        class="empty"
      >
        Belum ada pesan pada tiket ini.
      </div>

      <ComposeMessage
        :room="room"
        @close-all-chats="closeAllChats"
      />

      <SlidesShow
        :messages="room.messages"
      />
    </div>

  </div>
</template>

<script>
import Message from './Message';
import ComposeMessage from './ComposeMessage';
import SlidesShow from './SlidesShow';

export default {
  name: 'ChatLog',
      
  props: ['room'],

  components: {
    Message,
    ComposeMessage,
    SlidesShow,
  },


  mounted() {
    if (window.Echo.connector.channel[`private-chatroom.chatroom.${this.room.id}`]) {
      return;
    }

    window.Echo.private('chatroom.chatroom.' + this.room.id)
      .listen('RoomTyping', e => {
        if (e.user) {
          this.room.users.forEach(item => {
            if (item.id === e.user.id) {
              this.$set(item, 'typing', new Date());
            }
          });
        } else {
          window.console.warn(e);
        }
      })
      .listen('MessagePosted', e => {
        if (e.message) {
          const newMessage = e.message;
          newMessage.user = e.user;
          this.room.messages.push(newMessage);
          this.room.updated_at = newMessage.updated_at;
        } else {
          window.console.warn(e);
        }
      })
      .listen('MessageUpdated', e => {
        if (e.message) {
          const newMessage = e.message;
          newMessage.user = e.user;
          // Find and replace  the old message
          const index = this.room.messages.findIndex(item => item.id === newMessage.id);
          this.room.messages[index] = newMessage;
          this.room.messages.push(newMessage);
          this.room.messages.pop();
        } else {
          window.console.warn(e);
        }
      })
      .on('pusher:subscription_succeeded', e => {
        window.console.log(`Subscription to chatroom ${this.room.id} was successfully`);
      });
  },

  methods: {
    closeAllChats() {
      this.$emit('close-all-chats');
    },
  },
};
</script>

<style>
.empty {
  padding: 1rem;
  text-align: center;
}
</style>
