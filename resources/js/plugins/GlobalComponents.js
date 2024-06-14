import fgInput from '@/components/UIComponents/Inputs/formGroupInput.vue';
import DropDown from '@/components/UIComponents/Dropdown.vue';
import VuePhoneNumberInput from 'vue-phone-number-input';

/**
 * You can register global components here and use them as a plugin in your main Vue instance
 */

const GlobalComponents = {
  install(Vue) {
    Vue.component('fg-input', fgInput);
    Vue.component('drop-down', DropDown);
    Vue.component('VuePhoneNumberInput', VuePhoneNumberInput);
  },
};

export default GlobalComponents;
