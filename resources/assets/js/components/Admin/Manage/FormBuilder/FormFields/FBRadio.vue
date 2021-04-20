<template>
  <div>
    <div class="row form-field">

      <div class="col-md-3">
        <fb-form-field-label :field-data="fieldData" :is-child="isChild"></fb-form-field-label>
      </div>

      <div class="col-md-6">
        <div v-if="fieldData.options.length > 0">
          <div class="radio" v-for="radio in fieldData.options" :key="radio.id">
            <label :for="'radio_' + radio.id">
              <input type="radio" :name="fieldData.id" :id="'radio_' + radio.id" v-model="selectedOption" :value="radio">
              {{ radio.labels[0].label }}
            </label>
          </div>
        </div>
        <div v-else>
          <label for="radio_preview" style="color:#9a9a9a;">
            <input disabled type="radio" name="radio_preview" id="radio_preview">
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
      <div v-for="option in fieldData.options" :key="option.id" class="item-sub">
        <draggable-form-field-item v-if="selectedOption && selectedOption.id === option.id" :list="option.nodes" :isChild="true"/>
      </div>
    </div>

  </div>
</template>

<script>
import FBFormFieldLabel from './FBFormFieldLabel';

export default {

  name: 'FBRadio',

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
      selectedOption: null,
    }
  },

  methods: {
    // Show the node(if present) of the selected radio option
    showNestedFields(option, selVal) {
      const selValConst = selVal;
      return Boolean(selVal && selVal.id === option.id);
    },
  }

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