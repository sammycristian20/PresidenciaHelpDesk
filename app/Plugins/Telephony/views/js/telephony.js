
let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store';

import callStore from '../store/callStore';

store.registerModule('callStore', callStore)

const app = new Vue({
    el: '#telephony-settings',
    store: store,
    components: {
      'telephony-settings' : require('./components/TelephonySettings.vue'),
      'telephone-alert': require('./components/CallAlert/TelephoneAlert')
    }
});
