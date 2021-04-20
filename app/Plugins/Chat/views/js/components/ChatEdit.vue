<template>
    
    <div>
        
        <alert componentName="chatEdit" />

        <div class="card card-light">

            <div class="card-header">

                <h3 class="card-title">
                    {{ lang('edit') }}    
                </h3>     
                
            </div> <!-- header -->

            <div class="card-body">

                <div class="row">

                    <dynamic-select 
                    name="department" classname="col-sm-6" 
                    :apiEndpoint="departmentUrl" :multiple="false"
                    :prePopulate="false" :label="lang('department')" 
                    :value="department" :onChange="onChange"
                        :required="required" :clearable="false"
                    />

                    <dynamic-select 
                    name="helptopic" classname="col-sm-6" 
                    :apiEndpoint="helptopicUrl" :multiple="false"
                    :prePopulate="false" :label="lang('helptopic')" 
                    :value="helptopic" :onChange="onChange" 
                    :required="required" :clearable="false"
                    />

                </div> <!--row-->
                
                <div class="row">

                    <text-field 
                    :label="lang('secret_key')" :onChange="onChange" 
                    :value="secret_key" type="text" name="secret_key" 
                    classname="col-sm-6" 
                    :disabled="!secret_key_required"
                    :hint="lang('secret_key_hint')" id="secret_key" :required="required"
                    />

                    <text-field 
                    :label="lang('url')" :onChange="onChange" 
                    :value="url" type="text" name="url"
                    :disabled="true" 
                    classname="col-sm-6" 
                    :hint="lang('url_hint')" id="url" 
                    />

                </div> <!--row-->

                <div class="row">

                    <text-field
                    :label="lang('chat_widget_script')" :onChange="onChange"
                    :value="script" name="script"
                    classname="col-sm-12" rows="6" type="textarea"
                    :hint="lang('chat_widget_script_hint')" id="script"
                    />

                </div>
                
            </div> <!--box-body-->


            <div class="card-footer">

                 <button class="btn btn-primary" @click="submit">

                    <i class="fas fa-save"> </i> {{ lang('save') }}

                </button>
            </div> <!--footer-->

        </div>     <!--b0x-->
        
                
    </div>
</template>

<script>

import axios from "axios";
import {errorHandler, successHandler} from 'helpers/responseHandler';
import { mapGetters } from 'vuex'

export default {
    props: ['id'],
    data() {

       return {
        departmentUrl : 'api/dependency/departments?meta=true',
        helptopicUrl  : 'api/dependency/help-topics?meta=true',
        secret_key    : '',
        secret_key_required : false,
        department : '',
        helptopic : '',
        required: true,
        url : '',
        depUrlBit : '',
        helpUrlBit: '',
        name: '',
        script: '',
       }

    },
    computed :{
        ...mapGetters(['formattedTime','formattedDate'])
    },

    watch: {

        department(nv) {
            this.depUrlBit = nv.id
            this.generateUrl();
        },

        helptopic(nv) {
            this.helpUrlBit = nv.id
            this.generateUrl();
        }


    },

    methods : {

        onChange(value, name) {
            this[name]= value
        },

        generateUrl() {

            this.url = this.basePath()+'/chat/'+this.name+'/'+this.depUrlBit+'/'+this.helpUrlBit;

        },

        submit() {

            axios.put('chat/api/update/'+this.id,{
                department: this.department,
                helptopic: this.helptopic,
                secret_key : this.secret_key,
                url: this.url,
                script: this.script,
            })
            .then((res) => {
                this.$store.dispatch('unsetValidationError');
                successHandler(res,"chatEdit");
                setTimeout(()=>this.redirect('/chat/settings'),1200);
            })
            .catch((err) => {
                errorHandler(err,"chatEdit");
            })

        },

        getChatDetails() {
            axios.get('/chat/api/chats?ids[]='+this.id)
            .then((res) => {
                let data = res.data.data.chats[0];
                this.department = data.department
                this.helptopic = data.helptopic
                this.url = data.url;
                this.secret_key = data.secret_key;
                this.secret_key_required = (data.secret_key_required) ? true : false;
                this.name = data.short;
                this.script = data.script;
            })
        }
    },

    beforeMount() {

        this.getChatDetails();

    },

    components : {
        "text-field": require("components/MiniComponent/FormField/TextField"),
        'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
        'alert' : require('components/MiniComponent/Alert'),
    }
}
</script>

<style>
.search {
    display: none !important;
}
</style>