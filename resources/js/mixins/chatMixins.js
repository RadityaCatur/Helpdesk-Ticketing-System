import { mapActions, mapMutations } from 'vuex';
import ChatRoom from '@/components/Chat/ChatRoom';

export default {

  components: {
    ChatRoom,
  },

  data() {
    return {
      firstRun: true,
      secondRun: false,
    };
  },

  created() {
    this.safetyCheck();
  },

  watch: {
    tickets() {      
      this.rooms.map(room => {
        if (window.Echo.connector.channel[`private-${this.chatroomName}.chatroom.${room.id}`]) {
          return;
        }

        window.Echo.private(this.chatroomName + '.chatroom.' + room.id)
          .listen('MessagePosted', e => {
            if (e.message) {
              const newMessage = e.message;
              newMessage.user = e.user;
              room.messages.push(newMessage);
              room.updated_at = newMessage.updated_at;

              if (e.user.id !== this.user.id) {
                this.addToNewMessagesArrived(newMessage);
                // Notification
              }
              this.sortRooms();
            } else {
              window.console.warn(e);
            }
          })
          .listen('MessageUpdated', e => {
            if (e.message) {
              const newMessage = e.message;
              newMessage.user = e.user;
              // Find and replace  the old message
              const index = room.messages.findIndex(item => item.id === newMessage.id);
              room.messages[index] = newMessage;
              room.messages.push(newMessage);
              room.messages.pop();
            } else {
              window.console.warn(e);
            }
          })
          .listen('RoomTyping', e => {
            if (e.user) {
              room.users.forEach(item => {
                if (item.id === e.user.id) {
                  this.$set(item, 'typing', new Date());
                }
              });
            } else {
              window.console.warn(e);
            }
          })
          .on('pusher:subscription_succeeded', e => {
            window.console.log(`Subscription to chatroom ${room.id} was successfully`);
          });
      });

      const privateChannels = window.Echo.connector.channels;
      for (const key in privateChannels) {
        const chName = key.split('.');
        if (chName.length !== 2 || chName[0] !== 'private-chatroom') {
          continue;
        }

        if (this.rooms.find(item => item.id === parseInt(chName[1]))) {
          continue;
        }

        window.Echo.leave('chatroom.' + chName[1]);
      }
    },  
  },

  computed: {
    chatroomName() {
      return this.$store.state.chat.chatroomName;
    },

    newMessagesArrived() {
      return this.$store.state.chat.newMessagesArrived;
    },

    rooms() {
      return this.$store.state.chat.rooms;
    },

    user() {
      return this.$store.state.user.userDetails;
    },
  },  

  methods: {
    ...mapMutations({
      clearRoomFromNewMessagesArrived: 'chat/clearRoomFromNewMessagesArrived',
      addToNewMessagesArrived: 'chat/addToNewMessagesArrived',
      sortRooms: 'chat/sortRooms',
    }),

    ...mapActions({
      joinChatRoom: 'chat/joinChatRoom',
    }),    

    openChatRoom(val) {
      val.toggleDetails();
      this.userReadAllMessages(val.item.room.id);
    },

    userReadAllMessages(roomId) {
      this.clearRoomFromNewMessagesArrived(roomId);
    },

    safetyCheck() {
      // Safety Check - Make sure the presence channel has an active subscription!
      if (this.firstRun) {
        this.firstRun = false;
        return;
      }

      if (!window.Echo.connector.channels['presence-chatroom'].subscription.subscribed) {
        if (this.secondRun) {
          window.console.warn('Presence channel not active! Re-loading page!');
        } else {
          window.console.warn('Presence channel not active! Re-Joining it now!');
          this.joinChatRoom(this.user);
          this.secondRun = true;
        }
      }
    },
  },

};
