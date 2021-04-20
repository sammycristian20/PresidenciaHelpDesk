
let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store'

const app = new Vue({
    el: '#ldap-settings',
    store: store,
    components: {
      'ldap-list-page': require('./components/LdapListPage'),
      'ldap-settings' : require('./components/LdapSettings.vue')
    }
});

// injecting ldap-login component into login-box at login page on login-box-mounted event
bootstrap.injectComponentIntoView('ldap-login', require('./components/LdapLogin.vue'),'login-box-mounted','login-box');
