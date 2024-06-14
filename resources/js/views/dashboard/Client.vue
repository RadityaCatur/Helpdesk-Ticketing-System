<template>
  <div class="mt-4">
    <div class="container px-4">
      <counter />
    </div>
    <div>
      <div>
        <ticket />
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import ticket from '@/views/ticket/List.vue';
import project from '@/views/project/List.vue';
import Counter from './components/Counter.vue';

export default {
  name: 'ClientDashboard',
  components: {
    ticket, project, Counter,
  },
  computed: {
    ...mapGetters({
      user: 'user/userDetails',
    }),
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .dashboard-editor-container {
    background-color: #e3e3e3;
    min-height: 100vh;
    padding: 50px 60px 0px;
    .pan-info-roles {
      font-size: 12px;
      font-weight: 700;
      color: #333;
      display: block;
    }
    .info-container {
      position: relative;
      margin-left: 190px;
      height: 150px;
      line-height: 200px;
      .display_name {
        font-size: 48px;
        line-height: 48px;
        color: #212121;
        position: absolute;
        top: 25px;
      }
    }
  }
</style>
