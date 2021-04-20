<template>

  <!-- This component recieves one form-field at a time, so any one of the below component will get mounted at a time on basis of its type (;; for each form field there will be a seperate instance of this component) -->
  <div>

    <tiny-editor-with-validation v-if="formField.type ==='htmltextarea'" :id="formField.unique" :name="formField.unique" :label="formField.label" :value="selectedValue || ''" :hint="formField.description" :isInlineForm="true"  :onChange="onChange" :rules="validationRules" :required="isRequiredField" :mediaOption="formField.media_option" :attachments="attachments"
    :getAttach="getAttachments" :panel="panel">
      
    </tiny-editor-with-validation>

    <date-time-field v-if="formField.type === 'date'" type="datetime"  :label="formField.label" :value="selectedValue" :id="formField.unique" :name="formField.unique" :onChange="onChange" :required="isRequiredField" format="MMMM DD YYYY HH:mm" :clearable="true" :hint="formField.description" :isInlineForm="true" :rules="validationRules" outputFormat="YYYY-MM-DD HH:mm:ss" from="form">
      
    </date-time-field>

    <!-- Dynamic Select with options form api -->
    <dynamic-select v-if="(formField.type === 'api' || formField.type === 'multiselect') && !isMultipleRequester" :id="formField.unique" :label="formField.label" :multiple="formField.type === 'multiselect'" :name="formField.unique" :apiEndpoint="apiInfoUrl" :value="selectedValue" :onChange="onChange" :required="isRequiredField"
    :actionBtn="getActionButtonObj" :apiParameters="getFaveoFormData[formUniqueKey] ? getFaveoFormData[formUniqueKey].formDataMap : {}" :isInlineForm="true" :rules="validationRules" :hint="formField.description" :strlength="75"></dynamic-select>

    <file-upload v-if="formField.unique === 'requester' && isMultipleRequester && panel !== 'client'" :label="formField.label" :value="selectedValue" :id="formField.unique" :name="formField.unique" :onChange="onChange" :required="isRequiredField" accept=".csv,.xlxs" :isInlineForm="true" rules="required"></file-upload>

    <div class="checkbox multi-requester-checkbox pull-right" v-if="showMultiRequesterCheckbox">
      <label class="control-label">
        <input type="checkbox" v-model="isMultipleRequester" name="isMultipleRequester" @change="clearSelectedValue()">
        {{trans('upload_multi_requester')}}
      </label>
    </div>

    <!-- Dynamic select with static options -->
    <dynamic-select v-if="formField.type === 'select'" :id="formField.unique" :label="formField.label" :multiple="false" :name="formField.unique" :elements="dropdownOptions" :value="selectedValue" :onChange="onChange" :required="isRequiredField" :optionLabel="optionLabel" :isInlineForm="true" :rules="validationRules" :hint="formField.description" :strlength="75"></dynamic-select>

    <dynamic-select v-if="formField.type === 'taggable'" :id="formField.unique" :label="formField.label" :multiple="true" :taggable="true" :name="formField.unique" :elements="[]" :value="selectedValue" :onChange="onChange" :required="isRequiredField" :optionLabel="optionLabel" :isInlineForm="true" :rules="validationRules" :hint="formField.description" :strlength="75"></dynamic-select>

    <text-field v-if="formField.type === 'text' || formField.type === 'textarea' || formField.type === 'number' || formField.type === 'email'" :id="formField.unique" :label="formField.label" :type="formField.type" :name="formField.unique" :value="selectedValue" :onChange="onChange" :required="isRequiredField" :isInlineForm="true" :rules="validationRules" :pattern="formField.pattern" :hint="formField.description" :validation-message="formField.validation_message"></text-field>

    <client-requester v-if="formField.type === 'client-panel-requester'" :id="formField.unique" :label="formField.label" :name="formField.unique" :value="selectedValue" :onChange="onChange" :required="isRequiredField" :isInlineForm="true" :hint="formField.description" :rules="validationRules" :actionBtn="getActionButtonObj"></client-requester>

    <radio-field v-if="formField.type === 'radio'" :name="formField.label" :label="formField.label" :hint="formField.description" :required="isRequiredField" :options="formField.options" :value="selectedValue" :onChange="onChange" :isInlineForm="true" :rules="validationRules"></radio-field>

    <checkbox-field v-if="formField.type === 'checkbox'" :name="formField.label" :label="formField.label" :hint="formField.description" :required="isRequiredField" :options="formField.options" :value="selectedValue" :onChange="onChange" :isInlineForm="true" :rules="validationRules"></checkbox-field>

    <file-upload v-if="formField.type === 'file'" :label="formField.label" :value="selectedValue" :name="formField.unique" :onChange="onChange" :required="isRequiredField" :id="formField.unique" accept="" :isInlineForm="true" :rules="validationRules" :hint="formField.description" :multiple="formField.unique!=='organisation_logo' "></file-upload>

    <template v-if="formField.type === 'custom'">
      <div id="faveo-app-extra-form-field">{{customTypeFormFieldMounted()}}</div>
    </template>

    <div v-for="node in childNodes" :key="node.unique" class="form-field-wrapper">
      <form-field-renderer :form-field="node" :formUniqueKey="formUniqueKey" :panel="panel" :scenario="scenario"></form-field-renderer>
    </div>

    <transition name="modal">
      <user-form-modal v-if="showCreateUserModal" :show="showCreateUserModal" :closeModal="() => showCreateUserModal = false" :updateNewRequester="onChange" :panel="panel"></user-form-modal>
    </transition>

  </div>
  
</template>

<script>
import { MULTIPLE_PROPERTY_HELPER, boolean } from 'helpers/extraLogics';
import { getFormFieldValue } from 'helpers/formUtils';
import axios from 'axios';
import { mapGetters } from 'vuex';

export default {

  name: 'form-field-renderer',

  props: {

    // Form unique key to pick data from the Store
    formUniqueKey: { type: String, required: true },

    // Form field object
    formField: { type: Object, required: true },

    // One category may be present in differnet panels(admin/client/agent)
    panel: { type: String, default: 'agent' },

    // scenario will be the mode of the FaveoForm Category; may be create/edt/recur etc
    scenario: { type: String, require: true },

  },

  data: () => {
    return {

      // formField object as a child of another form field, we are updating this object in onChange fn
      childNodes: [],

      // For dynamic-select rendered with some static options, this array will have all the options
      dropdownOptions: [],

      // Checkbox value to switch between file-upload and dynamic-select to handle batch ticket
      isMultipleRequester: false,

      // selected value of the form field
      selectedValue: '',

      // if true one pop-up will appear to create new requester/user
      showCreateUserModal: false,

      attachments : []
    }
  },

  beforeMount () {
    
    // If type is 'select' we will assign options to the `dropdownOptions`
    if(this.formField.type === 'select') {
      this.getSelectFieldOptions()
    }

  },

  mounted () {
    // Assign form-field value to the `selectedValue` -- used when scenario is edit
    this.selectedValue = getFormFieldValue(this.formField.value, this.formField.options, this.formField.default, this.formField.type);

    this.bindValuesFromUrl();

    this.renderNestedFormFieldsIfAny(this.selectedValue);

    // update form-value object with current selected value. In case of create it will be empty, in case of edit it may have some value
    this.updateFormValuesToStore();
  },

  computed: {

    ...mapGetters({ getFaveoFormData: 'getFaveoFormData', isBatchTicketMode: 'getBatchTicketMode' }),

    /**
     * this will have url part of the api_info --used in case of <title = 'Api'>
     */
    apiInfoUrl () {

      let apiUrl = MULTIPLE_PROPERTY_HELPER.convertStringOfPropertiesToObject(this.formField.api_info).url;

      if(apiUrl == '/api/dependency/departments?meta=true' || apiUrl == '/api/dependency/help-topics?meta=true'){
        apiUrl = apiUrl + '&panel='+this.panel;
      }

      return apiUrl
    },

    optionLabel () {
      return MULTIPLE_PROPERTY_HELPER.convertStringOfPropertiesToObject(this.formField.api_info).key || 'label';
    },


    // returns Boolean of required property
    isRequiredField () {
      return Boolean(this.formField.required);
    },


    /**
     * Provides validation string joined by pipes
     * https://logaretm.github.io/vee-validate/guide/basics.html
     */
    validationRules () {
      let validationObj = [];

      if (this.isRequiredField) {
        validationObj.push('required')
      }

      return validationObj.join('|')
    },


    /**
     * Provides an object for adding a button to the dynamic-select for other operations
     */
    getActionButtonObj () {

      if (this.formField.unique === 'requester') {
        // on client panel, user will be registering itself. But on agent panel, agent will be creating other users
        let text = this.panel === 'client' ? 'Register' : 'Create User';
        return { text: text, action: () => this.showCreateUserModal = true }
      } else {
        return null;
      }
    },

    showMultiRequesterCheckbox () {
      return this.formField.unique === 'requester' && this.isBatchTicketMode && this.panel === 'agent';
    }
  },



  methods: {

    getAttachments(value) {

      this.attachments = value;

      this.$store.dispatch('setEditorAttachments', this.attachments)
    },

    // triggered when any form-field value detects any change
    onChange (value, name) {
      this.selectedValue = value ? value : ''
      this.updateFormValuesToStore()
      this.renderNestedFormFieldsIfAny(value)
    },

    // Update form value to the Store object for the corresponding faveoForm instance
    updateFormValuesToStore () {
      const formData = {
        id: this.formField.unique,
        isDefault: Boolean(this.formField.default),
        formUniqueKey: this.formUniqueKey,
        selectedValue: this.selectedValue || '',
        optionLabel: this.optionLabel
      }
      this.$store.dispatch('setFormData', formData)
    },

    // Render/mount nested form-fields if any
    renderNestedFormFieldsIfAny (value) {
      this.childNodes = []
      if (value) {
        if (Array.isArray(value)) { // In case of checkbox `value` will be an array
          value.forEach(v => {
            if (boolean(v.nodes)) {
              this.childNodes.push(...v.nodes)
            }
          })
        } else if (boolean(value.nodes)) { // In other case value is the selected object
          this.childNodes = value.nodes
        }
      }
    },

    getSelectFieldOptions () {
      if (boolean(this.apiInfoUrl)) {
        this.fetchOptionListForApiField();
      } else {
        this.dropdownOptions = this.formField.options;
      }
    },

    // Fetch option list -- specifically for 3rd party api config form-field
    fetchOptionListForApiField () {
      axios.get(this.apiInfoUrl)
        .then((response) => {
          this.dropdownOptions = response.data;
        })
        .catch((error) => {
          console.error(error);
        })
    },

    // clear the selected value to empty string
    clearSelectedValue () {
      this.selectedValue = ''
    },

    // triggered for a purpose to mount form-field of type = custom
    customTypeFormFieldMounted() {
      const data = {
        node: this.formField,
        showFilter: this.panel === "agent",
        formUniqueKey: this.formUniqueKey
      };
      window.eventHub.$emit('custom-type-form-field-mounted', data);
    },

    /**
     * Binds value from url to the form field
     */
    bindValuesFromUrl(){
      // if the unique_id exists in the url, it should make an API call and populate that
      let urlParams = new URLSearchParams(window.location.search);

      let unique = this.formField.unique.replace('_id', '');

      if(urlParams.get(unique) && !this.selectedValue) {

        if(this.formField.type === 'api'){
          axios.get(this.apiInfoUrl, {params: {'strict-search': 1, 'search-query': urlParams.get(unique), 'paginate': 1}}).then(res => {
            res.data.data.data[0] && (this.selectedValue = res.data.data.data[0]);
          });
        }

        this.formField.type === 'client-panel-requester' && (this.selectedValue = urlParams.get(unique));
      }
    }
  },

  beforeDestroy () {
    // Update form value object with deleting the specific property form the object -- valid in case if nested field get destroyed
    this.$store.dispatch('deleteFormDataByKey', { formUniqueKey: this.formUniqueKey , key: this.formField.unique })
  },

  components: {
    'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
    'text-field': require('components/MiniComponent/FormField/TextField'),
    'ckeditor': require('components/MiniComponent/FormField/CkEditorVue'),
    'radio-field': require('./RadioField'),
    'checkbox-field': require('./CheckboxField'),
    'file-upload': require('components/MiniComponent/FormField/fileUpload.vue'),
    'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
    'user-form-modal': require('./UserFormModal'),
    'client-requester': require('components/MiniComponent/FormField/ClientRequester.vue')
  }

}
</script>

<style scoped>
.multi-requester-checkbox {
  margin-top: -15px;
}
</style>