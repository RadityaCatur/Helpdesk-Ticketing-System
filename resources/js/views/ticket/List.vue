<template>
  <div class="p-3 px-4 mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="card border-0 shadow">
          <div class="card-header d-flex align-items-center justify-content-between mt-2">
            <h4 class="card-title">Tiket</h4>
            <b-button v-if="(isRole('admin'))" v-waves variant="info" :loading="downloading" class="btn btn-fill ml-auto mr-3 px-4" icon="download" @click="handleExportToExcel">
              Ekspor
            </b-button>
            <router-link v-if="(isRole('pic'))" to="/dashboard/add-ticket" tag="b-button" class="btn btn-fill btn-success text-light">
              Tambah Tiket
            </router-link>
          </div>
          <b-tabs>
            <b-tab title="Open" variant="success" active>
              <open-ticket />
            </b-tab>
            <b-tab title="On Progress" variant="warning">
              <on-progress-ticket />
            </b-tab>
            <b-tab title="Closed" variant="danger">
              <closed-ticket />
            </b-tab>
          </b-tabs>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/axios';
import { mapGetters, mapActions } from 'vuex';
import waves from '@/directive/waves'; // Waves directive
import formatMixin from '@/mixins/formatMixin';
import OpenTicket from './OpenTicket';
import ClosedTicket from './ClosedTicket';
import OnProgressTicket from './OnProgressTicket';

export default {
  name: 'TicketList',
  components: {
    OpenTicket,
    ClosedTicket,
    OnProgressTicket,
  },
  directives: {
    waves,
  },
  mixins: [formatMixin],
  computed: {
    ...mapGetters({
      userDetails: 'user/userDetails',
      tickets: 'ticket/tickets',
    }),
  },
  data() {
    return {
      downloading: false,
    };
  },
  methods: {
    handleExportToExcel() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = [
          'id',
          'subject',
          // 'response',
          'status',
          'duration pengerjaan',
          'sisa waktu pengerjaan',
          'awal waktu pengerjaan',
          'akhir waktu pengerjaan',
          'created at',
          'updated at',
        ];
        const filterVal = [
          'id',
          'subject',
          // 'response',
          'status',
          'duration',
          'remainingTime',
          'started_at',
          'ended_at',
          'created_at',
          'updated_at',
        ];

        axios.get('/tickets/export')
          .then((response) => {
            const respond = response.data.data;
            const data = this.formatJson(filterVal, respond);
            excel.export_json_to_excel({
              header: tHeader,
              data,
              filename: `ticket-list-${this.formatDate()}`,
            });
            this.downloading = false;
          })
          .catch((error) => {
            console.log(error);
          });
      });
    },
  },
};
</script>
