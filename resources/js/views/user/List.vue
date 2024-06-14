<template>
  <div class="p-3 px-4 mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="card border-0 shadow">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h4 class="card-title">Pengguna</h4>
          </div>
          <b-tabs>
            <b-tab title="Employee" active>
              <EmployeeList />
            </b-tab>
            <b-tab title="Admin">
              <AdminList />
            </b-tab>
          </b-tabs>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/axios';
import permission from '@/directive/permission';
import checkPermission from '@/utils/permission';
import permissionMixin from '@/mixins/permissionMixin';
import AdminList from './AdminList';
import EmployeeList from './EmployeList';

export default {
  name: 'UserList',
  components: { AdminList, EmployeeList },
  directives: { permission },
  mixins: [
    permissionMixin,
  ],
  data() {
    return {
      loading: true,
      downloading: false,
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
    };
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
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
</style>
