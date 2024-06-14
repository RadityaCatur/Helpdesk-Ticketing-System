<template>
  <div>
    <ul class="nav nav-mobile-menu">
      <li class="nav-item">
        <a class="nav-link" style="cursor: pointer;" @click="logoutModal=true">
          Logout
        </a>
      </li>
    </ul>
    <b-modal v-model="logoutModal" title="Are you sure want to logout?">
      <template v-slot:modal-footer>
        <div class="float-left">
          <b-button
            variant="secondary"
            size="sm"
            @click="logoutModal=false"
          >
            Close
          </b-button>
        </div>
        <div class="float-right">
          <b-button
            variant="danger"
            size="sm"
            @click="logout"
          >
            Yes
          </b-button>
        </div>
      </template>
    </b-modal>
  </div>
</template>
<script>
import DropDown from '@/components/UIComponents/Dropdown.vue';
import { mapActions } from 'vuex';

export default {
  components: {
    DropDown,
  },

  data() {
    return {
      logoutModal: false,
    };
  },

  methods: {
    ...mapActions({
      logoutUser: 'user/logoutUser',
    }),
    logout() {
      this.logoutUser()
        .then(() => {
          this.$router.push({ name: 'login' });
        });
    },
  },
};
</script>
<style>
</style>
