<template>
  <div>
    <b-overlay :show="loading" rounded="lg" no-wrap>
      <template #overlay>
        <div class="text-center align-center h-100">
          <b-icon icon="stopwatch" font-scale="3" animation="cylon" />
          <p id="cancel-label">Please wait...</p>
        </div>
      </template>
    </b-overlay>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
export default {
  name: 'VerifyEmail',
  data() {
    return {
      loading: false,
    };
  },
  methods: {
    ...mapActions({
      verifyEmail: 'user/verifyEmail',
    }),
  },
  created() {
    this.loading = true;
    this.verifyEmail(this.$route.query).then((response) => {
      this.loading = false;
      this.$message({
        type: 'info',
        message: 'Verifikasi email berhasil',
      });
      this.$router.push({ name: 'login' });
    }).catch((error) => {
      this.loading = false;
      this.$message({
        type: 'info',
        message: 'Verifikasi email berhasil',
      });
      this.$router.push({ name: 'login' });
    });
  },
};
</script>

<style scoped>
</style>
