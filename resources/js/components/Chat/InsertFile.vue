<template>
  <div
    id="insertFiles"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-labelledby="insertFilesLabel"
    aria-hidden="true"
    @keyup.esc="closeModal"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="insertFilesLabel" class="modal-title">Insert Files</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <vue-dropzone
            id="uploadDropZone"
            ref="uploadDropZone"
            :options="config"
            @vdropzone-complete="afterComplete"
            style="cursor: pointer;"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vueDropzone from 'vue2-dropzone';

export default {
  name: 'InsertFile',

  components: {
    vueDropzone,
  },

  props: ['room', 'capture'],

  data() {
    return {
      config: {
        url: `/api/messages/${this.room.id}/upload`,
        headers: {
          'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
          'X-XSRF-TOKEN': document.cookie.split('=')[1],
          'Authorization': 'Bearer ' + localStorage.getItem('token')
        },
      },
    };
  },

  methods: {
    closeDialog() {
      $('#insertFiles').modal('hide');
    },
    afterComplete(file) {
      this.$refs.uploadDropZone.removeFile(file);
      this.closeDialog();
    },
  },

};
</script>

<style>

</style>
