<template>
    <div id="azure-login" v-if="azureSettings">
      <img v-if="azureSettings.hide_default_login" :src="basePath() + '/images/azure.png'" style="margin: -60px 0 -10px 92px" :alt="trans('azure_active_directory')" width="256" height="256"/>

      <span v-for="(azureSetting, index) in azureSettings.directory_settings" class="azure-login-block">
        <button :id="'azure-login-button-'+index" class="azure-login-button btn btn-custom btn-block btn-flat" :style="buttonStyle" @click="()=>azureLoginSubmit(azureSetting.login_url)">
            <i class="fa fa-server" aria-hidden="true"></i>
            {{ azureSetting.login_button_label !== '' ? azureSetting.login_button_label : lang('login_via_azure') }}
        </button>
    </span>
    </div>
</template>

<script>

    import { mapGetters } from 'vuex'

    export default {

        props: ['data'],

        data(){
            return {
                azureSettings: null,
            }
        },

        beforeMount(){
            this.azureSettings = JSON.parse(this.data).azure_meta_settings;
            this.azureSettings.hide_default_login && this.hideDefaultLogin();
        },

        methods: {

            /**
             * Calls default login button simply
             * @return {undefined}
             */
            azureLoginSubmit(redirectUrl){
                // redirect to the redirect URL
                window.location = redirectUrl;
            },

            hideDefaultLogin() {
              document.getElementById('user_name').style.display= 'none';
              document.getElementById('password').style.display= 'none';
              document.getElementById('remember_me').style.display= 'none';
              document.getElementById('default-login-button').style.display= 'none';
              document.getElementById('default-forgot-password').style.display= 'none';
            }
        },

        computed:{
            ...mapGetters({buttonStyle : 'getButtonStyle',linkStyle : 'getLinkStyle'})
        }
    }
</script>

<style >
    .azure-login-button{
        margin-top: 5px;
        margin-bottom: 3px;
    }

    .azure-login-block{
        text-align : left;
        margin-bottom: 10px;
    }
</style>
