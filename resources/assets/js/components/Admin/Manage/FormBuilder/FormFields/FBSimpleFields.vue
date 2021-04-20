<template>
  <div class="row form-field">

    <div class="col-md-3">
      <fb-form-field-label :field-data="fieldData" :is-child="isChild"></fb-form-field-label>
    </div>
    <div class="col-md-6">
      <!-- text/number/date/email/file/api fields -->
      <input v-if="isSimpleField() || fieldData.title === 'Api'" :id="'short-text-input' + fieldData.id" class="form-control" :type="'text'" :placeholder="lang(getSimpleFieldPlaceholder)" disabled />

      <!-- textarea field-->
      <textarea v-if="fieldData.type=='textarea' || fieldData.type=='htmltextarea'" :id="'long-text-input' + fieldData.id" class="form-control" rows="1" :placeholder="lang('long_text')" disabled />
    </div>
    <div class="col-md-3" style="display: flex;">

      <span class="space-filler"></span>

      <div class="form-field-actions-btn">
        <form-field-actions :on-edit-form-field="onEditFormField" :field-data="fieldData"/>
      </div>

    </div>
  </div>

</template>

<script>

import FormFieldActions from './FormFieldActions';
import FBFormFieldLabel from './FBFormFieldLabel';

export default {

  components: {
    'form-field-actions': FormFieldActions,
    'fb-form-field-label': FBFormFieldLabel
  },

  props: {

    // Field data object
    fieldData: {
      type: Object,
      required: true
    },

    // Function to execute for editing field properties
    onEditFormField: {
      type: Function,
      required: true
    },

    // `true` if a node is not a root node
    isChild: {
      type: Boolean,
      default: () => false
    }
  },

  data: () => {
    return {
    }
  },

  computed: {
    getSimpleFieldPlaceholder: function () {
      let placeholder = 'short_text';
      if(this.fieldData.title === 'Attachments') {
        placeholder = 'attach_file';
      } else if(this.fieldData.type === 'email') {
        placeholder = 'email@example.com'
      } else if (this.fieldData.title === 'Api') {
        placeholder = 'api_endpoint';
      }
      return placeholder;
    }
  },

  methods: {
    // returns `true` if field is text/number/date/email/file
    isSimpleField() {
      const simpleFields = ['text', 'number', 'date', 'email', 'file', 'taggable'];
      return simpleFields.indexOf(this.fieldData.type) > -1;
    },
  },
}
</script>

<style lang="css" scoped>
.item-sub {
  margin: 0 0 0 1rem;
}
.space-filler {
  flex: 1 1 auto;
}
.form-field-actions-btn {
  visibility: hidden;
  margin-right: 1rem;
}
</style>