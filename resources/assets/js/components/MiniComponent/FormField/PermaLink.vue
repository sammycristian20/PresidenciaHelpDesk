<template>

    <form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" :required="required">

        <input class="form-control"
               :type="type"
               v-model="changedValue"
               v-on:input="onChange(changedValue, name)"
               :placeholder="placehold" 
               :maxlength="max"
               :style="fieldStyle"
        />
          <p class="row">
            <span class="col-md-10" id="edit_slug">{{base+'/show/'}}{{link}}</span>
            <template  v-if="!disabled">
              <span v-if="showBtn" class="col-md-2" id="edit_slug">
                <button class="btn btn-primary btn-xs float-right" @click="showField()">
                  <i class="fas fa-pencil-alt"></i> {{lang('edit')}}
                </button>
              </span>
               <span v-if="!showBtn" class="col-md-2" id="edit_slug">
                <button class="btn btn-success btn-xs float-right mt_2" @click="hideField()">
                  <i class="fas fa-check"></i> {{lang('ok')}}
                </button>
              </span>
            </template>
          </p>
          

    </form-field-template>

</template>

<script type="text/javascript">
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

    /**
    * for show labels of the fields
    * @type {Object}
    */
    labelStyle:{type:Object},

    placehold : { type: String, default : ''},

    max : { type : Number | String , default : 250},

    link : { type :  String , default :''},

    disabled : { type : Boolean, default : false }

  },
  data() {
    return {
      /**
       * The updated value in the text field
       * @type {String}
       */
      changedValue: this.value,

      base : window.axios.defaults.baseURL,

      fieldStyle : { display : 'none !important'},

      showBtn : true
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

  methods:{
    showField(){
      this.fieldStyle.display = 'block !important';
      this.showBtn = false;
    },
    hideField(){
      this.fieldStyle.display = 'none !important';
      this.showBtn = true;
    }
  },

  components: {
    "form-field-template": require("./FormFieldTemplate")
  }
};
</script>

<style scoped>
#edit_slug{
  word-break: break-all;
}
.mt_2 { margin-top: 2px; }
</style>
