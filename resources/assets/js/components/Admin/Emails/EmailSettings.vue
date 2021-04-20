<template>
  <div>
    <!--loader-->
    <div class="row" v-if="hasDataPopulated === false || loading === true">
      <custom-loader :duration="loadingSpeed"></custom-loader>
    </div>

    <!-- alert message which only gets mounted when vuex has non empty alert values -->
    <alert />
    <!--mail info-->
    <!-- will be visible only when component is mounted -->

    <faveo-box :title="lang('email_settings')" v-if="hasDataPopulated">
      <faveo-box :title="lang('email_info')">
        <div class="row">

          <!-- email address -->
          <text-field label="Email" :value="email_address" type="email" name="email_address" :onChange="onChange"
            classname="col-sm-3" :required="true"></text-field>

          <!-- email address -->
          <text-field label="Username" :value="user_name" type="email" name="user_name" :onChange="onChange"
            classname="col-sm-3" :hint="lang('user_name_hint')"></text-field>

          <!-- email name -->
          <text-field label="Name" :value="email_name" name="email_name" :onChange="onChange" classname="col-sm-3"
            :hint="lang('email_name_hint')" :required="true"> </text-field>

          <!-- password : if selected sending_protocol is phpmail and fetch mail is disabled,
                    there should be no password block-->
          <text-field v-if="hasPassword" label="Password" type="password" :value="password" name="password"
            :onChange="onChange" classname="col-sm-3" :required="true"> </text-field>

        </div>
      </faveo-box>

      <faveo-box :title="lang('ticket_settings')">
        <tool-tip slot="headerTooltip" :message="lang('ticket_settings_note')" size="medium"></tool-tip>

        <div class="row">
          <!--departments-->
          <dynamic-select :label="lang('department')" :multiple="false" name="department" 
            classname="col-sm-4" apiEndpoint="/api/dependency/departments" :value="department" :onChange="onChange">
          </dynamic-select>

          <!--help topics-->
          <dynamic-select :label="lang('help_topic')" :multiple="false" name="helptopic" 
            classname="col-sm-4" apiEndpoint="/api/dependency/help-topics" :value="helptopic" :onChange="onChange">
          </dynamic-select>

          <!--priority-->
          <dynamic-select :label="lang('priority')" :multiple="false" name="priority" 
            classname="col-sm-4" apiEndpoint="/api/dependency/priorities" :value="priority" :onChange="onChange">
          </dynamic-select>
        </div>

      </faveo-box>

      <faveo-box :title="lang('incoming_email')">

        <tool-tip slot="headerTooltip" :message="lang('incoming_emails_note')" size="medium"></tool-tip>

        <div class="card-tools switch-pos" slot="headerMenu">

          <status-switch slot="headerMenu" name="fetching_status" :value="fetching_status" :onChange="onChange"
            classname="btn-tool" :bold="true">
          </status-switch>
        </div>

        <!--for incoming mails-->
          <fetching-protocol :onChange="onChange" :fetching_protocol="fetching_protocol"
            :fetching_encryption="fetching_encryption" :fetching_host="fetching_host" :fetching_port="fetching_port"
            :fetching_status="fetching_status" :delete_email="delete_email"
            :version="version">
          </fetching-protocol>

      </faveo-box>

      <faveo-box :title="lang('outgoing_email')">

        <tool-tip slot="headerTooltip" :message="lang('outgoing_emails_note')" size="medium"></tool-tip>

        <div class="card-tools switch-pos" slot="headerMenu">
          
          <status-switch  name="sending_status" :value="sending_status" :onChange="onChange"
            classname="btn-tool" :bold="true">
          </status-switch>
        </div>

        <!--for outgoing mails-->
      
        <sending-protocol type="send" :onChange="onChange" :sending_protocol="sending_protocol"
          :sending_encryption="sending_encryption" :sending_host="sending_host" :sending_port="sending_port"
          :sending_status="sending_status" :api_key="api_key" :secret="secret" :domain="domain" :region="region"
          :version="version">
        </sending-protocol>
      
        <div class="row">
          <!-- internal notes -->
          <text-field :label="lang('internal_notes')" :value="internal_notes" type="textarea" name="internal_notes" :onChange="onChange" classname="col-sm-12"/>

          <!-- make system default email -->
          <checkbox name="is_system_default" :value="is_system_default" :label="lang('make_system_default_mail')" :onChange="onChange" classname="col-sm-12"/>
        </div>

      </faveo-box>

      <div slot="actions" class="card-footer">
        <button type="button" v-on:click="onSubmit" :disabled="loading" class="btn btn-primary update-btn">
          <span :class="iconClass"></span>&nbsp;{{lang(btnName)}}
        </button>
      </div>

    </faveo-box>
  </div>
</template>


<script type="text/javascript">
import axios from "axios";
import { errorHandler, successHandler } from "helpers/responseHandler";
import {
  validateEmailSettings,
  shallPasswordBeVisble
} from "helpers/validator/emailSettingsRules";
import {getIdFromUrl} from 'helpers/extraLogics';
import FaveoBox from 'components/MiniComponent/FaveoBox';

export default {
  name: "email-settings",

  description: "email settings page",

  beforeMount() {
      this.setMode();
      this.getInitialValues();
  },

  data: () => ({
    id: null,

    department: "", //selected department
    priority: "", //selected priority
    helptopic: "", //selected helptopic
    email_address: "", //typed email
    email_name: "", //types email_name
    user_name: "",
    password: "", // typed password

    fetching_status: true,
    fetching_protocol: "",
    fetching_encryption: "",
    fetching_host: "",
    fetching_port: "",

    sending_status: true,
    sending_protocol: "",
    sending_encryption: "",
    sending_host: "",
    sending_port: "",

    api_key: "",
    secret: "",
    region: "",
    domain: "",
    version:"",

    is_system_default: "",
    delete_email: "",

    loading: true,
    loadingSpeed: 4000,
    internal_notes: "",
    hasDataPopulated: false,
    mode : "create",
    iconClass : 'fas fa-save',
    btnName : 'save'
  }),

  methods: {

      setMode(){
          const path = window.location.pathname;
          this.mode = path.indexOf("edit") >= 0 ? "edit" : "create";
      },

      /**
     * gets initial state of states defined in this component
     * @return {void}
     */
    getInitialValues() {
      this.loading = true;

      if (this.mode === "edit") {

        this.iconClass = 'fas fa-sync';
        this.btnName = 'update';
        //match from the end
        const path = window.location.pathname;
        const emailSettingsId = getIdFromUrl(path);

        axios
          .get(`/api/email-settings/${emailSettingsId}`)
          .then(res => {
            this.updateStatesWithData(res.data.data);
            this.hasDataPopulated = true;
            this.loading = false;
          })
          .catch(err => {
            errorHandler(err)
            this.hasDataPopulated = true;
            this.loading = false;
          });

      } else {
        // for creating a new email
        this.hasDataPopulated = true;
        this.loading = false;
      }
    },

    /**
     * updates state data for this component
     * @param {Object} emailSettingsData    settings data object (from backend)
     * */
    updateStatesWithData(emailSettingsData) {
      const self = this;
      const stateData = this.$data;
      Object.keys(emailSettingsData).map(key => {
        //if vue state has a property with name same as 'key'
        if (stateData.hasOwnProperty(key)) {
          self[key] = emailSettingsData[key];
        }
      });
      // backend sends api_key as `key`, but `key` is a reserved word in javascript so it cannot be used
      // so we change its name to `api_key`
      self['api_key'] = emailSettingsData['key'];
    },

    /**
     * checks if the given form is valid
     * @return {Boolean} true if form is valid, else false
     * */
    isValid() {
      const { errors, isValid } = validateEmailSettings(this.$data);
      if (!isValid) {

        return false;
      }
      return true;
    },

    /**
     * sends an ajax request to server after validating it
     * */
    onSubmit() {
      if (this.isValid()) {
        this.loadingSpeed = 8000;
        this.loading = true;
        axios
          .post("/api/admin/email-settings", {
            id: this.id,
            department: this.department !== null ? this.department.id : null,
            priority: this.priority !== null ? this.priority.id : null,
            help_topic: this.helptopic !== null ? this.helptopic.id : null,
            email_address: this.email_address,
            user_name: this.user_name,
            email_name: this.email_name,
            password: this.password,

            fetching_status: this.fetching_status,
            fetching_protocol: this.fetching_protocol,
            fetching_encryption: this.fetching_encryption,
            fetching_host: this.fetching_host,
            fetching_port: this.fetching_port,
            delete_email: this.delete_email,

            sending_status: this.sending_status,
            sending_protocol: this.sending_protocol,
            sending_encryption: this.sending_encryption,
            sending_host: this.sending_host,
            sending_port: this.sending_port,

            key: this.api_key,
            secret: this.secret,
            region: this.region,
            domain: this.domain,
            version: this.version,

            is_system_default: this.is_system_default,
            internal_notes: this.internal_notes
          })
          .then(res => {
            this.loading = false;
            successHandler(res);
            this.redirectIfNeeded()
          })
          .catch(err => {
            this.loading = false;
            errorHandler(err);
          });
      }
    },

      /**
       * redirects to index page, if needed
       */
      redirectIfNeeded() {
          if(this.mode === "create"){
              setTimeout(() => {
                  this.redirect("/emails")
              }, 2000)
          }
      },

    /**
     * populates the states corresponding to 'name' with 'value'
     * @param  {string} value
     * @param  {[type]} name
     * @return {void}
     */
    onChange(value, name) {
      this[name] = value;
    }
  },

  computed: {
    /**
     * @return {Boolean}    whether password field should be visible
     * */
    hasPassword() {
      return shallPasswordBeVisble(this.$data);
    }
  },

  components: {
    "text-field": require("components/MiniComponent/FormField/TextField"),
    "dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
    alert: require("components/MiniComponent/Alert"),
    "fetching-protocol": require("./FetchingProtocol"),
    "sending-protocol": require("./SendingProtocol"),
    "status-switch": require("components/MiniComponent/FormField/Switch"),
    checkbox: require("components/MiniComponent/FormField/Checkbox"),
    "tool-tip": require("components/MiniComponent/ToolTip"),
    "custom-loader": require("components/MiniComponent/Loader"),
    'faveo-box': FaveoBox,
  }
};
</script>
<style type="text/css">
input .form-control {
  border-radius: 30px !important;
}

.with-switch {
  padding-bottom: 0px !important;
  margin-bottom: -3px;
}

.with-switch > h3 {
  margin-top: 2px !important;
}

.pointer-none {
  pointer-events: none;
}

.switch-pos{position: relative; top: 6px;}
</style>
