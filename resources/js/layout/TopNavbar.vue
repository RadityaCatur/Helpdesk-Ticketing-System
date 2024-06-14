<template>
  <nav class="navbar navbar-expand-lg border-0 shadow-sm">
    <div class="container-fluid">
      <button
        type="button"
        class="navbar-toggler navbar-toggler-right"
        :class="{toggled: $sidebar.showSidebar}"
        aria-controls="navigation-index"
        aria-expanded="false"
        aria-label="Toggle navigation"
        @click="toggleSidebar"
      >
        <span class="navbar-toggler-bar burger-lines" />
        <span class="navbar-toggler-bar burger-lines" />
        <span class="navbar-toggler-bar burger-lines" />
      </button>
      <div class="collapse navbar-collapse justify-content-end">
        <ul class="nav navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="#" data-toggle="dropdown">
              <i class="nc-icon nc-palette" />
            </a>
          </li>
        </ul>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a v-if="loggedIn" small class="nav-link" style="cursor: pointer;" @click="logoutModal=true">
              Keluar &nbsp;
              <b-icon icon="power" />
            </a>
          </li>
        </ul>
      </div>
    </div>
    <b-modal v-model="logoutModal" title="Apakah anda yakin ingin keluar?">
      <template v-slot:modal-footer>
        <div class="float-left">
          <b-button
            variant="secondary"
            size="sm"
            class="btn-fill"
            @click="logoutModal=false"
          >
            Batal
          </b-button>
        </div>
        <div class="float-right">
          <b-button
            variant="danger"
            size="sm"
            class="btn-fill"
            @click="logout"
          >
            Ya
          </b-button>
        </div>
      </template>
    </b-modal>
  </nav>
</template>
<script>
import { mapActions, mapGetters } from 'vuex';
import Breadcrumb from '@/components/Breadcrumb.vue';

export default {
  components: {
    Breadcrumb,
  },
  data() {
    return {
      activeNotifications: false,
      logoutModal: false,
    };
  },
  computed: {
    routeName() {
      const { name } = this.$route;
      return this.capitalizeFirstLetter(name);
    },
    ...mapGetters({
      loggedIn: 'user/loggedIn',
    }),
  },
  methods: {
    ...mapActions({
      logoutUser: 'user/logoutUser',
      addNotification: 'application/addNotification',
    }),
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1);
    },
    toggleNotificationDropDown() {
      this.activeNotifications = !this.activeNotifications;
    },
    closeDropDown() {
      this.activeNotifications = false;
    },
    toggleSidebar() {
      this.$sidebar.displaySidebar(!this.$sidebar.showSidebar);
    },
    hideSidebar() {
      this.$sidebar.displaySidebar(false);
    },
    logout() {
      this.logoutUser()
        .then(() => {
          this.$router.push({ name: 'login' });
        })
        .then(() => {
          this.$router.push({ name: 'login' });
        });
    },
  },
};
</script>

<style>
.navbar{
  min-height: 58px !important;
}
</style>
