import Vue from "vue";

let bootstrap = require('bootstrap');

require('../css/reportCommon.css');

import 'es6-promise/auto';

import {store} from 'store';


import vSelect from 'vue-select';

Vue.component('v-select', vSelect);

// Adding chart js data label plugin
import ChartDataLabels from 'chartjs-plugin-datalabels';

Vue.component('report-home-page', require('faveoReport/components/ReportHomePage'));
Vue.component('report-entry-page', require('faveoReport/components/ReportEntryPage'));
// for report settings page
Vue.component('report-settings', require('faveoReport/components/ReportSettings.vue'));

const app = new Vue({
    el: '#faveo-report',
    store: store,
});
