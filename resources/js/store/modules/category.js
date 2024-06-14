import axios from '@/axios';

const state = {
  categories: [],
};

const mutations = {
  FETCH_CATEGORIES(state, categories) {
    state.categories = [...state.categories, ...categories];
  },
};

const actions = {
  fetchCategories(ctx) {
    return new Promise((resolve, reject) => {
      axios.get('/api/categories')
        .then((response) => {
          ctx.commit('FETCH_CATEGORIES', response.data);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
};

const getters = {
  categories(state) {
    return state.categories;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions,
};
