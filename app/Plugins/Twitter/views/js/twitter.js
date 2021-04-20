
let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store'

const app = new Vue({
    el: '#twitter-settings',
    store: store,
    components: {
      'twitter-settings' : require('./components/TwitterSettingsPage.vue'),
    }
});

