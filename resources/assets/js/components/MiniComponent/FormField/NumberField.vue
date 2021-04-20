<template>

    <form-field-template :label="label" :labelStyle="labelStyle" :name="name"  :classname="classname" :hint="hint" :required="required">
        <span class="inline" >
                <input class="form-control" :style="formStyle"
                       :type="type"
                       id="number"
                       v-model="changedValue"
                      v-on:input="onChange(changedValue, name)"
                       @keypress="checkValue"  
                       @paste="onPaste"
                       min="0"
                       :placeholder="placeholder"
                /> 
        </span>
    </form-field-template>

</template>

<script type="text/javascript">

	export default {
		name:'time-field',

		description:'time field component along with error block',

		props:{

			/**
             * the label that needs to be displayed
             * @type {String}
             */
			label: { type: String, required: true },

            /**
             * Hint regarding what the field is about (it will be shown as tooltip message)
             * @type {String}
             */
            hint: { type:String, default: '' }, //for tooltip message

            /**
             * selected value of the field.
             * list of already selected element ids that has to be displayed
             * @type {Number|Boolean}
             */
			value: { required: true },


            /**
             * the name of the state in parent class
             * @type {String}
             */
            name: { type: String, required: true },

            /**
             * Type of the text field. Available options : text, textarea, password, number
             * @type {String}
             */
			type: {type: String, default: 'text'},


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
             * classname of the form field. It can be used to give this component any bootstrap class or a custom class
             * whose css will be defined in parent class
             * @type {String}
             */
            classname : {type: String, default:''},

            /**
             * Whether the given field is required or not.
             * If passed yes, an asterik will be displayed after the label
             * @type {Boolean}
             */
			required: { type: Boolean, default: false},

            /**
             * for show labels of the fields
             * @type {Object}
             */
            labelStyle:{type:Object},

            /**
             * for width of the fields
             * @type {Object}
             */
            formStyle:{type:Object},

            max : { type : String | Number, default :''},

            placeholder : { type : String, default : ''}

        },
		data(){
			return {
				
				/**
                   * The updated value in the text field
                   * @type {String}
                   */
                  changedValue: this.value
			}
		},

        created() {
            window.eventHub.$on('removeVal',this.initialState)
        },

		mounted(){
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

            /**
             * method for allowing users to entering only numbers
             * @param  {Event} event 
             * @return {Boolean}
             */
            checkValue(evt) {
                 evt = (evt) ? evt : window.event;
                  var charCode = (evt.which) ? evt.which : evt.keyCode;
                  if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
                    evt.preventDefault();;
                  } else {
                    return true;
                  }
            },

            /**
             * method for check values on paste 
             * @return {Boolean}
             */
            onPaste(evt) {

                evt = (evt) ? evt : window.event;
                
                if (evt.clipboardData.getData('Text').match(/[^\d]/)) {
                    
                    evt.preventDefault();
                }   
            },

            /**
             * initial state of the data
             * @return {Void}
             */
            initialState(){
                this.changedValue = 0;
            }
        },
        components:{
		    'form-field-template' : require('./FormFieldTemplate')
        }
    };

</script>

<style scoped>
	.inline {
        display:inline;
    }
    .form-control {
        display:inline !important;
    }
</style>