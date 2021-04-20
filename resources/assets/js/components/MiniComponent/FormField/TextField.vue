<template>

    <form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" :required="required"
                         :isInlineForm="isInlineForm" :actionBtn="actionBtn">

      <ValidationProvider :name="name" :rules="rules">

        <template slot-scope="{ failed, errors, classes }">

          <div v-if="type === 'textarea'">
            <small v-if="showWordLimit" slot="word-limit-counter">{{'(' + (changedValue.length || 0) + '/' + length + ')'}}</small>
              <textarea
                :id="id"
                :name="name"
                :class="['form-control', inputClass, classes]"
                :maxlength="length"
                :type="type"
                v-model="changedValue"
                v-on:input="onChange(changedValue, name)"
                :cols="columns"
                :rows="rows"
                :style="inputStyle"
                :pattern="pattern ? pattern : null"
                :placeholder="placehold"
              ></textarea>
          </div>

          <input
            v-else
            :id="id"
            :name="name"
            :class="['form-control', inputClass, classes]"
            :type="type"
            :disabled="disabled"
            :style="inputStyle"
            v-model="changedValue"
            v-on:input="onChange(changedValue, name)"
            @keyup="keyupListener($event,name)"
            @keydown="keydownListener($event,name)"
            @keypress="keypressEvt($event,name)"
            @paste="pasteEvt($event,name)"
            :placeholder="placehold"
            :maxlength="max ? max : undefined"
            :pattern="pattern ? pattern : null"
          />

          <span v-show="failed" class="error-block is-danger">{{errors[0]}} {{validationMessage ? '(' + validationMessage + ')' : ''}}</span>

        </template>
      </ValidationProvider>

    </form-field-template>

</template>

<script type="text/javascript">
import { boolean } from "helpers/extraLogics";

export default {
  name: "text-field",

  description: "text field component along with error block",

  props: {
    /**
     * the label that needs to be displayed
     * @type {String}
     */
    label: { type: String, required: true },

    /**
     * Hint regarding what the field is about (it will be shown as tooltip message)
     * @type {String}
     */
    hint: { type: String, default: "" }, //for tooltip message

    /**
     * selected value of the field.
     * list of already selected element ids that has to be displayed
     * @type {Number|Boolean}
     */

    value: { type: String|null, required: true },

    /**
     * the name of the state in parent class
     * @type {String}
     */
    name: { type: String, required: true },

    /**
     * Type of the text field. Available options : text, textarea, password, number
     * @type {String}
     */
    type: { type: String, default: "text" },

    /**
     * The function which will be called as soon as value of the field changes
     * It should have two arguments `value` and `name`
     *     `value` will be the updated value of the field
     *     `name` will be thw name of the state in the parent class
     *
     * An example function :
     *         onChange(value, name){
     *             this[name]= selectedValue
     *         }
     *
     * @type {Function}
     */
    onChange: { type: Function, Required: true },

    /**
     * classname of the form field. It can be used to give this component any bootstrap class or a custom class
     * whose css will be defined in parent class
     * @type {String}
     */
    classname: { type: String, default: "" },

    /**
     * Whether the given field is required or not.
     * If passed yes, an asterik will be displayed after the label
     * @type {Boolean}
     */
    required: { type: Boolean, default: false },

    length: {type: Number|String, default: 2000},

    keyupListener: { type: Function , default : ()=>{} },

    keydownListener: { type: Function , default : ()=>{} },

    keypressEvt: { type: Function ,  default : ()=>{} },

    pasteEvt: { type: Function ,  default : ()=>{} },

    /**
    * for show labels of the fields
    * @type {Object}
    */
    labelStyle:{type:Object},

    /**
     * for showing placeholder
     * @type {String}
     */
    placehold : { type: String, default : 'Enter a value'},

    /**
     * Id of the text field
     * @type {String|Number}
     */
    id : {type: String|Number, default:'text-field'},

    disabled : { type : Boolean, default : false},

    columns : { type : String | Number, default : ''},

    inputStyle : { type : Object, default : ()=>{}},

    max : { type : Number | String , default : ''},

    rows : { type : Number | String , default : ''},

    cols : { type : Number | String , default : ''},

    inputClass : { type : String, default : ''},

    // if true, will show the word counter in textbox
    showWordLimit: { type: Boolean, default: false },

    isInlineForm: { type: Boolean, default: false },

    rules: { type: String, default: '' },

    pattern: { type: String, default: null },

    validationMessage: { type: String, default: '' },

    actionBtn: { type: Object, default: () => null },

  },
  data() {
    return {
      /**
       * The updated value in the text field
       * @type {String}
       */
      changedValue: this.value
    };
  },

  mounted() {
    this.changedValue = this.value;
  },

  /**A watcher metod has been added since at firt the changevalue is empty and fetch the data accordingly
   * we need a watcher to update it with new value
   * @type {String}
   */
  watch: {
    value(newVal) {
      this.changedValue = newVal;
    }
  },

  components: {
    "form-field-template": require("./FormFieldTemplate"),
  }
};
</script>

<style>
</style>
