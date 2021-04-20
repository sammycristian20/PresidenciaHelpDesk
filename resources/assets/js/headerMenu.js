require('./bootstrap');

import 'es6-promise/auto';

import { store } from 'store';

import Vue from 'vue';

import VueRouter from 'vue-router';

Vue.component('system-updates', require('components/HeaderNavigation/Updates/SystemUpdates'));

Vue.component('database-updates', require('components/HeaderNavigation/Updates/DatabaseUpdates'));

import VTooltip from 'v-tooltip'

Vue.use(VTooltip);

Vue.use(VueRouter);

new Vue({
    el: '#header-container',
    store: store
});
