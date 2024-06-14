<template>
  <div :class="{'nav-open': $sidebar.showSidebar}">
    <router-view />
  </div>
</template>

<script>
import axios from '@/axios';
import { mapActions, mapGetters, mapMutations } from 'vuex';

export default {
  name: 'App',
  created() {
    this.checkUserState()
      .then(() => {
        if (this.isLoggedIn) {
          axios.get('check-auth')
            .then(() => {
              // Set Auth User
              this.me()
                .then(() => {
                  // Get central chatroom name from server
                  this.setChatroomName('chatroom');

                  // Load the rooms for this user
                  this.loadRooms();

                  this.loadUsers();

                  // Start listening to backend broadcast channel
                  this.joinChatroomAction(this.authUser);
                })
                .catch((error) => {
                  console.log(error);
                });
            })
            .catch((error) => {
              console.log(error);
              this.$router.push({ name: 'login' });
              localStorage.removeItem('token');
            });
        }
      })
      .catch(() => {
        this.$router.push({ name: 'login' });
        localStorage.removeItem('token');
      });
  },
  computed: {
    ...mapGetters({
      isLoggedIn: 'user/loggedIn',
      authUser: 'user/userDetails',
    }),
  },
  methods: {
    ...mapActions({
      checkUserState: 'user/setLoggedInState',
      me: 'user/me',
      loadUsers: 'user/loadUsers',
      loadRooms: 'chat/loadRooms',
      joinChatroomAction: 'chat/joinChatroom',
    }),
    ...mapMutations({
      setChatroomName: 'chat/setChatroomName',
    }),
  },
};
</script>

<style lang="scss">
    .vue-notifyjs.notifications{
        .list-move {
            transition: transform 0.3s, opacity 0.4s;
        }
        .list-item {
            display: inline-block;
            margin-right: 10px;
        }
        .list-enter-active {
            transition: transform 0.2s ease-in, opacity 0.4s ease-in;
        }
        .list-leave-active {
            transition: transform 1s ease-out, opacity 0.4s ease-out;
        }
        .list-enter {
            opacity: 0;
            transform: scale(1.1);
        }
        .list-leave-to {
            opacity: 0;
            transform: scale(1.2, 0.7);
            }
    }
    .btn.btn-fill {
        font-size: 15px !important;
        border-radius: 2px !important;
        padding: 5px 15px !important;
    }
    .btn.btn-block {
      padding: 10px 15px !important;
}
</style>
