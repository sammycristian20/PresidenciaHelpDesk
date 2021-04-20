<template>
    <div>

        <Alert componentName="projectEdit" />

        <div class="card card-light">

            <Loader v-if="loading" :animation-duration="4000" color="#1d78ff" :size="60"/>

            <div class="card-header">
                <h3 class="card-title">{{ lang('project_edit') }}</h3>
            </div>

            <div class="card-body">

            <div class="row">

              
                    <TextField 
                    :label="lang('project_name')" id="project_name" 
                    :onChange="onChange" :value="project_name" 
                    type="text" name="project_name" 
                    :required=required
                    classname="col-sm-12"
                    />


            </div> <!--row-->

            </div>


            <div class="card-footer">

                <button class="btn btn-primary" @click.prevent="projectSubmit">
                    {{ lang('save') }}
                </button>
            </div> <!--row-->

        </div> <!--box-->
    </div>
</template>

<script>
import TextField from "components/MiniComponent/FormField/TextField";
import Loader from "components/MiniComponent/Loader";
import Alert from "components/MiniComponent/Alert";
import axios from "axios";
import {errorHandler, successHandler} from 'helpers/responseHandler';
import { getIdFromUrl } from 'helpers/extraLogics';

export default {
    components: {
        Alert,TextField,Loader
    },

    data() {
        return {
            project_name : '',
            required     : 'required',
            projectId    : '',
            loading: false
        }
    },

    beforeMount() {

        this.setUp();

    },

    methods: {

        setUp() {

            this.projectId = getIdFromUrl(window.location.pathname);
            let apiEndPoint = 'tasks/api/projects?'+'projectIds[]='+this.projectId;

            axios.get(apiEndPoint)
            .then(res => this.project_name = res.data.data.projects[0].name)
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
          },'projectEdit');
      },

        projectSubmit() {

            if(!this.project_name) {
            this.setAlert(this.lang('project_name_empty'));
            return;
            } 

        let formData = {
          name : this.project_name
        }

        this.loading = true;

        axios.put('tasks/api/project/edit/'+this.projectId,formData)
        .then(res =>  {this.loading = false; successHandler(res,'projectEdit'); setTimeout(()=>window.location = this.basePath() + '/tasks/settings'),1500 })
        .catch(err => {this.loading = false; errorHandler(err,'projectEdit'); })

      }
    }
}
</script>