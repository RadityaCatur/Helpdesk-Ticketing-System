import axios from '@/axios';

const state = {
  tickets: [],
  ticket: {},
  meta: {
    total: 0,
    current_page: '',
    per_page: '',
    from: '',
    to: '',
  },
};

const mutations = {
  SET_TICKETS(state, payload) {
    state.tickets = payload;
  },
  SET_TICKET(state, payload) {
    state.ticket = payload;
  },
  SET_TICKETS_META(state, payload) {
    state.meta = payload;
  },
};

const actions = {
  createTicket(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios.post('/tickets', payload)
        .then((response) => {
          state.tickets.push(response.data.data);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  getTickets(ctx) {
    return new Promise((resolve, reject) => {
      axios.get('/tickets')
        .then((response) => {
          const responseData = response.data;
          ctx.commit('SET_TICKETS', responseData.data);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
  showTicket(ctx, payload) {
    return new Promise((resolve, reject) => {
      axios.get(`/tickets/${payload}`)
        .then((response) => {
          ctx.commit('SET_TICKET', response.data.data);
          resolve(response);
        })
        .catch((error) => {
          reject(error);
        });
    });
  },
};

const getters = {
  tickets(state) {
    return state.tickets;
  },
  ticket(state) {
    return state.ticket;
  },
};

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
};
