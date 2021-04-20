<template>
    
    <div>

        <custom-loader v-if="loading" :duration="loadingSpeed" />
        <alert componentName="dataTableModal" />

        <div class="card card-light">

            <div class="card-header">

                <h3 class="card-title">
                    {{ lang('whatsapp_settings') }}
                </h3>

                

            </div> <!--box-header-->


            <div class="card-body">

                <div class="row">

                    <text-field 
                        :label="lang('name')" :onChange="onChange" 
                        :value="name" type="text" name="name" 
                        :required=required  classname="col-sm-6" 
                        :hint="lang('name_hint')" id="name" 
                    />

                    <text-field 
                        :label="lang('account_sid')" :onChange="onChange" 
                        :value="sid" type="text" 
                        name="sid" :required=required  
                        classname="col-sm-6" :hint="lang('sid_hint')" 
                        id="sid" 
                    />

                </div> <!--row-->

                <div class="row">

                    <text-field 
                        :label="lang('auth_token')" :onChange="onChange" 
                        :value="token" type="password" 
                        name="token" :required=required  
                        classname="col-sm-6" :hint="lang('token_hint')" 
                        id="token" 
                    />

                    <text-field 
                        :label="lang('business_phone')" :onChange="onChange" 
                        :value="business_phone" type="text" 
                        name="business_phone" :required=required  
                        classname="col-sm-6" :hint="lang('business_phone_hint')" 
                        id="business_phone"
                    />

                </div> <!--row-->
					
                <div class="row">

                    <text-field 
                        :label="lang('webhook_url')" :onChange="onChange" 
                        :value="webhook_url" type="text" 
                        name="webhook_url" :required=required  
                        classname="col-sm-6" :hint="lang('webhook_url_hint')" 
                        id="webhook_url" :disabled="true"
                    />

                    <dynamic-select 
                        name="new_ticket_interval" classname="col-sm-6" 
                        :elements="elements_new_ticket" :multiple="false"
                        :prePopulate="false" :label="lang('new_ticket_interval')" 
                        :value="new_ticket_interval" :onChange="onChange"
                        :searchable="false"
                        :clearable="false"
                    />

                </div>

                <div class="row">

                    <dynamic-select 
                        name="is_image_inline" classname="col-sm-6" 
                        :elements="elements_is_image_inline" :multiple="false"
                        :prePopulate="false" :label="lang('is_image_inline')" 
                        :value="is_image_inline" :onChange="onChange" 
                        :searchable="false"
                        :clearable="false"
                    />

                    <text-field
                        :label="lang('approved_template_for_whatsapp')" :onChange="onChange"
                        :value="template" type="textarea" name="template"
                        classname="col-sm-6" :hint="lang('approved_template_hint')"
                        id="template" :disabled="true"
                    />

                </div> <!--row-->

            </div> <!--box-body-->

            <div class="card-footer">

                <button type="button" @click = "submit" class="btn btn-primary" >
                    <i class="fas fa-save"></i>&nbsp;
                    {{(!app_exists) ? lang('save') : lang('upd8')}}
                </button>


                <button class="btn btn-primary" @click="toggleModal" :disabled="!app_exists">
                    <i class="glyphicon glyphicon-repeat"></i>&nbsp;
                    {{ lang('reset') }}
                </button>

            </div> <!--box-footer-->

        </div> <!--box-->

        <transition name="modal">
            <!-- reset pop-up -->
                <modal v-if="showModal" :showModal="true" :onClose="()=> showModal = false" containerStyle="width: 500px">

                  <!-- if mode is reset, we only show the confirmation message that if they really want to reset -->
                  <div slot="title">
                    <h4 class="modal-title">{{lang('reset')}}</h4>
                  </div>

                  <!-- if mode is reset, we only show the confirmation message that if they really want to reset -->
                  <div v-if="!isLoading" slot="fields">
                      <span>
                        {{lang('reset_confirm')}}
                      </span>
                  </div>

                  <div v-if="isLoading" class="row" slot="fields" >
                    <loader :animation-duration="4000" color="#1d78ff" :size="size" :class="{spin: lang_locale == 'ar'}" />
                </div>

                  <div slot="controls">
                    <button type="button" @click = "onSubmit" class="btn btn-primary">
                      <i class="glyphicon glyphicon-repeat" aria-hidden="true"></i>
                      &nbsp;{{lang('reset')}}
                    </button>
                  </div>

                </modal>

        </transition>

    </div>

</template>

<script>

import axios from 'axios';
import {errorHandler, successHandler} from 'helpers/responseHandler';
import { WhatsappMixin } from "../mixins/whatsappMixin";

export default {
    
    mixins: [WhatsappMixin],

    components:{
      "text-field": require("components/MiniComponent/FormField/TextField"),
      'alert' : require('components/MiniComponent/Alert'),
      "custom-loader": require("components/MiniComponent/Loader"),
      'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
      "data-table" : require('components/Extra/DataTable'),
      "modal": require('components/Common/Modal'),
      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
    },

    beforeMount() {
      this.getValuesForEdit();
      this.webhook_url = this.basePath() + "/whatsapp";
    },

    methods: {
        onChange(value, name){
            this[name]= value
        },

        submit() {
            
            this.loading = true;

            let formObj = {
                sid: this.sid,
                token: this.token,
                name: this.name,
                business_phone: this.business_phone,
                is_image_inline: this.is_image_inline.value,
				reply_interval : this.new_ticket_interval.value,
                template: this.template
            }

            let url,method;

            if(this.app_exists) {
                url = 'whatsapp/api/update/';
                method = 'PUT';
            } else {
                url = '/whatsapp/api/create';
                method = 'POST';
            }
            axios.request({
                method,
                url,
                data: formObj
            })
            .then((res)=>{
                this.loading = false;
                successHandler(res,'dataTableModal');
                setTimeout(()=>{
                    location.reload();
                },1200)
            })
            .catch((err)=>{
                this.loading = false;
                errorHandler(err,'dataTableModal');
            })


        },

        onSubmit(){
		//for reset
			this.isLoading = true
			this.isDisabled = true
			axios.delete("whatsapp/api/delete").then(res=>{

				successHandler(res,"dataTableModal");
                this.isLoading = false;
                this.showModal = false
				this.redirect('/whatsapp/settings')
			}).catch(err => {

				errorHandler(err,"dataTableModal");
                this.showModal = false;
                this.isLoading = false;
			})
		},

        getValuesForEdit() {
            this.loading  = true;
            axios.get('whatsapp/api/accounts')
            .then((res) => {
                
                if(parseInt(res.data.data.total) >= 1) {
                    this.app_exists = true;
                    this.data = res.data.data.accounts[0];
                    this.sid = this.data.sid;
                    this.token = this.data.token;
                    this.name = this.data.name;
                    this.is_image_inline = this.filterValues(
                        this.elements_is_image_inline,this.data.is_image_inline
                    );
                    this.new_ticket_interval = this.filterValues(
                        this.elements_new_ticket,this.data.new_ticket_interval
                    );
                    this.business_phone = this.data.business_phone;
                    this.template = this.data.template;
                }
                this.loading = false;
            })
            .catch((err) => this.loading = false);

        },

        filterValues(array,value) {
            return array.filter(x => {
                if(x.value == value)
                    return x;
            })[0];
        },

        toggleModal() {
            this.showModal = !this.showModal;
        },

        onClose() {
            this.showModal = false;
        }
    }

}
</script>


<style>
.search {
    display: none !important;
}
#H5{
	margin-left:16px;
	/*margin-bottom:18px !important;*/
}
.spin{
	left:0% !important;right: 43% !important;
 }
</style>