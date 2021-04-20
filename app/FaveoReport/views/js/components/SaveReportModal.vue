<template>
  <div>
    <modal :showModal="true" :onClose="onClose">

      <div slot="title">
        <h4 class="modal-title">{{lang(title)}}</h4>
      </div>

      <div slot="alert">

        <alert componentName="save-report-modal"/>
      </div>

      <div slot="fields">
        
        <custom-loader v-if="isLoading"></custom-loader>
      </div>

      <div slot="fields">
        <text-field
          id="name"
          :label="lang('name')"
          type="text"
          name="name"
          classname="col-md-12"
          :value="name"
          :onChange="onPropertyChange"
          :required="true">
        </text-field>

        <text-field
          id="description"
          :label="lang('description')"
          type="textarea"
          name="description"
          classname="col-md-12"
          :value="description"
          :onChange="onPropertyChange"
          :required="true">
        </text-field>

        <checkbox
          name="isPublic"
          :value="isPublic"
          :label="lang('make_this_report_public')"
          :onChange="onPropertyChange"
          classname="col-md-12"
          id="allow-only-ldap-login">
        </checkbox>
      </div>

      <div slot="controls">
        <button class="btn btn-primary" id="new-report-submit" @click="onSubmit" :disabled="isLoading">
          <span><i class="fas fa-code-branch"></i></span>
          {{ trans('fork') }}
        </button>
      </div>

    </modal>
  </div>
</template>

<script type="text/javascript">
import axios from 'axios';
import {errorHandler,successHandler} from "helpers/responseHandler";
import {getIdFromUrl} from "helpers/extraLogics";

export default {

    name:'save-report-modal',

    props: {

      // fork(new report) or update existing one
      modalMode: {
        type: String,
        required: true
      },

      // report data oject need to be saved
      reportDataObj: {
        type: Object,
        required: true
      },

      // on clodse modal fn
      onClose: {
        type: Function,
        required: true
      },
    },

    data() {
      return {
        title: '', // modal heading
        name: '', // report name
        description: '', // report description
        isPublic: true, // is public/private report
        isLoading: false
      }
    },

    beforeMount() {
      this.setUpComponentPropertiesBasisOfMode();
    },

    methods: {

      setUpComponentPropertiesBasisOfMode() {
        if(this.modalMode === 'fork') {
          this.title = 'fork_this_report';
        } else if(this.modalMode === 'update') {
          this.name = this.reportDataObj.name;
          this.description = this.reportDataObj.description;
          this.isPublic = this.reportDataObj.is_public;
          this.title = 'update_this_report';
        }
      },

      onSubmit() {
        this.isLoading = true;

        // if creating,
        let postConfigUrl = 'api/agent/report-config';

        if(this.modalMode === 'fork'){
          // if forking, we need parent id
          postConfigUrl = postConfigUrl + "/" + getIdFromUrl(window.location.pathname);
        }

        axios.post(postConfigUrl,  this.getSaveReportParams())
        .then(res => {
          setTimeout(() => this.onClose(), 1000); // close the modal after 1 second
          successHandler(res, 'save-report-modal');
          if(this.modalMode === 'fork') { // redirect to report list page only if the case of `fork`
            this.redirectToReporListPage();
          } else { // refresh the entry page while update completion
            window.eventHub.$emit('refreshReportEntryPage');
          }
        }).catch(err => {
          errorHandler(err, 'save-report-modal');
        }).finally(res => {
          this.isLoading = false;
        });
      },

      /**
       * Get the parameters to be saved for the report
       */
      getSaveReportParams() {
        const clonedReportDataObj = JSON.parse(JSON.stringify(this.reportDataObj));
        if(this.modalMode === 'fork') { // assign null to report id to create a fresh new report
          clonedReportDataObj.id = null;
        }
        clonedReportDataObj.name = this.name;
        clonedReportDataObj.description = this.description;
        clonedReportDataObj.is_public = this.isPublic;

        return clonedReportDataObj;
      }, 

      redirectToReporListPage() {
        window.location.href = window.axios.defaults.baseURL + '/report/get';
      },

      // Assign value to component properties
      onPropertyChange(value, property) {
        this[property] = value;
      },
    },

    components: {
      'modal': require('components/Common/Modal'),
      'alert': require("components/MiniComponent/Alert"),
      'text-field': require('components/MiniComponent/FormField/TextField'),
      'checkbox': require("components/MiniComponent/FormField/Checkbox"),
      "custom-loader": require("components/MiniComponent/Loader")
    }
  }

</script>

<style>
</style>
