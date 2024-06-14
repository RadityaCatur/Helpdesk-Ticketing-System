<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between mt-2">
        <h4 class="card-title">Buat Pengguna</h4>
        <b-button class="btn btn-secondary btn-fill" @click="$router.go(-1)">Kembali</b-button>
      </div>

      <form method="POST">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 mt-n3">
              <fg-input
                v-model="newUser.fullname"
                type="text"
                label="Nama Lengkap"
                placeholder="Nama Lengkap"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mt-n3">
              <fg-input
                v-model="newUser.username"
                type="text"
                label="Nama Pengguna"
                placeholder="Nama Pengguna"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mt-n3">
              <fg-input
                v-model="newUser.email"
                type="email"
                label="Email"
                placeholder="Email"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mt-n3">
              <fg-input
                v-model="newUser.alamat"
                type="text"
                label="Alamat"
                placeholder="Alamat"
              />
            </div>
          </div>

          <div class="row mt-n3">
            <div class="col-md-12">
              <label for="handphone">No. Telepon</label>
              <input
                type="number"
                v-model="newUser.handphone"
                class="form-control"
              />
            </div>
          </div>

          <!-- <div class="row">
            <div class="col-md-12">
              <label for="">Pilih Perusahaan</label>
              <select v-model="newUser.company_id" class="form-control">
                <option :value="null" disabled selected>Pilih Perusahaan</option>
                <option v-for="(item, index) in companies" :key="index" :value="item.id">
                  {{ item.name }}
                </option>
              </select>
            </div>
          </div> -->

          <div class="row">
            <div class="col-md-12">
              <label for="">Pilih Role</label>
              <select v-model="newUser.roles" class="form-control">
                <option :value="null" disabled selected>Pilih role</option>
                <option v-for="(role, index) in roles" :key="index" :value="role.name">
                  {{ role.name }}
                </option>
              </select>
            </div>
          </div>

          <div v-if="newUser.roles === 'employee'" class="row">
            <div class="col-md-12">
              <label for="">Pilih Posisi</label>
              <select v-model="newUser.position_id" class="form-control">
                <option :value="null" disabled selected>Pilih posisi</option>
                <option v-for="(item, index) in positions" :key="index" :value="item.id">
                  {{ item.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <fg-input
                ref="newPasswordInput"
                v-model="newUser.password"
                label="Password"
                type="password"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="text-center" />
            </div>
          </div>
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
import PasswordValidationMixin from '@/mixins/passwordValidationMixin';
import { AlertUtils } from '@/mixins/alertUtils';
import Card from '@/components/UIComponents/Cards/Card.vue';
import VuePhoneNumberInput from 'vue-phone-number-input';

export default {
  name: 'AddUser',
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
      roles: [],
      companies: [],
      positions: [],
      newUser: {
        roles: null,
        company_id: null,
        position_id: null,
      },
      loading: false,
      phoneNumberTranslations: {
        countrySelectorLabel: 'Kode Telp',
        countrySelectorError: 'Pilih Negara',
        phoneNumberLabel: 'Masukan No. Telepon',
        example: 'Contoh :',
      },
    };
  },

  created() {
    this.resetNewUser();
    this.getPermissions();
    this.getRoles();
    this.getCompanies();
    this.getPositions();
  },

  methods: {
    checkPermission,
    async getPermissions() {
      await axios.get('/permissions')
        .then((response) => {
          const { all, menu, other } = this.classifyPermissions(response.data.data);
          this.permissions = all;
          this.menuPermissions = menu;
          this.otherPermissions = other;
        });
    },

    async getRoles() {
      await axios.get('/roles')
        .then((response) => {
          this.roles = response.data.data.filter(role => role.name !== 'pic');
        })
        .catch((error) => {
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
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

    async getPositions() {
      await axios.get('/positions')
        .then((response) => {
          this.positions = response.data.data;
        })
        .catch((error) => {
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },

    createUser() {
      this.loading = true;
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: 'Apakah anda yakin ingin menambahkan pengguna baru?',
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
            axios.post('/users', this.newUser)
              .then((response) => {
                this.resetNewUser();
                this.loading = false;
                this.alertStoreSuccess();
              }).catch(() => {
                this.alertStoreFailed();
                this.loading = false;
              });
          } else if (result.dismiss) {
            this.loading = false;
            this.alertCancel('Tambah User');
          }
        });
    },

    resetNewUser() {
      this.newUser = {
        fullname: '',
        username: '',
        email: '',
        password: '',
        handphone: '',
        roles: 'pic',
      };
    },
  },
};
</script>

<style>

</style>
