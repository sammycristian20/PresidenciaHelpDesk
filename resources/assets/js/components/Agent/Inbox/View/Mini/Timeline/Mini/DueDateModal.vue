<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="divStyle">

		<div slot="title">
	
			<h4 class="modal-title">{{lang('change_due_date')}}</h4>
		</div>

		<div v-if="!loading" slot="fields" class="row">
			
			<date-time-field 
				:label="lang('select_date')"
				value="" 
				type="datetime" 
				key="due_date"
				id="due_date"
				name="due_date"
				 :onChange="onChange" 
				 :required="true" 
				 format="YYYY-MM-DD HH:mm:ss" 
				 classname="col-sm-12"
				 :clearable="true" 
				 :disabled="false" 
				 :editable="true"
				 :pickers="true"
				 :currentYearDate="false" 
				 :notBefore="new Date()"
				 >
			  </date-time-field>
		</div>

		<div class="row" slot="fields" v-if="loading">

			<loader :duration="4000" :size="60"></loader>

		</div>
						
		<button slot="controls" type="button"  @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">
			
			<i class="fas fa-sync-alt"></i> {{lang('update')}}
		</button>
	</modal>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	import moment from 'moment'

	export default {

		name:'due-date-modal',

		description:'Due Date modal Component',

		props:{

			showModal : { type : Boolean, default : false },

			onClose : { type : Function },

			ticketId : { type : String | Number, default : '' },

			reloadDetails : { type : Function }
		},

		data(){
			
			return {

				isDisabled:false,

				loading:false,

				due_date : '',

				divStyle : { width : '500px' }
			} 
		},

		beforeMount() {

			this.isDisabled = this.due_date ? false : true;
		},

		methods:{

			onChange(value,name) {

				this[name] = value;

				this.isDisabled = value ? false : true;
			},

			onSubmit(){
				
				this.loading = true;

				this.isDisabled = true;

				const data = {};

				data['ticket-id'] = this.ticketId;

				data['duedate'] =  moment(this.due_date).format('YYYY-MM-DD HH:mm:ss');

				axios.post('/api/ticket/change-duedate',data).then(res=>{

					this.reloadDetails('duedate');

					successHandler(res,'timeline');

					this.loading = false;

					this.isDisabled = false;

					this.onClose();

				}).catch(err=>{

					errorHandler(err,'timeline');

					this.loading = false;

					this.isDisabled = false;

					this.onClose();
				})
			},
		},

		components:{
			
			'modal':require('components/Common/Modal.vue'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
		},
	};
</script>

<style scoped>
	
</style>