<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">Kategori Aplikasi</h4>
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
              id="categories-table"
              :fields="fields"
              :items="list"
              :per-page="perPage"
              :current-page="currentPage"
              :busy="loading"
              striped
              hover
              show-empty
              responsive
              empty-text="Tidak ada data kategori aplikasi"
            >
              <template v-slot:cell(no)="data">
                {{ data.index + 1 }}
              </template>
              <template v-slot:cell(name)="data" width="800">
                {{ data.item.name }}
              </template>
              <template v-if="checkPermission(['manage permission'])" v-slot:cell(actions)="row" align="center">
                <button
                  v-if="row.item.name !== 'admin'"
                  v-permission="['manage permission']"
                  class="btn-fill btn-warning btn-sm"
                  @click="handleEditCategory(row.item)"
                >
                  Ubah
                </button>
                <button
                  v-if="row.item.name !== 'admin'"
                  v-permission="['manage permission']"
                  class="btn-fill btn-danger btn-sm"
                  @click="handleDeleteCategory(row.item)"
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
              aria-controls="categories-table"
            />
          </div>
        </div>
      </div>
    </div>

    <el-dialog :visible.sync="dialogVisible" :title="category.status + ' Kategori Aplikasi'">
      <form method="POST" @submit.prevent="postData">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group mt-3">
              <label for="company">Nama Kategori Aplikasi</label>
              <input
                v-model="category.name"
                type="text"
                class="form-control"
                placeholder="Masukkan Nama Kategori Aplikasi"
                name="name"
                :class="{
                  'is-invalid': $v.category.name.$error,
                }"
                @input="setCategoryName($event.target.value)"
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
                {{ category.status }}
              </b-button>
            </div>
          </div>
        </div>
      </form>
    </el-dialog>

  </div>
</template>

<script>
import _ from 'lodash';
import axios from '@/axios';
import waves from '@/directive/waves';
import permission from '@/directive/permission';
import checkPermission from '@/utils/permission';
import { required } from 'vuelidate/lib/validators';
import { AlertUtils } from '@/mixins/alertUtils';

export default {
  name: 'Categories',

  directives: {
    permission,
    waves,
  },

  mixins: [
    AlertUtils,
  ],
  data() {
    return {
      category: {
        id: '',
        name: '',
        status: 'Buat',
      },
      currentCategory: {},
      list: [],
      fields: [
        { key: 'no', label: 'No', sortable: false },
        { key: 'name', label: 'Nama Kategori', sortable: true, thStyle: { width: "70%" } },
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
    category: {
      name: {
        required,
      },
    },
  },

  computed: {
    rows() {
      return this.list.length;
    },
  },

  watch: {
    dialogVisible: function() {
      if (!this.dialogVisible && this.category.status === 'Perbarui') {
        this.category = {
          id: '',
          name: '',
          status: 'Buat',
        };
      }
    },
  },

  created() {
    this.getCategories();
  },

  methods: {
    checkPermission,

    setCategoryName(value) {
      this.category.name = value;
      this.$v.category.name.$touch();
    },

    async getCategories() {
      this.loading = true;
      await axios.get('/categories', {
        params: {
          q: this.search,
        },
      })
        .then((response) => {
          this.list = response.data.data.data;
          this.loading = false;
        });
    },

    handleSearch: _.debounce(function(e) {
      this.search = e.target.value;
      this.getCategories();
    }, 500),

    handleEditCategory(val) {
      this.dialogVisible = true;
      const found = this.list.find(item => item.id === val.id);
      this.category = {
        id: found.id,
        name: found.name,
        status: 'Perbarui',
      };
      this.currentCategory = found;
    },

    postData() {
      switch (this.category.status) {
        case 'Buat':
          this.handleCreateCategory();
          break;
        case 'Perbarui':
          this.confirmUpdateCategory();
          break;
        default:
          this.category = {
            id: '',
            name: '',
            status: 'Buat',
          };
          break;
      }
    },

    handleDeleteCategory(val) {
      this.$confirm(val.name + ' akan dihapus secara permanen, yakin untuk menghapus? ', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        axios.delete(`categories/${val.id}`)
          .then((response) => {
            this.alertDeleteSuccess();
            this.getCategories();
          })
          .catch((error) => {
            this.alertDeleteFailed();
          });
      }).catch(() => {
        this.alertCancel('Hapus Kategori');
      });
    },

    confirmUpdateCategory() {
      this.$confirm(`Anda yakin ingin memperbaharui data ${this.currentCategory.name} menjadi ${this.category.name}?`, 'Warning', {
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
          axios.put(`/categories/${this.category.id}`, { name: this.category.name })
            .then(response => {
              this.dialogVisible = false;
              this.category = {
                id: '',
                name: '',
                status: 'Buat',
              };
              this.alertUpdateSuccess();
              this.getCategories();
              this.$v.$reset();
            })
            .catch(error => {
              this.dialogVisible = false;
              this.alertUpdateFailed();
            });
        }
      }).catch(() => {
        this.dialogVisible = false;
        this.alertCancel('Edit Kategori');
      });
    },

    handleCreateCategory() {
      this.$confirm(`Anda yakin akan membuat kategori?`, 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        this.$v.$touch();
        if (this.$v.$invalid) {
          this.loading = false;
          this.alertStoreFailed();
          return;
        } else {
          axios.post('/categories', { name: this.category.name })
            .then(response => {
              this.dialogVisible = false;
              this.category = {
                id: '',
                name: '',
                status: 'Buat',
              };
              this.alertStoreSuccess();
              this.getCategories();
              this.$v.$reset();
            })
            .catch(error => {
              this.dialogVisible = false;
              this.alertUpdateFailed();
            });
        }
      }).catch(() => {
        this.alertCancel('Tambah Kategori');
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
