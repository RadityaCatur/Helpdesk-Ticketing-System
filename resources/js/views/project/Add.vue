<template>
  <div class="p-3 px-4 mt-3">
    <b-overlay :show="loading">
      <form method="POST" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-12">
            <div class="card border-0 shadow">

              <div class="card-header d-flex align-items-center justify-content-between">
                <h4 slot="header" class="card-title">Tambah Aplikasi</h4>
                <b-button class="btn btn-secondary btn-fill" @click="$router.go(-1)">Kembali</b-button>
              </div>

              <div class="card-body">
                <div class="form-group mt-3">
                  <label for="company">Nama Aplikasi</label>
                  <input
                    v-model="project.name"
                    type="text"
                    class="form-control"
                    placeholder="Masukkan Nama Aplikasi"
                    name="project_name"
                    :class="{
                      'is-invalid': $v.project.name.$error,
                    }"
                    @input="setNamaAplikasi($event.target.value)"
                  >
                </div>

                <div class="form-group mt-3">
                  <label for="">Client</label>
                  <select
                    v-model="project.company_id"
                    class="form-control"
                    name="user"
                    :class="{
                      'border-danger': $v.project.company_id.$error,
                    }"
                  >
                    <option :value="null" disabled selected>Pilih Client</option>
                    <option v-for="(value, index) in client" :key="index" :value="value.id">
                      {{ value.name }}
                    </option>
                  </select>
                </div>

                <div class="form-group mt-3">
                  <label for="">Penanggung Jawab Aplikasi (opsional)</label>
                  <select
                    v-model="project.leader_id"
                    class="form-control"
                    name="user"
                    :class="{
                      'border-danger': $v.project.leader_id.$error,
                    }"
                  >
                    <option :value="null" disabled selected>Pilih Penanggung Jawab</option>
                    <option v-for="(user, index) in employees" :key="index" :value="user.id">
                      {{ user.fullname }}
                    </option>
                  </select>
                </div>

                <div class="form-group mt-3">
                  <label for="">Nomor Kontrak</label>
                  <input
                    v-model="project.contract"
                    type="text"
                    class="form-control"
                    placeholder="Masukkan Nomor Kontrak"
                    name="contract"
                  >
                </div>
                
                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="">Tanggal Mulai Kontrak</label>
                    <div class="col-sm-12">
                      <b-form-datepicker
                        v-model="project.contract_started_at"
                        class="form-control"
                        name="contract_started_at"
                      />
                    </div>
                  </div>

                  <div class="form-group col-md-6">
                    <label for="">Tanggal Berakhir Kontrak</label>
                    <div class="col-sm-12">
                      <b-form-datepicker
                        v-model="project.contract_ended_at"
                        class="form-control"
                        name="contract_ended_at"
                      />
                    </div>
                  </div>
                </div>

                <div class="form-group mt-3">
                  <label for="">Kategori Aplikasi</label>
                  <select
                    v-model="project.category_id"
                    class="form-control"
                    name="project"
                    :class="{
                      'border-danger': $v.project.category_id.$error,
                    }"
                  >
                    <option :value="null" disabled selected>Pilih Kategori Aplikasi</option>
                    <option v-for="(category, index) in categories" :key="index" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                </div>

                <div class="form-group mt-3">
                  <label for="">Prioritas Aplikasi</label>
                  <select
                    v-model="project.priority_id"
                    class="form-control"
                    name="project"
                    :class="{
                      'border-danger': $v.project.priority_id.$error,
                    }"
                  >
                    <option :value="null" disabled selected>Pilih Prioritas</option>
                    <option v-for="(item, index) in priorities" :key="index" :value="item.id">
                      {{ item.name }}
                    </option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="" class="">Service Level Agreement</label>
                  <select
                    v-model="project.service_level_agreement"
                    class="form-control"
                    name="service_level_agreements"
                  >
                    <option value="service_request">Service Request</option>
                    <option value="incident">Incident</option>
                    <option value="change_request">Change Request</option>
                    <option value="bug">Bug</option>
                  </select>
                </div>
                
                <div class="form-group mt-3">
                  <label for="">Jumlah Hari: </label>
                  <input
                    v-model="project.jumlah_hari"
                    type="number"
                    class="form-control"
                    placeholder="Masukkan Jumlah Hari"
                    :disabled="!project.service_level_agreement"
                  >
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
                    <label class="custom-file-label" for="customFile" data-browse="Pilih File">
                      {{ project.logo ? project.logo.name : 'Logo' }}
                    </label>
                  </div>
                </div>
              </div>

              <div class="card-footer text-right my-3">
                <button
                  type="button"
                  class="btn btn-success btn-fill px-4"
                  @click="saveProject"
                >
                  <b-spinner v-if="loading" small />
                  <span class="sr-only">Loading...</span>
                  Tambah
                </button>
              </div>

            </div>
          </div>
        </div>
      </form>
    </b-overlay>

    <b-modal v-model="dialogVisible" title="Logo" hide-footer>
      <img
        class="img-fluid"
        :src="dialogImageUrl"
      >
    </b-modal>

    <div class="clearfix" />
  </div>
</template>

<script>
import _ from 'lodash';
import axios from '@/axios';
import { mapGetters, mapActions, mapState } from 'vuex';
import { AlertUtils } from '@/mixins/alertUtils';
import Card from '@/components/UIComponents/Cards/Card.vue';
import Multiselect from 'vue-multiselect';
import { required } from 'vuelidate/lib/validators';

export default {
  name: 'AddProject',

  components: {
    Card, Multiselect,
  },

  mixins: [
    AlertUtils,
  ],

  data() {
    return {
      project: {
        company_id: null,
        category_id: null,
        priority_id: null,
        leader_id: null,
        name: '',
        logo: '',
      },
      categories: [],
      client: [],
      priorities: [],
      employees: [],
      dialogImageUrl: '',
      dialogVisible: false,
      loading: false,
    };
  },

  validations: {
    project: {
      name: {
        required,
      },
      company_id: {
        required,
      },
      category_id: {
        required,
      },
      priority_id: {
        required,
      },
      leader_id: {
        required,
      },
    },
  },

  created() {
    this.getClient();
    this.getCategories();
    this.getPriorities();
    this.getEmployees();
  },

  methods: {

    setNamaAplikasi(value) {
      this.project.name = value;
      this.$v.project.name.$touch();
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

    handlePictureRemove() {
      this.dialogImageUrl = '';
    },

    prepareFormData() {
      const formData = new FormData();
      Object.keys(this.project).forEach((key) => {
        formData.append(key, this.project[key]);
      });
      console.log("😊😊 data", formData);
      return formData;
    },

    handleFileUpload(e) {
      this.project.logo = e.target.files[0];
    },

    resetForm() {
      this.project = {
        company_id: '',
        category_id: '',
        leader_id: '',
        name: '',
        estimated_budget: 0,
        description: '',
        logo: '',
      };
      this.dialogImageUrl = '';
    },

    saveProject(e) {
      e.preventDefault();
      const formData = this.prepareFormData();
      console.log("😁😁 formData", formData);
      this.loading = true;
      this.$v.$touch();
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: `Apakah anda yakin ingin menambahkan projek ?`,
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
            if (this.$v.$invalid) {
              this.loading = false;
              this.alertStoreFailed();
              return;
            } else {
              axios.post('/projects', formData)
                .then((response) => {
                  this.resetForm();
                  this.loading = false;
                  this.alertStoreSuccess();
                  this.$v.$reset();
                })
                .catch((error) => {
                  this.loading = false;
                  if (error.response.data.status == false) {
                    this.alertStoreMessage(error.response.data.message);
                  }else{
                    this.alertStoreFailed();
                  }
                  this.$v.$reset();
                });
            }
          } else if (result.dismiss) {
            this.loading = false;
            this.alertCancel('Tambah Aplikasi');
            this.$v.$reset();
          }
        });
    },

    showFullname({ fullname }) {
      return `${fullname}`;
    },
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
