<template>
<div>

  <!--loader-->
  <custom-loader :duration="loadingSpeed" v-if="!hasDataPopulated || loading" />
  <!-- ALERT COMPONENT-->
  <!-- :alertComponentName='ldap' -->

  <alert componentName="ldap" />
  <!-- ALERT COMPONENT -->
  <div v-if="hasDataPopulated">

    <!-- CONFIGURATION SETTINGS -->
    <faveo-box :title="lang('ldap_configuration_settings')">
      <h6 id="ldap-configure-warning" v-if="message" class="alert alert-warning  text-uppercase">
        <span><i class="fa fa-exclamation-triangle"></i></span> <span>{{message}}</span>
      </h6>
      
      <div style="margin-top: 2rem;">
        <!-- connection settings -->
        <faveo-box :title="lang('connection_settings')" >
          <div class="row">
            <!-- Domain address -->
            <text-field :label="lang('ldap_domain')" :value="domain" type="text" name="domain" :onChange="onChange" classname="col-md-4" :required="true" id="domain">
            </text-field>

            <!-- Username -->
            <text-field label="Username" :value="username" type="text" name="username" :onChange="onChange" classname="col-sm-4" :required="true" id="username">
            </text-field>

            <!-- Password  -->
            <text-field label="Password" :value="password" type="password" name="password" :onChange="onChange" classname="col-sm-4" :required="true" id="password">
            </text-field>

            <static-select :label="lang('ldap_schema')" :elements="schemas" name="schema" :value="schema" classname="col-sm-4" :onChange="onChange" :hint="lang('ldap_schema_description')" id="schema">
            </static-select>

            <text-field :label="lang('port')" :value="port" type="number" name="port" :onChange="onChange" classname="col-sm-4" :hint="lang('ldap_port_hint')" id="port">
            </text-field>

            <static-select :label="lang('encryption')" :elements="encryptions" name="encryption" :value="encryption" classname="col-sm-4" :onChange="onChange" :hint="lang('ldap_encryption_hint')" id="encryption">
            </static-select>
          </div>
        </faveo-box>
      </div>

      <div style="margin-top: 2rem;">

        <!-- login settings -->
        <faveo-box :title="lang('login_settings')" >
          <div class="row">
            <!-- ldap label  -->
            <text-field :label="lang('ldap_label')" :value="ldap_label" type="text" name="ldap_label" :onChange="onChange" classname="col-sm-4" :hint="lang('ldap_label_hint')" id="label">
            </text-field>

            <!-- forgot password link  -->
            <text-field :label="lang('forgot_password_link')" :value="forgot_password_link" type="text" name="forgot_password_link" :onChange="onChange" classname="col-sm-4" :hint="lang('forgot_password_link_description')" id="forgot-password-link">
            </text-field>

            <!--   -->
            <text-field :label="lang('username_prefix')" :value="prefix" type="text" name="prefix" :onChange="onChange" classname="col-sm-4" :hint="lang('username_prefix_description')" id="prefix">
            </text-field>

            <!-- ldap label  -->
            <text-field :label="lang('username_suffix')" :value="suffix" type="text" name="suffix" :onChange="onChange" classname="col-sm-4" :hint="lang('username_suffix_description')" id="suffix">
            </text-field>

          </div>
        </faveo-box>
      </div>

      <!-- SAVE THE CONFIGURATION -->
      <div class="card-footer" slot="actions">
        <button id="ldap-settings-submit" class="btn btn-primary" @click="saveConfiguration" :disabled="loading">
          <span class="fas fa-save"></span>&nbsp;
          {{lang('save_configuration')}}
        </button>
      </div>
  </faveo-box>
    <!-- CONFIGURATION SETTINGS END -->

    <!-- SEARCH BASIS COMPONENT -->
    <search-basis v-if="is_valid != 0" :ldap-id="ldapId" :addUser="addUser" :searchBaseArray="search_bases" :getLdap="getldapSetting" :showOrganization="show_organization" :showDepartment="show_department" :showRole="show_role" :loadingValue="loading">
    </search-basis>

    <user-import-mapper v-if="is_valid != 0" :api-endpoint="'/api/ldap/advanced-settings/' + ldapId" :updateParent="getldapSetting" :table-headings="tableHeadings"></user-import-mapper>

    <ldap-table  v-if="is_valid != 0" :ldap-id="ldapId"></ldap-table>

  </div>
</div>
</template>


<script type="text/javascript">
  import axios from "axios";
  import { extractOnlyId, fetchNameAsPerId, getIdFromUrl } from "helpers/extraLogics.js";
  import {errorHandler,successHandler} from "helpers/responseHandler";
  import {validateLdapSettings} from "./../validator/ldapSettingsRules.js";
  import FaveoBox from 'components/MiniComponent/FaveoBox';

  const LDAP_API = 'api/ldap/settings/';

  export default {
    name: "ldap-settings",

    description: "ldap setting page",

    beforeMount() {
      // this.getDepartmentList();
      // this.getOrganizationsList();
      const LDAP_ID = getIdFromUrl(window.location.pathname);

      this.ldapId =  LDAP_ID || '';

      // call getldapSetting fn in case of edit
      if(this.ldapId !== '') {
        this.getldapSetting();
      } else { // case of create
        this.hasDataPopulated = true;
        this.loading = false;
      }
    },

    data: function() {
      return {
        domain: "", // domain name
        is_valid: 0, // parameter used for showing the search base query the valid i default set to false and would be true once the save configuration is done
        password: "", //password
        port: null,
        encryption: null,
        schema:'active_directory',

        ldap_label: "", //ldap label at login page
        forgot_password_link: "", // forgot password link for ldap
        prefix:'',
        suffix:'',

        username: "", //username
        user_type: "", // default value set to user
        search_bases: [], // search basis array
        show_organization: true,
        show_department: true,
        show_role: true,
        loading: true, //to show the loader
        loadingSpeed: 4000, // loader speed
        hasDataPopulated: false, // variable use to display data once the getldapsetting is successfull
        message: '',

        ldapId: '',
        tableHeadings: {
          faveo : {
            header: 'faveo_attribute',
            description: 'faveo_attribute_description'
          },
          thirdParty: {
            header: 'active_directory_attribute',
            description: 'active_directory_attribute_description'
          },
          overwrite: {
            header: 'overwrite',
            description: 'ldap_overwrite_description'
          }
        },
      };
    },
    watch: {},
    methods: {
      /**to fetch data for the search query basis
       * @returns {void}
       *
       */
      getldapSetting() {
        this.loading = true;
        axios
          .get(LDAP_API + this.ldapId)
          .then(res => {
            this.loading = false;
            this.hasDataPopulated = true;
            this.updateStatesWithData(res.data.data);

            this.message = res.data.message;

            if (res.data.data.search_bases.length > 0) {} else {
              setTimeout(() => {
                res.data.data.search_bases.push({
                  id: "",
                  search_base: "",
                  filter: "",
                  user_type: "user",
                  departments: [],
                  organizations: []
                });
              }, 5);
            }
          })
          .catch(err => {
            this.loading = false;
          });
      },

      /**
       * @param {Object} ldapSettingsData
       * function helps us to direactly access the data through its key.
       */
      updateStatesWithData(ldapSettingsData) {
        const self = this;
        const stateData = this.$data;
        Object.keys(ldapSettingsData).map(key => {
          if (stateData.hasOwnProperty(key)) {
            self[key] = ldapSettingsData[key];
          }
        });
      },

      /**
       * methods used to add a new user in search query user
       * ie: push the object in the array
       */
      addUser(data) {
        this.search_bases.push(data);
      },

      /**
       * populates the states corresponding to 'name' with 'value'
       * @param  {string} value
       * @param  {[type]} name
       * @return {void}
       */
      onChange(value, name) {
        this[name] = value;
      },

      /**
       * method to get the server response for the single search base query
       * @param {string}
       * @returns {void}
       */
      confirmLdapPing(data) {
        this.loading = true;
        axios
          .get("api/ldap/search-base/ping", {
            params: {
              search_base: data
            }
          })
          .then(res => {
            this.loading = false;
            successHandler(res, "searchbase");
          })
          .catch(err => {
            this.loading = false;
            errorHandler(err, "searchbase");
          });
      },

      /**
       * method to delete the user for the search base query array
       * @param {Number}
       * ie Number is the index value of the element which needs to be deleted from the array
       */
      deleteUser(index) {
        this.search_bases.splice(index, 1);
      },

      /**
       * saves ldap configuration
       *
       */
      saveConfiguration() {
        if (this.isValid()) {
          this.loading = true;
          axios
            .post(LDAP_API, this.getSaveApiParams())
            .then(res => {
              this.loading = false;
              this.showSearchQueryBlock = true;
              successHandler(res, "ldap");

              this.ldapId = res.data.data.ldap_id;

              this.getldapSetting();
            })
            .catch(err => {
              this.loading = false;
              errorHandler(err, "ldap");
            });
        }
      },

      getSaveApiParams() {
        let params = {
          id: this.ldapId !== '' ? this.ldapId : undefined,
          domain: this.domain,
          username: this.username,
          password: this.password,
          port: this.port,
          encryption: this.encryption,
          schema: this.schema,

          ldap_label: this.ldap_label,
          forgot_password_link: this.forgot_password_link,
          prefix:this.prefix,
          suffix: this.suffix,
        }

        return params;
      },
      /**check if the validations are proper
       * @returns {Boolean}
       */
      isValid() {
        const {errors,isValid} = validateLdapSettings(this.$data);

        if (!isValid) {
          return false;
        }
        return true;
      }
    },

    computed:{
      schemas(){
        return [
          {id:'active_directory',name: 'ActiveDirectory'},
          {id:'open_ldap', name:'OpenLDAP'},
          {id: 'free_ipa', name: 'FreeIPA'}
        ];
      },

      encryptions(){
        return [
          {id: null,name: 'None'},
          {id: 'ssl',name: 'SSL'},
          {id: 'tls',name: 'TLS'}
        ];
      },
    },

    components: {
      "static-select": require("components/MiniComponent/FormField/StaticSelect"),
      "text-field": require("components/MiniComponent/FormField/TextField"),
      alert: require("components/MiniComponent/Alert"),
      "custom-loader": require("components/MiniComponent/Loader"),
      "search-basis": require("./SearchBasis.vue"),
      "user-import-mapper": require("components/Extra/UserImportMapper"),
      "ldap-table": require("./LdapTable.vue"),
      checkbox: require("components/MiniComponent/FormField/Checkbox"),
      'faveo-box': FaveoBox,
    }
  };
</script>

<style>
</style>
