<template>
  <div>
    <div class="row form-field">

      <div class="col-md-3">
        <fb-form-field-label :field-data="fieldData" :is-child="isChild"></fb-form-field-label>
      </div>

      <div class="col-md-6">
        <div v-if="fieldData.options.length > 0">
          <div class="checkbox" v-for="(checkbox, index) in fieldData.options" :key="index">
            <label :for="'checkbox_' + checkbox.id">
              <input type="checkbox" :id="'checkbox_' + checkbox.id" :name="fieldData.id" :value="checkbox" v-model="selectedOption">
              {{ checkbox.labels[0].label }}
            </label>
          </div>
        </div>
        <div v-else>
          <label for="checkbox_preview" style="color:#9a9a9a;">
            <input disabled type="checkbox" name="checkbox_preview" id="checkbox_preview">
              Add option
            </label>
        </div>
      </div>

      <div class="col-md-3" style="display: flex;">

        <span class="space-filler"></span>

        <div class="form-field-actions-btn">
          <form-field-actions :on-edit-form-field="onEditFormField" :on-child-click="onChildClick" :selected-option="selectedOption" :field-data="fieldData"/>
        </div>
      </div>

    </div>

    <div v-if="fieldData.options && fieldData.options.length !== 0">
      <div v-for="(selVal, index) in selectedOption" :key="index">
        <div v-for="option in fieldData.options" :key="option.id" class="item-sub">
          <draggable-form-field-item v-if="showNestedFields(option, selVal)" :list="option.nodes" :isChild="true"/>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import FBFormFieldLabel from './FBFormFieldLabel';

export default {

  name: 'FBCheckbox',

  components: {
    'form-field-actions': require('./FormFieldActions'),
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

    // Function to add new node element
    onChildClick: {
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
      selectedOption: [],
    }
  },

  methods: {
    // Show the node(if present) of the selected checkbox
    showNestedFields(option, selVal) {
      if(typeof selVal === 'undefined') {
        selVal = this.selectedOption;
      }
      return Boolean(selVal && selVal.id === option.id);
    },
  }

}
</script>

<style lang="css" scoped>
.form-field-label > input {
  font-weight: 600;
  padding: 5px;
  width: 85%;
  border: 1px dashed #fff;
}
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