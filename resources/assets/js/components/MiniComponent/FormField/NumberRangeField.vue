<template>

	<form-field-template :label="label" 
		:labelStyle="labelStyle" 
		:name="name"  
		:classname="classname" 
		:hint="hint" 
		:required="required"
	>
		
		<span class="inline" >
			
			<input class="form-control" :style="formStyle"
				:type="type"
				id="txtMin"
				v-model="minValue"
				v-on:input="onInput()"
				:placeholder="min_placeholder" 
				@keypress="checkValue"  
				@paste="onPaste" 
			/>

			<input class="form-control" :style="formStyle"
				:type="type"
				id="txtMax"
				v-model="maxValue"
				v-on:input="onInput()"
				:placeholder="max_placeholder"
				@keypress="checkValue" 
				@paste="onPaste" 
			/>
		</span>
	</form-field-template>
</template>

<script>

	export default {
		
		name:'number-range-field',

		description:'Number Range component along with error block',

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

			min_placeholder : { type : String | Number, default : 'Min' },

			max_placeholder : { type : String | Number, default : 'Max' },
		},

		data(){
		
			return {
				
				/**
				 * The initial value in the hours field
				 * @type {String}
				 */
				maxValue: '',
		
				/**
				 * The initial value in the minutes field
				 * @type {String}
				 */
				minValue: ''
			}
		},

		beforeMount(){

			this.updateValue()
		},

		methods:{
			
			updateValue() {

				if(this.value){

					this.minValue = this.value.min;

					this.maxValue = this.value.max;

					this.onInput();
				}
			},

			onInput(){
			
				this.onChange({min : this.minValue,max : this.maxValue},this.name);
			},

			checkValue(evt) {

				evt = (evt) ? evt : window.event;

				var charCode = (evt.which) ? evt.which : evt.keyCode;

				if ((charCode > 31 && (charCode < 48 || charCode > 57))) {
				
					evt.preventDefault();;
				} else {
					
					return true;
				}
			},

			onPaste(evt) {
			  
			  evt = (evt) ? evt : window.event;
				
				evt.preventDefault();    
			}
		},

		components:{
		
			'form-field-template' : require('./FormFieldTemplate')
		}
	};
</script>

<style scoped>
	.inline {
		display:flex;
	}
	#txtMax { border-top-left-radius : 0 !important;border-bottom-left-radius : 0 !important;}
	
	#txtMin { border-top-right-radius : 0 !important;border-bottom-right-radius : 0 !important;}
	</style>