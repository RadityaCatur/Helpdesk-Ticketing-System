<template>
  <div class="p-3 px-4 mt-3">
    <div class="card border-0 shadow">
      <div class="card-header">
        <h4 class="card-title">Hak akses</h4>
      </div>
      <div class="card-body">
        <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%">
          <el-table-column align="center" label="ID" width="80">
            <template slot-scope="scope">
              <span>{{ scope.row.index }}</span>
            </template>
          </el-table-column>

          <el-table-column width="700" align="center" label="Nama">
            <template slot-scope="scope">
              <span>{{ scope.row.name }}</span>
            </template>
          </el-table-column>

          <el-table-column v-if="checkPermission(['manage permission'])" align="center" label="Aksi">
            <template slot-scope="scope">
              <button
                v-if="scope.row.name !== 'admin'"
                v-permission="['manage permission']"
                class="btn-fill btn-success btn-sm"
                @click="handleEditPermissions(scope.row.id)"
              >
                Tetapkan izin
              </button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <el-dialog :visible.sync="roleDialogVisible" :title="role.status + ' Role'">
        <form method="POST">
          <div class="row">
            <div class="col-md-12">
              <fg-input
                v-model="role.name"
                type="text"
                label="Nama"
                placeholder="Nama Peran"
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="text-center">
                <button
                  v-if="role.status === 'Create'"
                  type="button"
                  class="btn-fill btn-success btn-lg btn-fill mt-4"
                  @click="handleCreateRole"
                >
                  Buat
                </button>
                <button
                  v-else-if="role.status === 'Update'"
                  type="button"
                  class="btn-fill btn-success btn-lg mt-4"
                  @click="confirmUpdateRole"
                >
                  Perbarui
                </button>
              </div>
            </div>
          </div>
        </form>
      </el-dialog>

      <el-dialog :visible.sync="dialogVisible" :title="'Edit Permissions - ' + currentRole.name">
        <div v-loading="dialogLoading" class="form-container mt-n4">
          <div class="permissions-container">
            <div class="block">
              <el-form :model="currentRole" label-width="80px" label-position="top">
                <el-form-item label="Izin">
                  <el-tree ref="otherPermissions" :data="otherPermissions" :default-checked-keys="permissionKeys(roleOtherPermissions)" :props="permissionProps" show-checkbox node-key="id" class="permission-tree" />
                </el-form-item>
              </el-form>
            </div>
            <div class="clear-left" />
          </div>
          <div style="text-align:right;">
            <el-button type="danger" @click="dialogVisible=false">
              Batal
            </el-button>
            <el-button type="success" @click="confirmPermission">
              Konfirmasi
            </el-button>
          </div>
        </div>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import axios from '@/axios';
import waves from '@/directive/waves'; // Waves directive
import permission from '@/directive/permission'; // Permission directive (v-permission)
import checkPermission from '@/utils/permission'; // Permission checking

export default {
  name: 'RoleList',
  directives: {
    permission,
    waves,
  },
  data() {
    return {
      currentRoleId: 1,
      role: {
        id: '',
        name: '',
        status: 'Create',
      },
      roleFields: [
        'no', 'name', 'actions',
      ],
      list: [],
      loading: true,
      dialogLoading: false,
      dialogVisible: false,
      roleDialogVisible: false,
      permissions: [],
      menuPermissions: [],
      otherPermissions: [],
      permissionProps: {
        children: 'children',
        label: 'name',
        disabled: 'disabled',
      },
    };
  },
  computed: {
    currentRole() {
      const found = this.list.find(role => role.id === this.currentRoleId);
      if (found === undefined) {
        return { name: '', permissions: [] };
      }

      return found;
    },
    rolePermissions() {
      return this.classifyPermissions(this.currentRole.permissions).all;
    },
    roleMenuPermissions() {
      return this.classifyPermissions(this.currentRole.permissions).menu;
    },
    roleOtherPermissions() {
      return this.classifyPermissions(this.currentRole.permissions).other;
    },
  },
  created() {
    this.getRoles();
    this.getPermissions();
  },

  methods: {
    checkPermission,
    async getRoles() {
      this.loading = true;
      await axios.get('/roles')
        .then((response) => {
          this.list = response.data.data;
          this.list.forEach((role, index) => {
            role['index'] = index + 1;
          });
        });
      this.loading = false;
    },

    async getPermissions() {
      await axios.get('/permissions')
        .then((response) => {
          const data = response.data.data;
          const { all, menu, other } = this.classifyPermissions(data);
          this.permissions = all;
          this.menuPermissions = menu;
          this.otherPermissions = other;
        });
    },

    classifyPermissions(permissions) {
      const all = []; const menu = []; const other = [];
      permissions.forEach(permission => {
        const permissionName = permission.name;
        all.push(permission);
        if (permissionName.startsWith('view menu')) {
          menu.push(this.normalizeMenuPermission(permission));
        } else {
          other.push(this.normalizePermission(permission));
        }
      });
      return { all, menu, other };
    },

    normalizeMenuPermission(permission) {
      return { id: permission.id, name: permission.name.substring(10) };
    },

    normalizePermission(permission) {
      return { id: permission.id, name: permission.name, disabled: permission.name === 'manage permission' };
    },

    permissionKeys(permissions) {
      return permissions.map(permssion => permssion.id);
    },

    handleEditPermissions(id) {
      this.dialogVisible = true;
      this.currentRoleId = id;
      this.$nextTick(() => {
        this.$refs.menuPermissions.setCheckedKeys(this.permissionKeys(this.roleMenuPermissions));
        this.$refs.otherPermissions.setCheckedKeys(this.permissionKeys(this.roleOtherPermissions));
      });
    },

    handleEditRole(id) {
      this.roleDialogVisible = true;
      const found = this.list.find(role => role.id === id);
      this.role = {
        id: found.id,
        name: found.name,
        status: 'Update',
      };
    },

    handleDeleteRole(val) {
      this.$confirm('Data ' + val.name + '. akan dihapus, apakah anda yakin?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        axios.delete(`roles/${val.id}`)
          .then((response) => {
            this.$message({
              message: 'Data berhasil dihapus',
              type: 'success',
              duration: 5 * 1000,
            });
            this.getRoles();
          })
          .catch((error) => {
            this.$message({
              message: error,
              type: 'error',
              duration: 5 * 1000,
            });
          });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Hapus data dibatalkan',
        });
      });
    },

    confirmUpdateRole() {
      this.$confirm(`Data  ${this.role.name}. akan diubah, apakah anda yakin?`, 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        axios.put(`/roles/${this.role.id}`, { name: this.role.name })
          .then(response => {
            this.roleDialogVisible = false;
            this.role = {
              id: '',
              name: '',
              status: 'Create',
            };
            this.$message({
              message: 'Data berhasil diubah',
              type: 'success',
              duration: 5 * 1000,
            });
            this.getRoles();
          });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Update canceled',
        });
      });
    },

    handleCreateRole() {
      this.$confirm(`Apakah anda yakin akan menambahkan role?`, 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        axios.post('/roles', { name: this.role.name })
          .then(response => {
            this.roleDialogVisible = false;
            this.role = {
              id: '',
              name: '',
              status: 'Create',
            };
            this.$message({
              message: 'Data berhasil ditambahkan',
              type: 'success',
              duration: 5 * 1000,
            });
            this.getRoles();
          });
      }).catch(() => {
        this.$message({
          type: 'info',
          message: 'Tambah data dibatalkan',
        });
      });
    },

    confirmPermission() {
      const checkedMenu = this.$refs.menuPermissions.getCheckedKeys();
      const checkedOther = this.$refs.otherPermissions.getCheckedKeys();
      const checkedPermissions = checkedMenu.concat(checkedOther);
      this.dialogLoading = true;

      axios.put(`/roles/${this.currentRole.id}`, { permissions: checkedPermissions })
        .then(response => {
          this.$message({
            message: 'Data berhasil diubah',
            type: 'success',
            duration: 5 * 1000,
          });
          this.dialogLoading = false;
          this.dialogVisible = false;
          this.getRoles();
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.permissions-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
  h1, .h1, h2, .h2, h3, .h3, h4, .h4 {
    margin: 0 !important;
  }
</style>
