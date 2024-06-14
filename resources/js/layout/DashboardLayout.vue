<template>
  <div class="wrapper">
    <side-bar>
      <div class="logo mb-4">
        <router-link to="/dashboard" class="simple-text">
          <div class="logo-img">
            <img src="@/assets/avatar.png" alt="">
          </div>
          Hi, {{ user.fullname }}
        </router-link>
      </div>

      <sidebar-link to="/dashboard">
        <b-icon icon="house-door" />
        <p class="ml-2">Beranda</p>
      </sidebar-link>
      
      <sidebar-link v-if="isRole('admin', 'employee')" to="/dashboard/companies">
        <b-icon icon="people-fill" aria-hidden="true" />
        <p class="ml-2">Client</p>
      </sidebar-link>

      <sidebar-link v-if="isRole('admin')" to="/dashboard/client">
        <b-icon icon="person-badge" aria-hidden="true" />
        <p class="ml-2">PIC</p>
      </sidebar-link>

      <sidebar-link to="/dashboard/projects">
        <b-icon icon="card-text" />
        <p class="ml-2">Aplikasi</p>
      </sidebar-link>

      <sidebar-link to="/dashboard/tickets">
        <b-icon icon="clipboard-x" />
        <p class="ml-2">Tiket</p>
      </sidebar-link>

      <li v-if="isRole('admin')" class="b-nav-dropdown dropdown">
        <a v-b-toggle="'collapse-1'" class="nav-link dropdown-toggle "><b-icon icon="person-lines-fill" />
          <p class="ml-2">User Manajemen</p>
        </a>

        <ul>
          <b-collapse id="collapse-1" class="nav-item pl-3">
            <router-link v-if="isRole('admin', 'employee')" to="/dashboard/users" class="nav-link">
              <font color="white">Pengguna</font>
            </router-link>

            <router-link to="/dashboard/roles-permissions" class="nav-link">
              <font color="white">Hak Akses</font>
            </router-link>
          </b-collapse>
        </ul>
      </li>

      <!-- <sidebar-link
        v-if="isRole('admin')"
        to="/dashboard/activities"
      >
        <b-icon icon="broadcast" />
        <p class="ml-2">Broadcast</p>
      </sidebar-link> -->

      <li v-if="isRole('admin', 'employee')" class="b-nav-dropdown dropdown">
        <a v-b-toggle="'collapse-2'" class="nav-link dropdown-toggle">
          <b-icon icon="gear" />
          <p class="ml-2">Pengaturan</p>
        </a>

        <ul>
          <b-collapse id="collapse-2" class="nav-item pl-3">
            <router-link to="/dashboard/categories" class="nav-link">
              <font color="white">Kategori Aplikasi</font>
            </router-link>

            <router-link to="/dashboard/positions" class="nav-link">
              <font color="white">List Posisi</font>
            </router-link>
          </b-collapse>
        </ul>
      </li>

      <sidebar-link to="/dashboard/profile">
        <b-icon icon="person" />
        <p class="ml-2">Profil</p>
      </sidebar-link>

      <sidebar-link
        v-if="isRole('admin') || isRole('employee')"
        to="/dashboard/activities"
      >
        <b-icon icon="clock" />
        <p class="ml-2">Catatan Aktivitas</p>
      </sidebar-link>

      <mobile-menu slot="content" />
    </side-bar>
    <div class="main-panel">
      <top-navbar />

      <dashboard-content @click="toggleSidebar" />
    </div>
  </div>
</template>

<script>
import TopNavbar from './TopNavbar.vue';
import DashboardContent from './Content.vue';
import MobileMenu from './MobileMenu.vue';
import store from '@/store';
import { mapGetters } from 'vuex';

export default {
  components: {
    TopNavbar,
    DashboardContent,
    MobileMenu,
  },
  methods: {
    toggleSidebar() {
      if (this.$sidebar.showSidebar) {
        this.$sidebar.displaySidebar(false);
      }
    },
  },
  computed: {
    ...mapGetters({
      user: 'user/userDetails',
    }),
  },
};
</script>

<style scoped>
li.b-nav-dropdown.dropdown:hover {
  cursor: pointer;
}

/* li.b-nav-dropdown.dropdown ul {
  background: #142333;
} */
</style>
