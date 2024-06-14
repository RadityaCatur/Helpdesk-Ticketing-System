<template>
  <div class="p-3 px-4 mt-3">
    <div class="container shadow p-3 mb-5 bg-white rounded">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="panel">
              <div class="panel-heading">
                <h4>Filter</h4>
                <div class="row mt-3">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="">Tanggal</label>
                      <select v-model="day" class="form-control">
                        <option v-for="(d, i) in days" :key="i" :value="d">
                          {{ d }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="">Bulan</label>
                      <select v-model="month" class="form-control">
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="">Tahun</label>
                      <select v-model="year" class="form-control">
                        <option v-for="(y, i) in years" :key="i" :value="y">
                          {{ y }}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <div class="card border-0 shadow">
      <div class="cold-md-12">
        <div class="card-header">
          <h4 class="mb-0">Aktivitas</h4>
        </div>
        <div class="card-body">
          <b-overlay :show="loading">
            <ul
              v-for="(activity, index) in activities"
              :key="index"
              class="timeline"
            >
              <li>
                <a href="#" class="float-right">{{
                  activity.created_at | moment
                }}</a>
                <p class="mr-3">{{ activity.description }}</p>
              </li>
            </ul>
          </b-overlay>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/axios';
import formatMixin from '@/mixins/formatMixin';
import moment from 'moment';
import _ from 'lodash';
import { mapState, mapActions } from 'vuex';

export default {
  name: 'Activities',

  filters: {
    moment: function(date) {
      return moment(date).format('MMMM Do YYYY, h:mm:ss');
    },
  },

  mixins: [formatMixin],

  data() {
    return {
      month: moment().format('MM'),
      year: moment().format('Y'),
      day: moment().format('D'),
      loading: false,
    };
  },

  watch: {
    month() {
      this.loading = true;
      this.getActivitiesData({
        month: this.month,
        year: this.year,
        day: this.day > 9 ? this.day : '0' + this.day,
      }).then(() => {
        this.loading = false;
      });
    },
    year() {
      this.loading = true;
      this.getActivitiesData({
        month: this.month,
        year: this.year,
        day: this.day > 9 ? this.day : '0' + this.day,
      }).then(() => {
        this.loading = false;
      });
    },
    day() {
      this.loading = true;
      this.getActivitiesData({
        month: this.month,
        year: this.year,
        day: this.day > 9 ? this.day : '0' + this.day,
      }).then(() => {
        this.loading = false;
      });
    },
  },

  created() {
    try {
      this.loading = true;
      this.getActivitiesData({
        month: this.month,
        year: this.year,
        day: this.day > 9 ? this.day : '0' + this.day,
      }).then(() => {
        this.loading = false;
      });
    } catch (error) {
      this.loading = false;
      console.log(error);
    }
  },

  computed: {
    ...mapState('activity', {
      activities: (state) => state.activities,
    }),
    years() {
      return _.range(2020, moment().add(1, 'years').format('Y'));
    },
    days() {
      return moment(`${this.year}-${this.month}`, 'YYYY-MM').daysInMonth();
    },
  },

  methods: {
    ...mapActions('activity', ['getActivitiesData']),
  },
};
</script>

<style>
.bg {
  background-color: #fff;
  border-radius: 10px;
  margin-top: 10px;
}

ul.timeline {
  list-style-type: none;
  position: relative;
}
ul.timeline:before {
  content: ' ';
  background: #d4d9df;
  display: inline-block;
  position: absolute;
  left: 29px;
  width: 2px;
  height: 100%;
  z-index: 400;
}
ul.timeline > li {
  margin: 20px 0;
  padding-left: 50px;
}
ul.timeline > li:before {
  content: ' ';
  background: white;
  display: inline-block;
  position: absolute;
  border-radius: 50%;
  border: 3px solid #22c0e8;
  left: 20px;
  width: 20px;
  height: 20px;
  z-index: 400;
}
h1,
.h1,
h2,
.h2,
h3,
.h3,
h4,
.h4 {
  margin: 0 !important;
}
</style>
