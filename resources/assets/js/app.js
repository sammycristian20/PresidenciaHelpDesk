require('./bootstrap');

var moment = require('moment');

import 'es6-promise/auto';

import {store} from 'store'

import VueProgressBar from 'vue-progressbar';

import Popover from 'vue-js-popover';

import { FulfillingBouncingCircleSpinner } from 'epic-spinners';

import StarRating from 'vue-star-rating';

import VTooltip from 'v-tooltip';

import vueHeadful from 'vue-headful';

import router from './router';

import CKEditor from '@ckeditor/ckeditor5-vue';

Vue.use( CKEditor );

Vue.use(VueProgressBar, { color: '#009aba', failedColor: 'red', height: '2px' });

Vue.use(Popover);

Vue.use(require("vuejs-uib-pagination"));

Vue.use(require("vue-simple-uploader"));

Vue.component('fulfilling-bouncing-circle-spinner', FulfillingBouncingCircleSpinner);

Vue.component('star-rating', StarRating);

Vue.use(VTooltip);

Vue.component('vue-headful', vueHeadful);

Vue.use(require('vddl'));

Vue.component('loader', require('./components/Client/Pages/ReusableComponents/Loader'));

Vue.component('client-panel-layout', require('./components/Client/ClientPanelLayout'));

Vue.component('reset-password', require('./components/Client/Pages/Auth/PasswordReset'));

Vue.component('meta-component', require('./components/Client/Pages/MetaComponent.vue'));

Vue.component('license-error', require('./components/Client/Pages/NotFound/LicenseError'));

Vue.component('form-renderer', require('./components/Common/Form/FormRenderer'));

Vue.filter('truncate', function (str, max) {
        return str.length > max ? str.substr(0, max - 1) + '…' : str;
})

setTimeout(()=>{

  let app = new Vue({
    el: '#app-client-panel',
    store,
    router,
  });

}, 1000);

/**
 * Should be called to inject routes from outside the bundle
 * @param {Array} wildCardRoutes
 */
window.addRoutes = (wildCardRoutes) => {
  router.addRoutes(wildCardRoutes);
}