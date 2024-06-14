<template>
  <div>
    <!-- chatRoom header -->
    <div :id="'heading-'+room.id" class="card-header px-1 pt-1 pb-1 p-sm-1 p-md-2 my-0 mb-2">
      <!-- show messages counter -->
      <!-- <span class="nowrap overflow-hidden">        
        <span
          v-if="unreadMessages + arrivedMessages"
          class="badge badge-danger badge-pill"
        >{{ unreadMessages }}</span>        
      </span> -->
    </div>

    <ChatLog
      v-if="room.id !== 0"
      :room="room"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import ChatLog from './ChatLog';

export default {
  name: 'ChatRoom',

  components: {
    ChatLog,
  },
  props: ['room'],

  computed: {

    user() {
      return this.$store.state.user.userDetails;
    },

    unreadMessages() {
      const usersReadingProgress = this.room.users.find(usr => usr.id === this.user.id);
      if (!usersReadingProgress) {
        return 0;
      }
      const lastReadMessageIdx = this.room.messages.findIndex(
        msg => msg.updated_at > usersReadingProgress.pivot.updated_at
      );
      if (lastReadMessageIdx < 0) {
        return 0;
      }
      return this.room.messages.length - lastReadMessageIdx;
    },

    arrivedMessages() {
      let num = 0;
      this.newMessagesArrived.forEach(el => {
        if (el.room_id === this.room.id) {
          num += 1;
        }
      });
      return num;
    },

    newMessagesArrived() {
      return this.$store.state.chat.newMessagesArrived;
    },
  },

  mounted() {
    // check if user's last typing dates are outdated
    setTimeout(() => {
      this.checkTypingState();
    }, 9000);
  },

  methods: {
    checkTypingState() {
      this.room.users.forEach(usr => {
        if (usr.typing) {
          const diff = Math.floor((new Date() - usr.typing));
          if (diff > 9000) {
            usr.typing = false;
          }
        }
      });
      setTimeout(() => {
        this.checkTypingState();
      }, 9000);
    },

  },

};
</script>

<style>
.chatroom-header {
  opacity: 0.6;
  font-size: small;
}
.chatroom-header:hover {
  opacity: 1;
  font-size: inherit;
}
.chat-room-body {
  background-image: url("/static/paper.gif");
  background-repeat: repeat;
}
.room-props-and-name {
  text-overflow: ellipsis;
  white-space: nowrap;
  overflow: hidden;
}
.room-name {
  font-family: 'Times New Roman', Times, serif;
  font-size: larger;
  color:darkred;
}
.nowrap {
  white-space: nowrap;
}
</style>
