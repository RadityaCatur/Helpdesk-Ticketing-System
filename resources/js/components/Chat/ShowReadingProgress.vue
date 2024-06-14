<template>
  <div>
    <span
      v-for="member in members"
      v-if="readingProgress(member)"
      :key="member.id"
      :title="member.fullname"
    >
      <span v-if="!simple && member.typing" class="mr-2 typing">
        <span>&bull;</span>
        <span>&bull;</span>
        <span>&bull;</span>
      </span>
    </span>
  </div>
</template>

<script>
export default {
  name: 'ShowReadingProgress',

  props: [
    'message',
    'room',
    'index',
    'simple',
  ],

  mounted() {
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
  },

  computed: {
    user() {
      return this.$store.state.user.userDetails;
    },
    messages() {
      return this.room.messages;
    },
    members() {
      return this.room.users;
    },
  },

  methods: {
    readingProgress(member) {
      // Set reading progress only for other user
      if (member.id === this.user.id) {
        return false;
      }

      // Set reading progress only for the last message
      if (!this.simple && this.index + 1 < this.messages.length) {
        return false;
      }

      const userProgress = this.$moment(member.pivot.updated_at);
      const messageDate = this.$moment(this.message.updated_at);

      if (!this.simple) {
        if (userProgress.isSameOrAfter(messageDate)) {
          return true;
        }
        return false;
      }

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
  },
};
</script>

<style>
.member-avatar {
  width: 25px;
  height: 25px;
}
/* from https://codepen.io/xwildeyes/pen/KpqVzN */
@keyframes blink {
    /**
     * At the start of the animation the dot
     * has an opacity of .2
     */
    0% {
      opacity: .2;
    }
    /**
     * At 20% the dot is fully visible and
     * then fades out slowly
     */
    20% {
      opacity: 1;
    }
    /**
     * Until it reaches an opacity of .2 and
     * the animation can start again
     */
    100% {
      opacity: .2;
    }
}
.typing span {
    /**
     * Use the blink animation, which is defined above
     */
    animation-name: blink;
    /**
     * The animation should take 1.4 seconds
     */
    animation-duration: 1.4s;
    /**
     * It will repeat itself forever
     */
    animation-iteration-count: infinite;
    /**
     * This makes sure that the starting style (opacity: .2)
     * of the animation is applied before the animation starts.
     * Otherwise we would see a short flash or would have
     * to set the default styling of the dots to the same
     * as the animation. Same applies for the ending styles.
     */
    animation-fill-mode: both;
}
.typing span:nth-child(2) {
    /**
     * Starts the animation of the third dot
     * with a delay of .2s, otherwise all dots
     * would animate at the same time
     */
    animation-delay: .2s;
}
.typing span:nth-child(3) {
    /**
     * Starts the animation of the third dot
     * with a delay of .4s, otherwise all dots
     * would animate at the same time
     */
    animation-delay: .4s;
}
</style>
