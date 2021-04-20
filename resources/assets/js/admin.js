import Vue from 'vue';

require('./bootstrap');

var moment = require('moment');

import 'es6-promise/auto';

import { store } from 'store';

import faveoAutomator from './../store/modules/faveoAutomator';
import formBuilderStore from './../store/modules/formBuilderStore';
store.registerModule('faveoAutomator', faveoAutomator);
store.registerModule('formBuilderStore', formBuilderStore);

import { lang } from 'helpers/extraLogics'

import CKEditor from '@ckeditor/ckeditor5-vue';

Vue.use( CKEditor );

Vue.use(require("vue-simple-uploader"));

Vue.use(require('vddl'));

import VTooltip from 'v-tooltip'

Vue.use(VTooltip);

Vue.component('form-group-list', require('components/Admin/Manage/FormGroups/FormGroupList.vue'));

Vue.component('form-builder-main', require('./components/Admin/Manage/FormBuilder/FormBuilderMain.vue'));

Vue.component('faveo-automator', require('./components/Admin/Automator/FaveoAutomator.vue'));

Vue.component('faveo-form', require('./components/Common/Form/FaveoForm.vue'));

Vue.component('form-renderer', require('./components/Common/Form/FormRenderer'));

Vue.component('email-settings', require('./components/Admin/Emails/EmailSettings.vue'));

// for time track settings component
Vue.component('timetrack-settings', require('./components/Admin/TimeTrack/TimeTrackSettings.vue'));

// for business hours create
Vue.component('business-hours', require('./components/Admin/Manage/BusinessHours/BusinessHours.vue'));

// for business hours table
Vue.component('business-hours-index', require('./components/Admin/Manage/BusinessHours/BusinessHoursIndex.vue'));

// for approval workflow component
Vue.component('approval-workflow-index', require('./components/Admin/ApprovalWorkflow/ApprovalWorkflowIndex.vue'));

// for workflow component
Vue.component('workflow-index', require('./components/Admin/Workflow/WorkflowIndex.vue'));

// for listener component
Vue.component('listener-index', require('./components/Admin/Workflow/ListenerIndex.vue'));

Vue.component('approval-workflow', require('./components/Admin/ApprovalWorkflow/ApprovalWorkflow.vue'));

// registering agent-list component
Vue.component('agent-list', require('./components/Admin/Staff/Agent/AgentList.vue'));

// registering create-update-agent component
Vue.component('create-update-agent', require('./components/Admin/Staff/Agent/CreateUpdateAgent.vue'));

//Company settings page
Vue.component('company-settings', require('./components/Admin/Settings/Company/CompanySettings.vue'));

// System backup component
Vue.component('system-backup-list', require('./components/Admin/Settings/SystemBackup/SystemBackupList.vue'));

Vue.component('draggable-form-field-item', require('./components/Admin/Manage/FormBuilder/DraggableFormFieldItem.vue'));

Vue.component('sla-index', require('./components/Admin/Manage/SlaPlans/SlaIndex.vue'));

Vue.component('sla-create-edit', require('./components/Admin/Manage/SlaPlans/SlaCreateEdit.vue'));

Vue.component('plugins-page',require('./components/Admin/Plugins/PluginsPage.vue'));

Vue.component('import-upload',require('./components/Admin/Import/Upload.vue'));



Vue.component('widget-list', require('./components/Admin/Manage/Widgets/WidgetList.vue'));

Vue.component('social-widget-list', require('./components/Admin/Manage/SocialWidget/SocialList.vue'));

Vue.component('recaptcha-settings', require('./components/Admin/Settings/ReCaptcha/ReCaptchaSettings.vue'));

Vue.component('web-socket-settings', require('./components/Admin/Settings/WebSocket/WebSocketSettings.vue'));

Vue.component('close-ticket-workflow', require('./components/Admin/Tickets/CloseTicketWorkflow/CloseTicketWorkflow.vue'));

//TICKET SETTINGS
Vue.component('ticket-settings', require('./components/Admin/Tickets/Settings/TicketSettings.vue'))

//Emails
Vue.component('emails-index', require('./components/Admin/Emails/EmailsIndex.vue'));

Vue.component('email-diagnostics', require('./components/Admin/Emails/Diagnostics/EmailDiagnostics.vue'));

Vue.component('form-renderer', require('./components/Common/Form/FormRenderer'))

Vue.component('web-socket-settings', require('./components/Admin/Settings/WebSocket/WebSocketSettings.vue'))

Vue.component('file-system-settings',require('./components/Admin/Settings/FileSystem/FileSystemSettings'));

//User Options
Vue.component('user-options', require('./components/Admin/Settings/UserOptions/UserOptions.vue'));

Vue.component('api-settings', require('./components/Admin/Settings/Api/ApiSettings.vue'));

Vue.component('web-hook-settings', require('./components/Admin/Settings/WebHook/WebHookSettings.vue'));

//System settings page
Vue.component('system-settings', require('./components/Admin/Settings/System/SystemSettings.vue'));

Vue.component('updates-list', require('./components/HeaderNavigation/Updates/UpdatesList.vue'));


new Vue({
    el: '#app-admin',
    store: store
});
