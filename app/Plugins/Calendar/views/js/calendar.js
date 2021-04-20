let bootstrap = require('bootstrap');

import {store} from 'store';

import VueScrollTo from 'vue-scrollto';

Vue.use(VueScrollTo);

bootstrap.injectComponentIntoView('associated-task-list', require('./components/AssociatedTasks.vue'),"ticket-timeline-mounted-tasks",'timeline-display-box-tasks');

bootstrap.injectComponentIntoView('task-ticket-actions', require('./components/TaskTicketActions.vue'),'timeline-action-div-mounted','timeline-action-div');

const vm = new Vue({
    el: '#calendar-view',
    store,
    components: {
        'task-create'          : require('./components/TaskCreateEdit.vue'),
        'task-individual-view' : require('./components/TaskIndividualView.vue'),
        'task-view'            : require('./components/TaskView.vue'),
        'task-create-wrapper'  : require('./components/TaskCreateWrapper.vue'),
        'task-settings'        : require('./components/TaskSettings.vue'),
        'project-edit'         : require('./components/ProjectEdit.vue'),
        'tasklist-edit'        : require('./components/TaskListEdit.vue'),
        'task-template-settings' : require('./components/TaskTemplate'),
        'task-template-create-edit' : require('./components/TaskTemplateCreateEdit'),
    }
});
