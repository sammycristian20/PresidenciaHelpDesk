<template>
    <div class="form-group">
        <div :class="classname">
          <label :title="title">
            <input :id="id" :name="name" v-model="checked" type="checkbox" :disabled="disabled"/>
                {{lang(label)}}
                <tool-tip v-if="hint !=''" :message="hint" size="small"></tool-tip>
            </label>
        </div>
    </div>
</template>

<script>
    import ToolTip from 'components/MiniComponent/ToolTip'
    export default {
        name : "checkbox",

        description : "Checkbox with default css class and other required props to be used throughout the project",

        props : {

            /**
             * value of the field. (either true/false or 0/1)
             * @type {Number|Boolean}
             */
            value: { type:Boolean|Number, default: false },

            /**
             * the name of the state in parent class
             * @type {String}
             */
            name: { type:String|Number, required: true },

            /**
             * the label that needs to be displayed
             * @type {String}
             */
            label: { type:String, required: true },

            /**
             * Hint regarding what that checkbox is about (it will be shown as tooltip message)
             * @type {String}
             */
            hint: { type:String, default: '' },

            /**
             * Title on label
             * @type {String}
             */
            title: {
              type: String,
              default: ""
            },

            /**
             * classname of the form field. It can be used to give this component any bootstrap class or a custom class
             * whose css will be defined in parent class
             * @type {String}
             */
            classname : {type: String, default: ''},

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

            disabled:{type: Boolean, default: false},

            /**
             * Id of the field
             * @type {String|Number}
             */
            id : {type: String|Number, default:'checkbox'}
        },

        data(){
            return {

                /**
                 * value of the checkbox field
                 * @type {Boolean}
                 */
                checked: (this.value == "" || this.value == null || this.value == "0") ? false : true
            }
        },

        watch:{
            checked(newVal){

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
</style>
