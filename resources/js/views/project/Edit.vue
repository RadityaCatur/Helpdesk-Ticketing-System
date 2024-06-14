<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header d-flex align-items-center justify-content-between px-4">
        <h4 slot="header" class="card-title">Ubah Aplikasi</h4>
        <b-button class="btn btn-secondary btn-fill" @click="$router.go(-1)">Kembali</b-button>
      </div>
      <b-overlay :show="loading">
        <form method="POST" enctype="multipart/form-data">
          <div class="card-body">

            <div class="col-md-12">

              <div class="form-group mt-3">
                <label for="company">Nama Aplikasi</label>
                <input
                  v-model="project.name"
                  type="text"
                  class="form-control"
                  placeholder="Enter your Project Name"
                  name="project_name"
                >
              </div>

              <div class="form-group mt-3">
                <label for="">Pengguna</label>
                <select v-model="project.company_id" class="form-control" name="user">
                  <option v-for="(value, index) in client" :key="index" :value="value.id">
                    {{ value.name }}
                  </option>
                </select>
              </div>

              <div class="form-group mt-3">
                <label for="">Penanggung Jawab Aplikasi</label>
                <select v-model="project.leader_id" class="form-control" name="user">
                  <option v-for="(user, index) in employees" :key="index" :value="user.id">
                    {{ user.fullname }}
                  </option>
                </select>
              </div>

              <div class="form-group mt-3">
                <label for="">Kategori Aplikasi</label>
                <select v-model="project.category_id" class="form-control" name="project">
                  <option v-for="(category, index) in categories" :key="index" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <div class="form-group mt-3">
                <label for="" class="ml-2">Prioritas Aplikasi</label>
                <select v-model="project.priority_id" class="form-control" name="project">
                  <option v-for="(item, index) in priorities" :key="index" :value="item.id">
                    {{ item.name }}
                  </option>
                </select>
              </div>

              <div class="form-group mt-3">
                <label for="">Status Aplikasi</label>
                <select v-model="project.status" class="form-control" name="project">
                  <option value="onProgress">On Progress</option>
                  <option value="maintaince">Maintaince</option>
                  <option value="warranty">Warranty</option>
                </select>
              </div>

              <div class="form-group mt-3">
                <label for="">Logo (Optional)</label>
                <div class="custom-file">
                  <input
                    id="customFile"
                    type="file"
                    class="custom-file-input"
                    accept="image/*"
                    @change="handleFileUpload"
                  >
                  <label class="custom-file-label" for="customFile" data-browse="Pilih File" v-if="project.logo">
                    {{ typeof project.logo === "string" ? project.logo : project.logo.name }}
                  </label>
                  <label class="custom-file-label" for="customFile" data-browse="Pilih File" v-else>
                  </label>
                </div>
              </div>

              <div class="col-xs-1" align="center">
                <img
                  alt="..."
                  class="img-fluid"
                  :src="project.fileUrl"
                  @error="$event.target.src='/images/images_not_available.png'"
                  style="max-width: 500px; max-height: 500px;"
                >
              </div>
            </div>
          </div>
          <div class="card-footer text-right my-3">
            <button
              type="button"
              class="btn btn-success btn-fill px-4 mt-4"
              @click="openConfirmModal"
            >
              Ubah
            </button>
          </div>
        </form>
      </b-overlay>
    </div>

    <b-modal v-model="confirmModal" title="Apakah anda yakin?">
      <p>Edit Aplikasi</p>
      <template v-slot:modal-footer>
        <div class="float-right">
          <b-button
            variant="danger"
            size="sm"
            class="btn-fill"
            @click="confirmModal=false"
          >
            Tutup
          </b-button>
        </div>
        <div class="float-left">
          <b-button
            variant="success"
            size="sm"
            class="btn-fill"
            data-save
            @click="editProject"
          >
            Ya
          </b-button>
        </div>
      </template>
    </b-modal>
    <div class="clearfix" />
  </div>
</template>

<script>
import _ from 'lodash';
import axios from '@/axios';
import { mapGetters, mapActions, mapState } from 'vuex';
import Card from '@/components/UIComponents/Cards/Card.vue';
import Multiselect from 'vue-multiselect';

export default {
  name: 'EditProject',
  components: {
    Card, Multiselect,
  },
  data() {
    return {
      project: {},
      categories: [],
      client: [],
      priorities: [],
      employees: [],
      programmers: [],
      dialogImageUrl: '',
      dialogVisible: false,
      confirmModal: false,
      previewLogo: '',
      loading: false,
    };
  },

  computed: {
    options() {
      return _.map(this.employees, function(item) {
        return {
          id: item.id,
          fullname: item.fullname,
        };
      });
    },
  },

  created() {
    this.getProject();
    this.getClient();
    this.getCategories();
    this.getPriorities();
    this.getEmployees();
  },

  methods: {
    async getProject() {
      this.loading = true;
      const id = this.$route.params && this.$route.params.id;
      axios.get(`/projects/${id}`)
        .then((response) => {
          this.project = response.data.data;
          this.project.company_id = this.project.user.company.id;
          this.dialogImageUrl = this.project.fileUrl;
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

    async getCategories() {
      this.loading = true;
      await axios.get('/categories')
        .then((response) => {
          this.categories = response.data.data.data;
          this.loading = false;
        });
    },

    async getClient() {
      this.loading = true;
      await axios.get('/projects/client')
        .then((response) => {
          this.client = response.data.data;
          this.loading = false;
        });
    },

    async getEmployees() {
      this.loading = true;
      await axios.get('/projects/employees')
        .then((response) => {
          this.employees = response.data.data;
          this.loading = false;
        });
    },

    async getPriorities() {
      this.loading = true;
      await axios.get('/priorities')
        .then((response) => {
          this.priorities = response.data.data;
          this.loading = false;
        });
    },

    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },

    prepareFormData() {
      const formData = new FormData();
      Object.keys(this.project).forEach((key) => {
        formData.append(key, this.project[key]);
      });
      formData.append('_method', 'PUT');
      return formData;
    },

    updateFile(file) {
      this.project.logo = file.raw;
      this.previewLogo = file.url;
    },

    handleFileUpload(e) {
      this.project.logo = e.target.files[0];
    },

    editProject(e) {
      e.preventDefault();
      const formData = this.prepareFormData();
      this.loading = true;
      axios.post(`/projects/${this.project.id}`, formData)
        .then((response) => {
          this.confirmModal = false;
          this.project = response.data.data;
          this.$message({
            message: 'Edit projek berhasil',
            type: 'success',
            duration: 5 * 1000,
          });
          this.loading = false;
        })
        .catch((error) => {
          if (error.response.data.status == false) {
              this.$message({
              message: error.response.data.message,
              type: 'error',
              duration: 5 * 1000,
            });
          }else{
            this.$message({
              message: 'Edit projek gagal',
              type: 'error',
              duration: 5 * 1000,
            });
          }
          this.confirmModal = false;
          this.loading = false;
        });
    },

    openConfirmModal() {
      this.confirmModal = true;
    },

    showFullname({ fullname }) {
      return `${fullname}`;
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
