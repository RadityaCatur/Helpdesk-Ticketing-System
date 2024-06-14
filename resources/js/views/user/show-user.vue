<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between pt-0">
        <h4 class="card-title">Detail Pengguna</h4>
        <b-button class="btn btn-secondary btn-fill mt-2" @click="$router.go(-1)">Kembali</b-button>
      </div>

      <b-row class="card-body justify-content-md-center">
        <b-col cols="6">
          <b-row>
            <b-col sm="4">
              <label for="" class="text-dark"><b>Nama Lengkap</b></label>
            </b-col>
            <b-col sm="8">
              : {{ user.fullname }}
            </b-col>
          </b-row>
          <b-row class="mt-3">
            <b-col sm="4">
              <label for="" class="text-dark"><b>Nama Pengguna</b></label>
            </b-col>
            <b-col sm="8">
              : {{ user.username }}
            </b-col>
          </b-row>
          <b-row class="mt-3">
            <b-col sm="4">
              <label for="" class="text-dark"><b>Email</b></label>
            </b-col>
            <b-col sm="8">
              : {{ user.email }}
            </b-col>
          </b-row>
          <b-row v-if="user.position" class="mt-3">
            <b-col cols="4">
              <label for="" class="text-dark"><b>Posisi</b></label>
            </b-col>
            <b-col cols="8">
              <p class="text-break">
                : {{ user.position ? user.position.name : '-' }}
              </p>
            </b-col>
          </b-row>
        </b-col>

        <b-col cols="6">
          <b-row>
            <b-col sm="4">
              <label for="" class="text-dark"><b>Telepon</b></label>
            </b-col>
            <b-col sm="8">
              : <b-badge>{{ user.handphone }}</b-badge>
            </b-col>
          </b-row>
          <b-row class="mt-3">
            <b-col sm="4">
              <label for="" class="text-dark"><b>Perusahaan</b></label>
            </b-col>
            <b-col sm="8">
              <p class="text-break">: {{ user.company ? user.company.name : '-' }}</p>
            </b-col>
          </b-row>
          <b-row class="mt-2">
            <b-col sm="4">
              <label for="" class="text-dark"><b>Role</b></label>
            </b-col>
            <b-col cols="8">
              <p class="text-break">: {{ user.roles[0] }}</p>
            </b-col>
          </b-row>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  data() {
    return {
      user: {},
    };
  },
  watch: {
    $route: 'getUser',
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getUser(id);
  },
  methods: {
    async getUser(id) {
      axios
        .get(`/users/${id}`)
        .then((response) => {
          this.user = response.data.data;
          console.log(this.user);
        })
        .catch((error) => {
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
.center {
  margin-top: 20px;
  margin-left: 230px;
  justify-content: center;
}

.bg {
  background-color: #fff;
  margin-top: 10px;
  border-radius: 10px;
}

h3 {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
    Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  font-size: 38px;
  font-weight: 400;
}

b {
  font-weight: 600;
}
</style>
