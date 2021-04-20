<template>

    <div>

        <alert componentName="taskView"/>

        <div class="card card-light">

            <div class="card-header">

                <h3 class="card-title">{{lang('associated_tasks') }}</h3>

                <div class="card-tools">

                    <a id="associates_tab" class="btn-tool" @click="refreshTable"
                       v-tooltip="lang('refresh')">

                        <i class="fas fa-sync"></i>
                    </a>
                </div>
            </div>

            <div class="card-body" id="associated_tasks">
                <data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="problem-list"></data-table>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">

    import Vue from 'vue';

    import { mapGetters } from 'vuex'

    Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));
    Vue.component('task-status', require('./TaskStatus'));

    export default {

        props: ['ticketProp'],

        data: () => ({

            columns: ['task_name', 'task_template', 'status', 'task_start_date', 'task_end_date', 'assigned', 'action'],

            options: {},

            apiUrl:'',

            hideData : '',

            ticketId: ''
        }),

        computed :{

            ...mapGetters(['formattedTime','formattedDate','getStoredTicketId'])
        },

        watch:{

            getStoredTicketId(newValue,oldValue){

                this.apiUrl = '/tasks/api/get-all-ticket-tasks?ticket_id='+newValue
            }
        },

        created() {
            window.eventHub.$on('refreshData',this.updateSideBar);
        },

        beforeMount() {
            if(this.ticketProp)
                this.apiUrl = '/tasks/api/get-all-ticket-tasks?ticket_id='+this.getStoredTicketId;

            const self = this;

            this.options = {

                headings: {
                    task_name: 'Name', task_template : 'Task Template',
                    status:'Status', task_start_date: 'Start Date',
                    action:'Actions', task_end_date: 'Due Date', assigned: 'Assignee(s)'
                },

                columnsClasses : {

                    task_name: 'task-name',

                    task_start_date:'task-date',

                    task_end_date: 'task-date',

                    status: 'task-status',

                    action: 'task-action',

                    task_template: 'task-template'

                },

                sortIcon: {

                    base : 'glyphicon',

                    up: 'glyphicon-chevron-up',

                    down: 'glyphicon-chevron-down'
                },

                texts: { filter: '', limit: '' },

                templates: {

                    task_start_date(h,row) {
                        return self.formattedTime(row.task_start_date)
                    },

                    task_template(h,row) {
                      return (row.task_template) ? row.task_template.name : '--';
                    },

                    task_end_date(h,row) {
                        return self.formattedTime(row.task_end_date)
                    },

                    status: 'task-status',

                    assigned: 'table-list-elements',

                    action: 'table-actions'
                },

                sortable:  ['task_name', 'task_start_date','task_end_date'],

                filterable:  ['task_name'],

                pagination:{chunk:5,nav: 'fixed',edge:true},

                requestAdapter(data) {

                    return {

                        'sort_field' : data.orderBy ? data.orderBy : 'id',

                        'sort_order' : data.ascending ? 'desc' : 'asc',

                        'search_term' : data.query,

                        page : data.page,

                        limit : data.limit,
                    }
                },

                responseAdapter({data}) {

                    return {
                        data: data.data.tasks.map(data => {

                            data.view_url = self.basePath() + '/tasks/task/' + data.id;

                            data.edit_url = self.basePath() + '/tasks/task/' + data.id + '/edit';

                            data.delete_url = self.basePath() + '/tasks/task/' + data.id;

                            data.alertComponentName = 'timeline';

                            data.tableName =  'task'

                            data.listElementObj = {
                                key: 'assigned_agents',
                                redirectUrl: self.basePath() + '/user/'
                            }

                            return data;
                        }),
                        count: data.data.total
                    }
                },
            }
        },

        methods:{

            refreshTable() {

                window.eventHub.$emit('refreshData');
            },

            updateSideBar() {
                window.eventHub.$emit('update-sidebar');
            },

        },

        components : {

            "data-table" : require('components/Extra/DataTable'),

            "alert": require("components/MiniComponent/Alert"),


        }
    };
</script>

<style>

    .task-name,.task-date,.task-action,.task-status,.task-description,.task-assigned{ max-width: 250px; word-break: break-all;}

    #associated_tasks .VueTables .table-responsive {
        overflow-x: auto;
    }

    #associated_tasks .VueTables .table-responsive > table{
        width : max-content;
        min-width : 100%;
        max-width : max-content;
        overflow: auto !important;
    }

    #associates_tab{ cursor: pointer; }

</style>
