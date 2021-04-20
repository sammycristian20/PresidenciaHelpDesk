let bootstrap = require('bootstrap');
import {store} from 'store';

new Vue({
    el: '#whatsapp',
    store,
    components: {
        'settings' : require('./components/WhatsappSettings.vue'),
    }
})