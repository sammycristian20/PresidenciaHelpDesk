<template>

  <div :class="classname">
    <label for="HOurs">{{ label }}</label>
    <tool-tip v-if="hint !=''" :message="hint" size="small"></tool-tip>
    <div class="row">
        <span v-for="(option,index) in options" :class="class_name">
            <input class="check_align" :name="name" v-model="checked" type="checkbox" :value="option.value ? option.value : option.id">
                {{lang(option.name ? option.name : option.label)}}&nbsp;&nbsp;
         </span>
    </div>
  </div>


</template>

<script>
import ToolTip from 'components/MiniComponent/ToolTip'
export default {
  name : "checkbox",

  description : "Checkbox with default css class and other required props to be used throughout the project",

  props : {

    options : {type:Array,default:()=>{}},

    value : {type: Array, default: ()=>[]},

    name : {type: String, default: 'check'},

    label : {type: String, default: 'label'},

    classname : {type: String, default: 'col-sm-4'},

    class_name : {type: String, default: 'col-sm-4'},

    spanClass : { type : String, default : 'check_inline'},


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
    onChange:{type: Function, Required: true},

    /**
     * Hint regarding what the field is about (it will be shown as tooltip message)
     * @type {String}
     */
    hint: { type: String, default: "" },
  },

  data(){
    return {

      /**
       * value of the checkbox field
       * @type {Boolean}
       */
      checked: this.value
    }
  },

  watch:{

    value(newValue,oldValue){
      this.checked = newValue
    },

    checked(newVal){
      console.log(newVal,'newVal')
      //calls onChange method of parent component to update values
      this.onChange(newVal, this.name)
    }
  },

  components:{
    'tool-tip': ToolTip
  }

};

</script>

<style type="text/css">
.absolute {
  position: absolute;
  margin-left: 5px;
}

.check_align {
  width: 13px;
  height: 13px;
  padding: 0;
  margin:0;
  vertical-align: bottom;
  position: relative;
  top: -3px;
  *overflow: hidden;
}

.check_inline { display: inline; }

.check_grid { display: grid; }
</style>