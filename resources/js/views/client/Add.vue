<template>
  <div class="p-3 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between mt-2">
        <h4 slot="header" class="card-title">Tambah Data PIC</h4>
        <b-button class="btn btn-secondary btn-fill" v-show="buttonBack" @click="backClient()">Kembali</b-button>
        <b-button class="btn btn-secondary btn-fill" v-show="buttonBackDefault" @click="$router.go(-1)">Kembali</b-button>
      </div>

      <form method="POST">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <label for="fullname">Nama Lengkap</label>
              <input
                v-model="newUser.fullname"
                type="text"
                class="form-control"
                placeholder="Masukkan Nama Lengkap"
                name="nama_lengkap"
                :class="{
                  'is-invalid': $v.newUser.fullname.$error,
                }"
                @input="setFullname($event.target.value)"
              >
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="username">Nama Pengguna</label>
              <input
                v-model="newUser.username"
                type="text"
                class="form-control"
                placeholder="Masukkan Nama Pengguna"
                name="username"
                :class="{
                  'is-invalid': $v.newUser.username.$error,
                }"
                @input="setUsername($event.target.value)"
              >
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="">Nama Perusahaan</label>
              <select
                v-model="newUser.company_id"
                class="form-control"
                :class="{
                  'border-danger': $v.newUser.company_id.$error,
                }"
                :disabled="companyDropdown"
              >
                <option v-for="(item, index) in companies" :key="index" :value="item.id">
                  {{ item.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="email">Email</label>
              <input
                v-model="newUser.email"
                type="text"
                class="form-control"
                placeholder="Masukkan Email"
                name="email"
                :class="{
                  'is-invalid': $v.newUser.email.$error,
                }"
                @input="setEmail($event.target.value)"
              >
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="handphone">No. Telepon</label>
              <input
                type="number"
                class="form-control"
                v-model="newUser.handphone"
                :error="$v.newUser.handphone.$error"
                @input="setHandphone($event.target.value)"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="alamat">Alamat</label>
              <textarea
                v-model="newUser.alamat"
                class="form-control"
                placeholder="Masukkan Alamat"
                name="alamat"
                :class="{
                  'is-invalid': $v.newUser.alamat.$error,
                }"
                @input="setAlamat($event.target.value)"
              ></textarea>
            </div>
          </div>

          <div class="mt-2">
            <label for="">Password</label>
            <div class="input-group">
              <input
                v-model="newUser.password"
                :type="[showPassword ? 'text' : 'password']"
                class="form-control"
                placeholder="Password"
                :class="{
                  'is-invalid': $v.newUser.password.$error,
                }"
                @input="setPassword($event.target.value)"
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
            <div v-if="!$v.newUser.password.minLength" class="form__error">
              <p class="text-danger">
                *Password minimal harus 8 karakter
              </p>
            </div>
          </div>

          <!-- <div class="row mt-2">
            <div class="col-md-12">
              <b-form-checkbox-group id="checkbox-group-2" v-model="sendEMail" name="flavour-2">
                <b-form-checkbox value="true">Kirim notifikasi ke User?</b-form-checkbox>
              </b-form-checkbox-group>
            </div>
          </div> -->

        </div>
        <div class="card-footer text-right mb-4 pt-0">
          <button
            type="button"
            class="btn btn-success btn btn-fill btn-md mt-4 px-4"
            @click="createUser"
          >
            <b-spinner v-if="loading" small />
            <span class="sr-only">Loading...</span>
            Submit
          </button>
        </div>
      </form>
    </div>
    <div class="clearfix" />
  </div>
</template>

<script>
import axios from '@/axios';
import permissionMixin from '@/mixins/permissionMixin';
import checkPermission from '@/utils/permission';
import { AlertUtils } from '@/mixins/alertUtils';
import PasswordValidationMixin from '@/mixins/passwordValidationMixin';
import Card from '@/components/UIComponents/Cards/Card.vue';
import VuePhoneNumberInput from 'vue-phone-number-input';
import { minLength, required, email } from 'vuelidate/lib/validators';

export default {
  name: 'AddClient',
  components: {
    Card,
    VuePhoneNumberInput,
  },

  mixins: [
    permissionMixin,
    AlertUtils,
  ],

  data() {
    return {
      buttonBack: false,
      buttonBackDefault: true,
      companyDropdown: false,
      confirmModal: false,
      companies: [],
      newUser: {},
      sendEMail: [],
      showPassword: false,
      loading: false,
      phoneNumberTranslations: {
        countrySelectorLabel: 'Kode Telp',
        countrySelectorError: 'Pilih Negara',
        phoneNumberLabel: 'Masukan No. Telepon',
        example: 'Contoh :',
      },
    };
  },
  validations: {
    newUser: {
      fullname: {
        required,
      },
      username: {
        required,
      },
      email: {
        required,
        email,
      },
      handphone: {
        required,
      },
      alamat: {
        required,
      },
      company_id: {
        required,
      },
      password: {
        required,
        minLength: minLength(8),
      },
    },
  },
  created() {
    this.resetNewUser();
    this.getCompanies();
  },

  methods: {
    checkPermission,

    setFullname(value) {
      this.newUser.fullname = value;
      this.$v.newUser.fullname.$touch();
    },

    setUsername(value) {
      this.newUser.username = value;
      this.$v.newUser.username.$touch();
    },

    setEmail(value) {
      this.newUser.email = value;
      this.$v.newUser.email.$touch();
    },

    setAlamat(value) {
      this.newUser.alamat = value;
      this.$v.newUser.alamat.$touch();
    },

    setHandphone(value) {
      this.newUser.handphone = value;
      this.$v.newUser.handphone.$touch();
    },

    setPassword(value) {
      this.newUser.password = value;
      this.$v.newUser.password.$touch();
    },

    async getCompanies() {
      await axios.get('/companies')
        .then((response) => {
          this.companies = response.data.data;
        })
        .catch((error) => {
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },
    createUser(e) {
      e.preventDefault();
      this.loading = true;
      const formData = this.prepareFormData();
      this.$v.$touch();
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: `Apakah anda yakin ingin menambahkan PIC ?`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya',
          cancelButtonText: 'Tidak',
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          reverseButtons: true,
        })
        .then(result => {
          if (result.value) {
            if (this.$v.$invalid) {
              this.loading = false;
              this.alertStoreFailed();
              return;
            } else {
              axios.post('/client', formData)
                .then((response) => {
                  this.resetNewUser();
                  this.loading = false;
                  this.alertStoreSuccess();
                  this.$v.$reset();
                }).catch((error) => {
                  this.loading = false;
                  this.alertStoreMessage(error.response.data.message);
                  this.$v.$reset();
                });
            }
          } else if (result.dismiss) {
            this.loading = false;
            this.alertCancel('Tambah Client');
            this.$v.$reset();
          }
        });
    },

    prepareFormData() {
      const formData = new FormData();
      Object.keys(this.newUser).forEach((key) => {
        formData.append(key, this.newUser[key]);
      });
      formData.append('sendEmail', this.sendEMail);
      return formData;
    },

    resetNewUser() {
      this.newUser = {
        fullname: '',
        username: '',
        email: '',
        password: '',
        handphone: '',
        alamat: '',
        roles: 'pic',
      };
      var company_id = this.$route.query.company_id;
      if (company_id) {
        this.newUser.company_id = company_id;
        this.companyDropdown = true;
        this.buttonBack = true;
        this.buttonBackDefault = false;
      }
      this.sendEMail = [];
    },
    openConfirmModal() {
      this.confirmModal = true;
    },
    closeConfirmModal() {
      this.confirmModal = false;
    },
    backClient() {
      this.$router.push({ path: 'companies' })
    }
  },
};
</script>

<style>

</style>
