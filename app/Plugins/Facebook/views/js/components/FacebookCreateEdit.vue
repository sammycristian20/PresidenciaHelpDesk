<template>

    <div>

        <div class="row" v-if="loading === true">
            <custom-loader :duration="loadingSpeed"></custom-loader>
        </div>

        <alert componentName="facebookCreateEdit" />

        <faveo-box :title="(id) ? lang('facebook_page_edit') : lang('facebook_page_create')">

            <div class="row">

                <text-field
                        :label="lang('facebook_page_name')" :onChange="onChange"
                        :value="page_name" type="text" name="page_name"
                        :required=required  classname="col-sm-6"
                        :hint="lang('facebook_page_name_hint')" id="facebook_page_name"
                />

                <text-field
                        :label="lang('facebook_page_id')" :onChange="onChange"
                        :value="page_id" type="text" name="page_id"
                        :required=required  classname="col-sm-6"
                        :hint="lang('facebook_page_id_hint')" id="page_id"
                />

            </div> <!--row-->

            <div class="row">

                <text-field
                        :label="lang('facebook_page_access_token')" :onChange="onChange"
                        :value="page_access_token" type="password" name="page_access_token"
                        :required=required  classname="col-sm-6"
                        :hint="lang('facebook_page_access_token_hint')" id="facebook_page_access_token"
                />

              <dynamic-select
                  name="new_ticket_interval" classname="col-sm-6"
                  :elements="elements_new_ticket" :multiple="false"
                  :prePopulate="false" :label="lang('facebook_new_ticket_interval')"
                  :value="new_ticket_interval" :onChange="onChange"
                  :clearable="false" :searchable="false"
                  :hint="lang('facebook_new_ticket_interval_hint')"
                  :required=required
              />

            </div> <!--row-->

            <div class="card-footer" slot="actions">

                <button class="btn btn-primary" @click="submit">
                    <i class="fas fa-save"></i>
                    {{ lang('save') }}
                </button>

            </div> <!--row-->

        </faveo-box>

    </div>

</template>

<script>

    import FaveoBox from "components/MiniComponent/FaveoBox";
    import axios from "axios";
    import {errorHandler, successHandler} from 'helpers/responseHandler';

    export default {

        name: "FacebookCreateEdit",

        components:{
            "text-field": require("components/MiniComponent/FormField/TextField"),
            'alert' : require('components/MiniComponent/Alert'),
            "custom-loader": require("components/MiniComponent/Loader"),
            'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
            "modal": require('components/Common/Modal'),
            'faveo-box' : FaveoBox,
        },

        props: {
            integrationData : {
                type: String,
                default: '',
            }
        },

        data() {
            return {
                id: '',
                loadingSpeed: 4000,
                loading: false,
                required: true,
                page_access_token: '',
                page_name: '',
                page_id: '',
                elements_new_ticket: [
                    {
                        name: "One Day",
                        value: "1",
                    },

                    {
                        name: "Five Days",
                        value: "5",
                    },

                    {
                        name: "Ten Days",
                        value: "10",
                    },

                    {
                        name: "Fifteen Days",
                        value: "15",
                    },

                    {
                        name: "Thirty Days",
                        value: "30",
                    },
                ],
            }
        },

        beforeMount() {
            this.fillFieldsIfEdit();
        },

        methods: {

            filterValues(array,value) {
                return array.filter(x => {
                    if(x.value == value)
                        return x;
                })[0];
            },

            fillFieldsIfEdit() {
                if(this.integrationData) {
                    let integrationObject = JSON.parse(this.integrationData);
                    this.id = integrationObject.id;
                    this.page_access_token = integrationObject.page_access_token;
                    this.page_id = integrationObject.page_id;
                    this.page_name = integrationObject.page_name;
                    this.new_ticket_interval = this.filterValues(
                        this.elements_new_ticket,integrationObject.new_ticket_interval
                    );
                }
            },

            onChange(value, name){
                this[name]= value
            },

            submit() {
                this.loading = true;
                let formDataObj = {
                    page_id: this.page_id,
                    page_access_token: this.page_access_token,
                    page_name  : this.page_name,
                    new_ticket_interval : (this.new_ticket_interval) ? this.new_ticket_interval.value : null,
                    verify_token : this.verify_token,
                }

                let url,method;
                if(this.id) {
                    url = 'facebook/api/integration/'+this.id;
                    method = 'PUT';
                } else {
                    url = 'facebook/api/integration';
                    method = 'POST';
                }

                axios.request({
                    method,
                    url,
                    data: formDataObj
                })
                .then(res=>{
                    this.loading = false;
                    successHandler(res,'facebookCreateEdit');
                    this.loading = false;
                    if (!this.id) {
                        //means this form is create form
                        setTimeout(()=>{
                            this.redirect('/facebook/settings')
                        },1200)
                    } else {
                        this.$store.dispatch('unsetValidationError');
                    }
                })
                .catch(err => {
                    this.loading = false;
                    errorHandler(err,'facebookCreateEdit');
                });
            },

        }
    }
</script>

<style scoped>

</style>