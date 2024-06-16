<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">List Posisi</h4>
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
              id="positions-table"
              :fields="fields"
              :items="items"
              :per-page="perPage"
              :current-page="currentPage"
              :busy="loading"
              striped
              hover
              show-empty
              responsive
              empty-text="Tidak ada data posisi"
            >
              <template v-slot:cell(no)="data">
                {{ data.index + 1 }}
              </template>
              <template v-slot:cell(name)="data" width="800">
                {{ data.item.name }}
              </template>
              <template v-slot:cell(actions)="row" align="center">
                <button
                  class="btn-fill btn-warning btn-sm"
                  @click="handleEditPosition(row.item)"
                >
                  Ubah
                </button>
                <button
                  class="btn-fill btn-danger btn-sm"
                  @click="handleDeletePosition(row.item)"
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
              aria-controls="positions-table"
            />
          </div>
        </div>
      </div>
    </div>
    <el-dialog :visible.sync="dialogVisible" :title="position.status + ' Posisi'">
      <form method="POST" @submit.prevent="postData">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group mt-3">
              <label for="position">Nama Posisi</label>
              <input
                v-model="position.name"
                type="text"
                class="form-control"
                placeholder="Masukkan Nama Posisi"
                name="name"
                :class="{
                  'is-invalid': $v.position.name.$error,
                }"
                @input="setPositionName($event.target.value)"
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
                {{ position.status }}
              </b-button>
            </div>
          </div>
        </div>
      </form>
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
      position: {
        id: '',
        name: '',
        status: 'Buat',
      },
      currentPosition: {},
      items: [],
      fields: [
        { key: 'no', label: 'No', sortable: false },
        { key: 'name', label: 'Nama Posisi', sortable: true },
        { key: 'actions', label: 'Aksi', sortable: false },
      ],
      search: '',
      perPage: 10,
      currentPage: 1,
      loading: true,
      dialogLoading: false,
      dialogVisible: false,
    };
  },

  validations: {
    position: {
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
      if (!this.dialogVisible && this.position.status === 'Perbarui') {
        this.position = {
          id: '',
          name: '',
          status: 'Buat',
        };
      }
    },
  },

  created() {
    this.getPositions();
  },

  methods: {
    checkPermission,

    setPositionName(value) {
      this.position.name = value;
      this.$v.position.name.$touch();
    },

    async getPositions() {
      this.loading = true;
      await axios.get('/positions', {
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
      this.getPositions();
    }, 500),

    handleEditPosition(val) {
      this.dialogVisible = true;
      const found = this.items.find(item => item.id === val.id);
      this.position = {
        id: found.id,
        name: found.name,
        status: 'Perbarui',
      };
      this.currentPosition = found;
    },

    postData() {
      switch (this.position.status) {
        case 'Buat':
          this.handleCreatePosition();
          break;
        case 'Perbarui':
          this.confirmUpdatePosition();
          break;
      }
    },

    handleDeletePosition(val) {
      this.$confirm(val.name + ' akan dihapus secara permanen, yakin untuk menghapus? ', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        axios.delete(`positions/${val.id}`)
          .then((response) => {
            this.alertDeleteSuccess();
            this.getPositions();
          })
          .catch((error) => {
            this.alertDeleteFailed();
          });
      }).catch(() => {
        this.alertCancel('Hapus Posisi');
      });
    },

    confirmUpdatePosition() {
      this.$confirm(`Anda yakin ingin memperbaharui data ${this.currentPosition.name} menjadi ${this.position.name}?`, 'Warning', {
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
          axios.put(`/positions/${this.position.id}`, { name: this.position.name })
            .then(response => {
              this.dialogVisible = false;
              this.position = {
                id: '',
                name: '',
                status: 'Buat',
              };
              this.alertUpdateSuccess();
              this.getPositions();
              this.$v.$reset();
            })
            .catch(error => {
              this.dialogVisible = false;
              this.alertUpdateFailed();
            });
        }
      }).catch(() => {
        this.dialogVisible = false;
        this.alertCancel('Edit Posisi');
      });
    },

    handleCreatePosition() {
      this.$confirm(`Anda yakin akan membuat Posisi ?`, 'Warning', {
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
          axios.post('/positions', { name: this.position.name })
            .then(response => {
              this.dialogVisible = false;
              this.position = {
                id: '',
                name: '',
                status: 'Buat',
              };
              this.alertStoreSuccess();
              this.getPositions();
              this.$v.$reset();
            })
            .catch(error => {
              this.dialogVisible = false;
              this.alertStoreFailed();
            });
        }
      }).catch(() => {
        this.dialogVisible = false;
        this.alertCancel('Tambah Posisi');
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
