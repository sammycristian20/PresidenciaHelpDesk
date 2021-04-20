<template>
  <form-field-template :name="name" :label="label" :hint="hint" :required="required" :labelStyle="labelStyle" :isInlineForm="isInlineForm">
    <ValidationProvider :name="name" :rules="rules" class="row" tag="div">
      <template slot-scope="{ failed, errors, classes }">
        <div class="col-md-4 form-radio" v-for="option in options" :key="option.id">
          <label class="control-label">
            <input type="radio" v-model="selectedValue" :value="option" :name="getUniqueName(option.form_identifier)" @change="onChange(selectedValue, label)" :class="classes">
          {{option.label}}</label>
        </div>
        <div v-show="failed" class="error-block is-danger col-md-12">{{errors[0]}}</div>
      </template>
    </ValidationProvider>
  </form-field-template>
</template>

<script>
import FormFieldTemplate from 'components/MiniComponent/FormField/FormFieldTemplate'
import { boolean } from 'helpers/extraLogics';

export default {

  name: 'radio-field',

  props: {
    name: { type: String, required: true },

    label: { type: String, required: true },

    hint: { type: String, default: '' },

    options: { type: Array, required: true },

    required: { type: Boolean, default: false },

    onChange: { type: Function, Required: true },

    value: { type: String | Object, default: '' },
    
    labelStyle: { type: Object, default: () => {} },

    isInlineForm: { type: Boolean, default: false },

    rules: { type: String, default: '' }
  },

  data: () => {
    return {
      selectedValue: ''
    }
  },

  mounted() {
    this.selectedValue = boolean(this.value) ? this.value : ''
  },

  watch: {
    value(newVal) {
      this.selectedValue = boolean(newVal) ? newVal : ''
    }
  },

  methods: {
    getUniqueName (id = '') {
      return id + '__' + Math.floor(Math.random() * 100000);
    }
  },

  components: {
    'form-field-template': FormFieldTemplate,
  }
}
</script>

<style scoped>
  .form-radio > label {
    font-weight: 400;
    cursor: pointer;
  }
</style>