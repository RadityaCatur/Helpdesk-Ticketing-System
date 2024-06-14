<template>
  <div class="home container-fluid">
    <b-row class="mt-lg-12" align-v="center">
      <b-col class="col-xl-6 col-12 px-md-5 px-4">
        <b-container>
          <b-row>
            <b-col class="col-lg-9 px-md-5 px-0 mx-auto">
              <h3 class="font-weight-bold">Lupa Password</h3>
              <p class="mt-n2 text-muted">Kami akan mengirimkan email</p>

              <!-- <div v-if="alert == false" class="mt-3 mb-n4">
                <v-alert dismissible type="danger" class="mx-auto alert alert-danger btn-block text-center">
                  Email tidak ditemukan!
                </v-alert>
              </div> -->

              <b-overlay :show="loading">
                <div class="form-group my-4 mt-5">
                  <label for="">Email</label>
                  <input
                    v-model="email"
                    :rules="emailRules"
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="email address"
                    class="form-control"
                  >
                </div>
              </b-overlay>

              <div align-h="between" class="align-items-center mt-5">
                <b-button variant="primary" class="mt-1 btn btn-primary btn-block btn-fill" @click="sendForgotPassword">Reset Password</b-button>
              </div>
              <div class="text-center mt-4">
                <router-link to="/login" class="text-decoration-none btn-link">Kembali ke Login</router-link>
              </div>
            </b-col>
          </b-row>
        </b-container>
      </b-col>

      <b-col class="col-md-6 bg-login-side d-none d-sm-none d-lg-block d-xl-block d-md-none" :style="{ 'background-image': 'url(' + data.image + ')' }" />
    </b-row>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import PasswordValidationMixin from '@/mixins/passwordValidationMixin';
export default {
  name: 'ForgotPassword',
  mixins: [PasswordValidationMixin],
  data() {
    return {
      alert: true,
      data: {
        image: '../images/login_page.png',
      },
      email: '',
      loading: false,
    };
  },
  methods: {
    ...mapActions({
      forgotPassword: 'user/forgotPassword',
    }),
    sendForgotPassword() {
      this.loading = true;
      this.forgotPassword({ email: this.email }).then(() => {
        this.loading = false;
        this.email = '';
        this.$message({
          type: 'success',
          message: 'Dimohon untuk mengecek Email!',
        });
      }).catch(() => {
        this.loading = false;
        this.$message({
          type: 'error',
          message: 'Email tidak ditemukan.',
        });
      });
    },
  },
};
</script>

<style scoped>
.login {
  width: 80px;
}

.bg-login-side {
    height: 100vh;
    background-size: cover;
    background-position: center;
}
.input-group-append:hover {
    cursor: pointer;
}

</style>
