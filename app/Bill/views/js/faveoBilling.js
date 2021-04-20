let bootstrap = require('bootstrap');

import 'es6-promise/auto';

import {store} from 'store'

bootstrap.injectComponentIntoView('billing-packages', require('faveoBilling/components/Package/Agent/BillingPackages'),'user-page-mounted','user-page-table');

Vue.component('package-index', require('faveoBilling/components/Package/PackageIndex'));

Vue.component('package', require('faveoBilling/components/Package/Package'));

Vue.component('payment', require('faveoBilling/components/Payment/Payment'));

Vue.component('payment-index', require('faveoBilling/components/Payment/PaymentIndex'));

Vue.component('billing-packages', require('faveoBilling/components/Package/Agent/BillingPackages'));

Vue.component('package-invoice', require('faveoBilling/components/Package/Agent/PackageInvoice'));

Vue.component('order-details', require('faveoBilling/components/Package/Agent/OrderDetails'));

Vue.component('invoices', require('faveoBilling/components/Billing/Invoices'));

store.dispatch('deleteUser');
store.dispatch('updateUser');

const app = new Vue({
    el: '#app-billing',
    store: store,
});
