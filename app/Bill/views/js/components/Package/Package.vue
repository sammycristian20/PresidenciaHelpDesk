<template>
	<div>

		<div class="row" v-if="hasDataPopulated === false || loading === true">

			<custom-loader :duration="loadingSpeed"></custom-loader>

		</div>

		<alert componentName="package"/>

		<div class="card card-light" v-if="hasDataPopulated === true">

			<div class="card-header">
				
				<h3 class="card-title">{{lang(title)}}</h3>
			</div>
					
			<div class="card-body">
		
				<div class="row">
			
					<text-field label="Name" :value="name" type="text" 
						name="name" :onChange="onChange" classname="col-sm-4" 
						:required="true">
						
					</text-field>

					<number-field :label="lang('display_order')" :value="display_order" 
						name="display_order" classname="col-sm-4"
            :onChange="onChange" type="number" :required="true">
            
          </number-field>

          <dynamic-select :label="lang('departments')" :multiple="true" name="departments" :prePopulate="true"
            classname="col-sm-4" apiEndpoint="/api/dependency/departments" :value="departments" :onChange="onChange">
          
          </dynamic-select>

				</div>

				<div class="row">
					
					<text-field :label="lang('description')" :value="description" 
						type="textarea" name="description"
						:onChange="onChange" classname="col-sm-12" :required="true">
							
					</text-field>

				</div>

				<div class="row">
					
					<static-select :label="lang('billing_cycle')"  
						:elements="cycle_options" name="validity" :value="validity" 
						classname="col-sm-6" :onChange="onChange" :required="true">
					</static-select>

					<radio-button :options="radioOptions" :label="lang('credit_type')" name="credit_type" :value="credit_type"
					:onChange="onChange" classname="form-group col-sm-3" >
						
					</radio-button>

					<div class="col-sm-3"> 

						<label for="package" class="col-sm-12 control-label">{{lang('status')}}</label>
						
						<div class="col-sm-2">

							<status-switch name="status" :value="status" :onChange="onChange"
								classname="pull-left" :bold="true">
							</status-switch>
						</div>
					</div>
				</div>
				
				<div class="row">

					<time-field v-if="credit_type === 0" :label="lang('time')" :value="validity"  
						:formStyle="formStyle" type="text" name="validity" 
						:onChange="onChange" classname="col-sm-6" :required="true">
							
					</time-field>

					<number-field v-else :label="lang('incident_credit')" :value="allowed_tickets" 
						name="allowed_tickets" classname="col-sm-6"
            :onChange="onChange" type="number" :required="true">
            
          </number-field>

					<number-field :label="lang('price')" :value="price" 
						name="price" classname="col-sm-6"
            :onChange="onChange" type="number" :required="true">
            
          </number-field>
				</div>

				<div class="row">

					<text-field :label="lang('terms_conditions_page_link')" :value="kb_link" 
						type="text" name="kb_link"
						:onChange="onChange" classname="col-sm-6">
							
					</text-field>

					<file-upload :label="lang('image')" :value="package_pic" name="package_pic" 
					:onChange="onChange" classname="col-sm-6" accept="image/*">
					</file-upload>
				</div>
				
			</div>

			<div class="card-footer">
				
				<button type="button" id="submit_btn" v-on:click="onSubmit()" :disabled="loading" class="btn btn-primary">
						
					<span :class="iconClass"></span>&nbsp;{{lang(btnName)}}
					
				</button>
			</div>
		
		</div>

	</div>
</template>

<script>
	
	import axios from "axios";
	
	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { validatePackageCreateSettings } from "faveoBilling/helpers/validator/packageCreateRules";
	
	import {getIdFromUrl} from 'helpers/extraLogics';
	
	import { mapGetters } from 'vuex'

	export default {

		name : 'pacakges',

		description : 'Pacakges data table component',

		props : {

		},

		data(){

			return {

				base : '',

				name : '',

				credit_type : 1,

				description : '',

				display_order : '',

				status : 0,

				// radioOptions : [{name:'incident_credit',value:1},{name:'time_credit',value:0}],

				radioOptions : [{name:'incident_credit',value:1}],

				cycle_options : [
					{ id:"one_time",name:"One time" },
					{ id:"monthly",name:"Monthly" },
					{ id:"quarterly",name:"Quarterly" },
					{ id:"semi_annually",name:"Semi annually" },
					{ id:"annually",name:"Annually" }
					],

				validity : 'one_time',

				title :'create_new_package', 

				iconClass:'fas fa-save',

				btnName:'save',

				loading: false,//loader status
				
				loadingSpeed: 4000,
				
				hasDataPopulated: true,

				package_id :'',

				formStyle:{ width:'15%' },

        price : '',

        allowed_tickets : '',

        package_pic : '',

        kb_link : '',

        departments : []
			}
		},

		computed :{
 			
 			...mapGetters(['getUserData'])
		},

		watch : {

			getUserData(newValue,oldValue){
			
				this.base = newValue.system_url
	   		
	   		return newValue
		   }
		},

		beforeMount(){

			const path = window.location.pathname;

			this.getValues(path);
		},

		created(){

			if(this.getUserData.system_url){
				this.base = this.getUserData.system_url
			}
		},

		methods : {

			getValues(path){

				const packageId = getIdFromUrl(path);

				if (path.indexOf("edit") >= 0) {
					
					this.title = 'edit-package';
							
					this.iconClass = 'fas fa-sync'
					
					this.btnName = 'update'

					this.hasDataPopulated = false;
					
					this.getInitialValues(packageId);
				 
				 } else {

					this.loading = false;

					this.hasDataPopulated = true
				}
			},

			getInitialValues(id) {

				this.loading = true;			
				
				axios.get('/api/bill/package/edit/'+id).then(res => {

					this.package_id  = id

					this.hasDataPopulated = true;
					
					this.loading = false;

					this.updateStatesWithData(res.data.data);
				
				}).catch(err => {
					
					errorHandler(err)
					
					this.hasDataPopulated = true;
					
					this.loading = false;
				
				});
			
			},

			updateStatesWithData(packageData) {
				
				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(packageData).map(key => {

					if (stateData.hasOwnProperty(key)) {
					
						self[key] = packageData[key];
					
					}
				
				});
			
			},

			isValid() {

				const { errors, isValid } = validatePackageCreateSettings(this.$data);
			
				if (!isValid) {
			
					return false;
			
				}
			
					return true;
			
			},

			onSubmit() {
				
				this.kb_link = this.kb_link === null ? '' : this.kb_link;
				
				if (this.isValid()) {
			
					this.loadingSpeed = 8000;
			
					this.loading = true;

					var fd = new FormData();
					
					if(this.package_id != ''){
					
						fd.append('id',parseInt(this.package_id));
					
					}
					
					fd.append('name', this.name)

					fd.append('status', this.status === true || this.status === 1 ? 1 : 0)

					fd.append('description', this.description)

					fd.append('display_order', this.display_order)

					fd.append('price', parseInt(this.price))

					fd.append('validity', this.validity)

					if(this.departments.length > 0){

						fd.append('departments', this.departments.map(a => a.id))						
					}

					this.credit_type === 1 ? fd.append('allowed_tickets', this.allowed_tickets) : fd.append('validity', this.validity)
					//Faveo ke purvajon Bhagwaan iskliye tumhe kabhi maaf nhi krega
					this.package_pic.length===1?fd.append('package_pic', this.package_pic.shift()):fd.append('package_pic', this.package_pic)

					fd.append('kb_link', this.kb_link)
				
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/bill/package/store-update', fd,config).then(res => {

						this.loading = false;
						
						successHandler(res,'package');
						
						if(this.package_id === ''){
						
							this.redirect('/bill/package/inbox');
						
						}else {
							this.getInitialValues(this.package_id)
						}
					
					}).catch(err => {

						this.loading = false;
					
						errorHandler(err,'category');
					});
				}
			},

			onChange(value,name){

				this[name] = value;
			}
		},

		components : {
			
		'text-field': require('components/MiniComponent/FormField/TextField'),
		
		'number-field': require('components/MiniComponent/FormField/NumberField'),
		
		'radio-button':require('components/MiniComponent/FormField/RadioButton'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
		"custom-loader": require("components/MiniComponent/Loader"),

		'static-select':require('components/MiniComponent/FormField/StaticSelect'),

		'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),

		'status-switch':require('components/MiniComponent/FormField/Switch'),

		'time-field': require('components/MiniComponent/FormField/DayHourMinuteField'),

		'file-upload': require('components/MiniComponent/FormField/fileUpload.vue'),

		}
	};
</script>