<template>
    <div>
        <modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

            <div slot="title">
                <h4 class="modal-title">{{lang('delete')}}</h4>
            </div>

            <div v-if="!loading" slot="fields">
                <span>
                    {{ alertMessage }}
                </span>
            </div>

            <div v-if="loading" class="row" slot="fields" >
                <loader :animation-duration="4000" color="#1d78ff" :size="size" :class="{spin: lang_locale == 'ar'}" />
            </div>

            <div slot="controls">
                <button type="button" @click = "onSubmit" class="btn btn-danger" :disabled="isDisabled"><i class="fas fa-trash" aria-hidden="true"></i> {{lang('delete')}}</button>
            </div>

        </modal>
    </div>
</template>

<script type="text/javascript">

    import {errorHandler, successHandler} from 'helpers/responseHandler'

    export default {

        name : 'task-delete-modal',

        description : 'Delete Modal component for task plugin',

        props:{

            /**
             * status of the modal popup
             * @type {Object}
             */
            showModal:{type:Boolean,default:false},

            /**
             * status of the delete popup modal
             * @type {Object}
             */
            deleteUrl:{type:String},

            /**
             * The function which will be called as soon as user click on the close button
             * @type {Function}
             */
            onClose:{type: Function},

            alertComponentName : { type : String, default : 'dataTableModal'},

            redirectUrl : { type : String, default : ''},

            componentTitle : { type : String, default : ''}

        },

        computed: {
          alertMessage() {
            switch (this.componentTitle) {
              case 'TaskList':
                return this.lang('task-plugin-delete-confirmation-tasklists');

              case 'TaskProjects':
                return this.lang('task-plugin-delete-confirmation-projects');

              case 'TaskTemplate':
                return this.lang('task-plugin-template-delete-alert');
            }
          }
        },

        data:()=>({


            /**
             * buttons disabled state
             * @type {Boolean}
             */
            isDisabled:false,

            /**
             * width of the modal container
             * @type {Object}
             */
            containerStyle:{
                width:'500px'
            },

            /**
             * initial state of loader
             * @type {Boolean}
             */
            loading:false,

            /**
             * size of the loader
             * @type {Number}
             */
            size: 60,

            /**
             * for rtl support
             * @type {String}
             */
            lang_locale:'',

        }),

        created(){
            // getting locale from localStorage
            this.lang_locale = localStorage.getItem('LANGUAGE');
        },

        methods:{
            /**
             * api calls happens here
             * @return {Void}
             */
            onSubmit(){
                //for delete
                this.loading = true
                this.isDisabled = true
                axios.delete(this.deleteUrl).then(res=>{

                    successHandler(res,this.alertComponentName);

                    this.afterRespond();
                }).catch(err => {

                    errorHandler(err,this.alertComponentName);

                    this.afterRespond();
                })
            },

            afterRespond(){

                window.eventHub.$emit(this.componentTitle+'refreshData');

                window.eventHub.$emit(this.componentTitle+'uncheckCheckbox');

                if(this.redirectUrl){

                    this.redirect(this.redirectUrl);
                }

                if(this.alertComponentName == 'timeline') {

                    window.eventHub.$emit('actionDone');
                }

                this.onClose();

                this.loading = false;

                this.isDisabled = true
            }
        },

        components:{
            'modal':require('components/Common/Modal.vue'),
            'alert' : require('components/MiniComponent/Alert'),
            'loader':require('components/Client/Pages/ReusableComponents/Loader'),
        }

    };
</script>

<style type="text/css">
    .has-feedback .form-control {
        padding-right: 0px !important;
    }
    #H5{
        margin-left:16px;
        /*margin-bottom:18px !important;*/
    }
    .fulfilling-bouncing-circle-spinner{
        margin: auto !important;
    }
    .margin {
        margin-right: 16px !important;margin-left: 0px !important;
    }
    .spin{
        left:0% !important;right: 43% !important;
    }
</style>
