/**
 * Contains all Navigation related components
 * REASON :  if we put these components inside agent or admin bundle and if a plugin
 * is loading, we don't need rest of the data in the bundle so keeping this seperate makes
 * it more efficient
 * We cannot load navigation dynamically because it will be required on each and every page,
 * so better to load this bundle before any bundle for better user experience
 * @author avinash kumar <avinash.kumar@ladybirdweb.com>
 */

require('./bootstrap');

import 'es6-promise/auto';

import { store } from 'store';

Vue.component('admin-navigation-bar', require('components/Navigation/AdminNavigationBar.vue'));

Vue.component('agent-navigation-bar', require('components/Navigation/AgentNavigationBar.vue'));

import VTooltip from 'v-tooltip'

Vue.use(VTooltip);

new Vue({
    el: '#navigation-container',
    store: store
});
