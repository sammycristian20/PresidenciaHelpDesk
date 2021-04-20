let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store'

Vue.component('logs-index', require('faveoLog/components/LogsIndex'));

const app = new Vue({
    el: '#app-system-logs',
    store: store,
});
