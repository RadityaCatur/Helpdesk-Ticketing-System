<template>
  <div
    class="sidebar bg-primary"
    :style="sidebarStyle"
    :data-color="backgroundColor"
    :data-image="backgroundImage"
  >
    <div class="sidebar-wrapper">
      <slot name="content" />
      <ul class="nav">
        <!--By default vue-router adds an active class to each route link. This way the links are colored when clicked-->
        <slot>
          <sidebar-link
            v-for="(link,index) in sidebarLinks"
            :key="link.name + index"
            :to="link.path"
            :link="link"
            @click="closeNavbar"
          >
            <i :class="link.icon" />
            <p>{{ link.name }}</p>
          </sidebar-link>
        </slot>
      </ul>
    </div>
  </div>
</template>
<script>
import SidebarLink from './SidebarLink.vue';

export default {
  components: {
    SidebarLink,
  },
  props: {
    title: {
      type: String,
      default: 'Hi, Admin!',
    },
    backgroundColor: {
      type: String,
      default: 'black',
      validator: (value) => {
        const acceptedValues = ['', 'blue', 'azure', 'green', 'orange', 'red', 'purple', 'black'];
        return acceptedValues.indexOf(value) !== -1;
      },
    },
    backgroundImage: {
      type: String,
      default: '@/static/img/sidebar-5.jpg',
    },
    activeColor: {
      type: String,
      default: 'success',
      validator: (value) => {
        const acceptedValues = ['primary', 'info', 'success', 'warning', 'danger'];
        return acceptedValues.indexOf(value) !== -1;
      },
    },
    sidebarLinks: {
      type: Array,
      default: () => [],
    },
    autoClose: {
      type: Boolean,
      default: true,
    },
  },
  provide() {
    return {
      autoClose: this.autoClose,
    };
  },
  computed: {
    sidebarStyle() {
      return {
        backgroundImage: `url(${this.backgroundImage})`,
      };
    },
  },
};

</script>
<style>

</style>
