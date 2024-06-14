<template>
  <div class="home container-fluid ">
    <b-row align-v="center">
      <b-col class="col-xl-6 col-12 px-md-5 px-4">
        <b-container>
          <b-row>
            <b-col class="col-lg-9 px-md-5 px-0 mx-auto">
              <h2 class="font-weight-bold">TMS</h2>
              <h4 class="mt-n3 text-muted">Ticketing Management System</h4>

              <b-form-group class="mt-5" @keyup.enter="loginUser">
                <label for="">Username</label>
                <b-input
                  v-model="user.username"
                  type="text"
                  name="username"
                  required
                  placeholder="Username"
                  class="mt-1"
                  @keyup.enter="loginUser"
                />

                <div class="mt-3">
                  <label for="">Password</label>
                  <div class="input-group">
                    <input
                      v-model="user.password"
                      :type="[showPassword ? 'text' : 'password']"
                      class="form-control"
                      placeholder="Password"
                      @keyup.enter="loginUser"
                    >
                    <div class="input-group-append">
                      <span
                        class="input-group-text bg-white"
                        @click="showPassword = !showPassword"
                      >
                        <b-icon
                          :icon="showPassword ? 'eye-slash' : 'eye'"
                          aria-hidden="true"
                        />
                      </span>
                    </div>
                  </div>
                </div>
              </b-form-group>

              <b-row class="mt-3">
                <b-col cols="md-6">
                  <b-checkbox-group>
                    <b-form-checkbox>Ingat Saya</b-form-checkbox>
                  </b-checkbox-group>
                </b-col>
                <b-col cols="md-6" class="text-right pl-0">
                  <router-link
                    to="/forgot-password"
                    class="text-decoration-none"
                  >Lupa password?</router-link>
                </b-col>
              </b-row>
              <b-button variant="primary" type="submit" class="mt-5 p-2 btn-fill btn-block" @click="loginUser">Masuk</b-button>
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
import loginLogo from '@/assets/images/animations.gif';

export default {
  name: 'Login',
  mixins: [PasswordValidationMixin],
  data() {
    return {
      alert: true,
      data: {
        image: '../images/login_page.png',
      },
      user: {
        username: '',
        password: '',
      },
      showPassword: false,
    };
  },
  methods: {
    ...mapActions({
      login: 'user/loginUser',
      addNotification: 'application/addNotification',
    }),
    loginUser() {
      this.login(this.user)
        .then(() => {
          this.$router.push({ name: 'dashboard' });
        })
        .catch(() => {
          this.alert = false;
          this.$message({
            message: 'Username atau Password anda tidak sesuai!',
            type: 'error',
            duration: 3 * 1000,
          });
        });
    },
  },
};
</script>

<style>
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
