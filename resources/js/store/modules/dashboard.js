import axios from '@/axios';

const state = () => ({
  tickets: [],
});

const mutations = {
  ASSIGN_DATA_TICKETS(state, payload) {
    state.tickets = payload;
  },
};

const actions = {
  getChartData({ commit }, payload) {
    return new Promise((resolve, reject) => {
      axios
        .get(
          `/chart?year=${payload.year}`
        )
        .then(response => {
          commit('ASSIGN_DATA_TICKETS', response.data);
          resolve(response.data);
        });
    });
  },
};

export default {
  namespaced: true,
  state,
  actions,
  mutations,
};
