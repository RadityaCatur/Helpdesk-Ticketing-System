<template>
  <div class="home container-fluid">
    <b-row class="mt-lg-12" align-v="center">
      <b-col class="col-xl-6 col-12 px-md-5 px-4">
        <b-container>
          <b-row>
            <b-col class="col-lg-9 px-md-5 px-0 mx-auto">
              <h2 class="font-weight-bold">TMS</h2>
              <h4 class="mt-n3 text-muted">Ticketing Management System</h4>

              <b-overlay :show="loading">
                <div class="form-group mt-5">
                  <label for="email" class="text-capitalize">Email</label>
                  <input
                    v-model="user.email"
                    class="form-control"
                    :rules="[...requiredRules, ...emailRules]"
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="Email Address"
                  >
                </div>

                <div class="form-group">
                  <label for="password" class="text-capitalize">Password</label>
                  <div class="input-group">
                    <input
                      id="password"
                      v-model="user.password"                      
                      name="password"
                      :rules="[...requiredRules, ...passwordRules, passwordValidator]"
                      :type="[showPassword ? 'text' : 'password']"
                      class="form-control"
                      placeholder="Password"
                    >
                    <div class="input-group-append">
                      <span class="input-group-text bg-white" @click="showPassword = !showPassword">
                        <b-icon :icon="showPassword ? 'eye' : 'eye-slash'" aria-hidden="true" />
                      </span>
                    </div>
                  </div>
                  <div v-if="!$v.password.minLength" class="form__error">
                    <p class="text-danger">
                      *Password must be at least 8 characters
                    </p>
                  </div>
                </div>

                <div class="form-group">
                  <label for="password" class="text-capitalize">Konfirmasi Password</label>
                  <div class="input-group">
                    <input
                      id="password_confirmation"
                      v-model="user.password_confirmation"                      
                      name="password_confirmation"
                      :rules="[...requiredRules, ...passwordRules, passwordValidator]"
                      :type="[showPasswordConfirm ? 'text' : 'password']"
                      class="form-control"
                      placeholder="Password"
                    >
                    <div class="input-group-append">
                      <span class="input-group-text bg-white" @click="showPasswordConfirm = !showPasswordConfirm">
                        <b-icon :icon="showPasswordConfirm ? 'eye' : 'eye-slash'" aria-hidden="true" />
                      </span>
                    </div>
                  </div>                  
                </div>
              </b-overlay>

              <div align-h="between" class="align-items-center mt-5">
                <b-button variant="primary" class="btn-block btn-primary p-2 btn-fill" @click="sendResetPassword">Reset Password</b-button>
              </div>
              <div class="text-center mt-4">
                <router-link class="btn-link" to="login">Kembali Ke Login</router-link>
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
import { minLength, required } from 'vuelidate/lib/validators';

export default {
  name: 'ResetPassword',
  mixins: [PasswordValidationMixin],
  data() {
    return {
      data: {
        image: '../images/login_page.png',
      },
      user: {
        email: '',
        password: '',
        password_confirmation: '',
      },
      password: '',
      showPassword: false,
      showPasswordConfirm: false,
      loading: false,
    };
  },

  validations: {
    password: {
      required,
      minLength: minLength(8),
    },
  },
  methods: {
    ...mapActions({
      resetPassword: 'user/resetPassword',
    }),
    sendResetPassword() {
      this.loading = true;
      const token = this.$route.query.token;
      this.resetPassword({ ...this.user, token })
        .then(() => {
          this.loading = false;
          this.user = {
            email: '',
            password: '',
            password_confirmation: '',
          };
          this.$message({
            message: 'Reset password berhasil',
            type: 'success',
            duration: 5 * 1000,
          });
          this.$router.push({ name: 'login' });
        })
        .catch(() => {
          this.loading = false;
          this.$message({
            message: 'Reset password gagal',
            type: 'danger',
            duration: 5 * 1000,
          });
          this.$router.push({ name: 'reset-password' });
        });
    },
    passwordValidator() {
      return (
        this.user.password_confirmation === this.user.password ||
        'Password harus sama!'
      );
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
