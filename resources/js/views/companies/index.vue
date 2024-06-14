<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">List Client</h4>
        <b-button class="btn-fill btn-success px-4" @click="dialogVisible = true">Tambah</b-button>
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
              id="companies-table"
              :fields="fields"
              :items="items"
              :per-page="perPage"
              :current-page="currentPage"
              :busy="loading"
              striped
              hover
              show-empty
              responsive
              empty-text="Tidak ada data Perusahaan"
            >
              <template v-slot:cell(no)="data">
                {{ data.index + 1 }}
              </template>
              <template v-slot:cell(name)="data">
                {{ data.item.name }}
              </template>
              <template v-slot:cell(actions)="row" align="center">
                <button
                  class="btn-fill btn-info btn-sm"
                  @click="detailPic(row.item)"
                >
                  Tambah PIC
                </button>
                <button
                  class="btn-fill btn-warning btn-sm"
                  @click="handleEditCompany(row.item)"
                >
                  Ubah
                </button>
                <button
                  class="btn-fill btn-danger btn-sm"
                  @click="handleDeleteCompany(row.item)"
                >
                  Hapus
                </button>
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
              aria-controls="companies-table"
            />
          </div>
        </div>
      </div>
    </div>
    <el-dialog :visible.sync="dialogVisible" :title="company.status + ' Perusahaan'">
      <form method="POST" @submit.prevent="postData">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group mt-3">
              <label for="company">Nama Perusahaan</label>
              <input
                v-model="company.name"
                type="text"
                class="form-control"
                placeholder="Masukkan Nama Perusahaan"
                name="name"
                :class="{
                  'is-invalid': $v.company.name.$error,
                }"
                @input="setCompanyName($event.target.value)"
              >
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="text-center">
              <b-button
                type="button"
                class="btn-fill btn-success float-right px-4 mt-4"
                @click="postData"
              >
                {{ company.status }}
              </b-button>
            </div>
          </div>
        </div>
      </form>
    </el-dialog>

    <el-dialog :visible.sync="dialogPicVisible" :title="lableDialogPicVisible + ' Perusahaan'">
      <div class="row">
        <div class="col-md-12">
          <table class="table table-hovered">
            <thead class="bg-primary text-white">
              <th>No</th>
              <th>Nama PIC</th>
              <th>No. Handphone</th>
              <th>Email</th>
              <th>Address</th>
            </thead>
            <tbody>
              <tr v-for="(value,key) in pic" :key="key">
                <td class="text-break" width="7%">{{ key+1 }}</td>
                <td class="text-break" width="22%">{{ value.name }}</td>
                <td class="text-break" width="21%">{{ value.handphone }}</td>
                <td class="text-break" width="25%">{{ value.email }}</td>
                <td class="text-break" width="25%">{{ value.address }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="text-center">
            <b-button
              type="button"
              class="btn-fill btn-success float-right px-4 mt-4"
              @click="addPic(addPicCompanyId)"
            >
            Tambah PIC
            </b-button>
          </div>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import axios from '@/axios';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive (v-permission)
import checkPermission from '@/utils/permission'; // Permission checking
import { required } from 'vuelidate/lib/validators';
import { AlertUtils } from '@/mixins/alertUtils';

export default {
  name: 'Client',

  directives: {
    permission,
    waves,
  },

  mixins: [
    AlertUtils,
  ],

  data() {
    return {
      addPicCompanyId: "",
      lableDialogPicVisible: "",
      company: {
        id: '',
        name: '',
        status: 'Buat',
      },
      currentCompany: {},
      items: [],
      dataPic: [],
      pic: {
        id: "",
        name: "",
        address: "",
        handphone: "",
        email: ""
      },
      fields: [
        { key: 'no', label: 'No', sortable: false },
        { key: 'name', label: 'Nama Perusahaan', sortable: true, thStyle: { width: '70%'} },
        { key: 'actions', label: 'Aksi', sortable: false},
      ],
      search: '',
      perPage: 10,
      currentPage: 1,
      loading: true,
      dialogLoading: false,
      dialogVisible: false,
      dialogPicVisible: false,
    };
  },

  validations: {
    company: {
      name: {
        required,
      },
    },
  },

  computed: {
    rows() {
      return this.items.length;
    },
  },

  watch: {
    dialogVisible: function() {
      if (!this.dialogVisible && this.company.status === 'Perbarui') {
        this.company = {
          id: '',
          name: '',
          status: 'Buat',
        };
      }
    },
  },

  created() {
    this.getCompanies();
    this.getAllClients();
  },

  methods: {
    checkPermission,

    setCompanyName(value) {
      this.company.name = value;
      this.$v.company.name.$touch();
    },

    async getCompanies() {
      this.loading = true;
      await axios.get('/companies', {
        params: {
          q: this.search,
        },
      })
        .then((response) => {
          this.items = response.data.data;
          this.loading = false;
        });
    },

    handleSearch: _.debounce(function(e) {
      this.search = e.target.value;
      this.getCompanies();
    }, 500),

    handleEditCompany(val) {
      this.dialogVisible = true;
      const found = this.items.find(item => item.id === val.id);
      this.company = {
        id: found.id,
        name: found.name,
        status: 'Perbarui',
      };
      this.currentCompany = found;
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
          this.dataPic = response.data.data;
          this.loading = false;
        });
    },

    detailPic(val) {
      this.dialogPicVisible = true;
      var dataPIC = [];
      this.dataPic.forEach( el => {
        if (el.company_id == val.id) {
          dataPIC.push({
            id: el.company_id,
            name: el.fullname,
            address: el.alamat,
            handphone: el.handphone,
            email: el.email,
          });
        }
      });
      this.pic = dataPIC;
      console.log(JSON.stringify(this.pic))
      this.addPicCompanyId = val.id;
      this.lableDialogPicVisible = 'PIC';
    },

    addPic(val) {
      this.$router.push({ path: 'add-client', query: { company_id: val } })
    },

    postData() {
      switch (this.company.status) {
        case 'Buat':
          this.handleCreateCompany();
          break;
        case 'Perbarui':
          this.confirmUpdateCompany();
          break;
      }
    },

    handleDeleteCompany(val) {
      this.$confirm(val.name + ' akan dihapus secara permanen, yakin untuk menghapus? ', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        axios.delete(`companies/${val.id}`)
          .then((response) => {
            this.alertDeleteSuccess();
            this.getCompanies();
          })
          .catch((error) => {
            this.alertDeleteFailed();
          });
      }).catch(() => {
        this.alertCancel('Hapus Perusahaan');
      });
    },

    confirmUpdateCompany() {
      this.$confirm(`Anda yakin ingin memperbaharui data ${this.currentCompany.name} menjadi ${this.company.name}?`, 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        this.$v.$touch();
        if (this.$v.$invalid) {
          this.loading = false;
          this.alertUpdateFailed();
          return;
        } else {
          axios.put(`/companies/${this.company.id}`, { name: this.company.name })
            .then(response => {
              this.dialogVisible = false;
              this.company = {
                id: '',
                name: '',
                status: 'Buat',
              };
              this.alertUpdateSuccess();
              this.getCompanies();
              this.$v.$reset();
            })
            .catch(error => {
              this.dialogVisible = false;
              this.alertUpdateFailed();
            });
        }
      }).catch(() => {
        this.dialogVisible = false;
        this.alertCancel('Edit Perusahaan');
      });
    },

    handleCreateCompany() {
      this.$confirm(`Anda yakin akan membuat mitra perusahaan?`, 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        this.$v.$touch();
        if (this.$v.$invalid) {
          this.loading = false;
          this.alertUpdateFailed();
          return;
        } else {
          axios.post('/companies', { name: this.company.name })
            .then(response => {
              this.dialogVisible = false;
              this.company = {
                id: '',
                name: '',
                status: 'Buat',
              };
              this.alertStoreSuccess();
              this.getCompanies();
              this.$v.$reset();
            })
            .catch(error => {
              this.dialogVisible = false;
              this.alertSuccessFailed();
            });
        }
      }).catch(() => {
        this.dialogVisible = false;
        this.alertCancel('Edit Perusahaan');
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
