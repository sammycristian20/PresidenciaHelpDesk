<template>
    
    <div>
        
        <alert componentName="web-hook-settings" />

        <faveo-box :title="trans('webhook_configurations')">

            <tool-tip slot="headerTooltip" :message="trans('webhook_update_tooltip')" size="small"></tool-tip>

            <loader v-if="isLoading"></loader>

            <div class="row" v-if="hasDataPopulated">
                
                <text-field :label="trans('web_hook_url')" type="text" name="web_hook" :value="web_hook"
                    classname="col-sm-12" :onChange="onChange" :required="false" :hint="trans('enter_url_to_send_ticket_details')" placehold="https://www.example.com">

                </text-field>    
            </div>
            
            <div slot="actions" class="card-footer">

                <button class="btn btn-primary" type="button" :disabled="isLoading" @click="onSubmit">
                
                    <i class="fas fa-save"></i> {{ trans('submit')}}
                </button>
            </div>
        </faveo-box>
    </div>
</template>

<script>
    
    import axios from 'axios';
    
    import {errorHandler, successHandler} from "helpers/responseHandler";

    export default {

        name: 'web-hook-settings',

        data () {
            
            return {
                
                web_hook: null,
                
                isLoading: true,

                hasDataPopulated : false
            }
        },

        beforeMount() {
            
            this.getValues();    
        },

        methods: {
            
            getValues() {

                axios.get('/api/admin/get-webhook').then(response => {
                    
                    this.isLoading = false;
                
                    this.web_hook = response.data.data.web_hook ? response.data.data.web_hook : '';
                    
                    this.hasDataPopulated = true;

                }).catch(error => {
                    
                    this.hasDataPopulated = true;

                    this.isLoading = false;
                
                    errorHandler(error, 'web-hook-settings');
                })
            },

            onSubmit() {
                
                this.isLoading = true;
            
                const data = {};

                data['web_hook'] = this.web_hook;

                axios.post('/api/admin/save-webhook', data ).then(response => {
                    
                    this.isLoading = false;
                
                    successHandler(response, 'web-hook-settings');

                    this.$store.dispatch('unsetValidationError');
                    
                    this.getValues()
                
                }).catch(error => {
                    
                    this.isLoading = false;
                
                    errorHandler(error, 'web-hook-settings');
                })
            },

            onChange(value, name) {
                
                this[name] = value;
            }
        },

        components: {
            
            'faveo-box': require('components/MiniComponent/FaveoBox'),
            
            'loader': require("components/MiniComponent/Loader"),
            
            'text-field': require("components/MiniComponent/FormField/TextField"),

            "tool-tip": require('components/MiniComponent/ToolTip'),

            'alert' : require('components/MiniComponent/Alert'),
        }
    };
</script>