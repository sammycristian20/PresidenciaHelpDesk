<template>
    <div>

        <Loader v-if="loading" :animation-duration="4000" color="#1d78ff" :size="60"/>
        <Alert componentName="taskEdit" />

        <div class="card card-light ">
            <div class="card-header">
                <h3 class="card-title">{{ lang('task_edit') }}</h3>
            </div>
            <div class="card-body">

                    <div class="row">

                        <div class="col-sm-6">

                            <TextField 
                            :label="lang('task_name')" 
                            :onChange="onChange" 
                            :value="task_name" 
                            type="text" 
                            name="task_name" 
                            :required=required   
                            id="task_name"
                            >
					        </TextField>

                        </div>

                        <div class="col-sm-6">

                            <TextField rows="1" 
                            :label="lang('task_description')" 
                            :onChange="onChange" 
                            :value="task_description" 
                            type="textarea" 
                            name="task_description" 
                            :required=required  
                            id="task_description">
					        </TextField>

                        </div>

                    </div> <!--row-->

                    <div class="row">
                        <div class="col-sm-3">
                          <label>{{ lang('start_at') }} <span class="text-red">*</span></label>
                            <DatePicker 
                                type="datetime" 
                                v-model="start_at"
                                format="YYYY-MM-DD HH:mm:ss" 
                                :clearable="true" 
                                lang="en" 
                                :editable="true"
                                pickers="true"
                                currentYearDate="false" 
                                :time-picker-options="timeOptions"
                                placeholder="Task Starts On"
                                >
                            </DatePicker>

                        </div>

                        <div class="col-sm-3">
                          <label>{{ lang('ends_at') }} <span class="text-red">*</span></label>
                             <DatePicker 
                                type="datetime" 
                                v-model="ends_at"
                                format="YYYY-MM-DD HH:mm:ss" 
                                :clearable="true" 
                                lang="en" 
                                :editable="true"
                                :pickers="true"
                                currentYearDate="false" 
                                :time-picker-options="timeOptions"
                                placeholder="Task Ends On"
                                valueType="format"
                                >
                            </DatePicker>

                        </div>


                        <div class="col-sm-6">

                            <label>{{ lang('task_alert') }} </label>
                            <vSelect 
                                :multiple="false"
                                :options="elements_task_alert"
                                item-text="name"
                                item-value="value"
                                :disabled="false"
                                placeholder="Select"
                                :filterable="true"
                                :clearable="false"
                                label="name"
                                v-model="task_alert"
                                class="faveo-dynamic-select"
                            >
                            </vSelect>

                        </div>
                    </div> <!--row-->

                   <div class="row">
                        <div class="col-sm-6">

                            <label>{{ lang('alert_repeat') }}</label>
                            <vSelect 
                                :multiple="false"
                                :options="elements_alert_repeat"
                                item-text="name"
                                item-value="value"
                                :disabled="false"
                                placeholder="Search or Select"
                                :filterable="true"
                                :clearable="false"
                                label="name"
                                v-model="alert_repeat"
                                class="faveo-dynamic-select"
                            >
                            </vSelect>

                        </div>

                        <div class="col-sm-6">
                            <label>{{ lang('assignee') }}</label>

                            <vSelect 
                                :multiple="true"
                                :options="agentElements"
                                item-text="name"
                                item-value="id"
                                :disabled="false"
                                placeholder="Search or Select"
                                :filterable="true"
                                :clearable="true"
                                label="name"
                                v-model="agentSelections"
                                class="faveo-dynamic-select"
						    >
                            </vSelect>
                            

                        </div>
                   </div> <!--row-->

                    <div class="row mt-10">

                        <div class="col-sm-6">

                            <label>{{ lang('link-ticket') }}</label>

                            <vSelect 
                                :multiple="false"
                                :options="ticketElements"
                                item-text="name"
                                item-value="id"
                                :disabled="false"
                                placeholder="Search or Select"
                                :filterable="true"
                                label="name"
                                :clearable="true"
                                v-model="defaultSelectedTicket"
                                :onChange="onVChange"
                                class="faveo-dynamic-select"
						    >
                            </vSelect>

                        </div>
                        
                        <div class="col-sm-6">

                            <label>{{ lang('task_list') }} <span class="text-red">*</span></label>

                            <vSelect 
                                :multiple="false"
                                :options="tasklistElements"
                                item-text="name"
                                item-value="id"
                                :disabled="false"
                                placeholder="Search or Select"
                                :filterable="true"
                                :clearable="false"
                                label="name"
                                v-model="taskList"
                                class="faveo-dynamic-select"
						    >
                            </vSelect>


                        </div>

                    </div> <!--row-->

                    <div class="row mt-10">
                        <div class="col-sm-6">
                            <label>{{lang('task_type') }}</label><br>
                            <label class="radio-inline">
                                <input type="radio" value="1" v-model="is_private">{{lang('private')}}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" value="0" v-model="is_private">{{lang('public')}}
                            </label>
                        </div>    
                    </div> <!--row-->
            </div>

            <div class="card-footer">
                
                <button class="btn btn-primary" @click="taskSubmit"><i class="fas fa-save"></i>&nbsp;{{ lang('save') }}</button>
            </div> <!--row-->
        </div>
    </div>
</template>


<script>

    import DatePicker from 'vue2-datepicker';
    import axios from 'axios'
    import {flatten} from "helpers/extraLogics";
    import {errorHandler, successHandler} from 'helpers/responseHandler'
    import TextField from "components/MiniComponent/FormField/TextField";
    import vSelect from "vue-select";
    import Alert from "components/MiniComponent/Alert";
    import { getIdFromUrl,getSubStringValue } from 'helpers/extraLogics';
    import { TaskMixin } from "../TaskMixin";
    import TaskActions from './TaskActions';
    import moment from 'moment';
    import Loader from "components/MiniComponent/Loader";
    export default {


        mixins: [TaskMixin],

        data() {
            return {
                loading : false
            }
        },

        beforeMount() {

            this.getTaskDetails();
            this.getTicketElements();
            this.getAgentElements();
            this.getTasklistElements();
            
        },

        components: {
            TextField,DatePicker,Alert,vSelect,TaskActions,Loader
        },

        methods: {

            onVChange(value) {
               this.associated_ticket = value.id
            },

            getTaskDetails() {

                this.taskId = getIdFromUrl(window.location.pathname);

                axios.get('/tasks/get-task-by-id/'+this.taskId)
                .then((res)=>{
                    let data = res.data;
                    this.task_name = data.task_name;
                    this.task_description = data.task_description;
                    this.start_at = moment.parseZone(data.task_start_date).format("YYYY-MM-D H:mm:ss");
                    this.ends_at =  moment.parseZone(data.task_end_date).format("YYYY-MM-D H:mm:ss");
                    this.ticketId = data.ticket_id;

                    this.task_alert = JSON.parse(data.due_alert_text);
                    this.alert_repeat = JSON.parse(data.alert_repeat_text);

                    //sets the tasklist
                    data.task_list.name = this.subString(data.task_list.name);
                    this.taskList = (data.task_list);

                    this.getAssignees(data.assigned_to);
                    this.getSelectedTicket(this.ticketId);
                    this.task = data;   
                    this.is_private = data.is_private;
                })

            },

            /**
             * Gets the ticket which should be selected by default when creating from ticket inbox
             */
            getSelectedTicket(ticket) {
                
                axios.get('api/agent/ticket-details/'+ticket)
                .then((res) => {
                    
                    let ticketSubject = res.data.data.ticket.title+ ' (' + res.data.data.ticket.ticket_number + ')';
                    this.defaultSelectedTicket = {
                        id  : ticket,
                        name:ticketSubject
                    }
                });
            },

             /**
             * Gets the agents which should be selected by default
             */
            getAssignees(data) {
                var users = [];
                var promises = [];
                for(const element of data) {
                    promises.push(
                        axios.get('/api/admin/agent/' + element.user_id).then(response => {
                        let names = {
                            id : response.data.data.agent.id,
                            name: response.data.data.agent.first_name+" "+response.data.data.agent.last_name
                        }
                        users.push(names);
                        })
                    )
                }
                Promise.all(promises).then(() => this.agentSelections = users );
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

            /**
             * Gets agents as options
             */
            getAgentElements() {
                axios.get('api/dependency/agents?meta=true')
                .then((res) => {
                    this.agentElements = flatten(res.data.data)
                });
            },

             /**
             * Gets tasklists as options
             */
            getTasklistElements() {
                axios.get('tasks/tasklist/view')
                .then((res) => {
                    this.tasklistElements = flatten(res.data.data);
                    Object.keys(this.tasklistElements).forEach( (item) => {
                        this.tasklistElements[item].name = this.subString(this.tasklistElements[item].name); 
                        console.log(this.tasklistElements[item].name)
                    });
                });
            },

            subString(value){
                return getSubStringValue(value,25)
            },

            
            /**
             * Default onChange accepted by dynamic-select
             */
            onChange(value, name){
                this[name]= value;
            },

            setAlert(msg) {
                errorHandler({
                    response: {
                        status: 400,
                        data: {
                            message: msg
                        }
                    }
                },'taskEdit');
            },

            

            taskSubmit() {

                if(!this.defaultSelectedTicket) this.associated_ticket = false;

                if(!this.task_name) {
                    this.setAlert(this.lang('task_name_empty'));
                    return;
                }
                if(!this.task_description) {
                    this.setAlert(this.lang('task_desc_empty'));
                    return;
                }

                if(!this.start_at) {
                    this.setAlert(this.lang('start_date_empty'));
                    return;
                } 

                if(!this.ends_at) {
                    this.setAlert(this.lang('end_date_empty'));
                    return;
                }

                let endDate = new Date(this.ends_at);
                let startDate = new Date(this.start_at);
                if(endDate < startDate){
                    this.setAlert(this.lang('date_error'));
                    return;
                }

                if(!this.taskList) {
                    this.setAlert(this.lang('task_list_empty'));
                    return;
                }
                
                let formObj = {
                    task_start_date: moment(this.start_at).format("YYYY-MM-D H:mm:ss"),
                    task_end_date: moment(this.ends_at).format("YYYY-MM-D H:mm:ss"),
                    due_alert: this.task_alert,
                    associated_ticket: this.associated_ticket,
                    alert_repeat: this.alert_repeat,
                    assignee: this.agentSelections,
                    task_name: this.task_name,
                    task_description: this.task_description,
                    is_private: this.is_private,
                    task_list_id: this.taskList,
                }

                this.loading = true;
                axios.put('/tasks/task/'+this.taskId,formObj)
                .then((res)=>{
                    this.loading = false;
                    successHandler(res,'taskEdit');
                    setTimeout(()=>{
                        window.location.href =  window.axios.defaults.baseURL+"/thread/"+res.data.data;
                    },1200)
                })
                .catch((err)=>{
                    this.loading = false;
                    errorHandler(err,'taskEdit');
                })    
            }
        }
    }

</script>


<style>

.mx-input{
border-radius: 0 !important;
}

.task-event{
display: inline-block;
}
.task-list{
border-bottom:1px solid; width: auto !important;    padding-bottom: 15px;padding-top: 15px;
}

.mx-datepicker{
width: 100% !important;
}


.mx-datepicker-range {
width: 100% !important;
}

.mx-calendar-icon{
height: auto !important;
}

.mx-shortcuts-wrapper .mx-shortcuts {
text-transform: capitalize;
}
.mx-calendar-content {
width: 224px !important;
}
.mx-input-wrapper input {
background-color: transparent !important;
/*height :30px !important;*/
}
.mx-input-append{
background-color: transparent !important;
}

.mt-5 {
margin-top: 20px;
}

.mt-10 {
margin-top: 15px !important;
}

.mb-15 {
margin-bottom: 15px;
}

.mx-input-icon.mx-clear-icon {
margin-top: -5px !important;
}

input[type="radio"] {
  margin-top: 1px !important;
  vertical-align: middle;
}


</style>