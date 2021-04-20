<template>

    <form-field-template :label="label" :labelStyle="labelStyle" :name="name"  :classname="classname" :hint="hint" :required="required">
        <span class="inline" >
               
                 <input class="form-control" :style="formStyle" maxlength="4"
                       :type="type"
                       id="txtYears"
                       v-model="yearsValue"
                       v-on:input="onInput()"
                       placeholder="0" 
                       @keypress="checkValue"  
                       @paste="onPaste" 
                /> {{lang('years')}}

                 <input class="form-control" :style="formStyle" maxlength="4"
                       :type="type"
                       id="txtMonths"
                       v-model="monthsValue"
                       v-on:input="onInput()"
                       placeholder="0" 
                       @keypress="checkValue"  
                       @paste="onPaste" 
                /> {{lang('months')}}

                <input class="form-control" :style="formStyle" maxlength="4"
                       :type="type"
                       id="txtDays"
                       v-model="daysValue"
                       v-on:input="onInput()"
                       placeholder="0" 
                       @keypress="checkValue"  
                       @paste="onPaste" 
                /> {{lang('days')}}

                <input class="form-control" :style="formStyle" maxlength="4"
                       :type="type"
                       id="txtHours"
                       v-model="hoursValue"
                       v-on:input="onInput()"
                       placeholder="0" 
                       @keypress="checkValue"  
                       @paste="onPaste" 
                /> {{lang('hours')}}
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
            formStyle:{type:Object}

        },
		data(){
			return {
				
                yearsValue : 0,

                monthsValue : 0,
                /**
                 * The initial value in the hours field
                 * @type {String}
                 */
                daysValue: 0,

				/**
				 * The initial value in the hours field
				 * @type {String}
				 */
				hoursValue: 0,
                    /**
                 * The initial value in the minutes field
                 * @type {String}
                 */
                minutesValue: 0
			}
		},

        created() {
            window.eventHub.$on('removeVal',this.initialState)
        },

		mounted(){

            if(this.value === ''){
                this.yearsValue = 0
                this.monthsValue = 0
                this.daysValue = 0
                this.hoursValue = 0
            } else {
                this.yearsValue = this.value.years
                this.monthsValue = this.value.months
                this.daysValue = this.value.days
                this.hoursValue = this.value.hours
            }
            

	       // if(this.value !== '' && this.value !== 0){
        //         this.daysValue=Math.floor(this.value/24);
        //         this.hoursValue=Math.floor(this.value/60);
        //         this.minutesValue = this.value%60;
        //    } else {
        //         this.hoursValue=0;
        //         this.minutesValue = 0;
        //         this.minutesValue = 0;
        //    }
		},


        methods:{
            /**
             * for updating hours and minutes value when values changed in the fields
             * @return {[type]} [description]
             */
            onInput(){
                let time = {
                    years : this.yearsValue,
                    months : this.monthsValue,
                    days : this.daysValue,
                    hours : this.hoursValue
                }

                this.onChange(time,this.name)
                // this.onChange((+this.daysValue * 24 * 60) + (+this.hoursValue * 60) + +this.minutesValue, this.name);
            },

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
                    evt.preventDefault();    
            },

            /**
             * initial state of the data
             * @return {Void}
             */
            initialState(){
                this.hoursValue = 0;
                this.monthsValue = 0;
                this.yearssValue = 0;
                this.daysValue = 0;
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