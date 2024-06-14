<template>
  <div class="p-3 px-4 mt-3">
    <div class="card shadow border-0">
      <form>
        <div class="card-header d-flex align-items-center justify-content-between">
          <h4 class="card-title">Ubah tiket</h4>
          <b-button class="btn btn-secondary btn-fill mr-2" @click="$router.go(-1)">Kembali</b-button>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <label for="" class="col-form-label">Tanggal Mulai</label>
              <div class="col-sm-12">
                <b-form-datepicker
                  v-model="ticket.started_at"
                  class="mb-2 text-lowercase"
                  locale="id"
                />
              </div>
            </div>
            <div class="col-md-6">
              <label for="" class="col-form-label">Tanggal Akhir</label>
              <div class="col-sm-12">
                <b-form-datepicker
                  v-model="ticket.ended_at"
                  class="mb-2 text-lowercase"
                  locale="id"
                />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="" class="col-sm-3 col-form-label">Status</label>
              <div class="col-sm-12">
                <b-form-select v-model="ticket.status" :options="options" />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="" class="col-sm-3 col-form-label">Respon</label>
              <div class="col-sm-12">
                <b-form-textarea
                  id="textarea"
                  v-model="ticket.response"
                  placeholder="Enter something..."
                  rows="5"
                  max-rows="6"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer text-right my-3">
          <button type="button" class="btn btn-success btn-fill" @click="updateTicket">
            <b-spinner v-if="loading" small />
            <span class="sr-only">Loading...</span>
            Perbarui Tiket
          </button>
        </div>
      </form>
    </div>
    <div class="clearfix" />
  </div>
</template>

<script>

import axios from '@/axios';
import { AlertUtils } from '@/mixins/alertUtils';
import Card from '@/components/UIComponents/Cards/Card.vue';

export default {
  name: 'EditTicket',

  components: {
    Card,
  },

  mixins: [
    AlertUtils,
  ],

  data() {
    return {
      ticket: {},
      loading: false,
      selected: null,
      options: [
        { value: null, text: 'Please select an option' },
        { value: 'open', text: 'Open' },
        { value: 'onProgress', text: 'On Progress' },
        { value: 'closed', text: 'Closed' },
      ],
    };
  },

  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getTicket(id);
  },

  methods: {
    async getTicket(id) {
      axios.get(`/tickets/${id}`)
        .then((response) => {
          this.ticket = response.data.data;
        })
        .catch((error) => {
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },

    updateTicket() {
      this.loading = true;
      const id = this.$route.params && this.$route.params.id;
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: `Apakah anda yakin ingin mengedit tiket ?`,
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
              url: `/tickets/${id}`,
              data: {
                status: this.ticket.status,
                response: this.ticket.response,
                started_at: this.ticket.started_at,
                ended_at: this.ticket.ended_at,
              },
              method: 'PUT',
            }).then((response) => {
              this.loading = false;
              this.alertUpdateSuccess();
            }).catch((err) => {
              this.$message({
                message: err.response.data.message,
                type: 'error',
                duration: 3 * 1000,
              });
              this.loading = false;
            });
          } else if (result.dismiss) {
            this.loading = false;
            this.alertCancel('Edit Ticket');
          }
        });
    },
  },
};
</script>

<style>

</style>
