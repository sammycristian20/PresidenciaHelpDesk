<template>
  <div>
    <div class="row form-field">

      <div class="col-md-3">
        <fb-form-field-label :field-data="fieldData" :is-child="isChild"></fb-form-field-label>
      </div>

      <div class="col-md-6">
        <select id="select-field-id" class="form-control" v-model="selectedOption" :disabled="isDisabled">
          <option selected>Select option</option>
            <option v-for="option in fieldData.options" :key="option.id" v-bind:value="option">
              {{option.labels[0].label}}
            </option>
        </select>
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
        <draggable-form-field-item v-if="showNestedFields(option)" :list="option.nodes" :isChild="true"/>
      </div>
    </div>

  </div>
</template>

<script>
import FBFormFieldLabel from './FBFormFieldLabel';

export default {

  name: 'FBSelect',

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

    // disable the dropdwon if true
    isDisabled: {
      type: Boolean,
      default: () => false
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
      selectedOption: 'Select option',
    }
  },

  beforeMount() {
    // If the case of help topic OR department, fetch their options
    this.getDepartmentHelptopicOptions();
  },

  methods: {
    // Function to fetch helptopic or department options frpom server
    getDepartmentHelptopicOptions() {
      if (this.fieldData.title === 'Help Topic' || this.fieldData.title === 'Department') {

        this.selectedOption = 'Select option';

        const apiConfigObj = this.getApiConfigObj();

        const params = {'meta': true, 'limit': 'all', 'sort-order': 'asc', 'sort-field': 'id', 'config': true};

        axios.get(apiConfigObj.apiUrl, {params}).then(resp => {
          this.fieldData.options = [];
          let newOptions = resp.data.data[apiConfigObj.dataKay];
          newOptions.map(data => {
            const tmpOption = {
              id: data.id,
              value: data.id,
              labels: [
                {
                  id: null,
                  language: 'en',
                  label: data.name
                }
              ],
              nodes: data.nodes,
              sort_order: 0
            }
            this.fieldData.options.push(tmpOption);
          });
        });
      }
    },

    getApiConfigObj() {
      let apiConfigObj = {};

      if (this.fieldData.title === 'Help Topic') {
          apiConfigObj.apiUrl = 'api/dependency/help-topics';
          apiConfigObj.dataKay = 'help_topics';
        } else if (this.fieldData.title === 'Department') {
          apiConfigObj.apiUrl = 'api/dependency/departments';
          apiConfigObj.dataKay = 'departments';
        }

        return apiConfigObj;
    },

    // Show the node(if present) of the selected option in the dropdown
    showNestedFields(option, selectedOption) {
      if(typeof selectedOption === 'undefined') {
        selectedOption = this.selectedOption;
      }
      return selectedOption && selectedOption.id === option.id;
    },
  },

  watch: {
    //  Watch fieldData prop, if change detected refetch the helptopic/department
    // TODO: This need to be improved as fetching helptopic/department for humongous dataset is burden 
    fieldData: function (newValue, oldValue) {
      if(this.fieldData.options && this.fieldData.options.length === 0) {
        this.getDepartmentHelptopicOptions();
      }
    }
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