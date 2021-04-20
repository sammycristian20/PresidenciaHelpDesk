<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" @close="showModal = false" 
		:containerStyle="containerStyle">
		
		<div slot="title">
		
			<h4 class="modal-title">{{lang('edit')}} {{this.data.name}}</h4>
		</div>
			
		<div v-if="!loading" slot="fields">
			
			<div slot="alert">

				<alert componentName="telephony-edit"/>
			</div>

			<div class="row">

				<text-field :label="lang('app-id')" :value="app_id" type="text" name="app_id"
					:onChange="onChange" classname="col-sm-6" :hint="lang('telephony_app_id_hint')"></text-field>

				<text-field :label="lang('api_token')" :value="token" type="text" name="token"
					:onChange="onChange" classname="col-sm-6" :hint="lang('telephony_app_token_hint')"></text-field>
			</div>

			<div class="row">

				<text-field :label="lang('conversion_waiting_time')" :value="conversion_waiting_time" type="text" name="conversion_waiting_time"
				:onChange="onChange" classname="col-sm-6" :hint="lang('conversion_waiting_time_hint')"></text-field>

				<dynamic-select :label="lang('select_default_region')" :multiple="false"
				name="iso" :required="true" :prePopulate="true" classname="col-sm-6" apiEndpoint="/telephony/api/get-regions-list" :value="iso" 
				:onChange="onChange" :clearable="iso ? true : false" 
				:hint="lang('default_region')">
				</dynamic-select>
			</div>
			
			<div class="row">
				<radio-button :options="radioOptions" :label="lang('log_miss_call')" 
				name="log_miss_call" :value="log_miss_call" :onChange="onChange" 
				classname="form-group col-sm-6" :hint="lang('telephony_log_missed_call_hint')" >
				</radio-button>
			</div>
		</div> 
		
		<div slot="controls">
			
			<button type="button" @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">

				<i class="fas fa-save"></i> {{lang('save')}}
			</button>
		</div>

		<div v-if="loading" class="row" slot="fields">
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import axios from 'axios'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { findObjectByKey } from 'helpers/extraLogics'

	export default {
		
		name : 'telephony-settings-modal',

		description : 'Telephony settings modal component',

		props:{
			
			showModal:{type:Boolean,default:false},

			onClose:{type: Function},

			data : { type : Object , default:()=>{}}
		},

		data(){
			
			return {
				app_id : '',

				token : '',
				
				log_miss_call : '',

				iso : '',

				containerStyle : { width:'1000px' },

				loading:false,

				isDisabled : false,

				radioOptions : [{name:'yes',value:1},{name:'no',value:0}],

				conversion_waiting_time : 0
			}
		},

		beforeMount(){

			this.getValues();
		},

		methods : {

			getValues(){

				this.loading = true;

				this.isDisabled = true;

				axios.get('/telephony/api/get-provider-details/'+this.data.short).then(res=>{

					this.token = findObjectByKey(res.data.data, 'key', 'token').value;

					this.log_miss_call = findObjectByKey(res.data.data, 'key', 'log_miss_call').value;

					this.app_id = findObjectByKey(res.data.data, 'key', 'app_id').value;

					this.iso = findObjectByKey(res.data.data, 'key', 'iso').value;

					this.fullName = findObjectByKey(res.data.data, 'key', 'name').value;
					
					this.shortName = findObjectByKey(res.data.data, 'key', 'short').value;

					this.conversion_waiting_time = findObjectByKey(res.data.data, 'key', 'conversion_waiting_time').value;

					this.loading = false;

					this.isDisabled = false;

				}).catch(error=>{

					this.loading = false;

					this.isDisabled = false;

					errorHandler(error,'telephony-edit')

				})
			},

			onChange(value,name){

				this[name] = value;
			},

			onSubmit(){

				this.loading = true;

				this.isDisabled = true;
				

				if(!this.iso) {
					this.loading = false;
					this.isDisabled = false;
					this.$store.dispatch('setValidationError', {'iso' : ''});
					return false;
				}
				
				const data = {
					"app_id": this.app_id,
					"token" : this.token ,
					"log_miss_call" : this.log_miss_call,
					"iso": this.iso.iso,
					"conversion_waiting_time": this.conversion_waiting_time,
				}

				axios.post('/telephony/api/update-provider-details/'+this.data.short,data).then(res=>{

					this.loading = false;

					this.isDisabled = false;

					this.onClose();

					successHandler(res,'dataTableModal')

				}).catch(error=>{

					this.loading = false;

					this.isDisabled = false;

					errorHandler(error, 'telephony-edit')
				})
			}
		},

		components:{

		'modal':require('components/Common/Modal.vue'),

		'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		'text-field': require('components/MiniComponent/FormField/TextField'),

		'radio-button':require('components/MiniComponent/FormField/RadioButton'),

		'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),

		'alert' : require('components/MiniComponent/Alert'),
	}
};
</script>