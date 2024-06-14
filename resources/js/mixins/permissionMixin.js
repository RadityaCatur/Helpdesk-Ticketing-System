import _ from 'lodash';
import axios from '@/axios';
import checkPermission from '@/utils/permission';

export default {
  data() {
    return {
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

  created() {
    if (checkPermission(['manage permission'])) {
      this.getPermissions();
      this.getRoles();
    }
  },

  computed: {
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
        name: 'Bawaan dari role',
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
  methods: {
    checkPermission,
    async getPermissions() {
      await axios.get('/permissions')
        .then((response) => {
          const { all, other } = this.classifyPermissions(response.data.data);
          this.permissions = all;
          this.otherPermissions = other;
        });
    },

    handleEditPermission(val) {
      console.log(val);
      this.dialogPermissionLoading = true;
      this.dialogPermissionVisible = true;
      const found = this.items.find(user => user.fullname === val.item.fullname);
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
          this.getList();
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
  },
};
