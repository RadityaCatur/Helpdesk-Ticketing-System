<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="mb-0">List PIC</h4>
        <b-button v-if="(isRole('admin'))" v-waves variant="info" :loading="downloading" class="btn btn-fill ml-auto mr-3 px-4" icon="download" @click="handleExportToExcel">
          Ekspor
        </b-button>
        <router-link tag="b-button" class="btn-fill btn-success px-4" to="add-client">
          Tambah
        </router-link>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-2">
            <div class="form-inline">
              <label class="mr-2">Menampilkan</label>
              <select v-model="perPage" class="form-control">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
              <label class="ml-2">Data</label>
            </div>
          </div>
          <div class="col-md-4 offset-md-4">
            <div class="form-inline float-right">
              <label class="mr-2">Cari</label>
              <input type="text" class="form-control" placeholder="Cari..." @input="handleSearch">
            </div>
          </div>
          <div class="col-md-12">
            <b-table
              id="client-table"
              :items="items"
              :fields="fields"
              :per-page="perPage"
              :current-page="currentPage"
              striped
              hover
              show-empty
              :busy="loading"
              responsive
              empty-text="Tidak ada data client"
            >
              <template v-slot:cell(no)="data">
                {{ data.index + 1 }}
              </template>
              <template v-slot:cell(company)="row">
                {{ row.item.company ? row.item.company.name : "-" }}
              </template>
              <template v-slot:cell(actions)="row">
                <button class="btn-fill btn-info btn-sm" @click="handleDetail(row)">Rincian</button>
                <button class="btn-fill btn-warning btn-sm" @click="handleEdit(row)">Ubah</button>
                <button class="btn-fill btn-danger btn-sm" @click="handleDestroy(row)">Hapus</button>
                <button class="btn-fill btn-secondary btn-sm" @click="handleEditPermission(row);">Hak akses</button>
              </template>
              <template #table-busy>
                <div class="text-center text-primary my-2">
                  <b-spinner class="align-middle" />
                  <strong>Loading...</strong>
                </div>
              </template>
            </b-table>
          </div>
          <div class="col-md-12">
            <b-pagination
              v-model="currentPage"
              align="right"
              :total-rows="rows"
              :per-page="perPage"
              aria-controls="client-table"
            />
          </div>
        </div>
      </div>

      <b-modal v-model="dialogVisible" title="Hak Akses" hide-footer>
        <form action="POST">
          <div class="row">
            <div class="col-md-12">
              <label for="company">Username</label>
              <input
                v-model="item.username"
                type="text"
                class="form-control"
                placeholder="Masukkan Username"
                name="username"
                :class="{
                  'is-invalid': $v.item.username.$error,
                }"
                @input="setUsername($event.target.value)"
              >
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <label for="">Password Baru</label>
              <div class="input-group">
                <input
                  v-model="item.password"
                  :type="[showPassword ? 'text' : 'password']"
                  class="form-control"
                  placeholder="Password"
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
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <p class="mb-1">Status</p>
              <input
                v-model="item.status"
                type="radio"
                name="status"
                value="1"
                class="mr-1"
                :class="{
                  'is-invalid': $v.item.status.$error,
                }"
                @input="setStatus($event.target.value)"
              > Aktif
              <input
                v-model="item.status"
                type="radio"
                name="status"
                value="0"
                class="ml-4 mr-1"
                :class="{
                  'is-invalid': $v.item.status.$error,
                }"
                @input="setStatus($event.target.value)"
              > Tidak Aktif
            </div>
          </div>
          <div v-if="$v.item.status.$error" class="form__error">
            <p class="text-danger">
              *Status harus diisi.
            </p>
          </div>
          <div class="text-right mb-4 pt-0">
            <button
              type="button"
              class="btn btn-success btn btn-fill btn-md mt-4 px-4"
              @click="updateClient"
            >
              <b-spinner v-if="loading" small />
              <span class="sr-only">Loading...</span>
              Submit
            </button>
          </div>
        </form>
      </b-modal>
    </div>
  </div>
</template>

<script>
import axios from '@/axios';
import waves from '@/directive/waves'; // Waves directive
import formatMixin from '@/mixins/formatMixin';
import permission from '@/directive/permission'; // Permission directive (v-permission)
import checkPermission from '@/utils/permission'; // Permission checking
import { AlertUtils } from '@/mixins/alertUtils';
import { minLength, required } from 'vuelidate/lib/validators';

export default {
  name: 'Companies',

  directives: {
    permission,
    waves,
  },

  mixins: [
    AlertUtils, formatMixin,
  ],

  data() {
    return {
      password: '',
      fields: [
        { key: 'no', label: 'No', sortable: false },
        { key: 'fullname', label: 'Nama Lengkap', sortable: true },
        { key: 'email', label: 'Email', sortable: true },
        { key: 'handphone', label: 'No. Telepon', sortable: true },
        { key: 'company', label: 'Nama Perusahaan', sortable: true },
        { key: 'actions', label: 'Aksi', sortable: false },
      ],
      items: [],
      perPage: 10,
      showPassword: false,
      currentPage: 1,
      search: '',
      sortBy: 'created_at',
      sortByDesc: false,
      item: {},
      permissionModal: false,
      confirmModal: false,
      loading: false,
      downloading: false,
      dialogLoading: false,
      dialogVisible: false,
      companies: [],
    };
  },

  validations: {
    item: {
      username: {
        required,
      },
      status: {
        required,
      }
    },
  },

  computed: {
    rows() {
      return this.items.length;
    },
  },

  created() {
    this.getAllClients();
    this.getCompanies();
  },

  methods: {
    checkPermission,

    setUsername(value) {
      this.item.username = value;
      this.$v.item.username.$touch();
    },

    setPassword(value) {
      this.item.password = value;
      // this.$v.item.password.$touch();
    },

    setStatus(value) {
      this.item.status = value;
      this.$v.item.status.$touch();
    },

    async getAllClients() {
      this.loading = true;
      const current_page = this.search === '' ? 1 : this.currentPage;
      axios.get('/client', {
        params: {
          q: this.search,
        },
      })
        .then((response) => {
          this.items = response.data.data;
          this.loading = false;
        });
    },

    async updateClient() {
      this.loading = true;
      this.$v.$touch();
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: `Apakah anda yakin ingin mengubah hak akses client ?`,
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
              this.alertUpdateFailed();
              return;
            } else {
              axios.put(`/client/${this.item.id}`, this.item)
                .then((response) => {
                  this.loading = false;
                  this.dialogVisible = false;
                  this.$v.$reset();
                  this.getAllClients();
                  this.alertUpdateSuccess();
                }).catch(() => {
                  this.loading = false;
                  this.dialogVisible = false;
                  this.$v.$reset();
                  this.getAllClients();
                  this.alertUpdateFailed();
                });
            }
          } else if (result.dismiss) {
            this.loading = false;
            this.dialogVisible = false;
            this.$v.$reset();
            this.alertCancel('Edit Hak Akses Client');
          }
        });
    },

    handleSearch: _.debounce(function(e) {
      this.search = e.target.value;
      this.getAllClients();
    }, 500),

    handleEditPermission(val) {
      this.item = val.item;
      this.dialogVisible = true;
      this.$v.item.username.$touch();
    },

    handleDetail(val) {
      this.$router.push({
        name: 'show-user',
        params: {
          id: val.item.id,
        },
      });
    },

    handleEdit(val) {
      this.$router.push({
        name: 'edit-user',
        params: {
          id: val.item.id,
        },
      });
    },

    handleDestroy(val) {
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: `Apakah anda yakin ingin menghapus client ?`,
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
            axios.delete(`users/${val.item.id}`)
              .then((response) => {
                this.getAllClients();
                this.alertDeleteSuccess();
              })
              .catch((error) => {
                this.alertDeleteFailed();
              });
          } else if (result.dismiss) {
            this.loading = false;
            this.alertCancel('Delete Client');
          }
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

    handleExportToExcel() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = [
          'Id User',
          'Nama Lengkap',
          'Username',
          'Email',
          'No. Telepon',
          'Alamat',
        ];
        const filterVal = [
          'id',
          'fullname',
          'username',
          'email',
          'handphone',
          'alamat',
        ];
        const data = this.formatJson(filterVal, this.items);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: `client-list-${this.formatDate()}`,
        });
        this.downloading = false;
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.permissions-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
h1, .h1, h2, .h2, h3, .h3, h4, .h4 {
    margin: 0 !important;
  }
</style>
