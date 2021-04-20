<template>
    <div>
        <loader v-if="loading" animation-duration="4000" color="#1d78ff" size="60" />
        <alert componentName="taskCreate" v-if="popup" />

        <div class="row">

            <text-field :label="lang('task_name')"
                :onChange="onChange" :value="task_name"
                type="text" name="task_name" classname="col-sm-6"
                :required=required   id="task_name"
            />

            <div class="col-sm-6">
                <label>{{lang('task_type') }}</label><br>
                <label class="radio-inline control-label">
                    <input class="radioMargin" type="radio" value="1" v-model="is_private" :disabled="disableRadioInput">
                    <span class="font-weight-normal">&nbsp;{{lang('private')}}</span>
                </label>&nbsp;&nbsp;
                <label class="radio-inline control-label">
                    <input class="radioMargin" type="radio" value="0" v-model="is_private">
                    <span class="font-weight-normal">&nbsp;{{lang('public')}}</span>
                </label>
            </div>

        </div> <!--row-->

        <div class="row">

            <date-time-field
                    :label="lang('start_at')"
                    :value="task_start_date"
                    type="datetime"
                    name="task_start_date"
                    :onChange="onChange"
                    :required=required
                    format="MMMM Do YYYY, h:mm a"
                    classname="col-sm-6"
                    :clearable="true"
                    :disabled="false"
                    :editable="true"
                    :pickers="true"
                    :range="false"
                    :currentYearDate="false"
                    :time-picker-options="timeOptions"
                    :isDateSelector="false"
                    :numberStyle="numberStyle"
                    :confirm="true"
            />

            <date-time-field
                    :label="lang('ends_at')"
                    :value="task_end_date"
                    type="datetime"
                    name="task_end_date"
                    :onChange="onChange"
                    :required=required
                    format="MMMM Do YYYY, h:mm a"
                    classname="col-sm-6"
                    :clearable="true"
                    :disabled="false"
                    :editable="true"
                    :pickers="true"
                    :range="false"
                    :currentYearDate="false"
                    :time-picker-options="timeOptions"
                    :isDateSelector="false"
                    :numberStyle="numberStyle"
                    :confirm="true"
            />

        </div> <!--row-->

        <div class="row">

            <dynamic-select
                    name="status" classname="col-sm-6"
                    :elements="elementTaskStatus" :multiple="false"
                    :label="lang('task_status')" :clearble="false"
                    :value="status" :onChange="onChange"
                    :searchable="false" :required=required
            />


            <dynamic-select
                    name="task_category_id" classname="col-sm-6"
                    apiEndpoint="tasks/api/category/view" :multiple="false"
                    :label="lang('task_list')"
                    :value="task_category_id" :onChange="onChange"
                    :searchable="true"
            />

        </div> <!--row-->

        <div class="row">

            <dynamic-select
                    name="assignee" classname="col-sm-6"
                    :multiple="true" apiEndpoint="api/dependency/agents?meta=true"
                    :label="lang('assignee')" v-if="requiredForPrivate"
                    :value="assignee" :onChange="onChange"
                    :searchable="true" :required=required
            />

            <dynamic-select
                    name="associated_ticket" classname="col-sm-6"
                    :multiple="false" apiEndpoint="api/dependency/tickets"
                    :label="lang('link_ticket')" v-if="requiredForPrivate"
                    :value="associated_ticket" :onChange="onChange"
                    :searchable="true" :required=required
            />

        </div> <!--row-->

        <div class="row">

            <text-field rows="5" cols="1" :label="lang('task_description')"
                :onChange="onChange" :value="task_description"
                type="textarea" name="task_description" classname="col-sm-12 resizeTextArea"
                :required="false"  id="task_description"
            />

        </div> <!--row-->

        <div class="row mt-5">
            <div class="col-sm-2">
                <button v-if=" true !== popup" class="btn btn-primary" @click="taskSubmit"><i class="fa fa-save"></i>&nbsp;{{ lang('save') }}</button>
            </div>
        </div> <!--row-->

    </div> <!--main-->

</template>


<script>

    import axios from 'axios'
    import {errorHandler, successHandler} from 'helpers/responseHandler'
    import { getSubStringValue } from 'helpers/extraLogics';
    import { TaskMixin } from "../TaskMixin";
    import {mapGetters} from "vuex";
    import {boolean} from "../../../../../../resources/assets/js/helpers/extraLogics";

    export default {

        props : {

            ticketId : { type : String | Number, default : '' },

            popup : { type : Boolean , default : false },

            alertName : { type : String, default : 'taskCreate'},

            onComplete : { type : Function }
        },

        mixins: [TaskMixin],

        computed : {
            requiredForPrivate() {
              return !parseInt(this.is_private);
            },
            disableRadioInput() {
                return Boolean(this.ticketId)
            },
            ...mapGetters(['formattedTime','formattedDate'])
        },

        beforeMount() {
            if (this.isThisEditForm()) {
                this.getTaskDetails()
            }
            if (this.ticketId) {
               this.getSelectedTicket(this.ticketId)
            }
        },

        components: {
            "text-field"     : require("components/MiniComponent/FormField/TextField"),
            'alert'          : require('components/MiniComponent/Alert'),
            "loader"         : require("components/MiniComponent/Loader"),
            'dynamic-select' : require("components/MiniComponent/FormField/DynamicSelect"),
            'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
        },

        methods: {
            /**
             * Gets the ticket which should be selected by default when creating from ticket inbox
             */
            getSelectedTicket(ticketId) {
                if(ticketId) {
                    axios.get('api/dependency/tickets?ids[0]='+ticketId)
                    .then((res) => {
                        let data = res.data.data.tickets[0];
                        this.associated_ticket = {
                            id  : data.id,
                            name: data.name
                        }
                    }).catch((err) => {
                        this.associated_ticket = undefined;
                    });
                }
            },
            
            /**
             * Gets tickets as options
             */
            getTicketElements() {
                axios.get('api/dependency/tickets')
                .then((res) => {
                    this.ticketElements = flatten(res.data.data)
                });

                setTimeout(()=>{
                    this.associated_ticket = this.ticketId;
                },500);
            },

            getTaskDetails() {

                axios.get('/tasks/api/get-task-by-id/'+this.taskId)
                    .then((res)=>{
                        let data = res.data.data;
                        this.task_name = data.task_name;
                        this.task_description = data.task_description;
                        this.task_start_date = data.task_start_date;
                        this.task_end_date =  data.task_end_date;
                        this.due_alert = this.filterValues(this.elementsTaskAlert, JSON.parse(data.due_alert_text));
                        this.task_category_id = data.task_category
                        this.assignee = data.assigned_agents;
                        this.getSelectedTicket(data.ticket_id);
                        this.is_private = data.is_private;
                        this.status = {name: data.status,value:data.status}
                    })

            },

            filterValues(array,value) {
                return array.filter(x => {
                    if(x.value === value)
                        return x;
                })[0];
            },

            /**
             * Gets the agents which should be selected by default
             */
            getAssignees(data) {
                let apiEndPoint = 'api/dependency/agents?';
                for (let i in data) {
                    apiEndPoint += 'ids[' + i + ']=' + data[i].user_id + "&";

                }
                axios.get(apiEndPoint).then((res) => {
                    let data = res.data.data.agents;
                    this.assignee = [];
                    for (const element of data) {
                        let names = {};
                        names['name'] = element.name;
                        names['id'] = element.id;
                        this.assignee.push(names);
                    }
                });
            },

            /*
            * Verifies  the form is edit form or create form*/
            isThisEditForm() {
                let path = window.location.pathname.split('/');
                if(path[path.length-1] === 'edit'){
                    this.taskId = path[path.length-2];
                    this.mode = 'edit';
                    return true;
                }
                return false;
            },

            /**
             * Default onChange accepted by dynamic-select
             */
            onChange(value, name){
                this[name]= value;
            },

            subString(value){
                return getSubStringValue(value,25)
            },

            taskSubmit() {
                let url,method;

                if (parseInt(this.is_private)) {
                    this.assignee = [];
                    this.associated_ticket = undefined;
                }
                let formObj = {
                    task_start_date: this.task_start_date,
                    task_end_date: this.task_end_date,
                    due_alert: (this.due_alert) ? this.due_alert.value : null,
                    associated_ticket: (this.associated_ticket) ? this.associated_ticket.id : null,
                    assignee: (this.assignee) ? this.assignee.map(value => value.id) : [],
                    task_name: this.task_name,
                    task_description: this.task_description,
                    is_private: this.is_private,
                    task_category_id: (this.task_category_id) ? this.task_category_id.id : null,
                    status: (this.status) ? this.status.value : null
                }

                this.loading = true;

                if(this.mode === 'edit') {
                    url = '/tasks/task/'+this.taskId;
                    method = 'PUT';
                } else {
                    url = '/tasks/task';
                    method = 'POST';
                }
                axios.request({
                    method,
                    url,
                    data: formObj
                })
                .then((res)=>{
                    this.loading = false;
                    successHandler(res,this.alertName);

                    if(this.popup){
                        this.onComplete();
                        window.eventHub.$emit('refreshData');
                    } else if(this.mode === 'edit') {
                        this.$store.dispatch('unsetValidationError');
                        this.getTaskDetails();
                        window.eventHub.$emit('update-sidebar')
                    }
                    else {
                        this.redirect('/tasks/task?category=all')
                    }
                })
                .catch((err)=>{
                    this.loading = false;
                    errorHandler(err,this.alertName);
                })
            }
        }
    };

</script>


<style>

    .mt-5 {
        margin-top: 20px;
    }

    input.radioMargin {
      margin-top: 1px !important;
      vertical-align: middle;
    }

    textarea#task_description {
        resize: vertical !important;
    }

</style>

