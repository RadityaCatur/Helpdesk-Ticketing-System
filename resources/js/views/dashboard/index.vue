<template>
  <div class="dashboard-container">
    <!-- <component :is="currentRole" /> -->
    <div v-if="isRole('admin') || isRole('employee')">
      <admin-dashboard />
    </div>
    <div v-else-if="isRole('pic')">
      <client-dashboard />
    </div>
  </div>
</template>

<script>
import adminDashboard from './Admin';
import clientDashboard from './Client';
import { mapGetters } from 'vuex';

export default {
  name: 'Dashboard',
  components: { adminDashboard, clientDashboard },
  data() {
    return {
      currentRole: 'adminDashboard',
    };
  },
  computed: {
    ...mapGetters([
      'roles',
    ]),
  },
  created() {
    if (!this.roles.includes('admin')) {
      this.currentRole = 'clientDashboard';
    }
  },
};
</script>

<style>

</style>
