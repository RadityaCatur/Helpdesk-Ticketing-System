import axios from '@/axios';
import store from '@/store';

const state = () => ({
  companies: [],
});

const mutations = {
  SET_COMPANIES(state, payload) {
    state.companies = payload;
  },
};

const actions = {
  getCompanies({ commit }) {
    return new Promise((resolve, reject) => {
      axios.get('/companies')
        .then((response) => {
          commit('SET_COMPANIES', response.data.data);
          resolve(response.data);
        })
        .catch((error) => {
          console.log(error);
        });
    });
  },
};

const getters = {
  allCompanies(state) {
    return state.companies;
  },
};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
  getters,
};
