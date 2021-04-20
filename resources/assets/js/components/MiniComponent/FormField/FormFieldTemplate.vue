<template>
    <div :class="[classname, 'form-group', 'form-field-template', {'has-error': name in errors }, { 'row': isInlineForm } ]" v-bind:id="label">

        <div :class="{ 'col-md-2 flex': isInlineForm }">
          <label v-bind:for="label" :style="labelStyle">{{label}}</label>
          <label class="is-danger" :style="labelStyle" v-if="required">*</label>
          <tool-tip v-if="hint !=''" :message="hint" size="small"></tool-tip>
          <slot name="word-limit-counter"></slot>
          <!-- 
            Showing clear button only when field value is type of object, e.g DatePicker 
          -->
          <i v-if="isClearField && value && typeof(value) == 'object'" @click="clearField" class="fas fa-times clear-btn" title="Clear" aria-hidden="true"></i>

          <a v-if="showNewButton" class="btn btn-primary btn-xs float-right" href="javascript:;" @click="clickEvent(name)">

            <i class="fas fa-plus"> </i> {{lang('new')}}
          </a>

          <i class="float-right" v-if="showPreview">(e.g {{showPreview}})</i>
        </div>

        <div :class="[ isInlineForm ? 'col-md-10 flex' : '' ]">
            <div class="slot-container">
              <slot></slot>
              <div v-if="name in errors" class="error-block is-danger">{{errors[name]}}</div>
            </div>
            <button v-if="actionBtn" class="btn btn-default form-field-action-button" @click="() => actionBtn.action()"><span>{{trans(actionBtn.text)}}</span></button>
        </div>

    </div>
</template>

<script>
import { mapGetters } from "vuex";
import ToolTip from "components/MiniComponent/ToolTip";

export default {
  name: "form-field-template",

  description:
    "This component handles the error block by reading validation errors directly for vuex store and hint tooltip message",

  props: {
    /**
     * label that has to be displayed above the field
     * @type {String}
     */
    label: { type: String, required: true },

    /**
     * the name of the state in parent class, for looking into the vuex store for errors
     * @type {String}
     */
    name: { type: String | Number, required: true },

    /**
     * For showing Field label.
     * If passed display as none, the label will not be displayed
     * @type {Object}
     */
    labelStyle:{type:Object, default: function () { return { }}},

    /**
     * name of the class (for css, not really required)
     * @type {String}
     */
    classname: { type: String, default: "" },

    /**
     * Hint regarding what the field is about (it will be shown as tooltip message)
     * @type {String}
     */
    hint: { type: String, default: "" },

    /**
     * Whether the given field is required or not.
     * If passed yes, an asterik will be displayed after the label
     * @type {Boolean}
     */
    required: { type: Boolean, default: false },

    isClearField : { type : Boolean, default : false },
    
    clearField : { type : Function },

    value : { type: String | Date | Object | Array, default : '' },

     /**
     * The new button is need to show or not.
     * @type {Boolean}
     */
    showNewButton: { type: Boolean, default: false },
    
    onClickEvent : { type : Function},

    isInlineForm: { type: Boolean, default: false },

    actionBtn: { type: Object, default: () => null },

    showPreview : { type : String | Object, default : '' }

  },

  computed: {
    ...mapGetters(["getValidationErrors"]),

    //watches errors in vuex stroe for changes
    errors: {
      cache: false,

      get() {
        return this.getValidationErrors;
      }
    }
  },
  watch: {
    errors(newValue) {
      // console.log(" array got changed accordingly ");
      /**
       * watcher is set on the errors array we are adding a scroll function to it when the validation fails
       */
      this.newValue = this.errors;
      let x = {};

      setTimeout(() => {
        
             let errorBlock = document.querySelectorAll('.error-block:not([style*="display: none"])')[0]
              errorBlock && errorBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }, 1);
    }
  },

  methods : {
    
    clickEvent(name){
    
      this.onClickEvent(name);
    }
  },

  components: {
    "tool-tip": ToolTip,
  }
};
</script>

<style scoped>

.slot-container {
  width: inherit;
}
.form-field-action-button {
  height: fit-content;
  white-space: nowrap;
}
</style>