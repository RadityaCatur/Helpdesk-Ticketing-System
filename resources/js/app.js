require('./bootstrap');

import Vue from 'vue';
import VueSweetalert2 from 'vue-sweetalert2';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import ElementUI from 'element-ui';
import VueMoment from 'vue-moment';
import moment from 'moment-timezone';
require('moment/locale/id');

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import App from '@/App.vue';
import router from '@/router';
import store from '@/store';
import isRole from '@/utils/isRole';

import 'vue-phone-number-input/dist/vue-phone-number-input.css';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import LightBootstrap from '@/plugins/light-bootstrap-main.js';
import locale from 'element-ui/lib/locale/lang/en';
import 'element-ui/lib/theme-chalk/index.css';

import Vuesax from 'vuesax';
import 'vuesax/dist/vuesax.css';
Vue.use(Vuesax);

Vue.config.productionTip = false;
Vue.prototype.isRole = isRole;

Vue.use(ElementUI, { locale });

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(LightBootstrap);
Vue.use(VueSweetalert2);
Vue.use(VueMoment, { moment });

window.eventBus = new Vue();

new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App),
});
