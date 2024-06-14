<template>
  <div class="p-3 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">Aplikasi</h4>
        <b-button v-if="(isRole('admin'))" v-waves :loading="downloading" class="btn-fill btn-info ml-auto " icon="download" @click="handleExportToExcel">
          Ekspor
        </b-button>
        <router-link v-if="(isRole('admin'))" tag="b-button" to="/dashboard/add-project" class="btn-fill btn-success ml-3 text-light">
          Tambah
        </router-link>
      </div>
      <div class="card-body mt-2">
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
              responsive
              show-empty
              :busy="loading"
              empty-text="Tidak ada data Aplikasi"
            >
              <template v-slot:cell(no)="data">
                {{ data.index + 1 }}
              </template>
              <template v-slot:cell(actions)="row">
                <button class="btn-fill btn-info btn-sm" @click="handleDetail(row)">Rincian</button>
                <button v-if="isRole('admin')" class="btn-fill btn-warning btn-sm" @click="handleEdit(row)">Ubah</button>
                <button v-if="isRole('admin')" class="btn-fill btn-danger btn-sm" @click="handleDestroy(row)">Hapus</button>
              </template>
              <!-- Loading -->
              <template #table-busy>
                <div class="text-center text-primary my-2">
                  <b-spinner class="align-middle" />
                  <strong>Loading...</strong>
                </div>
              </template>
              <!-- Loading -->
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
    </div>
  </div>
</template>

<script>
import _ from 'lodash';
import axios from '@/axios';
import waves from '@/directive/waves'; // Waves directive
import formatMixin from '@/mixins/formatMixin';

export default {
  name: 'TicketList',
  directives: {
    waves,
  },
  mixins: [formatMixin],
  data() {
    return {
      fields: [
        { key: 'no', label: 'No', sortable: true },
        { key: 'name', label: 'Nama Aplikasi', sortable: true },
        { key: 'status', label: 'Status', sortable: true },
        { key: 'created_at', sortable: true },
        { key: 'actions', label: 'Aksi', sortable: false },
      ],
      items: [],
      perPage: 10,
      currentPage: 1,
      search: '',
      sortBy: 'created_at',
      sortByDesc: false,
      loading: false,
      sortByDesc: false,
      item: {},
      downloading: false,
    };
  },

  computed: {
    rows() {
      return this.items.length;
    },
  },
  created() {
    this.loadProjectsData();
  },

  methods: {

    async loadProjectsData() {
      this.loading = true;
      await axios.get('/projects', {
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
      this.loadProjectsData();
    }, 500),

    handleDetail(val) {
      this.$router.push({
        name: 'show-project',
        params: {
          id: val.item.id,
        },
      });
    },

    handleEdit(val) {
      this.$router.push({
        name: 'edit-project',
        params: {
          id: val.item.id,
        },
      });
    },

    handleExportToExcel() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = [
          'id',
          'Nama Project',
          'Status Aplikasi',
          'Duration Pengerjaan',
          'Awal Waktu Pengerjaan',
          'Akhir Waktu Pengerjaan',
          'Created At',
          'Updated At',
        ];
        const filterVal = [
          'id',
          'name',
          'status',
          'duration',
          'started_at',
          'ended_at',
          'created_at',
          'updated_at',
        ];
        const data = this.formatJson(filterVal, this.items);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: `project-list-${this.formatDate()}`,
        });
        this.downloading = false;
      });
    },

    handleDestroy(val) {
      this.$confirm('Aplikasi ' + val.item.id + ' akan dihapus, apakah anda yakin?', 'Warning', {
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        type: 'warning',
      }).then(() => {
        this.loading = true;
        axios.delete(`projects/${val.item.id}`)
          .then((response) => {
            this.loading = false;
            this.$message({
              type: 'success',
              message: 'Data berhasil dihapus',
            });
            this.loadProjectsData();
          })
          .catch((error) => {
            this.loading = false;
            this.$message({
              message: error,
              type: 'error',
              duration: 5 * 1000,
            });
          });
      }).catch(() => {
        this.loading = false;
        this.$message({
          type: 'info',
          message: 'hapus data dibatalkan',
        });
      });
    },
  },
};
</script>

<style>

</style>
