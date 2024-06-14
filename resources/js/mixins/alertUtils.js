export const AlertUtils = {

  methods: {
    alertStoreSuccess() {
      var vm = this;
      const Toast = vm.$swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
          toast.addEventListener('mouseenter', vm.$swal.stopTimer);
          toast.addEventListener('mouseleave', vm.$swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: 'success',
        title: 'Data Berhasil Ditambahkan',
      });
    },

    alertStoreFailed() {
      var vm = this;
      const Toast = vm.$swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
          toast.addEventListener('mouseenter', vm.$swal.stopTimer);
          toast.addEventListener('mouseleave', vm.$swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: 'error',
        title: 'Data Gagal Ditambahkan',
      });
    },

    alertStoreMessage($message) {
      var vm = this;
      const Toast = vm.$swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
          toast.addEventListener('mouseenter', vm.$swal.stopTimer);
          toast.addEventListener('mouseleave', vm.$swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: 'error',
        title: $message,
      });
    },

    alertUpdateSuccess() {
      var vm = this;
      const Toast = vm.$swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
          toast.addEventListener('mouseenter', vm.$swal.stopTimer);
          toast.addEventListener('mouseleave', vm.$swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: 'success',
        title: 'Data Berhasil Diubah',
      });
    },

    alertUpdateFailed() {
      var vm = this;
      const Toast = vm.$swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
          toast.addEventListener('mouseenter', vm.$swal.stopTimer);
          toast.addEventListener('mouseleave', vm.$swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: 'error',
        title: 'Data Gagal Diubah',
      });
    },

    alertDeleteSuccess() {
      var vm = this;
      const Toast = vm.$swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
          toast.addEventListener('mouseenter', vm.$swal.stopTimer);
          toast.addEventListener('mouseleave', vm.$swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: 'success',
        title: 'Data Berhasil Dihapus',
      });
    },

    alertDeleteFailed() {
      var vm = this;
      const Toast = vm.$swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
          toast.addEventListener('mouseenter', vm.$swal.stopTimer);
          toast.addEventListener('mouseleave', vm.$swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: 'error',
        title: 'Data Berhasil Dihapus',
      });
    },

    alertCancel(action = 'Aksi') {
      var vm = this;
      const Toast = vm.$swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: toast => {
          toast.addEventListener('mouseenter', vm.$swal.stopTimer);
          toast.addEventListener('mouseleave', vm.$swal.resumeTimer);
        },
      });
      Toast.fire({
        icon: 'info',
        title: `${action} Dibatalkan`,
      });
    },
  },

};
