<template>
    <div id="ldap-login">
    <span v-for="(ldapSetting, index) in ldapSettings.directory_settings" class="ldap-login-block">
        <button :id="'ldap-login-button-'+index" class="ldap-login-button btn btn-custom btn-block btn-flat" :disabled="disabled" :style="buttonStyle" @click="()=>ldapLoginSubmit(ldapSetting.id)">
            {{ ldapSetting.ldap_label !== '' ? ldapSetting.ldap_label : lang('login_via_ldap') }}
        </button>

        <a :id="'ldap-forgot-password-'+index" v-if="ldapSetting.forgot_password_link !== ''" :href="ldapSetting.forgot_password_link" target="_blank"
           :style="linkStyle">
              {{ lang('forgot_ldap_password') }}
        </a>
    </span>
    </div>
</template>

<script>

    import {boolean} from 'helpers/extraLogics';
    import { mapGetters } from 'vuex'
    import axios from 'axios'

    export default {
      props: ['data'],

      data(){
            return {
              disabled : false,
              ldapSettings: null,
              login_via_ldap: false,
            }
        },

        beforeMount(){
          this.ldapSettings = JSON.parse(this.data).ldap_meta_settings;
          this.ldapSettings.hide_default_login && this.hideDefaultLogin();
        },

        mounted(){

          // disable button if login is success
          window.eventHub.$on("login-success", () => {
            this.disabled = true;
          });

          // enable button when login is failure and mark login_via_ldap as false, so when clicking
          // on default login button it doesn't assume that as true already
          window.eventHub.$on("login-failure", () => {
            this.disabled = false;
            this.login_via_ldap = false;
          });

          window.eventHub.$on("logging-in-with-enter-key",() => {
            if(document.getElementById('default-login-button').style.display == "none"){
              document.getElementById('ldap-login-button-0').click();
            }
          })
        },

        methods: {

            /**
             * If Only Ldap Login is allowed
             * @return {undefined}
             */
            hideDefaultLogin(){

                // hiding default login button
                document.getElementById('default-login-button').style.display = "none";

                // hiding default forgot password
                document.getElementById('default-forgot-password').style.display = "none";
            },

            /**
             * Calls default login button simply
             * @return {undefined}
             */
            ldapLoginSubmit(ldapId){
                this.login_via_ldap = true;

                window.eventHub.$on("login-data-submitting", (params) => {
                    params.ldap = this.login_via_ldap;
                    params.ldap_id = ldapId;
                });

                document.getElementById('default-login-button').click();
            }
        },

        computed:{
            ...mapGetters({buttonStyle : 'getButtonStyle',linkStyle : 'getLinkStyle'})
        }
    }
</script>

<style >
    .ldap-login-button{
        margin-top: 5px;
        margin-bottom: 3px;
    }

    .ldap-login-block{
        text-align : left;
        margin-bottom: 10px;
    }
</style>
