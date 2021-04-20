<template>
    
<div>

  <!--loader-->
  <div class="row" v-if="loading === true">
    <custom-loader :duration="loadingSpeed"></custom-loader>
  </div>

  <!-- ALERT COMPONENT-->
  <alert componentName="dataTableModal" />
  <!-- ALERT COMPONENT -->
  

  <div class="card card-light">

    <div class="card-header">
        <h3 class="card-title">
            {{ app_exists ? lang('app_edit') : lang('facebook_integration_settings')}}
        </h3>
        <div class="card-tools">
        <button :disabled="!app_exists" class="btn btn-tool" @click="redirectToPages" v-tootip="lang('page_settings')">
          <i class="fas fa-cog"></i>
        </button>
        </div>        
    </div>

    <div class="card-body">


            <div class="row">

                <text-field
                    :label="lang('app-id')" :onChange="onChange"
                    :value="app_id" type="text" name="app_id"
                    :required=required  classname="col-sm-6"
                    :hint="lang('app_client_id')" id="id"
                    :disabled="app_exists"
                />

                <text-field
                    :label="lang('app-secret')" :onChange="onChange"
                    :value="secret" type="text" name="secret"
                    :required=required  classname="col-sm-6"
                    :hint="lang('app_client_secret')" id="secret"
                    :disabled="app_exists"
                />

            </div> <!--row-->

            <div class="row">

                <dynamic-select
                    name="new_ticket_interval" classname="col-sm-6"
                    :elements="elements_new_ticket" :multiple="false"
                    :prePopulate="false" :label="lang('time_check')"
                    :value="new_ticket_interval" :onChange="onChange"
                    :clearable="false" :searchable="false"
                />

                <dynamic-select
                    name="cron_confirm" classname="col-sm-6"
                    :elements="elements_cron" :multiple="false"
                    :prePopulate="false" :label="lang('cron_label')"
                    :value="cron_confirm" :onChange="onChange"
                    :clearable="false" :searchable="false"
                />

            </div> 

      </div> <!--box-body-->


      <div class="card-footer">
         
        <button class="btn btn-primary" @click.prevent="submitter">
            <i class="fas fa-save"> </i>
            {{ lang('save') }}
        </button>

        <button class="btn btn-primary" @click="toggleModal" :disabled="!app_exists">
            <i class="glyphicon glyphicon-repeat"> </i>
            {{ lang('reset') }}
        </button>
             
      </div>

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
            <button type="button" @click="deleteFacebook" class="btn btn-primary">
                <i class="glyphicon glyphicon-repeat" aria-hidden="true"> </i>{{lang('reset')}}
            </button>
            </div>

        </modal>

    </transition>


  </div>

  


</template>

<script>

import axios from "axios";
import {errorHandler, successHandler} from 'helpers/responseHandler';
import vSelect from "vue-select";
import { FbMixin } from './../Mixins/FbMixin.js';
import Vue from 'vue';
import { mapGetters } from 'vuex'


export default {
   mixins: [FbMixin],
   computed :{
        ...mapGetters(['formattedTime','formattedDate'])
   },

   created() {
       window.eventHub.$on('refreshData',() => {
           setTimeout(()=>location.reload(),1000);
       })
   },
   components:{
      "text-field": require("components/MiniComponent/FormField/TextField"),
      'alert' : require('components/MiniComponent/Alert'),
      "custom-loader": require("components/MiniComponent/Loader"),
      'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
      "modal": require('components/Common/Modal'),
    },

    beforeMount() {

        // this.checkCredentials();
        this.checkFacebook();
        
    },
    
    methods: {

        checkFacebook() {
            this.getAppDetails();
            this.getCronDetails();
        },

        redirectToPages() {
            this.redirect('/facebook/pages/list');
        },
        
        onChange(value, name){
            this[name]= value
        },

        checkCredentials() {
            axios.get("facebook/api/credentials")
            .then((res) => {
                this.app_exists = true;
            })
            .catch((err) => this.app_exists = false)
        },

        getAppDetails() {
            this.loading = true;
            axios.get('facebook/api/app')
            .then((res) => {
                let data = res.data.data.apps[0];
                if(data) {
                    this.app_exists = true;
                    this.app_id = data.app_id;
                    this.secret = data.secret;
                    this.new_ticket_interval = this.filterValues(
                        this.elements_new_ticket,data.new_ticket_interval
                    );
                    this.loading = false;
                }
                else {
                    Object.assign(this.$data, this.$options.data.apply(this));
                    this.app_exists = false;
                    this.loading = false;
                }
            })
            .catch((err) => {
                console.log(err)
            })

        },

        deleteFacebook() {
        	//for reset
			this.isLoading = true
			this.isDisabled = true
			axios.delete("facebook/api/app/delete").then(res=>{
				successHandler(res,"dataTableModal");
                this.isLoading = false;
                this.showModal = false
                // setTimeout(()=>this.redirect('/facebook/settings'),1200);
                this.loading =true;
                this.checkFacebook();
			}).catch(err => {
				errorHandler(err,"dataTableModal");
                this.showModal = false;
                this.isLoading = false;
			})
        },

        filterValues(array,value) {
            return array.filter(x => {
                if(x.value == value)
                    return x;
            })[0];
        },

        getCronDetails() {

            axios.get('facebook/api/check/condition')
            .then((res) => {
                this.cron_confirm = this.filterValues(
                    this.elements_cron,res.data.data.active
                )
            })
            .catch((err) => {
                console.log(err)
            })

        },

         toggleModal() {
            this.showModal = !(this.showModal);
        },
        onClose(){
            this.showModal = false;
        },

        submitter() {

            let formDataObj = {
				app_id: this.app_id,
				secret: this.secret,
				cron_confirm  : this.cron_confirm.value,
				new_ticket_interval : this.new_ticket_interval.value,

            }

            this.loading = true;
            let url,method,redirect;
            if(this.app_exists) {
                url = 'facebook/api/app/update/';
                method = 'PUT';
            } else {
                url = 'facebook/api/app/create';
                method = 'POST';
                redirect = true;
            }
            
            

            axios.request({
                method,
                url,
                data: formDataObj
            })
            .then(res=>{
                this.loading = false;
                if(redirect) location.href = res.data.data;
                else {
                    successHandler(res,'dataTableModal');
                    this.loading =false;
                    this.checkFacebook();
                }
            }).catch(err => {
                this.loading = false;
                errorHandler(err,'dataTableModal');
            });

        }
    }
}
</script>


<style>
    .mt-5 {
        margin-top: 5rem;
    }

    .myBtn{
        margin-left: 15px !important;
    }


    .app-id{
        width:17% !important;
        word-break: break-all;
    }

    .app-secret {
        width: 25% !important;
        word-break: break-all;
    }

    .app-created {
        width: 18%;
        word-break: break-all;
    }

    .app-action {
        width: 10%;
        word-break : break-all;
    }

    .app-cron {
        width: 10% !important;
        word-break: break-all;
    }

    .app-new-ticket-interval{
        width: 20% !important;
        word-break: break-all;
    }

    .search {
        display: none !important;
    }
    #H5{
        margin-left:16px;
        /*margin-bottom:18px !important;*/
    }

</style>