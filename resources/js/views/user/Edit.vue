<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between mt-2">
        <p class="mb-0">Ubah Pengguna</p>
        <b-button class="btn btn-secondary btn-fill" @click="$router.go(-1)">Kembali</b-button>
      </div>
      <form method="POST">
        <div class="card-body">
          <div class="row">
            <div class="col-md-12 mt-n3">
              <fg-input
                v-model="user.fullname"
                type="text"
                label="Nama Lengkap"
                placeholder="Nama Lengkap"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mt-n3">
              <fg-input
                v-model="user.username"
                type="text"
                label="Nama Pengguna"
                placeholder="Nama Pengguna"
                readonly
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mt-n3">
              <label for="">Nama Perusahaan</label>
              <select v-model="user.company_id" class="form-control">
                <option :value="null" disabled selected>Pilih Perusahaan</option>
                <option v-for="(item, index) in companies" :key="index" :value="item.id">
                  {{ item.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <fg-input
                v-model="user.email"
                type="email"
                label="Email"
                placeholder="Email"
                readonly
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12 mt-n3">
              <label for="handphone">Alamat</label>
              <textarea
                v-model="user.alamat"
                class="form-control"
                type="text"
                placeholder="Alamat"
              ></textarea>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="handphone">No. Telepon</label>
              <input
                type="number"
                class="form-control"
                v-model="user.handphone"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="">Pilih Role</label>
              <select v-model="user.roles" class="form-control">
                <option :value="null" disabled selected>Pilih role</option>
                <option v-for="(role, index) in roles" :key="index" :value="role.name">
                  {{ role.name }}
                </option>
              </select>
            </div>
          </div>

          <div v-if="user.roles === 'employee'" class="row">
            <div class="col-md-12">
              <label for="">Pilih Posisi</label>
              <select v-model="user.position_id" class="form-control">
                <option :value="null" disabled selected>Pilih posisi</option>
                <option v-for="(item, index) in positions" :key="index" :value="item.id">
                  {{ item.name }}
                </option>
              </select>
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
            @click="updateUser"
          >
            <b-spinner v-if="loading" small />
            <span class="sr-only">Loading...</span>
            Submit
          </button>
        </div>
      </form>
      <div class="clearfix" />
    </div>
  </div>
</template>

<script>
import axios from '@/axios';
import { AlertUtils } from '@/mixins/alertUtils';
import Card from '@/components/UIComponents/Cards/Card.vue';
import VuePhoneNumberInput from 'vue-phone-number-input';

export default {
  name: 'EditUser',

  components: {
    Card,
    VuePhoneNumberInput,
  },

  mixins: [
    AlertUtils,
  ],

  data() {
    return {
      user: {},
      roles: [],
      companies: [],
      positions: [],
      loading: false,
      phoneNumberTranslations: {
        countrySelectorLabel: 'Kode Telp',
        countrySelectorError: 'Pilih Negara',
        phoneNumberLabel: 'Masukan No. Telepon',
        example: 'Contoh :',
      },
    };
  },

  watch: {
    '$route': 'getUser',
  },

  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getRoles();
    this.getUser(id);
    this.getCompanies();
    this.getPositions();
  },
  methods: {
    async getRoles() {
      this.loading = true;
      await axios.get('/roles')
        .then((response) => {
          this.roles = response.data.data;
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },
    async getUser(id) {
      this.loading = true;
      await axios.get(`/users/${id}`)
        .then((response) => {
          this.user = response.data.data;
          this.user.roles = this.user.roles[0];
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },
    async getCompanies() {
      this.loading = true;
      await axios.get('/companies')
        .then((response) => {
          this.companies = response.data.data;
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },
    async getPositions() {
      this.loading = true;
      await axios.get('/positions')
        .then((response) => {
          this.positions = response.data.data;
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },
    updateUser() {
      this.loading = true;
      const id = this.$route.params && this.$route.params.id;
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: `Apakah anda yakin ingin mengedit data ${this.user.fullname}?`,
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
            axios({
              url: `/users/${id}`,
              data: {
                fullname: this.user.fullname,
                email: this.user.email,
                company_id: this.user.company_id,
                position_id: this.user.position_id,
                roles: this.user.roles,
                alamat: this.user.alamat,
                handphone: this.user.handphone,
              },
              method: 'PUT',
            }).then((response) => {
              this.loading = false;
              this.alertUpdateSuccess();
            }).catch((err) => {
              this.alertUpdateFailed();
              this.loading = false;
            });
          } else if (result.dismiss) {
            this.loading = false;
            this.alertCancel('Edit User');
          }
        });
    },
  },
};
</script>

<style>

</style>
