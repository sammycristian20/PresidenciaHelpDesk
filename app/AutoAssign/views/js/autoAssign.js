let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store'

Vue.component('auto-assign', require('./components/AutoAssign'));

store.dispatch('deleteUser');

store.dispatch('updateUser');

const app = new Vue({

    el: '#app-auto-assign',

    store: store
});
