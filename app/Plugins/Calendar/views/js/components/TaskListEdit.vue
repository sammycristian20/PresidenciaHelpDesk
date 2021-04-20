<template>
    <div>

        <Alert componentName="tasklistEdit" />

       <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{ lang('tasklist_edit') }}</h3>
            </div>

        <div class="card-body">

            <Loader v-if="loading" :animation-duration="4000" color="#1d78ff" :size="60"/>

            <div class="row">

                    <TextField 
                    :label="lang('tasklist_name')" id="tasklist_name" 
                    :onChange="onChange" :value="tasklist_name" 
                    type="text" name="tasklist_name" 
                    :required=required
                    classname="col-sm-6"
                    />

                    <DynamicSelect
                    id="project"
                    name="project"
                    :multiple="false"
                    :label="lang('project')"
                    :onChange="onChange"
                    strlength="35"
                    apiEndpoint='tasks/api/project/view'
                    :required="required"
                    :value="project"
                    classname="col-sm-6"
                    >
                    </DynamicSelect>

            </div> <!--row-->

             <!--row-->

            </div>

            <div class="card-footer">
                <button class="btn btn-primary" @click.prevent="tasklistSubmit">
                    {{ lang('save') }}
                </button>

            </div>
        </div> <!--box-->
    </div>
</template>

<script>
import TextField from "components/MiniComponent/FormField/TextField";
import Alert from "components/MiniComponent/Alert";
import axios from "axios";
import DynamicSelect from "components/MiniComponent/FormField/DynamicSelect";
import {errorHandler, successHandler} from 'helpers/responseHandler';
import { getIdFromUrl } from 'helpers/extraLogics';
import Loader from "components/MiniComponent/Loader";

export default {
    components: {
        Alert,TextField,DynamicSelect,Loader
    },

    data() {
        return {
            tasklist_name : '',
            required     : 'required',
            tasklistId    : '',
            project : '',
            projectId : '',
            loading: false
        }
    },

    beforeMount() {

        this.setUp();

    },

    methods: {

        setUp() {

            this.tasklistId = getIdFromUrl(window.location.pathname);
            let apiEndPoint = 'tasks/tasklists?'+'tasklistIds[]='+this.tasklistId;

            axios.get(apiEndPoint)
            .then(res => {
                this.tasklist_name = res.data.data.tasklists[0].name;
                this.project = res.data.data.tasklists[0].project;
                this.projectId = this.project.id;
            })
        },

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
          },'tasklistEdit');
        },

        onChange(value, name){
            if(typeof value == 'object') {
            this.projectId= value.id.toString();
            }
            this[name]= value;
        },

        tasklistSubmit() {
            if(!this.tasklist_name) {
            this.setAlert(this.lang('tasklist_name_empty'));
            return;
            } 

            if(!this.projectId) {
            this.setAlert(this.lang('project_name_for_tasklist'));
            return;
            }

            let formData = {
            name: this.tasklist_name,
            project_id : this.projectId
            }
            this.loading = true;
            axios.put('/tasks/tasklist/edit/'+this.tasklistId,formData)
            .then(res => {
                this.loading = false;
                successHandler(res,"tasklistEdit");
                setTimeout(()=>window.location = this.basePath() +'/tasks/settings',1500);
            })
            .catch(err => {
                this.loading = false;
                errorHandler(err,"tasklistEdit")
            })
        }
    }
}
</script>