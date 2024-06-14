<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">Rincian Tiket</h4>
        <b-button class="btn btn-secondary btn-fill" @click="$router.go(-1)">Kembali</b-button>
      </div>
      <div class="card-body">
        <table class="table table-striped mt-3">
          <tbody>
            <tr class="mr-5">
              <th>Tiket ID</th>
              <th>: {{ ticket.id }}</th>
            </tr>
            <tr class="ml-5">
              <th>Status Tiket</th>
              <th scope="row">
                <h5>
                  <span class="lead">: 
                    <span class="badge lead" :class="badge">{{
                      ticket.status
                    }}</span>
                  </span>
                </h5>
              </th>
            </tr>
            <tr class="ml-5">
              <th>Nama</th>
              <th scope="row">
                <font class="font-weight-light"><b class="font-weight-bold">:</b> {{
                  ticket.user ? ticket.user.fullname : ''
                }}</font>
              </th>
            </tr>
            <tr>
              <th scope="row">Email</th>
              <th scope="row">
                <font class="font-weight-light"><b class="font-weight-bold">:</b> {{
                  ticket.user ? ticket.user.email : ''
                }}</font>
              </th>
            </tr>
            <tr>
              <th scope="row">Perusahaan</th>
              <th scope="row">
                <font class="font-weight-light"><b class="font-weight-bold">:</b> {{
                  ticket.user ? ticket.user.company.name : ''
                }}</font>
              </th>
            </tr>
            <tr>
              <th scope="row">Aplikasi</th>
              <th scope="row">
                <font class="font-weight-light"><b class="font-weight-bold">:</b> {{
                  ticket.project ? ticket.project.name : ''
                }}</font>
              </th>
            </tr>
            <tr>
              <th scope="row">Tanggal Mulai</th>
              <th scope="row">
                <font class="font-weight-light"><b class="font-weight-bold">:</b> {{
                  ticket.started_at | moment
                }}</font>
              </th>
            </tr>
            <tr>
              <th scope="row">Tanggal Akhir</th>
              <th scope="row">
                <font class="font-weight-light"><b class="font-weight-bold">:</b> {{
                  ticket.ended_at | moment
                }}</font>
              </th>
            </tr>
            <tr>
              <th scope="row">Subjek</th>
              <th scope="row">
                <font class="font-weight-light"><b class="font-weight-bold">:</b> {{ ticket.subject }}</font>
              </th>
            </tr>
            <tr>
              <th scope="row">Deskripsi</th>
              <th scope="row">
                <font class="font-weight-light"><b class="font-weight-bold">:</b> {{ ticket.description }}</font>
              </th>
            </tr>
            <tr>
              <th scope="row">Berkas</th>
              <th scope="row">
                <b-button
                  v-b-toggle.collapse-2
                  :disabled="!files.length"
                  class="m-1 btn btn-fill btn-info"
                >{{ files.length ? 'Lihat Berkas' : '-' }}</b-button>
                <b-collapse id="collapse-2">
                  <b-button
                    v-for="(file, index) in files"
                    :key="index"
                    style="cursor: pointer"
                    @click="openFile(file)"
                  >
                    <p>{{ file.filename }}</p>
                  </b-button>
                </b-collapse>
              </th>
            </tr>
            <tr v-if="ticket.response">
              <th scope="row">Respons</th>
              <th scope="row">
                <font class="font-weight-light">: {{ ticket.response ? ticket.response : '' }}</font>
              </th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <b-modal id="modal-lg" v-model="fileModal" hide-footer>
      <template v-slot:modal-header="{ close }">
        <p>{{ selected.filename }}</p>
        <b-icon
          icon="x-circle"
          aria-hidden="true"
          font-scale="2"
          style="cursor: pointer"
          @click="close()"
        />
      </template>
      <div v-if="docExtensions.includes(selected.extension)">
        <a
          :href="selected.fileUrl"
          @click="downloadDoc"          
        >
          Download
        </a>
      </div>
      <div v-else-if="imageExtensions.includes(selected.extension)">
        <img class="img-fluid" :src="selected.fileUrl" alt="">
      </div>
    </b-modal>
  </div>
</template>

<script>
import axios from '@/axios';
import { mapGetters } from 'vuex';
import formatMixin from '@/mixins/formatMixin';
import moment from 'moment';

export default {
  filters: {
    moment: function(value) {
      return moment(String(value)).format('dddd, DD MMMM YYYY');
    },
  },
  data() {
    return {
      ticket: {},
      files: [],
      fileModal: false,
      selected: '',
      badge: 'badge-danger',
      imageExtensions: ['jpeg', 'jpg', 'png', 'gif'],
      docExtensions: ['pdf', 'docx', 'csv', 'xlx', 'xlsx'],
    };
  },

  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getTicket(id);
  },

  computed: {
    ...mapGetters({
      user: 'user/userDetails',
    }),
  },
  methods: {
    async getTicket(id) {
      axios
        .get(`/tickets/${id}`)
        .then((response) => {
          this.ticket = response.data.data;
          this.files = this.ticket.files ? this.ticket.files : [];
          switch (this.ticket.status) {
            case 'open':
              return (this.badge = 'badge-success');
            case 'onProgress':
              return (this.badge = 'badge-warning');
            case 'closed':
              return (this.badge = 'badge-danger');
            default:
              return (this.badge = 'badge-danger');
          }
        })
        .catch((error) => {
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },
    openFile(row) {
      this.fileModal = !this.fileModal;
      this.selected = row;
    },
    downloadDoc() {
      axios
        .get(this.selected.fileUrl, {
          responseType: 'blob',
        })
        .then((response) => {
          const blob = new Blob([response.data], {
            type: `application/${this.selected.extension}`,
          });
          const link = document.createElement('a');
          link.href = URL.createObjectURL(blob);
          link.download = this.selected.filename;
          URL.revokeObjectURL(link.href);
        })
        .catch((err) => {
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },
  },
};
</script>

<style>
.bg {
  background-color: #fff;
}
</style>
