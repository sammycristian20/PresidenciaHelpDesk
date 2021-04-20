<template>
	<div> 

		<!-- modal popup for crete and edit and delete holidays -->
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle" :addRow="addRow" :deleteRow="deleteRow">
			
			<div slot="alert">
				<alert-notification :successShow="SuccessAlert" :errorShow="ErrorAlert" :successMsg="lang(success)" :errorMsg="lang(error)" />
			</div>

			<div slot="title">
				<h4 class="modal-title">{{lang(page_title)}}</h4>
			</div>

			<div v-if="deleteData === 'yes'" slot="fields">
				<span>{{lang('are_you_sure_you_want_to_delete')}}</span>
			</div> 
		
			<div v-if="deleteData !== 'yes'" slot="fields">
				<div class="row">
					<div class="form-group col-sm-12">
						<date-time-field :label="lang('date')" :value="date" type="date" name="date"
						:onChange="onChange" :required="true" format="MM-DD-YYYY" classname="col-sm-6" :clearable="false" :disabled="false" :before="before" :after="after"></date-time-field>
					</div>
					<div class="form-group col-sm-12">		
						<text-field :label="lang('description')" :value="holiday_description" type="textarea" name="holiday_description"
						:onChange="onChange" :required="true" :length="35" classname="col-sm-12" :show-word-limit="true"></text-field>
					</div>
				</div>
			</div>

			<div slot="controls">
				<button type="button" @click = "onSubmit" :class="classname" ><i :class="iconClass" aria-hidden="true"></i> {{lang(btnName)}}</button>
			</div>

		</modal>
	</div>
</template>

<script type="text/javascript">

import DatePicker from 'vue2-datepicker'

import {validateHolidaySettings} from "helpers/validator/businessHoursSettingsRules.js"

import Modal from 'components/Common/Modal.vue'

import moment from 'moment'

	export default {
		
		name : 'holiday',

		description : 'holiday create edit and delete component',

		props:{
			
			/**
			 * status of the modal popup
			 * @type {Object}
			 */
			showModal:{type:Boolean,default:false},

			/**
				* The function which will be called as soon as user click on the close button        
				* @type {Function}
			*/
			onClose:{type: Function},

			/**
				* The function which will be called as soon as user click on the save button        
				* @type {Function}
			*/
			addRow:{type: Function},

			/**
				* The function which will be called as soon as user click on the delete button        
				* @type {Function}
			*/
			deleteRow:{type: Function},

			/**
			 * holiday list array
			 * @type {Array}
			 */
			data:{type:Array},

			/**
			 * edit holiday details
			 * @type {Object}
			 */
			editData:{type:Object},

			/**
			 * delete holiday details
			 * @type {String}
			 */
			deleteData:{type:String},

			/**
			 * index of the holiday
			 * @type {Number}
			 */
			index:{type:Number},

			before:moment().year() + '-01-01',

			after:moment().year() + '-12-31',

		},

		data: () => ({

			/**
			 * time track description
			 * @type {String}
			 */
			holiday_description:'',

			/**
			 * holiday
			 * @type {Number|String}
			 */
			date: '',

			/**
			* for rtl support
			* @type {String}
			*/
			lang_locale:'',

			/**
			 * width of the modal container
			 * @type {Object}
			 */
			containerStyle:{
				width:'500px'
			},

			/**
			 * alert fields
			 * @type {String}
			 */
			SuccessAlert:'none',success:'',error:'',ErrorAlert:'none',

			/**
			 * button name according to create and edit and delte popup
			 * @type {String}
			 */
			btnName:'',

			iconClass : 'fas fa-save',

			/**
			 * page title according to create and edit and delte popup
			 * @type {String}
			 */
			page_title:'',

			/**
			 * button class name according to create and edit and delte popup
			 * @type {String}
			 */
			classname : 'btn btn-primary',


			moment:moment

		}),
		
		created(){
			
			// getting locale from localStorage
			this.lang_locale = localStorage.getItem('LANGUAGE');

			//binding edit data values
			if(this.editData.description !== undefined ){
				this.btnName = 'update';
				this.iconClass = 'fas fa-sync';
				this.page_title = 'edit_holiday'
				this.holiday_description = this.editData.description;
				this.date = moment(this.editData.date+'-'+moment().year(),'MM-DD-YYYY');
			} else if (this.deleteData === 'yes') {
				this.btnName = 'delete';
				this.iconClass = 'fas fa-trash';
				this.page_title = 'delete_holiday'
				this.classname = 'btn btn-danger'
			}
			else{
				this.btnName = 'save';
				this.iconClass = 'fas fa-save';
				this.page_title = 'add_new'
			}
		},

		methods:{
			/**
			* checks if the given form is valid
			* @return {Boolean} true if form is valid, else false
			* */
			isValid(){
				const {errors, isValid} = validateHolidaySettings(this.$data)
				if(!isValid){
					return false
				}
				return true
			},

			/**
			* for showing time track fields in reply form
			* populates the states corresponding to 'name' with 'value'
			* @param  {string} value
			* @param  {[type]} name
			* @return {void}
			*/
			onChange(value,name){
				this[name]=value;
			},

			/**
			 * api calls happens here
			 * @return {Void} 
			 */
			onSubmit(){
				// for delete
				if(this.deleteData === 'yes'){
					this.deleteRow(this.index);
				// for create and edit
				} else if(this.isValid()){
					//for edit mode
						if(this.editData.description !== undefined){
							const filterArray = this.data.filter( array => array.date !== this.editData.date);
							this.updateData(filterArray);
						} else{
							// for create mode
							this.insertData();
						}
					}
				},

				updateData(data){
					const containsDate = data.some( element => element.date ===  moment(this.date).format("MM-DD-YYYY"));
					if(containsDate === false){
						this.editData.date= moment(this.date).format("MM-DD-YYYY");
						this.editData.description=this.holiday_description;
						this.data[this.index]= this.editData;
						this.onClose();
					} else {
						this.errorMessage();
					}
				},

				insertData(){
					const containsDate = this.data.some( element => element.date ===  moment(this.date).format("MM-DD-YYYY"));
					if(containsDate === false){
						const data = { date:  moment(this.date).format("MM-DD-YYYY"), description: this.holiday_description }
						this.addRow(data);
					} else {
							this.errorMessage();
					}
				},

				errorMessage(){
					this.ErrorAlert="block";
					this.error='date_already_taken';
					setTimeout(()=>{
						this.ErrorAlert="none";
					}, 5000);
				}
			},

			components:{

				DatePicker, 

				'modal':Modal,

				'text-field': require('components/MiniComponent/FormField/TextField'),

				'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),

				'alert-notification' : require('components/Common/pageNotification.vue')
			}

	};
</script>

<style type="text/css">
.mx-input{
		border-radius: 0 !important;
}
.mx-input-append {
	height: 33px !important;
}
#H5{
	margin-left:16px; margin-bottom:18px !important;
}
</style>