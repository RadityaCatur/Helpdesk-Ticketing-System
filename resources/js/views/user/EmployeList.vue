<template>
  <div class="card-body mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="float-right mr-4">
          <b-button v-waves :loading="downloading" class="btn-fill btn-info btn ml-auto mr-3" icon="download" @click="handleExportToExcel">
            Ekspor
          </b-button>
          <router-link to="/dashboard/add-user" tag="b-button" class="btn-fill btn btn-success text-light">
            Tambah
          </router-link>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4 mb-2">
          <div class="form-inline">
            <label class="mr-2">Menampilkan</label>
            <select v-model="perPage" class="form-control">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <label class="ml-2">Data</label>
          </div>
        </div>

        <div class="col-md-4 offset-md-4">
          <div class="form-inline float-right">
            <label class="mr-2">Cari</label>
            <input type="text" class="form-control" placeholder="Cari..." @input="handleSearch">
          </div>
        </div>

        <div class="col-md-12">
          <b-table
            id="client-table"
            :items="users"
            :fields="fields"
            :per-page="perPage"
            :current-page="currentPage"
            striped
            hover
            show-empty
            :busy="loading"
            responsive
            empty-text="Tidak ada employee"
          >
            <template v-slot:cell(no)="data">
              {{ data.index + 1 }}
            </template>
            <template v-slot:cell(actions)="row">
              <button class="btn-fill btn-info btn-sm" @click="handleDetail(row)">Rincian</button>
              <button class="btn-fill btn-warning btn-sm" @click="handleEdit(row)">Ubah</button>
              <button class="btn-fill btn-danger btn-sm" @click="handleDestroy(row)">
                <b-spinner v-if="deleteLoading" small />
                <span class="sr-only">Loading...</span>
                Hapus
              </button>
              <button class="btn-fill btn-secondary btn-sm" @click="handleEditPermission(row);">Hak akses</button>
            </template>
            <template #table-busy>
              <div class="text-center text-primary my-2">
                <b-spinner class="align-middle" />
                <strong>Loading...</strong>
              </div>
            </template>
          </b-table>
        </div>
        <div class="col-md-12">
          <b-pagination
            v-model="currentPage"
            align="right"
            :total-rows="rows"
            :per-page="perPage"
            aria-controls="client-table"
          />
        </div>
      </div>
    </div>
    <el-dialog :visible.sync="dialogPermissionVisible" :title="'Edit Permissions - ' + currentUser.fullname">
      <div v-if="currentUser.fullname" v-loading="dialogPermissionLoading" class="form-container">
        <div class="permissions-container">
          <div class="block">
            <el-form :model="currentUser" label-width="80px" label-position="top">
              <el-form-item label="Permissions">
                <el-tree ref="otherPermissions" :data="normalizedOtherPermissions" :default-checked-keys="permissionKeys(userOtherPermissions)" :props="permissionProps" show-checkbox node-key="id" class="permission-tree" />
              </el-form-item>
            </el-form>
          </div>
          <div class="block">
            <el-select v-model="currentUser.roles" placeholder="Select">
              <el-option
                v-for="role in roles"
                :key="role.id"
                :label="role.name"
                :value="role.name"
              />
            </el-select>
          </div>
          <div class="clear-left" />
        </div>
        <div style="text-align:right;">
          <el-button type="danger" class="btn-fill" @click="dialogPermissionVisible=false">
            Batal
          </el-button>
          <el-button type="success" class="btn-fill" @click="confirmPermission">
            Konfirmasi
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import _ from 'lodash';
import axios from '@/axios';
import { AlertUtils } from '@/mixins/alertUtils';
import waves from '@/directive/waves';
import formatMixin from '@/mixins/formatMixin';
import checkPermission from '@/utils/permission';

export default {
  name: 'EmployeeList',

  directives: {
    waves,
  },

  mixins: [
    AlertUtils,
    formatMixin,
  ],

  data() {
    return {
      users: [],
      user: {},
      meta: {},
      fields: [
        { key: 'no', label: 'No', sortable: false },
        { key: 'fullname', label: 'Nama Lengkap', sortable: true },
        { key: 'email', label: 'Email', sortable: true },
        { key: 'handphone', label: 'No. Telepon', sortable: true },
        { key: 'actions', label: 'Aksi', sortable: false },
      ],
      perPage: 10,
      currentPage: 1,
      search: '',
      sortBy: 'created_at',
      sortByDesc: false,
      loading: false,
      downloading: false,
      deleteLoading: false,
      dialogPermissionVisible: false,
      dialogPermissionLoading: false,
      currentUserId: 0,
      currentUser: {
        fullname: '',
        permissions: [],
        rolePermissions: [],
      },
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
    };
  },

  computed: {
    rows() {
      return this.users.length;
    },
    normalizedOtherPermissions() {
      let tmp = [];
      this.currentUser.permissions.role.forEach(permission => {
        tmp.push({
          id: permission.id,
          name: permission.name,
          disabled: true,
        });
      });
      const rolePermissions = {
        id: -1,
        name: 'Role',
        disabled: true,
        children: this.classifyPermissions(tmp).other,
      };

      tmp = this.otherPermissions.filter(permission => !this.currentUser.permissions.role.find(p => p.id === permission.id));
      const userPermissions = {
        id: 0,
        name: 'Extra akses',
        children: tmp,
        disabled: tmp.length === 0,
      };

      return [rolePermissions, userPermissions];
    },
    userOtherPermissions() {
      return this.classifyPermissions(this.userPermissions).other;
    },
    userPermissions() {
      return this.currentUser.permissions.role.concat(this.currentUser.permissions.user);
    },
  },

  created() {
    this.getUsers();
    if (checkPermission(['manage permission'])) {
      this.getPermissions();
      this.getRoles();
    }
  },

  methods: {
    checkPermission,
    async getUsers() {
      this.loading = true;
      await axios.get('/employee', {
        params: {
          q: this.search,
        },
      }).then((response) => {
        this.users = response.data.data;
        console.log(this.users);
        this.loading = false;
      });
    },

    handleSearch: _.debounce(function(e) {
      this.search = e.target.value;
      this.getUsers();
    }, 500),

    handleDetail(val) {
      this.$router.push({
        name: 'show-user',
        params: {
          id: val.item.id,
        },
      });
    },

    handleEdit(val) {
      this.$router.push({
        name: 'edit-user',
        params: {
          id: val.item.id,
        },
      });
    },

    handleDestroy(val) {
      this.deleteLoading = true;
      this.$swal
        .fire({
          title: 'Peringatan!',
          text: `Apakah anda yakin ingin menghapus data ${val.item.fullname}?`,
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
            axios.delete(`users/${val.item.id}`)
              .then((response) => {
                this.deleteLoading = false;
                this.getUsers();
                this.alertDeleteSuccess();
              })
              .catch((error) => {
                this.deleteLoading = false;
                this.alertDeleteFailed();
              });
          } else if (result.dismiss) {
            this.deleteLoading = false;
            this.alertCancel('Hapus User');
          }
        });
    },

    async getPermissions() {
      await axios.get('/permissions')
        .then((response) => {
          const { all, other } = this.classifyPermissions(response.data.data);
          this.permissions = all;
          this.otherPermissions = other;
        });
    },

    handleEditPermission(val) {
      this.dialogPermissionLoading = true;
      this.dialogPermissionVisible = true;
      const found = this.users.find(user => user.id === val.item.id);
      this.currentUserId = found.id;
      this.currentUser = {
        roles: found.roles[0].name,
      };
      axios.get(`users/${val.item.id}/permissions`)
        .then((response) => {
          this.currentUser = {
            id: found.id,
            fullname: found.fullname,
            permissions: response.data.data,
            roles: found.roles[0].name,
          };
          this.dialogPermissionLoading = false;
          this.$nextTick(() => {
            this.$refs.otherPermissions.setCheckedKeys(this.permissionKeys(this.userOtherPermissions));
          });
        });
    },

    confirmPermission() {
      const checkedOther = this.$refs.otherPermissions.getCheckedKeys();
      this.dialogPermissionLoading = true;
      axios.put(`users/${this.currentUserId}/permissions`, {
        permissions: checkedOther,
        roles: this.currentUser.roles,
      })
        .then((response) => {
          this.getUsers();
          this.$message({
            message: 'Data berhasil diubah',
            type: 'success',
            duration: 5 * 1000,
          });
          this.dialogPermissionLoading = false;
          this.dialogPermissionVisible = false;
        })
        .catch((error) => {
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },

    // Permissions Normalizer
    normalizePermission(permission) {
      const disabled = permission.disabled || permission.name === 'manage permission';
      return { id: permission.id, name: permission.name, disabled: disabled };
    },

    classifyPermissions(permissions) {
      const all = [];
      const other = [];
      permissions.forEach(permission => {
        const permissionName = permission.name;
        all.push(permission);
        other.push(this.normalizePermission(permission));
      });
      return { all, other };
    },

    // Get All Permissions in database
    async getPermissions() {
      return await axios.get('/permissions')
        .then((response) => {
          const data = response.data.data;
          const { all, other } = this.classifyPermissions(data);
          this.permissions = all;
          this.otherPermissions = other;
        });
    },

    async getRoles() {
      return await axios.get('roles')
        .then((response) => {
          this.roles = response.data.data;
        })
        .catch((error) => {
          this.$message({
            message: error,
            type: 'error',
            duration: 5 * 1000,
          });
        });
    },

    // Check all permissions
    permissionKeys(permissions) {
      return permissions.map(permssion => permssion.id);
    },

    normalizeMenuPermission(permission) {
      return {
        id: permission.id,
        name: permission.name.substring(10),
        disabled: permission.disabled || false,
      };
    },

    handleExportToExcel() {
      this.downloading = true;
      import('@/vendor/Export2Excel').then(excel => {
        const tHeader = [
          'Id User',
          'Nama Lengkap',
          'Username',
          'Email',
          'No. Telepon',
          'Alamat',
        ];
        const filterVal = [
          'id',
          'fullname',
          'username',
          'email',
          'handphone',
          'alamat',
        ];
        const data = this.formatJson(filterVal, this.users);
        console.log(data);
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: `employee-list-${this.formatDate()}`,
        });
        this.downloading = false;
      });
    },
  },
};
</script>

<style>

</style>
