<template>
  <div>

    <date-time-field v-if="formField.type === 'date'" :label="formField.label" :labelStyle="labelStyle" :value="selectedValue" :name="unique" :onChange="onChange" format="YYYY-MM-DD" :clearable="true" :rules="validationRules" :required="false" outputFormat="YYYY-MM-DD HH:mm:ss"></date-time-field>

    <!-- Dynamic Select with options form api -->
    <dynamic-select v-if="(formField.type === 'api' || formField.type === 'multiselect')" :id="formField.unique" :label="formField.label" :multiple="formField.type === 'multiselect'" :name="unique" :apiEndpoint="apiInfoUrl" :value="selectedValue" :onChange="onChange" :labelStyle="labelStyle" :rules="validationRules" :clearable="clearable"></dynamic-select>

    <!-- Dynamic select with static options -->
    <dynamic-select v-if="formField.type === 'select'" :id="formField.unique" :label="formField.label" :multiple="false" :name="unique" :elements="dropdownOptions" :value="selectedValue" :onChange="onChange" :optionLabel="optionLabel" :labelStyle="labelStyle" :rules="validationRules" :clearable="clearable"></dynamic-select>

    <text-field v-if="formField.type === 'text' || formField.type === 'textarea' || formField.type === 'number' || formField.type === 'email'" :id="formField.unique" :label="formField.label" :type="formField.type" :name="unique" :value="selectedValue" :onChange="onChange" :labelStyle="labelStyle" :rules="validationRules" :pattern="formField.pattern" :validation-message="formField.validation_message"></text-field>

    <radio-field v-if="formField.type === 'radio'" :name="unique" :label="formField.label" :options="formField.options" :value="selectedValue" :onChange="onChange" :labelStyle="labelStyle" :rules="validationRules"></radio-field>

    <checkbox-field v-if="formField.type === 'checkbox'" :name="unique" :label="formField.label" :options="formField.options" :value="selectedValue" :onChange="onChange"  :labelStyle="labelStyle" :rules="validationRules"></checkbox-field>

    <div  class="form-field-wrapper">
      <automator-form-field v-for="node in childNodes" :key="getEnforcerObject(node.unique)._id" :form-field="node" :enforcerType="enforcerType" :enforcer-object="getEnforcerObject(node.unique)" :isChild="true"></automator-form-field>
    </div>

  </div>
  
</template>

<script>
import axios from 'axios';
import _ from 'lodash-core';
import { MULTIPLE_PROPERTY_HELPER, boolean } from 'helpers/extraLogics';
import { getFormFieldValue } from 'helpers/formUtils';
import { FaveoAutomator, getNewEnforcerInstance } from 'helpers/AutomatorUtils'


export default {

  name: 'automator-form-field',

  props: {

    enforcerType: { type: String, required: true },

    // Form field object
    formField: { type: Object, required: true },

    labelStyle: { type: Object, default: () => {} },

    isChild: { type: Boolean, default: false },

    enforcerObject : {type: Object, default: () => ({})},

  },

  data: () => {
    return {
      _id: null,
      childNodes: [],
      dropdownOptions: [],
      selectedValue: null,
      mounted: false,
      enforcerList: [],
    }
  },

  beforeMount () {
    
    // If type is 'select' we will assign options to the `dropdownOptions`
    if (this.formField.type === 'select') {
      this.getSelectFieldOptions()
    }
  },

  mounted () {
    // Assign form-field value to the `selectedValue`
    this.selectedValue = getFormFieldValue(this.formField.value, this.formField.options, this.formField.default);

    this.renderNestedFormFieldsIfAny(this.selectedValue);

    setTimeout(() => {
      this.mounted = true;
    }, 1000);
  },

  computed: {

    /**
     * Gives a unique key at the instance level
     */
    unique(){
      return this.formField.unique + this.$vnode.key;
    },
    
    /**
     * this will have url part of the api_info --used in case of <title = 'Api'>
     */
    apiInfoUrl () {
      return MULTIPLE_PROPERTY_HELPER.convertStringOfPropertiesToObject(this.formField.api_info).url;
    },

    optionLabel () {
      return MULTIPLE_PROPERTY_HELPER.convertStringOfPropertiesToObject(this.formField.api_info).key || 'label';
    },

    clearable () {
      return this.isChild;
    },

    validationRules () {
      let validationObj = [];

      // Adding required validations only to the root {enforcer}
      if (!this.isChild) {
        validationObj.push('required')
      }

      return validationObj.join('|')
    },
  },

  methods: {

    getEnforcerObject(unique) {
      let enforcerObject = this.enforcerObject[this.enforcerType+'s'].find(element => {
        return element.field.unique === unique
      });

      if(!enforcerObject){
        return {_id: 'test'};
      }

      return enforcerObject;
    },

    onChange (value, name) {
      if (!this.mounted) return;

      if (Array.isArray(value)) {

        const prevVal = JSON.parse(JSON.stringify(this.selectedValue || []));

        this.selectedValue = value;

        if (value.length > prevVal.length) {
         this.processAndUpdateStore(_.differenceWith(value, prevVal, _.isEqual)[0], true);
        } else if (value.length < prevVal.length) {
          this.$store.dispatch('updateEnforcerList', { key: this.enforcerType, affectedUnique: this.$vnode.key, formFieldUnique: this.formField.unique, value: this.selectedValue, itemToRemove: _.differenceWith(prevVal, value, _.isEqual)[0] });
        }

      } else {
        this.selectedValue = value;
        this.processAndUpdateStore(value);
      }

      this.renderNestedFormFieldsIfAny(value);
    },

    processAndUpdateStore (data, isMultiple = false) {

      let enforcerList = [];

      if (boolean(data) && boolean(data.nodes)) {

        // for each node, one
        data.nodes.forEach((node) => {
          const newEnforcerInstance = getNewEnforcerInstance(this.enforcerType, node);
          enforcerList.push(newEnforcerInstance);
        });
      }
      this.enforcerList = enforcerList;
      this.$store.dispatch('updateEnforcerList', { key: this.enforcerType, affectedUnique: this.$vnode.key, formFieldUnique: this.formField.unique, enforcerList: enforcerList, value: this.selectedValue, isMultiple: isMultiple });
    },

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
          // before pushing child nodes, should iterate through added enforcerList and pick unique from there

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

    fetchOptionListForApiField () {
      axios.get(this.apiInfoUrl)
        .then((response) => {
          this.dropdownOptions = response.data;
        })
        .catch((error) => {
          console.error(error)
        })
    },

    clearSelectedValue () {
      this.selectedValue = null
    }
  },

  components: {
    'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
    'text-field': require('components/MiniComponent/FormField/TextField'),
    'radio-field': require('components/Common/Form/RadioField'),
    'checkbox-field': require('components/Common/Form/CheckboxField'),
    'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
  }

}
</script>

<style scoped>
.multi-requester-checkbox {
  margin-top: -20px;
}
</style>