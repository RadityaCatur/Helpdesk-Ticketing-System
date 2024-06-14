<template>
  <div>
    <v-container
      class="fill-height"
      fluid
    >
      <v-row
        align="center"
        justify="center"
      >
        <v-col
          cols="12"
          md="4"
          sm="8"
        >
          <v-card class="elevation-12">
            <v-toolbar
              color="primary"
              dark
              flat
            >
              <v-toolbar-title>Formulir Pendaftaran</v-toolbar-title>
              <v-spacer />
            </v-toolbar>
            <v-card-text>
              <v-form ref="registerForm" :value="formValid">
                <v-text-field
                  v-model="newUser.email"
                  :rules="emailRules"
                  label="Email"
                  name="email"
                  type="email"
                  @keyup.enter="registerUser"
                />

                <v-text-field
                  v-model="newUser.name"
                  :rules="nameRules"
                  label="Nama"
                  name="name"
                  type="text"
                  @keyup.enter="registerUser"
                />

                <v-text-field
                  id="password"
                  v-model="newUser.password"
                  :rules="passwordRules"
                  label="Password"
                  name="password"
                  type="password"
                  @keyup.enter="registerUser"
                />
                <v-text-field
                  id="password_confirmation"
                  v-model="newUser.password_confirmation"
                  :rules="passwordRules"
                  label="Konfirmasi Password"
                  name="password_confirmation"
                  type="password"
                  @keyup.enter="registerUser"
                />
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer />
              <v-btn color="primary" @click="registerUser">Daftar</v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <router-link :to="{name: 'login'}">Login</router-link>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import PasswordValidationMixin from '@/mixins/passwordValidationMixin';
export default {
  name: 'Register',
  mixins: [PasswordValidationMixin],
  data() {
    return {
      formValid: false,
      newUser: {
        email: '',
        name: '',
        password: '',
        password_confirmation: '',
      },
      nameRules: [
        v => !!v || 'The Name is required',
      ],
    };
  },
  methods: {
    ...mapActions({
      addNotification: 'application/addNotification',
      register: 'user/registerUser',
    }),
    registerUser() {
      if (this.$refs.registerForm.validate()) {
        this.register(this.newUser)
          .then((response) => {
            if (response.data && response.data.success) {
              this.addNotification({
                show: true,
                text: 'Success!',
              }).then(() => {
                this.$router.push({
                  name: 'login',
                });
              });
            }
          })
          .catch(() => {
            this.addNotification({
              show: true,
              text: 'Failed!',
            });
          });
      }
    },
  },
};
</script>

<style scoped>
</style>
