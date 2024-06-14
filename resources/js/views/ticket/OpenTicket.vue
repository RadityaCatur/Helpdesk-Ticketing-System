<template>
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
          :items="tickets"
          :fields="fields"
          :per-page="perPage"
          :current-page="currentPage"
          striped          
          show-empty
          :busy="loading"
          responsive
          empty-text="Tidak ada tiket dengan status open"
        >
          <template v-slot:cell(no)="data">
            {{ data.index + 1 }}
          </template>
          <template slot="checkbox" slot-scope="row">
            {{ row.item.subject.substring(0, 10) + '...' }}
          </template>
          <template v-slot:cell(status)="data">
            <div v-if="data.item.status == 'open'">On Check</div>
          </template>
          <template v-slot:cell(actions)="row">
            <button class="btn-fill btn-info btn-sm" @click="handleDetail(row)">
              Rincian
            </button>
            <!-- <button class="btn-fill btn-warning btn-sm" @click="openChatRoom(row)">
              {{ row.detailsShowing ? 'Hide' : 'Show' }} Chat
            </button> -->
            <button v-if="isRole('admin', 'employee')" class="btn-fill btn-warning btn-sm" @click="handleEdit(row)">
              Ubah
            </button>
            <button class="btn-fill btn-danger btn-sm" @click="handleDestroy(row)">
              Hapus
            </button>
          </template>
          <!-- Chat View -->
          <template #row-details="row">                      
            <div class="messages" style="width: 100%">
              <div class="messages-history">
                <ChatRoom
                  :room="row.item.room"
                  class="card mb-1 mb-sm-2 every-chatrooms-card"
                />
              </div>              
            </div>            
          </template>
          <!-- Chat View -->
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
    <b-modal v-model="confirmModal" title="Apakah anda yakin?">
      <p>Hapus tiket</p>
      <template v-slot:modal-footer>
        <div class="float-right">
          <b-button
            variant="danger"
            class="btn-fill"
            size="sm"
            @click="confirmModal=false"
          >
            Batal
          </b-button>
        </div>
        <div class="float-left">
          <b-button
            variant="success"
            class="btn-fill"
            size="sm"
            data-save
            @click="handleDestroy"
          >
            Ya
          </b-button>
        </div>
      </template>
    </b-modal>
  </div>
</template>

<script>
import _ from 'lodash';
import axios from '@/axios';
import { mapGetters, mapActions, mapMutations } from 'vuex';
import waves from '@/directive/waves'; // Waves directive
import chatMixins from '@/mixins/chatMixins';

export default {
  name: 'ListOpenTicket',

  mixins: [
    chatMixins,
  ],

  data() {
    return {
      tickets: [],
      fields: [
        { key: 'no', label: 'No', sortable: true },
        { key: 'project.name', label: 'Nama Aplikasi', sortable: true },
        { key: 'subject', label: 'Issue', sortable: true },
        { key: 'created_at', label: 'Tanggal Pelaporan', sortable: true },
        { key: 'status', label: 'Status', sortable: true },
        { key: 'actions', label: 'Aksi', sortable: false, _style: 'text-align: center;' },
      ],  
      perPage: 10,
      currentPage: 1,
      search: '',
      sortBy: 'created_at',
      sortByDesc: false,
      loading: false,
      confirmModal: false,
    };
  },

  computed: {
    rows() {
      return this.tickets.length;
    },    
  },

  created() {
    this.getTickets();
    this.safetyCheck();
  },  

  methods: {    
    async getTickets() {
      this.loading = true;
      await axios.get('/tickets', {
        params: {
          q: this.search,
          status: 'open',
        },
      })
        .then((response) => {
          this.tickets = response.data.data;
          this.loading = false;
        });
    },

    handleSearch: _.debounce(function(e) {
      this.search = e.target.value;
      this.getTickets();
    }, 500),

    handleDetail(val) {
      this.$router.push({
        name: 'show-ticket',
        params: {
          id: val.item.id,
        },
      });
    },

    handleEdit(val) {
      this.$router.push({
        name: 'edit-ticket',
        params: {
          id: val.item.id,
        },
      });
    },

    showDeleteConfirm(val) {
      this.confirmModal = true;
    },

    handleDestroy(val) {
      this.$confirm('Data ' + val.item.id + '. akan dihapus, apakah anda yakin?', 'Warning', {
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        type: 'warning',
      }).then(() => {
        this.loading = true;
        axios.delete(`tickets/${val.item.id}`)
          .then((response) => {
            this.loading = false;
            this.$message({
              type: 'success',
              message: 'Data berhasil dihapus',
            });
            this.getTickets();
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
.messages {
  float: left;
  padding: 30px 15px 0 25px;
  width: 100%;
}

.messages-history {
  height: 100%;
  overflow-y: auto;
}
</style>
