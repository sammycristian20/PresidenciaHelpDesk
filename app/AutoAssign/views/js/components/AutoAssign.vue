<template>
	
	<div>
		
		<alert componentName="AutoAssign"></alert> 

		<div class="card card-light">
			
			<div class="card-header">
				
				<h3 class="card-title">{{trans('settings')}}</h3>
			</div>

			<div class="card-body">
				
				<template v-if="loading || !hasDataPopulated">
					
					<div class="row">
						
						<loader :animation-duration="4000" :size="60"/>
					</div>
				</template>

				<template v-if="hasDataPopulated">
					
					<div class="row">
					
						<radio-button :options="radioOptions" :label="trans('enable')" name="status" :value="status"
							:onChange="onChange" classname="form-group col-sm-6"
							>
								
						</radio-button>

						<radio-button :options="radioOptions" :label="trans('only-login-agents')" name="only_login" :value="only_login"
							:onChange="onChange" classname="form-group col-sm-6"
							>
								
						</radio-button>
					</div>

					<div class="row">
						
						<radio-button :options="radioOptions" :label="trans('assign-ticket-even-agent-in-non-acceptable-mode')" name="assign_not_accept" 
							:value="assign_not_accept" :onChange="onChange" classname="form-group col-sm-6"
							>
								
						</radio-button>

						<radio-button :options="radioOptions" :label="trans('assign-ticket-with-agent-having-type')" name="assign_with_type" 
							:value="assign_with_type" :onChange="onChange" classname="form-group col-sm-6"
							>
								
						</radio-button>
					</div>

					<div class="row">
						
						<radio-button :options="radioOptions" :label="trans('assign-ticket-with-agent-having-location')" name="is_location" 
							:value="is_location" :onChange="onChange" classname="form-group col-sm-6"
							>
								
						</radio-button>

						<radio-button :options="deptOptions" :label="trans('auto-assign-enabled-departments')" name="assign_department_option" 
							:value="assign_department_option" :onChange="onChange" classname="form-group col-sm-6"
							>
								
						</radio-button>
					</div>

					<div class="row">
					
						<number-field :label="trans('maximum-number-of-ticket-can-assign-to-agent')" :value="threshold" name="threshold"
							:onChange="onChange" classname="col-sm-6" type="number" :required="false" placeholder="n"
						>
					
						</number-field>

						<dynamic-select v-if="assign_department_option === 'specific'" :label="trans('select-deparment')" :multiple="true" 
							name="department_list" classname="col-sm-6" apiEndpoint="/api/dependency/departments" :value="department_list" 
							:onChange="onChange" :strlength="30" :required="true">
						
						</dynamic-select> 
					</div>
				</template>
			</div>

			<div class="card-footer">
				
				<button class="btn btn-primary" @click="onSubmit">
					
					<i class="fas fa-sync"> </i> {{trans('update')}}
				</button>
			</div>
		</div>

		<div class="row" v-if="pageLoad">

			<custom-loader :duration="4000"></custom-loader>
		</div>
	</div>
</template>

<script>
	
	import { errorHandler, successHandler } from "helpers/responseHandler";

	import axios from 'axios';

	import { validateAutoAssignSettings } from "../helpers/validator/autoAssignRules";

	export default {

		name : 'auto-assign',

		data () {

			return {

				status : 0,
				
				only_login : 0,
				
				assign_not_accept : 0,
				
				assign_with_type : 0,
				
				is_location : 0,
				
				assign_department_option : "all",
				
				threshold : '',

				department_list : [],

				radioOptions:[{name:'yes',value:1},{name:'no',value:0}],

				deptOptions:[{name:'all',value:'all'},{name:'specific',value:'specific'}],

				loading : true,

				hasDataPopulated : false,

				pageLoad : false
			}
		},

		beforeMount () {

			this.getValues();
		},

		methods : {

			getValues() {

				axios.get('/api/get-auto-assign').then(res=>{

					this.loading = false;

					this.hasDataPopulated = true;

					this.updateStatesWithData(res.data.data.autoAssign);

				}).catch(err=>{

					this.loading = false;

					this.hasDataPopulated = true;
				})
			},

			updateStatesWithData(assignData) {
				
				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(assignData).map(key => {
				
					if (stateData.hasOwnProperty(key)) {
					
						self[key] = (key != 'assign_department_option' && key != 'department_list') ? parseInt(assignData[key]) : assignData[key];
					}
				});

				this.threshold = this.threshold ? this.threshold : ''
			},

			onChange(value,name) {

				this[name] = value;

				if(this.assign_department_option == 'all') {

					this.department_list = [];
				}
			},

			isValid() {

				const { errors, isValid } = validateAutoAssignSettings(this.$data);
			
				return isValid;
			
			},

			onSubmit() {

				if(this.isValid()){

					this.pageLoad = true;

					const data = {};

					data['status'] = this.status;
					data['only_login'] = this.only_login;
					data['assign_not_accept'] = this.assign_not_accept;
					data['assign_with_type'] = this.assign_with_type;
					data['is_location'] = this.is_location;
					data['threshold'] = this.threshold;
					data['assign_department_option'] = this.assign_department_option;

					if(this.assign_department_option === 'specific') {

						data['department_list'] = this.department_list.map(a => a.id);
					}

					axios.post('/api/auto-assign',data).then(res=>{

						this.pageLoad = false;

						successHandler(res, "AutoAssign");

						this.getValues();

					}).catch(err=>{

						errorHandler(err, "AutoAssign");

						this.pageLoad = false;
					})
				}
			}
		},

		components : {

			'radio-button':require('components/MiniComponent/FormField/RadioButton'),

			'number-field':require('components/MiniComponent/FormField/NumberField'),

			'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			"alert": require("components/MiniComponent/Alert"),

			'custom-loader' : require('components/MiniComponent/Loader'),
		}
	};
</script>