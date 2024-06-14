import axios from '@/axios';

const state = {
  isLoggedIn: false,
  userDetails: {},
  company: '',
  roles: [],
  permissions: [],
  users: [],
};

const mutations = {
  setLoggedIn(state, payload) {
    state.isLoggedIn = payload;
  },
  setUserDetails(state, payload) {
    state.userDetails = payload;
  },
  SET_COMPANY(state, payload) {
    state.company = payload;
  },
  SET_ROLES: (state, roles) => {
    state.roles = roles;
  },
  SET_PERMISSIONS: (state, permissions) => {
    state.permissions = permissions;
  },
  setUsers(state, payload) {
    state.users = payload;
  },
};

const actions = {
  loadUsers({ commit }) {
    axios
      .get('users')
      .then(response => {
        if (response.data) {
          commit('setUsers', response.data);
        } else {
          window.console.warn('failed to load users data', response);
        }
      })
      .catch(err => window.console.error(err));
  },
  registerUser(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios
        .post('register', payload)
        .then(response => {
          if (response.data) {
            resolve(response);
          } else {
            reject(response);
          }
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  loginUser(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios
        .post('login', payload)
        .then(response => {
          if (response.data.access_token) {
            localStorage.setItem('token', response.data.access_token);
            ctx.commit('setLoggedIn', true);
            ctx.dispatch('me').then(() => resolve(response));
          } else {
            reject(response);
          }
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  logoutUser(ctx) {
    return new Promise((resolve, reject) => {
      axios
        .get('/logout')
        .then(() => {
          localStorage.removeItem('token');
          ctx.commit('setLoggedIn', false);
          resolve(true);
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  setLoggedInState(ctx) {
    return new Promise(resolve => {
      if (localStorage.getItem('token')) {
        ctx.commit('setLoggedIn', true);
        resolve(true);
      } else {
        ctx.commit('setLoggedIn', false);
        resolve(false);
      }
    });
  },
  forgotPassword(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios
        .post('forgot-password', payload)
        .then(response => {
          resolve(response);
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  resetPassword(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios
        .post('reset-password', payload)
        .then(response => {
          resolve(response);
          this.$message({
            type: 'info',
            message: 'Dimohon untuk mengecek Email',
          });
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  me(ctx) {
    return new Promise((resolve, reject) => {
      axios
        .get('me')
        .then(response => {
          if (response.status === 401) {
            this.$router.push({ name: 'login' });
            localStorage.removeItem('token');
          } else {
            ctx.commit('setUserDetails', response.data);
            ctx.commit('SET_COMPANY', response.data.company);
            ctx.commit('SET_ROLES', response.data.roles);
            ctx.commit('SET_PERMISSIONS', response.data.permissions);
            resolve(response);
          }
        })
        .catch(error => {
          this.$router.push({ name: 'login' });
          localStorage.removeItem('token');
          reject(error);
        });
    });
  },
  changePassword(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios
        .post('change-password', payload)
        .then(response => {
          resolve(response);
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  updateDetails(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios
        .post('change-details', payload)
        .then(response => {
          resolve(response);
        })
        .catch(error => {
          reject(error);
        });
    });
  },
  verifyEmail(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios
        .get('email-verification', {
          params: payload,
        })
        .then(response => {
          resolve(response);
        })
        .catch(error => {
          reject(error);
        });
    });
  },
};

const getters = {
  loggedIn(state) {
    return state.isLoggedIn;
  },
  userDetails(state) {
    return state.userDetails;
  },
  getCompany(state) {
    return state.company;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
