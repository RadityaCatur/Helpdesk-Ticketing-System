<template>
  <span>

    <ShowReadingProgress
      simple="true"
      :index="index"
      :room="room"
      :message="message"
    ></ShowReadingProgress>

    <div
      :class="{
        'text-right' : message.user_id === user.id,
        'mb-2' : showUsername || showMessageDate
      }"
    >

      <!-- show the actual message -->
      <span
        class="show-message chat-rounded mb-0 p-2 chat-bg-tf"
        :class="[deleted ? 'text-white chat-bg-remove' : message.user_id === user.id ? 'chat-bg-tf' : 'chat-bg-tf']"
      >

        <span v-if="!deleting">

          <span v-if="message.filename">

            <img
              v-if="message.filetype === 'image'"
              :src="'/images/'+message.filename"
              class="cursor-pointer image-preview"
              :title="message.message"
              :alt="message.message"
              @click="showSlideshow"
            >

            <audio
              v-if="message.filetype === 'audio'"
              :src="'/images/'+message.filename"
              controls
            />

            <video
              v-if="message.filetype === 'video'"
              :src="'/images/'+message.filename"
              width="250"
              controls
            />

            <span v-if="notSupportedFileType()">
              <a :href="'/images/'+message.filename">{{ message.filename }}</a>
            </span>

          </span>

          <span class="text-white" v-else-if="!deleted" v-html="showLinks(message.message)" />

          <i
            v-if="message.user_id === user.id && !deleted"
            title="delete this message"
            class="delete-message cursor-pointer text-danger bootstrap-icons"
            @click="deleting = true"
          >
            <b-icon icon="trash" />
          </i>
        </span>

        <small v-if="deleted" class="font-italic">(Pesan Telah Dihapus)</small>

        <span v-if="deleting" class="text-white">
          Hapus Pesan?
          <span class="badge badge-danger cursor-pointer" @click="deleteMessage">Yes</span>
          <span class="badge badge-secondary cursor-pointer" @click="deleting = false">Cancel</span>
        </span>
      </span>

      <!-- show message date and time -->
      <span 
        class="time_date mb-3 font-weight-bold" 
        :title="$moment(message.updated_at).format('LLLL')"
      > {{ usersObj[message.user_id] ? usersObj[message.user_id].username : '-' }}    |    {{ adaptiveDate(message.updated_at) }}
      </span>

    </div>

    <!-- show names of members depending on their reading progress -->
    <ShowReadingProgress
      :index="index"
      :room="room"
      :message="message"
    />

  </span>
</template>

<script>
import { mapActions, mapMutations } from 'vuex';
import ShowReadingProgress from './ShowReadingProgress';

export default {
  name: 'Message',

  components: {
    ShowReadingProgress,
  },
  props: ['message', 'room', 'index'],

  data() {
    return {
      deleting: false,
    };
  },

  computed: {  
    users() {
      return this.$store.state.user.users;
    },

    user() {
      return this.$store.state.user.userDetails;
    },

    nextMessage() {
      if (this.index === this.room.messages.length) {
        return null;
      }
      return this.room.messages[this.index + 1];
    },

    messages() {
      return this.room.messages;
    },

    members() {
      return this.room.users;
    },

    usersObj() {
      const obj = {};
      this.users.map(elem => obj[elem.id] = elem);
      return obj;
    },

    deleted() {
      if (!this.message.message || this.message.message.length !== 19) {
        return false;
      }
      const dt = this.message.message.split(' ');
      if (dt.length !== 2) {
        return false;
      }
      const da = dt[0].split('-');
      if (da.length !== 3) {
        return false;
      }
      const tm = dt[1].split(':');
      if (tm.length !== 3) {
        return false;
      }
      return true;
    },

    showMessageDate() {
      if (!this.nextMessage) {
        return true;
      }
      if (this.message.user_id !== this.nextMessage.user_id) {
        return true;
      }
      return this.$moment(this.message.updated_at).add(2, 'minutes').isBefore(this.$moment(this.nextMessage.updated_at));
    },

    showUsername() {
      if (!this.nextMessage) {
        return true;
      }
      return this.message.user_id !== this.nextMessage.user_id;
    },
  },

  methods: {
    ...mapActions({
      deletingMessage: 'chat/deleteMessage',
    }),

    ...mapMutations({
      setDialog: 'shared/setDialog',
    }),

    adaptiveDate(val) {
      if (!val) {
        return '';
      }
      const dt = this.$moment(val);
      const now = this.$moment();
      if (dt.isSame(now, 'day')) {
        return dt.format('H:mm');
      }
      if (dt.isSame(now, 'week')) {
        return dt.format('ddd H:mm');
      }
      if (dt.isSame(now, 'year')) {
        return dt.format('D MMM H:mm');
      }
      return dt.format('D MM YYYY');
    },

    notSupportedFileType() {
      const supported = ['image', 'audio', 'video'];
      if (supported.indexOf(this.message.filetype) >= 0) {
        return false;
      }
      return true;
    },

    showLinks(text) {
      const urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
      return text.replace(urlRegex, function(url) {
        return '<a target="new" href="' + url + '">' + url + '</a>';
      });
    },

    readingProgressBefore(member) {
      if (member.id === this.user.id) {
        return false;
      } // don't show the current user
      // check if this member's reading progress is before this and after the previous message

      const userProgress = this.$moment(member.pivot.updated_at);
      const messageDate = this.$moment(this.message.updated_at);
      // if index is 0, we are at the very first message in this room
      if (this.index === 0) {
        if (userProgress.isBefore(messageDate)) {
          return true;
        } else {
          return false;
        }
      }
      const prevMessageDate = this.$moment(this.messages[this.index - 1].updated_at);
      if (userProgress.isBefore(messageDate) && userProgress.isAfter(prevMessageDate)) {
        return true;
      }
      return false;
    },

    deleteMessage() {
      this.message.message = 'deleting...';
      this.deleting = false;
      this.deletingMessage(this.message.id);
    },

    showSlideshow() {
      this.setDialog({
        messageId: this.message.id,
      });
      $('#imagesSlideShow').modal('show');
    },
  },
};
</script>

<style>
.image-preview {
  width: 200px;
}
@media (min-width: 768px) {
  .image-preview {
    width: 250px;
  }
}
@media (min-width: 1200px) {
  .image-preview {
    width: 350px;
  }
}
.delete-message {
  display: none;
}
.show-message:hover .delete-message {
  display: inline;
}

.time_date {
  color: #747474;
  display: block;
  font-size: 12px;
  margin: 8px 0 0;
}

.chat-rounded {
  border-radius: 8px !important;
}
.chat-bg-tf {
  background-color: #32bea6 !important;
}
.chat-bg-remove {
  background-color: #9de4d8 !important;
}
</style>
