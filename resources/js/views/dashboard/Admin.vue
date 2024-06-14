<template>
  <div class="container px-4 mt-4">
    <counter />
    <div>
      <div class="card shadow border-0 mt-4">
        <div
          class="card-header d-flex align-items-center justify-content-between"
        >
          <p class="mb-0">Grafik Aduan Berdasarkan Kategori</p>
          <div class="d-flex align-items-center">
            <label for="" class="mb-0">Tahun</label>
            <select v-model="year" class="form-control ml-4">
              <option v-for="(y, i) in years" :key="i" :value="y">
                {{ y }}
              </option>
            </select>
          </div>
        </div>
        <div class="card-body">
          <b-overlay :show="loading">
            <section class="content mb-3">
              <div class="row mt-3">
                <div class="col-md-12">
                  <div class="panel">
                    <div class="panel-body bg p-3 mb-4">
                      <chart
                        v-if="tickets.length > 0"
                        :open="openTicket"
                        :on-progress="onProgressTicket"
                        :closed="closedTicket"
                        :options="chartOptions"
                        :labels="labels"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </b-overlay>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Counter from './components/Counter.vue';
import Chart from './components/Chart.vue';
import moment from 'moment';
import _ from 'lodash';
import { mapState, mapActions } from 'vuex';

export default {
  name: 'AdminDashboard',
  components: {
    Counter, Chart,
  },

  data() {
    return {
      loading: false,
      chartOptions: {
        scales: {
          yAxes: [
            {
              ticks: {
                beginAtZero: true,
              },
              gridLines: {
                display: true,
              },
            },
          ],
          xAxes: [
            {
              gridLines: {
                display: false,
              },
            },
          ],
        },
        legend: {
          display: true,
        },
        responsive: true,
        maintainAspectRatio: false,
      },
      month: moment().format('MM'),
      year: moment().format('Y'),
    };
  },

  watch: {
    month() {
      this.loading = true;
      this.getChartData({
        month: this.month,
        year: this.year,
      }).then(() => {
        this.loading = false;
      });
    },
    year() {
      this.loading = true;
      this.getChartData({
        month: this.month,
        year: this.year,
      }).then(() => {
        this.loading = false;
      });
    },
  },

  created() {
    this.loading = true;
    this.getChartData({
      month: this.month,
      year: this.year,
    }).then(() => {
      this.loading = false;
    });
  },

  computed: {
    ...mapState('dashboard', {
      tickets: (state) => state.tickets,
    }),
    years() {
      return _.range(2020, moment().add(1, 'years').format('Y'));
    },
    labels() {
      return _.map(this.tickets, function(o) {
        return moment(o.date).format('MMMM');
      });
    },
    openTicket() {
      return _.map(this.tickets, function(o) {
        return o.open;
      });
    },
    onProgressTicket() {
      return _.map(this.tickets, function(o) {
        return o.onProgress;
      });
    },
    closedTicket() {
      return _.map(this.tickets, function(o) {
        return o.closed;
      });
    },
  },

  methods: {
    ...mapActions('dashboard', ['getChartData']),
  },
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.dashboard-editor-container {
  padding: 32px;
  background-color: rgb(240, 242, 245);
  .chart-wrapper {
    background: #fff;
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}

.bg {
  background-color: #fff;
  border-radius: 8px;
}
</style>
