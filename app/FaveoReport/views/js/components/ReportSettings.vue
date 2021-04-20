<template>
	<div>
		<alert/>
		
		<faveo-box :title="trans('settings')">

				<div class="row">
					<static-select :label="trans('record_per_export')" :value="records" name="records" classname="col-sm-6" :elements="elements"
								  :onChange="onChange" type="number" :required="true" :hint="trans('record_per_export_tooltip')">
					</static-select>

					<div v-if="loading === true" class="row" slot="fields">
						<loader :duration="4000"/>
					</div>
				</div>

				<div slot="actions" class="card-footer">

						<button type="button" id="submit_btn" @click="onSubmit()" class="btn btn-primary" :disabled="isDisabled">
							<i class="fas fa-save" aria-hidden="true"></i> {{trans('submit')}}</button>

					</div>
		</faveo-box>
	</div>
</template>

<script type="text/javascript">
	
	import axios from 'axios'
	import {errorHandler, successHandler} from 'helpers/responseHandler'
	import {validateReportSettings} from "../helpers/validator/reportSettingsRules"
	import FaveoBox from "components/MiniComponent/FaveoBox";
	
	export default {
	
		name:'reports-settings',

		description:'reports settings page',
		
		components:{
			FaveoBox,

			'static-select':require('components/MiniComponent/FormField/StaticSelect'),

			'loader':require('components/MiniComponent/Loader'),
			
			'alert' : require('components/MiniComponent/Alert'),
		},
		beforeMount(){
			this.getInitialValues()
		},
		data:()=> ({

			records : 0,

			/**
			 * initial state of loader
			 * @type {Boolean}
			 */
			loading:false,

			/**
			 * initial state of the button
			 * @type {Boolean}
			 */
			isDisabled:false,


			elements: [
				{id:200, name:200}, {id:400, name:400}, {id:600, name:600}, {id:800, name:800}, {id:1000, name:1000},
				{id:1200, name:1200}, {id:1400, name:1400}, {id:1600, name:1600}, {id:1800, name:1800}, {id:2000, name:2000},
				{id:2200, name:2200}, {id:2400, name:2400}, {id:2600, name:2600}, {id:2800, name:2800}, {id:3000, name:3000}
			],
		}),

		methods:{
			/**
			* gets initial state of states defined in this component
			* @return {void}
			*/
			getInitialValues(){
				this.loading = true
				this.isDisabled = true
				axios.get('/api/report-settings').then(res => {
					this.records = res.data.data.records_per_file
					this.loading = false
					this.isDisabled = false
				}).catch(err => errorHandler(err))
			},

			isValid(){
				const {errors, isValid} = validateReportSettings(this.$data)
				if(!isValid){
					return false
				}
				return true
			},

			onChange(value,name){
				this[name]=value;
			},

			/**
			 * initial state of the component
			 * @return {Void}
			 */
			initialState(){
				this.loading = false
				this.isDisabled = false
			},

			onSubmit(){
				if(this.isValid()){
					this.loading = true;
					this.isDisabled = true;
					axios.post('/api/report-settings',{records_per_file: this.records}).then(res=>{
						successHandler(res);
						this.initialState();
					}).catch(err => {
						errorHandler(err);
						this.initialState();
					})
				}
			}
	
		}
	};
</script>