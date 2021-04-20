
let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store'

const app = new Vue({
    el: '#azure-active-directory-settings',
    store: store,
    components: {
        'azure-active-directory-settings' : require('./components/AzureActiveDirectorySettings.vue'),
        'azure-active-directory-index' : require('./components/AzureActiveDirectoryIndex.vue')
    }
});

bootstrap.injectComponentIntoView('azure-login', require('./components/AzureLogin.vue'),'login-box-mounted','login-box');
