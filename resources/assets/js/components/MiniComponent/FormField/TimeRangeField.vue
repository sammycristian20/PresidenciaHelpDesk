<template>

	<form-field-template :label="lang(labelValue)" 
		:name="name" 
		:labelStyle="labelStyle"  
		:classname="classname" 
		:hint="hint" 
		:required="required"
		:isClearField="isDatePicker"
		:clearField="clearFieldValue"
		:value="changedValue">

		<div class="input-group">

			<input class="form-control" :style="numberStyle"
				v-if="!isDatePicker"
				type="number"
				id="range_count"
				v-model="count"
				v-on:input="onInput()"
				placeholder="n" 
				@keypress="checkValue"  
				@paste="onPaste" 
			/>

			<select id="range_option"
				class="form-control"
				v-if="!isDatePicker"
				v-model="option"
				v-on:change="onInput()">
				<option
					v-for="unit in options"
					:key="unit.id"
					:value="unit.id">
					{{ lang(unit.value) }}
				</option>
			</select>

			<date-picker v-if="isDatePicker" 
				v-model="changedValue" lang="en"  
				:type="type" 
				:time-picker-options="timePickerOptions" 
				:format="format" 
				:placeholder="place" 
				:disabled="disabled" 
				:range="range" 
				input-class="form-control index0" 
				v-on:input="onDateChange(changedValue, name)" 
				:clearable="clearable" 
				:confirm="confirm" 
				:editable="editable" 
				:shortcuts="pickers" 
				:not-before="notBefore">
						
			</date-picker>

			<button
				class="btn btn-default range_btn"
				type="button"
				@click="isDatePicker=!isDatePicker">
				<i
					class="fas fa-pencil-alt"
					title="Enter by hand"
					aria-hidden="true"
					v-if="isDatePicker">
				</i>
				<i
					class="far fa-calendar"
					title="Use datepicker"
					aria-hidden="true"
					v-else>
				</i>
			</button>
		</div>
	</form-field-template>
</template>

<script>

	import DatePicker from 'vue2-datepicker'
	
	import moment from 'moment'
	
	export default {
		
		name:'date-time-field',

		description:'date time field component along with error block',

		props:{

			isDateSelector : { type : Boolean, default : true },

			numberStyle : { type : Object, default : ()=> {} },
			/**
			 * the label that needs to be displayed
			 * @type {String}
			 */
			label: { type: Array, default: ()=>[] },

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
			value: { type: String|Date, required: true },

			/**
			 * the name of the state in parent class
			 * @type {String}
			 */
			name: { type: String|Number, required: true },

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
			 * for show labels of the fields
			 * @type {Object}
			 */
			labelStyle:{type:Object},

			/**
			 * Whether the given field is required or not.
			 * If passed yes, an asterik will be displayed after the label
			 * @type {Boolean}
			 */
			required: { type: Boolean, default: true},

			/**
			 * time picker options
			 * type {Object}
			 */
			timePickerOptions : { type: Object, default:()=>{}} ,

			/**
			 * date time picker format
			 * @type {String}
			 */
			format : { type: String,default: ''},

			/**
			 * date time picker disabled status
			 * @type {Boolean}
			 */
			disabled : { type: Boolean, default: false},

			/**
			 * date time picker clearable status
			 * @type {Boolean}
			 */
			clearable : { type: Boolean, default: false},

			/**
			 * date time picker range attribute 
			 * @type {Boolean}
			 */
			range: { type : Boolean},

			/**
			 * placeholde
			 * @type {String}
			 */
			place: { type : String},
			/**
			 * placeholde
			 * @type {String}
			 */
			notBefore: { type : String|Date },

			currentYearDate : { type : Boolean , default : false},

			confirm : { type : Boolean , default : false},

			editable : { type : Boolean , default : true},

			pickers : { type : Boolean | Array , default : false},
		},
		
		data(){
			
			return {
				
				isDatePicker : this.isDateSelector,			
				/**
				* The updated value in the text field
				* @type {String}
				*/
				changedValue: this.value,

				count : '',

				options : [
					{ id: 'minute', value: 'unit_minute' },
					{ id: 'hour', value: 'unit_hour' },
					{ id: 'day', value: 'unit_day' },
					{ id: 'month', value: 'unit_month' }
				],

				option : 'minute',

				moment:moment
			}
		},

		watch:{
			/**
			* returns new date time value
			* @return {Void}
			*/
			value(newValue,oldValue){
				
				this.changedValue = newValue === '' ? null : newValue
			}
		},

		computed : {

			labelValue() {

				return this.isDatePicker ? this.label[0] : this.label[1]
			}
		},

		beforeMount() {

			this.updateValue();
		},

		methods :{

			clearFieldValue() {

				this.changedValue = null;

				this.onChange(this.changedValue, this.name);

				window.eventHub.$emit('updateFilter',{ name : this.name, value : this.changedValue});
			},

			updateValue() {

				if(this.value){

					if(this.value.includes('last::')){

						this.isDatePicker = false;

						const splitByColon = this.value.split('::');
	          
			          const splitByTilt = splitByColon[1].split('~');
			          
			          this.count = splitByTilt[0];
			          
			          this.option = splitByTilt[1];

			          this.onInput();

					} else {

						this.isDatePicker = true;

						this.changedValue = this.value;

						this.onDateChange()
					}
				}
			},

			onInput(){
				
				if(this.count){

					this.onChange('last::'+this.count+'~'+this.option, this.name)
				} else {

					this.clearFieldValue()
				}
			},

			onDateChange() {

				this.onChange(this.changedValue, this.name)
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

			DatePicker,

			'form-field-template' : require('./FormFieldTemplate')
		}
	};
</script>

<style>
		.mx-input{
				border-radius: 0 !important;
		}
		.mx-shortcuts-wrapper .mx-shortcuts {
				text-transform: capitalize;
		}
		.mx-calendar-content {
				width: 224px !important;
		}
		.mx-datepicker{
						width: 100% !important;
		}
		.mx-datepicker-range {
				width: 87% !important;
		}
		.mx-input-wrapper input {
				background-color: transparent !important;
				border-top-right-radius : 0 !important;border-bottom-right-radius : 0 !important;
		}
		.mx-calendar-icon{
				height: auto !important;
		}
		.mx-input-append{
				background-color: transparent !important;
				top: 2px !important;
				width: 25px !important;
				visibility: hidden !important;
		}
		.input-group {
		  display: flex;
		}
		.index0{
			z-index: unset !important;
		}
		.clear-btn{ cursor: pointer; }

		.range_btn { border-top-left-radius : 0 !important;border-bottom-left-radius : 0 !important;}
</style>