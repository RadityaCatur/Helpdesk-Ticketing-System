<template>
  <div class="chat-composer mb-2">
    <div class="d-flex w-100">
      <textarea
        :id="'message-room-id-' + room.id"
        ref="messageInput"
        v-model="messageText"
        type="text"
        class="message-input-field"
        tabindex="room.id"
        placeholder="ketik pesan"
        @click="markMessagesAsRead"
        @keyup.enter="sendMessage"
        @keyup.esc="closeAllChats"
      />

      <button
        class="bg-white border-0 chat-btn-clip rounded-circle"
        type="button"
        title="Emoji"        
        @click="selectEmoticonDlg"
      >
       <h4 class="my-0"> <b-icon icon="emoji-smile" variant="dark" /></h4>
      </button>
      <button
        class="bg-white border-0 chat-btn-clip rounded-circle"
        type="button"
        title="Kirim Foto Atau Dokumen"
        @click="selectFilesDlg"
      >
       <h4 class="my-0"> <b-icon icon="paperclip" variant="dark" /></h4>
      </button>

      <div class="input-group-append">
        <button
          class="bg-white border-0 chat-btn-clip rounded-circle"
          type="button"          
          @click="sendMessage"
          title="Kirim Pesan"
        >
          <h3 class="my-0"><b-icon icon="cursor-fill" rotate="45" variant="primary"/></h3>
        </button>
      </div>
    </div>

    <InsertEmoticons v-on:insert-emo="insertEmo"/>      

    <InsertFiles :room="room" :capture="capture" />
  </div>
</template>

<style>
.chat-composer {
  display: flex;
}
.chat-composer input {
  flex: 1 auto;
}
.chat-composer button {
  border-radius: 0;
}
</style>

<script>
import { mapActions, mapMutations } from 'vuex';
import InsertFiles from './InsertFile';
import InsertEmoticons from './InsertEmoticons'

export default {
  name: 'ComposeMessage',

  components: {
    InsertFiles,
    InsertEmoticons
  },

  props: ['room'],

  data() {
    return {
      messageText: '',
      lastTime: 0,
      capture: 'microphone',
    };
  },

  computed: {
    action() {
      return this.$store.state.shared.action;
    },
    user() {
      return this.$store.state.user.userDetails;
    },
  },

  mounted() {
    if (
      this.action &&
      this.action.type &&
      this.action.type === 'roomAdded' &&
      this.room.id === this.action.what.id
    ) {
      const elem = window.document.getElementById('collapse-' + this.room.id);
      if (elem) {
        elem.classList.add('show');
      }
      this.$refs.messageInput.focus();
      this.setAction(null);
    }
  },

  methods: {
    ...mapActions({
      setReadingProgress: 'chat/setReadingProgress',
      userIsTyping: 'chat/userIsTyping',
      sendMessageAction: 'chat/sendMessage',
    }),

    ...mapMutations({
      clearNewMessagesArrived: 'chat/clearRoomFromNewMessagesArrived',
      setAction: 'shared/setAction',
    }),

    userIsTyping() {
      // only sent a broadcast every 10 secs
      if (Math.floor((new Date() - this.lastTime) / 1000) > 10) {
        // send the broadcast and reset the timer
        this.userIsTyping(this.room.id);
        this.lastTime = new Date();
      }
    },

    selectEmoticonDlg() {
      $('#selectEmoticons').modal('toggle');
    },

    selectFilesDlg() {
      $('#insertFiles').modal('toggle');
    },

    insertEmo(emo) {
      const unichar = String.fromCodePoint(emo);
      this.messageText = this.messageText + unichar + ' ';
    },

    sendMessage() {
      if (!this.messageText) {
        return;
      }

      this.sendMessageAction({
        message: this.messageText,
        room_id: this.room.id,
      });
      this.messageText = '';
    },

    markMessagesAsRead() {
      const roomLastUpdate = this.room.updated_at;
      const usersReadingProgress = this.room.users.find(
        (el) => el.id === this.user.id
      ).pivot.updated_at;
      if (
        this.$moment(usersReadingProgress).isBefore(
          this.$moment(roomLastUpdate)
        )
      ) {
        this.setReadingProgress(this.room.id);
      }
      this.clearNewMessagesArrived(this.room.id);
    },

    closeAllChats() {
      this.$emit('close-all-chats');
    },
  },
};
</script>

<style scoped>
textarea {
  height: 20px;
  width: 100%;
  border: 1px solid #adadad !important;
  line-height: 20px;
  overflow: hidden;
  outline: 0;
  resize: none;
  border-radius: 20px;
  padding: 12px 16px;
  box-sizing: content-box;
  font-size: 16px;
}
.chat-btn-clip, .chat-btn-send {
  padding: 5px 13px !important;
}
button {
  padding: 6px !important;
}
button:focus {
  outline: none !important;
}
</style>