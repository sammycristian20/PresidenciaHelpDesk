<template>

	<div>
		
		<alert componentName="system-settings"></alert> 

		<div class="card card-light">
			
			<div class="card-header">
				
				<h3 class="card-title">{{trans('system-settings')}}</h3>
			</div>

			<div class="card-body">

				<div class="row" v-if="!hasDataPopulated || loading">

					<loader :animation-duration="4000" :size="60"/>
				</div>

				<template v-if="hasDataPopulated">

					<div class="row">
						
						<text-field :label="trans('name')" type="text" :value="name" name="name" class="col-sm-4" :required="true" 
							:onChange="onChange">

						</text-field>

						<dynamic-select :label="trans('timezone')" :multiple="false" name="system_time_zone" classname="col-sm-4"
							apiEndpoint="/api/dependency/time-zones" :value="system_time_zone" :onChange="onChange" :strlength="25"
							:required="true">
						
						</dynamic-select> 

						<dynamic-select :label="trans('timeformat')" :multiple="false" name="time_format" classname="col-sm-4"
							apiEndpoint="/api/dependency/time-formats" :value="time_format" :onChange="onChange" :strlength="25"
							:required="true" :showPreview="timeFormat(time_format)">
						
						</dynamic-select>
					</div>

					<div class="row">
						
						<dynamic-select :label="trans('dateformat')" :multiple="false" name="date_format" classname="col-sm-4"
							apiEndpoint="/api/dependency/date-formats" :value="date_format" :onChange="onChange" :strlength="25"
							:required="true" :showPreview="previewMethod(date_format)">
						
						</dynamic-select> 
						
						<radio-button :options="statusOptions" :label="trans('status')" name="status" :value="status" :onChange="onChange" 
							classname="form-group col-sm-4">
								
						</radio-button>

						<radio-button :options="cdnOptions" :label="trans('cdn')" name="cdn" :value="cdn" :onChange="onChange" 
							classname="form-group col-sm-4" :hint="trans('cdn_tooltip')">
								
						</radio-button>
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
	
	import { validateSystemSettings } from "helpers/validator/systemSettingRules.js"

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'
	import moment from 'moment'

	export default {

		name : 'system-settings',

		data () {

			return {

				hasDataPopulated : false,

				loading : true,

				pageLoad : false,

				name : '',

				status : '',

				cdn : '',

				system_time_zone : '',

				time_format : '',

				date_format : '',

				radioOptions:[{name:'yes',value:1},{name:'no',value:0}],

				statusOptions:[{name:'Online',value:1},{name:'offline',value:0}],
				
				cdnOptions:[{name:'on',value:1},{name:'off',value:0,hint : 'cdn_off_tooltip'}],

			}
		},

		beforeMount () {

			this.getValues()
		},

		methods : {

			previewMethod(value) {
				return value ? moment(new Date()).format(value.js_format) : ''
			},

			timeFormat(value) {
				return moment(new Date()).format(value.js_format);
			},

			getValues(id){
				
				axios.get('/api/admin/get-system-setting').then(res=>{

					this.loading = false;

					this.hasDataPopulated = true;

					this.updatesStateWithData(res.data.data)
				
				}).catch(error=>{
					
					this.loading = false;

					this.hasDataPopulated = true;
				});
			},

			updatesStateWithData(system){

				const self = this

				const stateData = this.$data

				Object.keys(system).map(key=>{

					if(stateData.hasOwnProperty(key)){

						self[key] = system[key];
					}
				})
			},

			isValid() {

				const { errors, isValid } = validateSystemSettings(this.$data);
				
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

					data['name'] = this.name;

					data['time_zone_id'] = this.system_time_zone.id;

					data['time_format'] = this.time_format.format;
					
					data['date_format'] = this.date_format.name;

					data['cdn'] = this.cdn;

					data['status'] = this.status;

					axios.post('/api/admin/update-system-setting', data).then(res => {

						this.pageLoad = false;
						
						successHandler(res,'system-settings');
						
						this.getValues()
						
					}).catch(err => {

						this.pageLoad = false;
					
						errorHandler(err,'system-settings');
					});
				}
			}
		},

		components : {

			'alert' : require('components/MiniComponent/Alert'),

			'text-field' : require('components/MiniComponent/FormField/TextField.vue'),

			'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect.vue'),

			'custom-loader': require("components/MiniComponent/Loader"),

			'loader' : require('components/Client/Pages/ReusableComponents/Loader'),

			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
		}
	};
</script>