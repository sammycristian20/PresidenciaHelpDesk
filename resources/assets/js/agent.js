require('./bootstrap');

require('../css/dashboardCommon.css');

import 'es6-promise/auto';

require('select2');

var moment = require('moment');

import Vue from 'vue';

import Popover from 'vue-js-popover';

Vue.use(Popover,{ tooltip: true });

import VueRouter from 'vue-router';

Vue.use(require("vuejs-uib-pagination"));

Vue.use(require("vue-simple-uploader"));

import {store} from './../store/store'

import StarRating from 'vue-star-rating'
Vue.component('star-rating', StarRating);

import CKEditor from '@ckeditor/ckeditor5-vue';

Vue.use( CKEditor );

import InfiniteLoading from 'vue-infinite-loading';

Vue.use(InfiniteLoading);

import VTooltip from 'v-tooltip'

Vue.use(VTooltip);

// for loader
import { FulfillingBouncingCircleSpinner } from 'epic-spinners';

/*
For File Manager
 */

import fileManagerStore from 'components/Common/Media/store';

import FileManager from 'components/Common/Media/FileManager';

Vue.component('file-manager', FileManager)

store.registerModule('fm', fileManagerStore);

// import FileManager from 'laravel-file-manager';
// Vue.use(FileManager, {store});
//
import FileManagerContainer from "./components/Agent/Filemanager/FileManagerContainer";
Vue.component('file-manager-container', FileManagerContainer);

Vue.component('fulfilling-bouncing-circle-spinner', FulfillingBouncingCircleSpinner);

//Agent Panel

Vue.component('alert-notification', require('./components/Common/pageNotification.vue'));

// Kb
Vue.component('articles-list', require('./components/Agent/kb/article/index.vue'));

Vue.component('comments-list', require('./components/Agent/kb/comment/comments.vue'));

Vue.component('kb-settings', require('./components/Agent/kb/settings/kbSettings'));

Vue.component('user-info', require('./components/Common/UserInfo.vue'));

Vue.component('alert-notification', require('./components/Common/pageNotification.vue'));

Vue.component('report-exports', require('./components/Agent/Report/Exports.vue'));

Vue.component('articles', require('components/Agent/kb/article/Create/Article.vue'));

Vue.component('article-template', require('components/Agent/kb/article/ArticleTemplate/ArticleTemplate.vue'));

Vue.component('category', require('components/Agent/kb/Category/Category.vue'));

Vue.component('article-template-index', require('./components/Agent/kb/article/ArticleTemplate/ArticleTemplateIndex.vue'));

Vue.component('category-index', require('./components/Agent/kb/Category/CategoryIndex.vue'));

Vue.component('pages', require('components/Agent/kb/Pages/Pages.vue'));

Vue.component('pages-index', require('components/Agent/kb/Pages/PagesIndex.vue'));

Vue.component('faveo-form', require('./components/Common/Form/FaveoForm.vue'));

Vue.component('form-renderer', require('./components/Common/Form/FormRenderer'));

// Recurring tickets
Vue.component('recurring-tickets-index', require('./components/Agent/Tools/Recurring/RecurringTicketsIndex.vue'));

// Tools => Canned response module
Vue.component('canned-response-directory', require('./components/Agent/Tools/Canned/CannedResponse.vue'));

Vue.component('canned-response', require('./components/Agent//Tools/Canned/CannedCreateEdit.vue'));

// Organization module
Vue.component('org-directory', require('./components/Agent/Organization/OrganizationDirectory.vue'));

Vue.component('org-view', require('./components/Agent/Organization/OrganizationView.vue'));

// User module
Vue.component('user-directory', require('./components/Agent/User/UserDirectory.vue'));

Vue.component('user-export', require('./components/Agent/User/UserExport.vue'));

Vue.component('user-view', require('./components/Agent/User/UserView.vue'));

Vue.component('agent-profile-view', require('./components/Agent/Profile/AgentProfile.vue'));

Vue.component('agent-profile-edit', require('./components/Agent/Profile/AgentProfileEdit.vue'));

Vue.component('dashboard-page', require('./components/Agent/Dashboard/DashboardPage.vue'));

// Inbox New
Vue.component('inbox-layout', require('./components/Agent/Inbox/InboxLayout.vue'));

Vue.component('inbox-timeline', require('./components/Agent/Inbox/View/TicketTimeline'));

store.dispatch('setRatingTypes');

new Vue({
    el: '#app-agent',
    store: store
});
