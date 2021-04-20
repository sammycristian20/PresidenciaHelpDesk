<template>

    <div>
      <loader v-if="loading" animation-duration="4000"  />
      <alert componentName="task-template-create-edit" />

        <faveo-box :title="(templateDetails) ? lang('task-plugin-task-template-edit') : lang('task-plugin-task-template-create')">

            <div>

                <div class="row">

                    <text-field :label="lang('task-plugin-task-template-name')"
                        :onChange="onChange" :value="name"
                        type="text" name="name" classname="col-sm-6"
                        :required=true   id="task_name"
                    />

                    <dynamic-select
                        name="category_id" classname="col-sm-6"
                        :multiple="false" apiEndpoint="tasks/api/category/view"
                        :label="lang('task-plugin-category')"
                        :value="category_id" :onChange="onChange"
                        :searchable="true" :required="false"
                    />

                </div>

                <div class="row">

                    <text-field :label="lang('task-plugin-task-template-description')"
                        :onChange="onChange" :value="description"
                        type="textarea" name="description" classname="col-sm-12"
                        :required=true   id="task_description"
                    />

                </div>

                <div class="row">

                   <div class="col-sm-12 task-template-table">

                       <loader v-if="!hasDataPopulated" animation-duration="4000"  size="60" />

                       <table v-else class="table table-bordered">

                           <thead class="thead-default">

                           <tr>
                               <th class="temp-name">
                                   {{ lang('task-plugin-task-template-task-name') }}
                                   <span class="text-red">*</span>
                               </th>

                               <th class="temp-assign">
                                 {{ lang('task-plugin-task-template-assign-to-ticket-assignee') }}
                                 <tool-tip  :message="lang('task-plugin-task-template-assign-to-ticket-assignee-tooltip')" size="small"></tool-tip>
                               </th>

                               <th class="temp-assignee">
                                 {{ lang('task-plugin-task-template-assignees') }}
                                 <tool-tip  :message="lang('task-plugin-template-assignees-tooltip')" size="small"></tool-tip>
                               </th>

                               <th class="temp-due">
                                   {{ lang('task-plugin-task-template-due-in') }}
                                   <span class="text-red">*</span>
                               </th>

                               <th class="temp-action">{{ lang('task-plugin-template-action-label')}}</th>
                           </tr>

                           </thead>

                           <draggable :list="templateTasks" :options="{animation:200, handle:'.drag-handle'}" :element="'tbody'" @change="updateOrder">

                                <tr v-for="(task, index) in templateTasks" :key="index">

                                    <td>
                                      <form-field :name="`task_templates.${index}.taskName`" label="" :labelStyle="tasksLabelStyle">
                                        <input
                                            type="text" v-model="task.taskName"
                                            :name="`task_templates.${index}.taskName`"
                                            class="form-control adjustableLineHeight"
                                        >
                                      </form-field>
                                    </td>

                                    <td class="task-template-center">
                                      <form-field :name="`task_templates.${index}.assignTaskToTicketAssignee`" label="" :labelStyle="tasksLabelStyle">
                                        <input type="checkbox" v-model="task.assignTaskToTicketAssignee">
                                      </form-field>
                                    </td>

                                    <td class="task-assign">
                                      <form-field :name="`task_templates.${index}.assigneed`" label="" :labelStyle="tasksLabelStyle">
                                        <v-select :options="selectOptions" class="faveo-dynamic-select"
                                           v-model="task.assignees" :multiple="true" label="full_name"
                                           :reduce="assignee => assignee.id"
                                        />
                                      </form-field>
                                    </td>

                                    <td>

                                        <div class="row no-gutter">
                                          <div class="col-sm-4">
                                            <form-field :name="`task_templates.${index}.taskEnd`" label="" :labelStyle="tasksLabelStyle">
                                            <input type="text" v-model.number="task.taskEnd"
                                                class="form-control adjustableLineHeight"
                                            >
                                            </form-field>
                                          </div>

                                          <div class="col-sm-8">
                                            <form-field :name="`task_templates.${index}.taskEndUnit`" label="" :labelStyle="tasksLabelStyle">
                                            <v-select :options="timeFilterOptions" class="faveo-dynamic-select"
                                              v-model="task.taskEndUnit" :multiple="false" :clearable="false"
                                              :reduce="unit => unit.id"
                                            />
                                            </form-field>
                                          </div>

                                        </div>

                                    </td>

                                    <td>
                                      <div>
                                        <button class="btn btn-sm btn-success" @click="addTaskRow" v-tooltip="lang('add')">
                                          <i class="fas fa-plus"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger" @click="removeTaskRow(index)" v-tooltip="lang('remove')" 
                                          :class="disableRemoveButton">
                                          <i class="fas fa-minus"></i>
                                        </button>

                                        <button class="btn btn-sm btn-primary drag-handle" v-tooltip="lang('move')">
                                          <i class="fas fa-bars"></i>
                                        </button>
                                      </div>

                                    </td>

                                </tr>

                           </draggable>

                       </table>

                   </div>

                </div>

            </div>

          <div>

            <button @click.prevent="submit" class="btn btn-primary">
              <i class="fas fa-save"></i>
              {{ lang('save') | buttonText }}
            </button>

          </div>

        </faveo-box>

    </div>
    
</template>

<script>

import vSelect from "vue-select";
import FaveoBox from 'components/MiniComponent/FaveoBox';
import axios from "axios";
import {errorHandler, successHandler} from 'helpers/responseHandler'
import ToolTip from "components/MiniComponent/ToolTip";
import draggable from 'vuedraggable';

export default {
        name: "TaskTemplateCreateEdit",

        props: {
          templateDetails : {
            type: String,
            required: false,
            default: ''
          }
        },

        components: {
            "text-field"     : require("components/MiniComponent/FormField/TextField"),
            'alert'          : require('components/MiniComponent/Alert'),
            "loader"         : require("components/MiniComponent/Loader"),
            'dynamic-select' : require("components/MiniComponent/FormField/DynamicSelect"),
            'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
            'faveo-box'      : FaveoBox,
            'v-select'       : vSelect,
            "form-field"     : require("components/MiniComponent/FormField/FormFieldTemplate.vue"),
            "tool-tip": ToolTip,
            draggable
        },

        filters: {
          buttonText: (value) => {
            if (!value) return '';
            value = value.toString();
            return ` ${value}`;
          }
        },

        computed:{
          disableRemoveButton() {
            return (this.templateTasks.length === 1) ? 'disableMinus' : '';
          }
        },

        beforeMount() {
            this.getSelectOptions();
            this.fillFieldsIfEdit();
        },

        methods: {
           async getSelectOptions() {
               try {
                   let response = await axios.get('api/dependency/agents?meta=true')
                   this.selectOptions = response.data.data.agents;
               } catch (e) {
                   this.selectOptions = [];
               }
           },

          addTaskRow() {
             this.templateTasks.push({
               taskName: '',
               assignees: [],
               taskEnd: null,
               order: this.templateTasks.length + 1,
               taskEndUnit: 'day',
               assignTaskToTicketAssignee: false
             });
          },

          removeTaskRow(index) {
             if (this.templateTasks.length > 1) {
               this.templateTasks.splice(index, 1);
             } else {
               alert(this.lang('task-plugin-template-at-least-one-task'));
             }

          },

          onChange(value, name){
            this[name]= value;
          },

          fillFieldsIfEdit() {
            if(this.templateDetails) {
              const templateDetailsObject = JSON.parse(this.templateDetails);
              this.id = templateDetailsObject.id;
              this.name = templateDetailsObject.name;
              this.description = templateDetailsObject.description;
              this.category_id = templateDetailsObject.category;
              this.addFilledTaskRows(templateDetailsObject.template_tasks);
            }
          },

          addFilledTaskRows(tasks) {
            this.hasDataPopulated = false;
            this.templateTasks = []
            tasks.forEach((task) => {
              this.templateTasks.push({
                taskName: task.name,
                assignees: task.assignees.map(Number),
                taskEnd: task.end,
                order: task.order,
                taskEndUnit: task.end_unit,
                assignTaskToTicketAssignee: !!(task.assign_task_to_ticket_agent)
              });
            })
            setTimeout(() => {this.hasDataPopulated = true}, 1000)
          },

          submit() {
            let url,method;
            this.loading = true;
            let formObj = {
              name: this.name,
              description: this.description,
              category_id: (this.category_id) ? this.category_id.id : null,
              task_templates: this.templateTasks
            }

            if (this.id) {
              url = `/tasks/api/template/update/${this.id}`;
              method = 'PUT';
            } else {
              url = '/tasks/api/template/store';
              method = 'POST';
            }

            axios.request({
              method,
              url,
              data: formObj
            })
            .then((res)=>{
              successHandler(res,'task-template-create-edit');

              this.$store.dispatch('unsetValidationError');
              
              setTimeout(() => {
                this.redirect('/tasks/template/settings')
              },1000);
            })
            .catch((err)=>{
              errorHandler(err,'task-template-create-edit');
            })
            .finally(() => {
              this.loading = false;
            })

          },

          updateOrder() {
             this.templateTasks.map((templateTask, index) => {
               templateTask.order = index+1;
             })
          },

        },

        data() {
            return {
                hasDataPopulated: true,
                loading: false,
                name : '',
                description: '',
                timeFilterOptions: [
                    { id: 'minute', value: 'unit_minute', label: 'Minute(s)' },
                    { id: 'hour', value: 'unit_hour', label: 'Hour(s)' },
                    { id: 'day', value: 'unit_day', label: 'Day(s)' },
                    { id: 'month', value: 'unit_month', label: 'Month(s)' }
                ],
                filterObjects:{},
                templateTasks:[
                    {
                        taskName: '',
                        assignees: [],
                        taskEnd: null,
                        order: 1,
                        taskEndUnit: 'day',
                        assignTaskToTicketAssignee: false,
                    }
                ],
                selectOptions:[],
                category_id:null,
                id: '',
                tasksLabelStyle: {
                  display: "none"
                }
            }
        }
    };

</script>

<style>

    .task-template-center {
        text-align: center;
        vertical-align: middle;
    }

    .task-template-v-center {
        vertical-align: middle;
    }

    .actionBtn {
      font-size: 1.5em;
      cursor: pointer;
    }

    .disableMinus {
      cursor: not-allowed !important;
    }

    .disableMinus:active {
      pointer-events: none;
    }

    .row.no-gutter {
      margin-left: 0;
      margin-right: 0;
    }

    .row.no-gutter [class*='col-']:not(:first-child),
    .row.no-gutter [class*='col-']:not(:last-child) {
      padding-right: 0;
      padding-left: 0;
    }

    textarea#task_description {
      resize: vertical !important;
    }

    .adjustableLineHeight {
      line-height: 1.4;
    }

    .drag-handle {
      cursor: move;
    }

    #drag-handle-parent {
      font-size: 1.3em;
      margin-left: 2px;
    }

    .temp-name { width : 20%; } .temp-due { width: 27%; } .temp-assign { width: 15%; } 

    .temp-assignee { width: 25%; } .temp-action { width: 13%; }

    .task-template-table thead{ 

      background-color: #eee;
    }

    .vs__clear {
      top: -2px;
    }

    .task-assign .faveo-dynamic-select .vs__dropdown-toggle { height: auto !important; }
</style>