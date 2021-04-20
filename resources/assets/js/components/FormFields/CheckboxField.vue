<style scoped>

.fa-warning {
  color: red;
  font-size: 14px;
}

.checkbox-inline {
  margin-bottom: 0px;
  padding-left: 20px;
}
.form-check-input {
    position: absolute;
    margin-top: 0.45rem !important;
    margin-left: -1.25rem;
}
</style>
<template>
	    <div>
            <div v-for="(option,index) in options">

    	  	    <label :class="{'checkbox-inline': true, 'field-danger': errors.has(unique)}">
                  <input class="custom-field-checkbox form-check-input" type="checkbox" :name="unique" :data-vv-as="fieldName" v-model="option.checked" v-validate="isRequired ? validate : ''" :disabled="disableField" @change="getCheckboxValues(option.labels, option.checked)">{{option.labels}}
              </label>
    	      </div>
            <span v-show="errors.has(unique)" class="help is-danger">
              {{ errors.first(unique) }}
            </span>
        </div>
</template>
<script>
import {boolean} from 'helpers/extraLogics';

export default {

  props : {

    /**
     * Name of the field
     */
    fieldName : {type : String, required: true},

    /**
     * Value of the field
     */
    fieldValue : {type : Array|String, required: true},

    /**
     * Object for validation
     */
    validate : {type : Object, required: true},

    /**
     * Key for which value has to be assigned in form field object
     */
    objKey : {type: String, required: true},

    /**
     * If field has to be disabled
     */
    disableField : {type: Boolean, default: false},

    /**
     * Options in checkbox
     */
    options : {type: Array, required: true },

    /**
     * Checkbox node from form field
     */
    node : {type : Object, required: true},
  },

  data() {
    return {
      isRequired: true,
      checkboxArray: [],
      unique: this.fieldName + "_" + new Date().getTime(),
      editedValues: []
    };
  },

  mounted() {
    this.updateStateInEditMode();
  },

  watch : {
    "node.value"(newVal){
      this.updateStateInEditMode();
    },

    fieldValue(){
      this.updateStateInEditMode();
    }
  },

  methods: {

    updateStateInEditMode(){

      if (boolean(this.node.value)) {
        if (this.node.value.length > 0 && Array.isArray(this.node.value)) {
          let value = this.node.value;
          value.map(x => {
            this.getCheckboxValues(x, true);
          });
          this.options.map(y => {
            if (this.checkboxArray.includes(y.labels)) {
              y.checked = true;
            }
          });
        }
      } else {
        this.resetCheckboxIfNeeded();
      }
    },

    resetCheckboxIfNeeded(){
      if(this.node.value == ""){
        // when node value is marked as empty, it resets all the values of the node. useful when
        this.options.map(option => {
          option.checked = false;
        })
        this.checkboxArray = [];
      }
    },

    //get checkbox values
    getCheckboxValues(value, checked) {
      //getting checkbox value and push it into array
      if (this.checkboxArray.indexOf(value) == -1 && checked) {
        this.checkboxArray.push(value);
      } else {
        if(!checked){
          this.checkboxArray.splice(this.checkboxArray.indexOf(value), 1);
        }
      }
      //check required field
      if (this.checkboxArray.length == 0) {
        this.isRequired = true;
        this.$validator.validate(this.unique, "");
      } else {
        this.isRequired = false;
      }
      this.$emit("assignToModel", this.objKey, this.checkboxArray);
    }
  }
};
</script>

<style>
  .custom-field-checkbox {
    display: block !important;
  }
</style>
