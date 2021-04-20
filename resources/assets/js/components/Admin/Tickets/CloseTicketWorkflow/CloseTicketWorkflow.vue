<template>
	
	<div>
		
		<alert componentName="close-ticket-workflow"></alert>

		<div class="card card-light">
			
			<div class="card-header">
				
				<h3 class="card-title">{{trans('close_ticket_workflow')}}</h3>
			</div>

			<div class="card-body">
				
				<div class="row" v-if="!hasDataPopulated || loading">

					<loader :animation-duration="4000" :size="60"/>
				</div>

				<template v-if="hasDataPopulated">

					<div class="row">
						
						<number-field :label="trans('no_of_days')" :value="days" name="days" :onChange="onChange" 
							classname="col-sm-6" type="number" :required="true" placeholder="n" :hint="trans('close-msg1')"
						>
						
						</number-field>

						<radio-button :options="radioOptions" :label="trans('send_email_to_user')" name="send_email" 
							:value="send_email" :onChange="onChange" classname="form-group col-sm-6" :hint="trans('close-msg4')">
								
						</radio-button>
					</div>

					<div class="row">
						
						<dynamic-select :label="trans('ticket_status')" :multiple="true" name="ticket_status" classname="col-sm-6"
							apiEndpoint="/api/dependency/statuses?config=true" :value="ticket_status" :onChange="onChange" :strlength="30"
							:required="true" :hint="trans('close_ticket_statu_msg')">
						
						</dynamic-select> 

						<dynamic-select :label="trans('target_status')" :multiple="false" name="status" classname="col-sm-6"
							apiEndpoint="/api/dependency/statuses?supplements[purpose_of_status][]=2" :value="status" :onChange="onChange" 
							:strlength="30" :required="true" :clearable="status ? true : false" :hint="trans('close-msg3')">
						
						</dynamic-select> 
					</div>
				</template>
			</div>

			<div class="card-footer">

				<button class="btn btn-primary" @click="onSubmit()">
					
					<i class="fas fa-save"></i> {{trans('save')}}
				</button>
			</div>	
		</div>

		<div class="row" v-if="pageLoad">

			<custom-loader :duration="4000"></custom-loader>
		</div>
	</div>
</template>

<script>
	
	import { validateCloseWorkflowSettings } from "helpers/validator/closeWorkflowSettingRules.js"

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'close-ticket-workflow',

		data () {

			return {

				days : '',

				send_email : 1,

				radioOptions:[{name:'yes',value:1},{name:'no',value:0}],

				ticket_status : '',

				status : '',

				loading : true,

				hasDataPopulated : false,

				pageLoad : false
			}
		},

		beforeMount(){

			this.getValues();
		},

		methods : {

			getValues(id){
				
				axios.get('/api/close-workflow').then(res=>{

					this.loading = false;

					this.hasDataPopulated = true;

					let values = res.data.data.workflowClose;

					this.days = values.days;

					this.send_email = values.send_email;
					
					this.status = values.status;
					
					this.ticket_status = values.ticket_status;
				
				}).catch(error=>{
					
					this.loading = false;

					this.hasDataPopulated = true;
				});
			},

			isValid() {

				const { errors, isValid } = validateCloseWorkflowSettings(this.$data);
				
				return isValid;
			},

			onChange(value, name) {

				this[name] = value;

				if(value === null){

					this[name] = '';
				}
			},

			onSubmit() {
			
				if (this.isValid()) {
			
					this.pageLoad = true;
			
					const data = {};

					data['days'] = this.days;

					data['send_email'] = this.send_email;

					data['ticket_status'] = this.ticket_status.map(a => a.id);

					data['status'] = this.status.id;

					data['_method'] = 'PATCH';

					axios.post('/api/close-workflow-update', data).then(res => {

						this.pageLoad = false;
						
						successHandler(res,'close-ticket-workflow');
						
						this.getValues()
						
					}).catch(err => {

						this.pageLoad = false;
					
						errorHandler(err,'close-ticket-workflow');
					});
				}
			}
		},

		components : {

			'radio-button':require('components/MiniComponent/FormField/RadioButton'),

			'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),
			
			'number-field':require('components/MiniComponent/FormField/NumberField'),

			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			'loader' : require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>