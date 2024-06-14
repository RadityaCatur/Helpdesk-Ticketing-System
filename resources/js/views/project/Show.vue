<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h4 class="card-title">Rincian Aplikasi</h4>
        <b-button class="btn btn-secondary btn-fill" @click="$router.go(-1)">Kembali</b-button>
      </div>

      <b-overlay :show="loading">
        <div class="card-body" aria-hidden="true">
          <center class="d-none">
            <h3>{{ this.project.name }}</h3>
            <img :src="project.fileUrl" class="img-fluid" height="400" fluid alt="Logo">
          </center>

          <table class="table table-striped mt-3">
            <tbody>
              <tr>
                <th scope="row" class="w-25">Nama Pengguna</th>
                <th scope="row">
                  <font class="font-weight-light">{{ this.project.user ? this.project.user.fullname : '' }}</font>
                </th>
              </tr>
              <tr class="ml-5">
                <th>Nama Penanggung Jawab Aplikasi</th>
                <th scope="row">
                  <font class="font-weight-light">{{ this.project.leader ? this.project.leader.fullname : '' }}</font>
                </th>
              </tr>
              <tr>
                <th scope="row">Client</th>
                <th scope="row">
                  <font class="font-weight-light">{{ this.project.user ? this.project.user.company.name : '-' }}</font>
                </th>
              </tr>
              <tr>
                <th scope="row">Tanggal</th>
                <th scope="row">
                  <font class="font-weight-light">{{ this.project.createdAt | moment('dddd, MMMM YYYY') }}</font>
                </th>
              </tr>
              <tr>
                <th scope="row">Kategori</th>
                <th scope="row">
                  <font class="font-weight-light"> <b-badge variant="primary">{{ this.project.category ? this.project.category.name : '-' }}</b-badge></font>
                </th>
              </tr>
              <tr>
                <th scope="row">Status</th>
                <th scope="row">
                  <font class="font-weight-light"><b-badge variant="success">{{ this.project.status }}</b-badge></font>
                </th>
              </tr>
            </tbody>
          </table>
        </div>
      </b-overlay>
    </div>
  </div>
</template>

<script>
import axios from '@/axios';

export default {
  data() {
    return {
      project: {},
      loading: false,
    };
  },
  watch: {
    '$route': 'getProject',
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getProject(id);
  },
  methods: {
    async getProject(id) {
      this.loading = true;
      await axios.get(`/projects/${id}`)
        .then((response) => {
          this.project = response.data.data;
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
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
.center{
  margin-left: 230px;
  justify-content: center;
}

.bg{
  background-color: #fff;
  border-radius: 10px;
}

h3{
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  font-size: 38px;
  font-weight: 400;
}

b{
  font-weight: 600;
}
.image{
  padding: 10px;
}
</style>
