<template>
    <div>
        <!--loader-->
        <div class="row" v-if="hasDataPopulated === false || loading === true">
            <custom-loader></custom-loader>
        </div>
        <!-- ALERT COMPONENT-->
        <!-- :alertComponentName='ldap' -->

        <alert componentName="AzureActiveDirectorySettings" />
        <!-- ALERT COMPONENT -->
        <div v-if="hasDataPopulated">

            <!-- connection settings -->
            <faveo-box :title="lang('configuration_settings')" >

                <div class="row">
                    <text-field :label="trans('app_name')" :value="app_name" type="text" name="app_name" :onChange="onChange" classname="col-md-6" :required="true" id="app-name">
                    </text-field>

                    <text-field :label="lang('tenant_id')" :value="tenant_id" type="text" name="tenant_id" :onChange="onChange" classname="col-sm-6" :required="true" id="tenant-id">
                    </text-field>
                </div>

                <div class="row">
                    <text-field :label="trans('app_id')" :value="app_id" type="text" name="app_id" :onChange="onChange" classname="col-sm-6" :required="true" id="app-id">
                    </text-field>

                    <text-field :label="trans('app_secret')" :value="app_secret" type="password" name="app_secret" :onChange="onChange" classname="col-sm-6" :required="true" id="app-secret">
                    </text-field>
                </div>

                <div class="row">
                    <text-field :label="trans('login_button_label')" :value="login_button_label" type="text" name="login_button_label" :onChange="onChange" classname="col-sm-6" id="login-button-label">
                    </text-field>

                    <text-field :label="trans('azure_redirect_uri')" :hint="trans('azure_redirect_uri_hint')" :value="basePath()+'/azure-active-directory/auth-token/callback'" type="text" name="login_button_label" :onChange="onChange" classname="col-sm-6" disabled="true">
                    </text-field>
                </div>

                <div slot="actions" class="card-footer">

                    <button id="azure-settings-save" class="btn btn-primary" @click="() => saveConfiguration(false)" :disabled="loading">
                        <span class="fas fa-save"></span>&nbsp;
                        {{lang('save')}}
                    </button>

                    <!-- SAVE THE CONFIGURATION -->
                    <button id="azure-settings-submit" class="btn btn-primary" @click="()=> saveConfiguration(true)" :disabled="loading">
                        <span class="fas fa-save"></span>&nbsp;
                        {{lang('save_and_import')}}
                    </button>

                </div>

            </faveo-box>

        </div>
    </div>
</template>

<script>

    import FaveoBox from 'components/MiniComponent/FaveoBox';
    import {validateAzureSettings} from "../validator/azureActiveDirectoryRules";
    import axios from "axios";
    import {errorHandler, successHandler} from "../../../../../../resources/assets/js/helpers/responseHandler";
    import {getIdFromUrl} from "../../../../../../resources/assets/js/helpers/extraLogics";

    export default {
        name: "AzureActiveDirectorySettings",

        data : () => ({
            app_name: '',
            app_id: '',
            app_secret: '',
            tenant_id: '',
            login_button_label: '',
            hasDataPopulated: true,
            loading: false,
            azureAdId: null,
        }),

        beforeMount() {
            // if edit mode
            const azureAdId = getIdFromUrl(window.location.pathname);
            this.azureAdId =  azureAdId || '';
            if(this.azureAdId){
                this.getConfiguration();
            }
        },

        methods: {

            /**
             * populates the states corresponding to 'name' with 'value'
             * @param  {string} value
             * @param  {[type]} name
             * @return {void}
             */
            onChange(value, name){
                this[name] = value;
            },

            /**check if the validations are proper
             * @returns {Boolean}
             */
            isValid() {
                return validateAzureSettings(this.$data).isValid;
            },

            saveConfiguration(shallImport = false){
                if(this.isValid()){
                    this.loading = true;
                    axios.post("api/azure-active-directory/settings", {
                        id: this.azureAdId,
                        app_name: this.app_name,
                        tenant_id: this.tenant_id,
                        app_id: this.app_id,
                        app_secret: this.app_secret,
                        login_button_label: this.login_button_label,
                        import: shallImport
                    }).then(res => {
                        this.loading = false;
                        this.hasDataPopulated = true;
                        successHandler(res, 'AzureActiveDirectorySettings')
                        if(this.azureAdId){
                            this.redirect('/azure-active-directory/settings');
                        }
                        this.azureAdId = res.data.data.azure_ad_id;
                        this.getConfiguration();
                    }).catch(err => {
                        console.log(err);
                        this.loading = false;
                        errorHandler(err, 'AzureActiveDirectorySettings');
                    }).finally(() => {
                        this.loading = false;
                    });
                }
            },

            getConfiguration() {
                this.loading = true;

                axios.get('api/azure-active-directory/settings/'+this.azureAdId)
                .then(res=>{
                    this.app_name = res.data.data.app_name;
                    this.tenant_id = res.data.data.tenant_id;
                    this.app_id = res.data.data.app_id;
                    this.app_secret = res.data.data.app_secret;
                    this.login_button_label = res.data.data.login_button_label;
                }).catch(err=>{
                    errorHandler(err, 'AzureActiveDirectorySettings')
                }).finally(()=>{
                    this.loading = false;
                })
            }
        },

        components: {
            "static-select": require("components/MiniComponent/FormField/StaticSelect"),
            "text-field": require("components/MiniComponent/FormField/TextField"),
            alert: require("components/MiniComponent/Alert"),
            "custom-loader": require("components/MiniComponent/Loader"),
            checkbox: require("components/MiniComponent/FormField/Checkbox"),
            'faveo-box': FaveoBox,
        }
    }
</script>

<style scoped>

</style>