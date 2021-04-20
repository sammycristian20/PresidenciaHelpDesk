<template>
  <div>
    <div v-if="loading" class="row">
      <custom-loader :duration="4000" />
    </div>

    <alert componentName="createUpdateAgent" />

    <faveo-box :title="lang(title)" v-if="hasDataPopulated" id="form-main-box" boxClass="card-light">

      <faveo-box :title="lang('agent_info')" boxClass="card-light">
        <div class="row">
          <text-field id="email" :label="lang('email')" type="text" name="email" classname="col-sm-6" :value="email"
            :onChange="onChange" :required="true">
          </text-field>

          <text-field id="user_name" :label="lang('user_name')" type="text" name="user_name"
            classname="col-sm-6" :value="user_name" :onChange="onChange" :required="true">

          </text-field>
        </div> 

        <div class="row">

          <text-field id="first_name" :label="lang('firstname')" type="text" name="first_name" classname="col-sm-4"
            :value="first_name" :onChange="onChange" :required="true">
          </text-field>

          <text-field id="last_name" :label="lang('lastname')" type="text" name="last_name" classname="col-sm-4"
            :onChange="onChange" :value="last_name" :required="false">
          </text-field>

          <dynamic-select id="location" name="location" classname="col-sm-4" apiEndpoint="/api/dependency/locations"
            :multiple="false"  :label="lang('location')" :value="location" :required="false"
            :onChange="onChange">
          </dynamic-select>
        </div>

        <div class="row">
          <text-field id="phone_number" :label="lang('phone')" type="number" name="phone_number" class="col-sm-3"
            :value="phone_number" :onChange="onChange">
          </text-field>

          <text-field id="ext" :label="lang('ext')" type="number" name="ext" class="col-sm-1" :value="ext"
            :onChange="onChange">
          </text-field>

          <phoneWithCountryCode id="mobile" classname="col-sm-4" name="mobile" :onChange="onChange" :value="mobile"
            :countryCode="country_code" :countryIso="iso" @countCode="getCountCode" @countIso="getCountIso">
          </phoneWithCountryCode>

          <dynamic-select id="time-zone" name="agent_tzone" classname="col-sm-4"
            apiEndpoint="/api/dependency/time-zones" :label="lang('agent_time_zone')" :value="agent_tzone"
            :required="true" :multiple="false"  :onChange="onChange">
          </dynamic-select>
        </div>
      </faveo-box>

      <faveo-box :title="lang('account_status_setting')" boxClass="card-light">
        <div class="row">
            <dynamic-select id="departments" name="departments" classname="col-sm-6"
              apiEndpoint="/api/dependency/departments" :label="lang('department')" :hint="lang('primary_department')"
              :value="departments" :required="true" :multiple="true"  :onChange="onChange">
            </dynamic-select>
            <radio-button id="role" name="role" classname="form-group col-sm-6" :options="roleRadioOptions"
            :label="lang('role')" :value="role" :onChange="onChange">
            </radio-button>
        </div>
        <div class="row">
            <dynamic-select id="teams" name="teams" classname="col-sm-6" apiEndpoint="/api/dependency/teams"
              :label="lang('team')" :hint="lang('assigned_team')" :value="teams" :required="false" :multiple="true"
               :onChange="onChange">
            </dynamic-select>
            <dynamic-select id="type" name="type" classname="col-sm-6" apiEndpoint="/api/dependency/types"
               :label="lang('type')" :hint="lang('ticket_type')" :value="type" :onChange="onChange"
              :required="false" :multiple="true">
            </dynamic-select>
        </div>
      </faveo-box>

      <template v-if="role === 'agent'">
        
        <faveo-box :title="lang('access-permissions')" boxClass="card-light">
          <div class="row">
            <div class="col-sm-4" id="assign_groups" style="font-w" v-for="item in permissionsList" :key="item.id">
              <checkbox :name="item.key" :value="item.checked" :disabled="item.disabled" :label="item.key"
                :title="item.name" :onChange="checkBoxOperations" :id="item.key">
              </checkbox>
            </div>
          </div>
        </faveo-box>

        <div id="permission-list-box"></div>
      </template>
      
      <faveo-box :title="lang('agent_sign')" boxClass="card-light">
        <div class="row">
          <tiny-editor :value="agent_sign" type="text" :onChange="onChange" name="agent_sign" label=""
            classname="row col-sm-12" :lang="'en'" />
        </div>
      </faveo-box>

      <div class="card-footer" slot="actions">
        <button id="agent-submit" class="btn btn-primary" type="button" @click="onSubmit">
          <span :class="iconClass" id="submit-btn"></span>&nbsp; {{ lang(submitButton)}}
        </button>
      </div>
    </faveo-box>

  </div>
</template>

<script>

  import axios from "axios";
  import { getIdFromUrl } from 'helpers/extraLogics';
  import { errorHandler, successHandler } from "helpers/responseHandler";
  import { validateCreateAgentFormFields } from "helpers/validator/createAgentFormValidations.js";
  import {flatten, boolean} from "helpers/extraLogics";
  import FaveoBox from 'components/MiniComponent/FaveoBox';

  export default {
    name: "create-update-agent",
    description: "Agent create and update page",

    components: {
      'alert': require("components/MiniComponent/Alert"),
      'custom-loader': require("components/MiniComponent/Loader"),
      'text-field': require("components/MiniComponent/FormField/TextField"),
      'radio-button':require('components/MiniComponent/FormField/RadioButton'),
      'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
      'phoneWithCountryCode': require('components/MiniComponent/FormField/PhoneWithCountryCode.vue'),
      checkbox: require("components/MiniComponent/FormField/Checkbox"),
      'faveo-box': FaveoBox,
    },

    data: () => ({

      loading: true,
      hasDataPopulated: false,
      title: 'create_an_agent',
      iconClass: 'fas fa-save',
      submitButton: 'save',
      agentId: 0,
      first_name: '',
      last_name: '',
      user_name: '',
      email: '',

      ext: '', // extention of phone_number number
      phone_number: '',
      country_code: null,
      mobile: '',

      role: 'admin', // Admin or Agent
      roleRadioOptions: [{name:'Admin', value: 'admin'}, {name:'Agent', value: 'agent' }],

      teams: '',
      permissions: '',
      agent_sign: '',
      departments: [],
      type: [],
      location: '',
      agent_tzone: '', // ex- {"id": "81", "name": "(GMT+05:30) Asia/Kolkata"}

      permissionsList: [],
      selectedPermissionList: [],
      selectedPermissionIdsSD: '',
      iso : '',
      sdAgentPermissions : []
    }),

    beforeMount() {
      this.getInitialValue();
    },

    created() {
      // listen the event `selected-permission-list` fired from external script 
      window.eventHub.$on("selected-permission-list", (data) => {
        this.selectedPermissionIdsSD = data;
      })
    },

    methods: {
        /**
         * The function which will be called whaen value of the field changes.
         * @param {value}
         * `value` will be the updated value of the field
         * @param {name}
         * `name` will be thw name of the state in the parent class    
         * @return {Void} 
         */
        onChange(value, name){
            this[name] = value;
            if(name === 'role') {

              if(value === 'agent') {

                this.updatePermissions();

                this.showSdPermissionListBox(this.sdAgentPermissions)
              }
            }
        },

        getCountCode(value){
          this.country_code = value;
        },

        getCountIso(value){
          this.iso = value;
        },

        /** Get permission list
        * Adding `checked` & `disabled` properties
        * Call agent info if edit agent page
        */
        getInitialValue() {

          // Getting permission list
          axios.get('api/dependency/permissions?limit=all', {})
          .then(res => {
            this.permissionsList = flatten(res.data.data);
            this.updatePermissions();

            if (this.isEditAgent(window.location.pathname)) {// if it is an edit agent call
              this.agentEditMode();
            } else {
              this.showSdPermissionListBox();
              this.hasDataPopulated = true;
              this.loading = false;
            }
          }).catch(err=>{
            errorHandler(err)
          })
        },

        updatePermissions() {
          this.permissionsList.forEach(function(element) {
            element.checked = false ;
            element.disabled = false;
            element.title = ""
            if(element.key === "global_access"){
              element.title = "Restricted access will be disabled if global access is checked."
            } else if(element.key === "restricted_access"){
              element.title = "Global access will be disabled if restricted access is checked."
            }
          });
        },

        /**
        * Scenarios for agent edit mode
        */
        agentEditMode(){
          this.title = 'edit_an_agent';
          this.iconClass = 'fas fa-sync'
          this.submitButton = 'update'
          this.getAgentInfo(this.agentId);
        },

        /**
         * This method fetch the agent info
         * @param {agentId}
         * @return {Void} 
         */
        getAgentInfo(agentId) {
          this.loading = true;
          // api request of specific agent info
          axios.get('/api/admin/agent/' + agentId).then(res => {
            this.fillAgentInfo(res.data.data.agent);
          }).catch(err => {
            errorHandler(err)
            this.loading = false;
            this.hasDataPopulated = true
          });
        },

        /**
         * This method fill the view for update agent
         * @param {agentInfo}
         * @return {Void} 
         */
        fillAgentInfo(agentInfo) {
          try {
            this.email = agentInfo.email ? agentInfo.email : ''
            this.first_name = agentInfo.first_name ? agentInfo.first_name : ''
            this.last_name = agentInfo.last_name ? agentInfo.last_name : ''
            this.user_name = agentInfo.user_name ? agentInfo.user_name : ''
            this.agent_sign = agentInfo.agent_sign ? agentInfo.agent_sign : ''

            this.location = boolean(agentInfo.location) ? agentInfo.location: ''
            this.role = agentInfo.role ? agentInfo.role : ''
            this.ext = agentInfo.ext ? agentInfo.ext : ''
            this.country_code = agentInfo.country_code ? agentInfo.country_code.id: null
            this.iso = agentInfo.iso ? agentInfo.iso: null
            this.mobile = (agentInfo.mobile && agentInfo.mobile !== 'Not available') ? agentInfo.mobile : ''
            this.phone_number = agentInfo.phone_number ? agentInfo.phone_number : ''
            this.agent_sign = agentInfo.agent_sign ? agentInfo.agent_sign  : ''
            this.agent_tzone = agentInfo.agent_tzone ? agentInfo.agent_tzone : ''
            this.type = agentInfo['type'] ? agentInfo['type']: ''
            this.departments = agentInfo.departments ? agentInfo.departments: ''
            this.teams = agentInfo.teams ? agentInfo.teams : ''

            var that = this;
            var ctr = 0;
            var _tempVal = undefined
            agentInfo.permissions.forEach(function(element, index, array){
              ctr++;
              if(element.key === 'global_access' || element.key === 'restricted_access') {
                _tempVal = element.key
              }
              that.checkBoxOperations(true, element.key, true)
              if(ctr===array.length && _tempVal){
                that.handleGlobalResMode(true, _tempVal);
              }
            });

            this.sdAgentPermissions = agentInfo.sd_permissions;

            this.showSdPermissionListBox(agentInfo.sd_permissions);
            this.loading = false;
            this.hasDataPopulated = true
            
          } catch (e) {
            console.error('error in fillAgentInfo method ', e);
          }
        },

        checkBoxOperations(value, selectedId, isEdit) {
          let that = this;
          this.permissionsList.forEach(function(element) {
            if(element.key == selectedId) {
              element.checked = value;
              return;
            }
            that.handleGlobalResMode(value, selectedId);
          });
        },

        handleGlobalResMode(value, selectedId) {
          setTimeout(()=> {
            if(selectedId == 'global_access' || selectedId == 'restricted_access'){
              let restrictedId = selectedId == 'global_access' ? 'restricted_access' : 'global_access';
              if(this.permissionsList.find(obj => obj.key == selectedId).checked){
                  if(this.permissionsList.find(obj => obj.key == restrictedId).checked){
                    this.permissionsList.find(obj => obj.key == restrictedId).checked = false;
                    this.permissionsList.find(obj => obj.key == restrictedId).disabled = true;
                  }
                }
              document.getElementById(restrictedId).checked = false;
              document.getElementById(restrictedId).disabled = value;
            }
          },500)
        },

        /**
        * Check if url contains edit
        * @return {Boolean}
        */
        isEditAgent(path){
           if(path.indexOf("edit") >= 0) {
            this.agentId = getIdFromUrl(path); // Getting agent id from url
            return true
           } else {
            return false;
           }
        },

        /**
         * This method makes api call
         * @param {agentInfo}
         * @return {Void} 
         */
        onSubmit() {
          this.agent_sign = this.getAgentSign();
          if(this.isValidInputs()) {
            this.loading = true;
            let params = this.getSubmitParams();
            axios.post('api/admin/agent/', params)
            .then(res => {
              this.loading = false;
              successHandler(res,'createUpdateAgent');
              this.redirectToAgentListPage();
            })
            .catch(err => {
              this.loading = false;
              errorHandler(err,'createUpdateAgent');
            });
          }
        },

        getSubmitParams() {
          let params = {
            email: this.email,
            user_name: this.user_name,
            first_name: this.first_name,
            agent_tzone_id: this.agent_tzone ? this.agent_tzone.id : '',
            role: this.role,
            department_ids: this.departments.map(d => d.id),
            id: this.agentId,
            last_name: this.last_name,
            type_ids: this.type ? this.type.map(t => t.id) : '',
            location_id:this.location ? this.location.name : '',
            permission_ids: this.role === 'agent' ? this.permissionsList.filter(perm => perm.checked).map(p => p.id) : [],
            team_ids: this.teams ? this.teams.map(tm => tm.id) : '',
            agent_sign: this.agent_sign,
            ext: this.ext,
            phone_number: this.phone_number,
          };
          if(this.country_code !== null) {
            params['mobile'] = this.mobile;
            params['country_code'] = this.country_code;
            params['iso'] = this.iso;
          }

          if(this.selectedPermissionIdsSD){
            params['sd_permission_ids'] = this.role === 'agent' ? this.selectedPermissionIdsSD : [];
          }
          return params;
        },

        // redirect to agent list page
        redirectToAgentListPage() {
          if(window.location.href.indexOf('user') > -1) {
            window.location.href = window.axios.defaults.baseURL + '/user';
          } else {
            window.location.href = window.axios.defaults.baseURL + '/agents';
          }
        },

        getAgentSign() {

          return this.agent_sign;
        },

        /**
         * This method validates the user input for mandatory fields
         * @return {Boolean} 
         */
         isValidInputs() {
          const {errors, isValid} = validateCreateAgentFormFields(this.$data);
          return isValid;
         },

        /**
         * For any external script to be able to inject some javascript on sd permission box
         * @return {undefined}
        */
        showSdPermissionListBox(data) {
          window.eventHub.$emit("permission-list-box-mounted", data);
        },

      },
  };
</script>

<style scoped>

</style>