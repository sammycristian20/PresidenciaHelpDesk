let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store'

const app = new Vue({
    el: '#chat-settings',
    store: store,
    components: {
      'chat-settings' : require('./components/ChatSettings.vue'),
      'chat-edit' : require('./components/ChatEdit.vue')
    }
});

