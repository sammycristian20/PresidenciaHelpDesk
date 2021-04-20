
let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store'

const app = new Vue({
    el: '#facebook-settings',
    store: store,
    components: {
      'facebook-settings' : require('./components/FacebookSettings.vue'),
      'facebook-create-edit' : require('./components/FacebookCreateEdit.vue'),
      'facebook-general-settings' : require('./components/FacebookAppSettings'),
    }
});

