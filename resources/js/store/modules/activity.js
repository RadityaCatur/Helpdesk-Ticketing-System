import axios from '@/axios';

const state = () => ({
  activities: [],
});

const mutations = {
  ASSIGN_DATA_ACTIVITIES(state, payload) {
    state.activities = payload;
  },
};

const actions = {
  getActivitiesData({ commit }, payload) {
    return new Promise((resolve, reject) => {
      axios
        .get(
          `/activities?month=${payload.month}&year=${payload.year}&day=${payload.day}`
        )
        .then(response => {
          commit('ASSIGN_DATA_ACTIVITIES', response.data);
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
