import Vue from 'vue';
import Vuex from 'vuex';
import getters from './getters';

import user from './modules/user';
import category from './modules/category';
import company from './modules/company';
import ticket from './modules/ticket';
import dashboard from './modules/dashboard';
import activity from './modules/activity';
import chat from './modules/chat';
import shared from './modules/shared';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    chat,
    user,
    category,
    company,
    ticket,
    dashboard,
    activity,
    shared,
  },
  getters,
});
