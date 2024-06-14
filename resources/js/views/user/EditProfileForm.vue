<template>
  <div class="p-3 px-4 mt-3">
    <form>
      <!-- ============= UBAH PROFILE ============= -->
      <div class="card border-0 shadow">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class=" card-title">Ubah Profil</h4>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <fg-input
                v-model="userDetails.username"
                type="text"
                label="Nama Pengguna"
                placeholder="Nama Pengguna"
                readonly
              />
            </div>
            <div class="col-md-6">
              <fg-input
                v-model="userDetails.email"
                type="email"
                label="Email"
                placeholder="Email"
                readonly
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <fg-input
                v-model="userDetails.fullname"
                type="text"
                label="Nama Lengkap"
                placeholder="Nama Lengkap"
              />
            </div>
            <div class="col-md-6">
              <label for="handphone">No. Telepon</label>
              <input
                type="number"
                class="form-control"
                v-model="userDetails.handphone"
              />
            </div>
          </div>

          <div class="row my-4">
            <div class="col-md-12">
              <div class="text-right">
                <button type="submit" class="btn btn-success btn-fill" @click.prevent="changeDetails">
                  Perbarui Profil
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="clearfix" />
      </div>
      <!-- ============= END UBAH PROFILE ============= -->

      <!-- ============= GANTI PASSWORD ============= -->
      <div class="card border-0 shadow">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class="card-title">Ganti Password</h4>
        </div>
        <div class="card-body">

          <b-form ref="changePasswordForm">
            <div class="row">
              <div class="col-md-12">
                <fg-input
                  v-model="user.oldPassword"
                  :rules="[...requiredRules, ...passwordRules]"
                  type="password"
                  name="oldPassword"
                  label="Password Lama"
                  placeholder="Password Lama"
                />
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <fg-input
                  ref="newPasswordInput"
                  v-model="user.newPassword"
                  :rules="[...requiredRules, ...passwordRules, newPasswordValidator]"
                  label="Password Baru"
                  name="newPassword"
                  type="password"
                  placeholder="Password Baru"
                />
              </div>
              <div class="col-md-6">
                <fg-input
                  ref="newPasswordConfirmationInput"
                  v-model="user.newPasswordConfirmation"
                  :rules="[...requiredRules, ...passwordRules, newPasswordValidator]"
                  label="Konfirmasi Password Baru"
                  name="newPasswordConfirmation"
                  type="password"
                />
              </div>
            </div>

            <div class="row my-4">
              <div class="col-md-12">
                <div class="text-right">
                  <button type="submit" class="btn btn-success btn-fill" @click.prevent="changePassword">
                    Ganti password
                  </button>
                </div>
              </div>
            </div>
          </b-form>
        </div>

        <div class="clearfix" />
      </div>
      <!-- ============= END GANTI PASSWORD ============= -->
    </form>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import PasswordValidationMixin from '@/mixins/passwordValidationMixin';
import Card from '@/components/UIComponents/Cards/Card.vue';

export default {
  components: {
    Card,
  },
  mixins: [PasswordValidationMixin],
  data() {
    return {
      user: {
        oldPassword: '',
        newPassword: '',
        newPasswordConfirmation: '',
      },
      phoneNumberTranslations: {
        countrySelectorLabel: 'Kode Telp',
        countrySelectorError: 'Pilih Negara',
        phoneNumberLabel: 'Masukan No. Telepon',
        example: 'Contoh :',
      },
    };
  },
  computed: {
    ...mapGetters({
      userDetails: 'user/userDetails',
    }),
  },
  methods: {
    ...mapActions({
      updateDetails: 'user/updateDetails',
      changeUserPassword: 'user/changePassword',
      addNotification: 'application/addNotification',
    }),
    changeDetails() {
      this.updateDetails(this.userDetails)
        .then((response) => {
          this.$message({
            message: 'Berhasil mengubah data user',
            type: 'success',
            duration: 5 * 1000,
          });
        });
    },
    changePassword() {
      this.changeUserPassword(Object.assign({}, this.user, {
        newPassword_confirmation: this.user.newPasswordConfirmation,
      }))
        .then((response) => {
          this.$message({
            message: 'Password berhasil diubah',
            type: 'success',
            duration: 5 * 1000,
          });
          this.user = {
            oldPassword: '',
            newPassword: '',
            newPasswordConfirmation: '',
          };
        });
    },
    newPasswordValidator() {
      return (this.user.newPasswordConfirmation === this.user.newPassword) || 'New password is not confirmed';
    },
  },
};
</script>
<style>

hr {
  margin-top: 1rem;
  margin-bottom: 1rem;
  border: 0;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

</style>
