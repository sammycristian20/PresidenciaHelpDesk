 
window._ = require('lodash-core');

require('../css/common.scss');
require('../css/dynamicSelectCommon.css');
require('../css/tooltip.css');

require('../css/tooltip.css');

import "vue-select/src/scss/vue-select.scss";

window.Vue = require('vue');

window.eventHub = new Vue();

import 'es6-promise/auto';

import {store} from 'store';
import {boolean} from 'helpers/extraLogics';
import {lang} from 'helpers/extraLogics';
 import { getSubStringValue } from 'helpers/extraLogics'
/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

import Vue from 'vue';

try {
    window.$ = window.jQuery = require('jquery');
    require('daterangepicker');

} catch (e) { }

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

/**
 * Cache object for the request object
 */
var requestObjectCache = null;

/**
 * Clear the request cache object after 500 miliseconds
 */
var clearRequestCache = function() {
  setTimeout(() => {
    requestObjectCache = null;
  }, 500);
}

/**
 * AXIOS Request Interceptor
 * 
 * This interceptor is used for rejecting duplicating API call
 * The time window is 250 miliseconds
 * 
 */
window.axios.interceptors.request.use((request) => {
  /**
   * Allow request if
   * 1. it's first API call of the App
   * 2. request object doesn't match the cache object
   * 3. requested same API after 250 miliseconds
   */
  if (requestObjectCache === null || JSON.stringify(request) !== requestObjectCache) {
    requestObjectCache = JSON.stringify(request);
    clearRequestCache();
    return request;
  } else { // Reject the API call
    return Promise.reject({
      duplicateRequestRejection: true,
      message: 'Request rejected because of duplicate API call'
    });
  }
});

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = document.head.querySelector('meta[name="api-base-url"]').content;


/*
  uncomment these lines once modularity is implemented.
  this will add auth token to the header of each request sent from the code
  SOME ASSUMPTIONS WHICH CAN BE CONSIDERED :
 */

// window.axios.defaults.headers.common['Auth-Token'] = getAuthToken();

/**
 * gets token from localStorage
 * WARNING : storing tokens in cookies are vulnerable to csrf attacks, so store it in localstorage
 * Localstorage is vulnerable to XSS attacks.
 *
 * Our current architecture doesn't have much xss vulnerabilities(and will be gone in future)
 * as we are using vue, and even if someone injects javascipt in the DB it will not be rendered
 * as javascript until it is explicitly mentioned in the code
 *
 * @return {string} 	auth token
 */
// function getAuthToken(){
// 	//get from localStorage
// }


// update token every 30 mins
setInterval(keepPageAlive, 30 * 60 * 1000); // every 30 mins it will regenate the csrf token even if page is idle

function keepPageAlive() {
    axios.post("/keep-page-alive");
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     //encrypted: true
// });



/**
 * injects components into a container when passed event is triggered
 * NOTE: Will be used to inject html and javascipt code into existing view. Most important used for bilding faveo plugins
 *
 * @param  {String} componentName     Name of the component that you want to inject
 * @param  {Any} componentInstance    Vue component instance. For eg. require('path/to/the/component')
 * @param  {String} eventName         Name of the event to which this component has to be injected
 * @param  {String} containerId     Id of the container(for eg. id of the div) where component has to be injected
 * @return undefined
 */
export const injectComponentIntoView = (componentName ,componentInstance, eventName, containerId) => {

    // register an event, as soon as that event comes, append the component into it
    window.eventHub.$on( eventName, (data) => {
        // console.log('========',eventName, componentName ,componentInstance, eventName, containerId)
        // what to do if extra parameters is passed with the event ?
        // SOLUTION 1 : emit another event with that data that can be observed by the component
        // SOLUTION 2 : pass that as prop (prefer more)

        // making the appending asychronous so that appending happens only after div is present in the DOM
        setTimeout(()=>{

          let container = document.getElementById(containerId);

          // so that it appends only once
          if(container !== null && document.getElementById(componentName) === null){
              let parentNode = document.createElement('div');
              parentNode.setAttribute('id', componentName);

            let node = document.createElement(componentName);

            // before appending make sure to pass props
            node.setAttribute('data', JSON.stringify(data));

              parentNode.appendChild(node);
              container.appendChild(parentNode);

            let component = {};
            component[componentName] = componentInstance;

            console.log(new Vue({
                el: '#'+ componentName,
                store: store,
                components: component,
            }));
          }
        },100)

    });
}


//fetching language file from server and declaring that as global prop
//if file doesn't have the passed key, it is going to return string
Vue.prototype.lang = lang;

// gives basePath
Vue.prototype.basePath = () => (window.axios.defaults.baseURL)

// gives if something is true of false based on PHP creteria
Vue.prototype.boolean = (value) => (boolean(value))

Vue.mixin({
  methods: {
      currentPath: () => (window.location.pathname),
      basePath : () => (window.axios.defaults.baseURL),
      redirect : (path) => {window.location = window.axios.defaults.baseURL + path},
      sleep :  (ms) => new Promise(resolve => setTimeout(resolve, ms)),
      trans: (string) => lang(string)
  },

    data: () =>({
        isRtlLayout : boolean(sessionStorage.getItem('is_rtl')),
        headerColor:  sessionStorage.getItem('header_color'),
        recaptchaSiteKey: sessionStorage.getItem('siteKey'),
        recaptchaVersion: sessionStorage.getItem('version'),
        recaptchaApplyfor: sessionStorage.getItem('applyfor'),
    })
});

Vue.filter('checkValue', function (value,length) {
  
  if (!value) return '---'
  
  return value.name ? getSubStringValue(value.name,length) : getSubStringValue(value,length)
})

/**
 * For show/hide Recaptcha in pages
 */
Vue.directive("captcha", function(el, binding, vnode) {

  let value = binding.value;

  if(value) {

    if(sessionStorage.getItem('applyfor').includes(value)) {
      
      el.style.display = "block";      
    
    } else { el.style.display = "none";  }
  
  } else {

    el.style.display = "block"; 
  }
});


//=============================================== vee-validate start ===============================================//


import { ValidationObserver, ValidationProvider, extend, localize } from 'vee-validate';
import * as rules from 'vee-validate/dist/rules';

import { configure } from 'vee-validate';

configure({
  classes: {
    invalid: 'field-danger',
  }
})

Object.keys(rules).forEach(rule => {
  extend(rule, rules[rule]);
});

localize({
  en: {
    messages: {
      required: () => lang('this_field_is_required'),
      email: () => lang('invalid_email'),
      numeric : () => lang('invalid_number'),
      date_format: () => lang('invalid_date_format'),
      max: () => lang('max_length_exceeded'),
      regex: () => lang('invalid_field_format')
    }
  }
});

// Install components globally
Vue.component('ValidationObserver', ValidationObserver);
Vue.component('ValidationProvider', ValidationProvider);

//=============================================== vee-validate end ===============================================//



require('tinymce');
require('tinymce/themes/silver');
require('tinymce/plugins/advlist');
require('tinymce/plugins/fullscreen');
require('tinymce/plugins/wordcount');
require('tinymce/icons/default');
require('tinymce/plugins/image');
require('tinymce/plugins/autolink');
require('tinymce/plugins/lists');
require('tinymce/plugins/link');
require('tinymce/plugins/charmap');
require('tinymce/plugins/print');
require('tinymce/plugins/preview');
require('tinymce/plugins/anchor');
require('tinymce/plugins/searchreplace');
require('tinymce/plugins/visualblocks');
require('tinymce/plugins/code');
require('tinymce/plugins/insertdatetime');
require('tinymce/plugins/media');
require('tinymce/plugins/table');
require('tinymce/plugins/paste');
require('tinymce/plugins/help');
require('tinymce/plugins/importcss');
require('tinymce/plugins/autosave');
require('tinymce/plugins/directionality');
require('tinymce/plugins/visualchars');
require('tinymce/plugins/hr');
require('tinymce/plugins/pagebreak');
require('tinymce/plugins/nonbreaking');
require('tinymce/plugins/toc');
require('tinymce/plugins/imagetools');
require('tinymce/plugins/textpattern');
require('tinymce/plugins/noneditable');
require('tinymce/plugins/quickbars');
require('tinymce/plugins/template');
require('tinymce/plugins/codesample');

// Install components globally
Vue.component('tiny-editor', require('./components/Common/tinyMCE/TinyMCE'))
Vue.component('tiny-editor-with-validation', require('./components/Common/tinyMCE/TinyMCEWithValidation'))

// Observer for Infinite Scrolling
import { ObserveVisibility } from 'vue-observe-visibility'

Vue.directive('observe-visibility', ObserveVisibility)
