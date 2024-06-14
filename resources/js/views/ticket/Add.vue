<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title">
              Silahkan isi form dibawah ini untuk menambahkan tiket
            </h4>
            <b-button class="btn btn-secondary btn-fill" @click="$router.go(-1)">Kembali</b-button>
          </div>
          <form method="POST" enctype="multipart/form-data">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="fullname">Nama Lengkap</label>
                    <input
                      v-model="user.fullname"
                      type="text"
                      class="form-control"
                      placeholder="Masukkan Nama Lengkap Anda "
                      name="fullname"
                      disabled
                    >
                  </div>

                  <div class="form-group">
                    <label for="email" class="mt-2">Email</label>
                    <input
                      v-model="user.email"
                      type="text"
                      class="form-control"
                      placeholder="Masukkan Email Anda"
                      name="email"
                      disabled
                    >
                  </div>

                  <div class="form-group">
                    <label for="company" class="mt-2">Perusahaan</label>
                    <input
                      v-model="user.company.name"
                      type="text"
                      class="form-control"
                      placeholder="Masukkan Perusahaan Anda"
                      name="company"
                      disabled
                    >
                  </div>

                  <div class="form-group">
                    <label for="" class="">Aplikasi</label>
                    <select
                      v-model="ticket.project_id"
                      class="form-control"
                      name="project"
                      :class="{
                        'border-danger': $v.ticket.project_id.$error,
                      }"
                    >
                      <option :value="null" disabled selected>Pilih Aplikasi</option>
                      <option
                        v-for="(project, index) in projects"
                        :key="index"
                        :value="project.id"
                      >
                        {{ project.name }}
                      </option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="" class="">Tipe Tiket</label>
                    <select
                      v-model="ticket.type"
                      class="form-control"
                      name="type"
                    >
                      <option value="service_request">Service Request</option>
                      <option value="incident">Incident</option>
                      <option value="change_request">Change Request</option>
                      <option value="bug">Bug</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="" class="">Subjek</label>
                    <input
                      v-model="ticket.subject"
                      type="text"
                      class="form-control"
                      placeholder="Masukkan Subjek Anda"
                      name="subject"
                      :class="{
                        'is-invalid': $v.ticket.subject.$error,
                      }"
                      @input="setSubject($event.target.value)"
                    >
                  </div>

                  <div class="form-group">
                    <label for="" class="">Deskripsi</label>
                    <textarea
                      v-model="ticket.description"
                      class="form-control"
                      rows="10"
                      placeholder="Masukkan Deskripsi Anda"
                      name="description"
                      :class="{
                        'is-invalid': $v.ticket.description.$error,
                      }"
                      @input="setDescription($event.target.value)"
                    />
                  </div>

                  <div class="form-group">
                    <label for="uid-zwqayuopq3" class="font-weight-bold">File (Opsional)</label>
                    <br>
                    <ul class="list-group mb-2">
                      <li
                        v-for="(file, index) in fileList"
                        :key="index"
                        class="list-group-item py-1 d-flex justify-content-between align-items-center"
                      >
                        {{ file.name }}
                        <button class="btn btn-sm e-transparent" @click="handleRemoveFile(file, index)">
                          <b-icon icon="x" aria-hidden="true" />
                        </button>
                      </li>
                    </ul>
                    <div class="custom-file">
                      <input id="customFile" type="file" class="custom-file-input" multiple @change="handleFileUpload">
                      <label class="custom-file-label" for="customFile" data-browse="Pilih File">Attachments</label>
                    </div>
                    <el-dialog :visible.sync="dialogVisible">
                      <img width="100%" :src="dialogImageUrl" alt>
                    </el-dialog>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body text-right my-3">
              <button
                type="button"
                class="btn-fill btn-success btn px-4"
                @click="saveTicket"
              >
                <b-spinner v-if="loading" small />
                <span class="sr-only">Loading...</span>
                Submit
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="clearfix" />
    </div>
  </div>
</template>

<script>
import axios from '@/axios';
import { mapGetters, mapActions, mapState } from 'vuex';
import { AlertUtils } from '@/mixins/alertUtils';
import Card from '@/components/UIComponents/Cards/Card.vue';
import { required } from 'vuelidate/lib/validators';

export default {
  name: 'TicketAdd',

  components: {
    Card,
  },

  mixins: [
    AlertUtils,
  ],

  data() {
    return {
      ticket: {
        subject: '',
        type: '',
        description: '',
        project_id: null,
      },
      fileList: [],
      projects: [],
      dialogImageUrl: '',
      dialogVisible: false,
      loading: false,
    };
  },

  validations: {
    ticket: {
      subject: {
        required,
      },
      type: {
        required,
      },
      description: {
        required,
      },
      project_id: {
        required,
      },
    },
  },

  computed: {
    ...mapGetters({
      user: 'user/userDetails',
    }),
  },

  mounted() {
    this.getProjects();
  },

  methods: {

    setSubject(value) {
      this.ticket.subject = value;
      this.$v.ticket.subject.$touch();
    },

    setType(value) {
      this.ticket.type = value;
      this.$v.ticket.type.$touch();
    },

    setDescription(value) {
      this.ticket.description = value;
      this.$v.ticket.description.$touch();
    },

    updateFileList(file) {
      this.fileList.push(file.raw);
    },

    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.fileList.push(file);
      this.dialogVisible = true;
    },

    async getProjects() {
      this.loading = true;
      await axios.get('/projects/getAll').then((response) => {
        this.projects = response.data.data;
        this.loading = false;
      });
    },

    prepareFormData() {
      const formData = new FormData();
      Object.keys(this.ticket).forEach((key) => {
        formData.append(key, this.ticket[key]);
      });

      for (var i = 0; i < this.fileList.length; i++) {
        const file = this.fileList[i];
        formData.append(`files[${i}]`, file);
      }
      return formData;
    },

    resetForm() {
      this.ticket = {
        subject: '',
        type: '',
        description: '',
        project_id: '',
      };
      this.fileList = '';
    },

    handleFileUpload(e) {
      const file = e.target.files;
      if (file.length) {
        this.fileList.push(e.target.files[0]);
      }
    },

    handleRemoveFile(file, index) {
      this.fileList.splice(index, 1);
    },

    saveTicket(e) {
      e.preventDefault();
      const formData = this.prepareFormData();
      this.loading = true;
      this.$v.$touch();
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: `Apakah anda yakin ingin menambahkan tiket ?`,
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
              axios.post('/tickets', formData)
                .then((response) => {
                  this.loading = false;
                  this.alertStoreSuccess();
                  this.resetForm();
                  this.$v.$reset();
                })
                .catch((error) => {
                  this.loading = false;
                  this.alertStoreFailed();
                  this.$v.$reset();
                });
            }
          } else if (result.dismiss) {
            this.loading = false;
            this.alertCancel('Tambah Tiket');
            this.$v.$reset();
          }
        });
    },
  },
};
</script>

<style>
</style>
