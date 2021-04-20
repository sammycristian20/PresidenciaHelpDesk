<template>
	<div>
		<div>
			
			<alert componentName="company-settings"/>
		</div>

		<div v-if="loading">
			
			<custom-loader :loadingSpeed="4000"></custom-loader>
		</div>
		
		<div class="card card-light" v-if="hasDataPopulated">
			
			<div class="card-header">
				
				<h3 class="card-title">{{lang('company_settings')}}</h3>
			</div>

			<div class="card-body">
				
				<div class="row">
					
					<text-field :label="lang('name')" type="text" :value="company_name" name="company_name"
						class="col-sm-4" :required="true" :onChange="onChange">
					</text-field>

					<number-field :label="lang('phone')" :value="phone" name="phone" classname="col-sm-4"
						:onChange="onChange" type="number">
												
					</number-field>

					<text-field :label="lang('website')" type="text" name="website" :value="website"
						classname="col-sm-4" :onChange="onChange">
					</text-field>
				
				</div>

				<div class="row">
					
					<tiny-editor :value="address" type="text" :onChange="onChange" name="address" :label="lang('address')"
						classname="col-sm-12" :required="false" :lang="'en'">
						
					</tiny-editor>
				</div>

				<div class="row">
					
					<div for="Logo and Favicon" class="col-sm-12">
						
						<div class="card card-light">
					
							<div class="card-header">
					
								<h3 class="card-title">{{lang('agent_panel_color_configuartion')}}</h3>
							</div>

							<div class="card-body">
								
								<div class="row">
									
									<static-select :label="lang('admin_header_color')" :elements="header_colors"
											name="admin_header_color" :value="admin_header_color" 
											classname="col-sm-6" :onChange="onChange" :required="true">
									</static-select>

									<static-select :label="lang('agent_header_color')" :elements="header_colors"
											name="agent_header_color" :value="agent_header_color" 
											classname="col-sm-6" :onChange="onChange" :required="true">
									</static-select>
								</div>
							</div>
						</div>
						
						<div class="card card-light">
					
							<div class="card-header">
					
								<h3 class="card-title">{{lang('client_panel_configuartion')}}

									<tool-tip :message="lang('color_configuartion_tooltip')" size="medium"></tool-tip>
								</h3>
							</div>

							<div class="card-body">

								<div class="row">
									
									<div class="col-sm-2">
										<label for="Address"> {{ lang('client_header_color') }} </label>
									
										<label class="label_align">
											<input class="checkbox_align" type="checkbox" name="defaultheader" v-model="header_default">{{lang('use_default')}}
										</label>

										<color-picker  v-if="!header_default" :color="client_header_color" v-model="client_header_color" />
									</div>

									<div class="col-sm-2">
										<label for="Address"> {{ lang('client_button_color') }} </label>
										
										<label class="label_align">
											<input class="checkbox_align" type="checkbox" name="defaultbutton" v-model="button_default">{{lang('use_default')}}
										</label>

										<color-picker  v-if="!button_default" :color="client_button_color" v-model="client_button_color" />
									</div>

									<div class="col-sm-2">
										<label for="Address"> {{ lang('client_button_border_color') }} </label>
										
										<label class="label_align">
											<input class="checkbox_align" type="checkbox" name="defaultborder" v-model="border_default">{{lang('use_default')}}
										</label>

										<color-picker  v-if="!border_default" :color="client_button_border_color" v-model="client_button_border_color" />
									</div>

									<div class="col-sm-2">
										<label for="Address"> {{ lang('client_input_field_color') }} </label>
										
										<label class="label_align">
											<input class="checkbox_align" type="checkbox" name="defaultinput" v-model="input_default">{{lang('use_default')}}
										</label>

										<color-picker v-if="!input_default" :color="client_input_field_color" v-model="client_input_field_color" />
									</div>

									<div class="col-sm-2">
						
										<label for="status">{{lang('show_breadcrumbs')}}</label><br>
										
										<status-switch name="is_enabled_breadcrumb" :value="is_enabled_breadcrumb" :onChange="onChange" classname="float-left" 
											:bold="true">
										
										</status-switch>   
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					
					<div for="Logo and Favicon" class="col-sm-12">
					
						<div class="card card-light">
					
							<div class="card-header">
					
								<h3 class="card-title">{{lang('logo_and_favicon')}}

									<tool-tip :message="lang('logo_icon_config')" size="medium"></tool-tip>
								</h3>
							</div>

							<div class="card-body">

								<div class="row">

									<label class="label_align col-md-4 text-center">
										<input class="checkbox_align" type="checkbox" name="defaulticon" v-model="defaulticon">{{lang('use_default')}}
									</label>

									<label class="label_align col-md-4 text-center">
										<input class="checkbox_align" type="checkbox" name="defaultlogo" v-model="defaultlogo">{{lang('use_default')}}
									</label>

									<label class="label_align col-md-4 text-center">
										<input class="checkbox_align" type="checkbox" name="uselogo" v-model="uselogo">{{lang('use_logo')}}
									</label>
								</div>

								<div class="row">
									
									<image-upload :label="lang('favicon')" :labelStyle="logoStyle" :value="icon" 
										name="icon" :onChange="onChange" btnName="change_icon"
										classname="col-sm-4 text-center" :is_default="defaulticon">
									</image-upload>

									<image-upload :label="lang('admin_logo')" :labelStyle="logoStyle" 
										:value="logo"
										name="logo" :onChange="onChange" 
										classname="col-sm-4 text-center" :is_default="defaultlogo">
									</image-upload>

									<image-upload :label="lang('client_logo')" :labelStyle="logoStyle" :value="clientlogo" 
										name="clientlogo" :onChange="onChange" 
										classname="col-sm-4 text-center">
									</image-upload>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="card-footer">
				<button id="submit_btn" type="button" v-on:click="onSubmit()" :disabled="loading" class="btn btn-primary">
									<span class="fas fa-save"></span>&nbsp;{{lang('save')}}
							</button>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import { mapGetters } from 'vuex'
	
	import { companySettingsValidation } from 'helpers/validator/companySettingRules.js'

	import {boolean} from 'helpers/extraLogics'

	export default {

		name : 'company-settings',

		description : 'Company Settings page',

		props : {

		},

		data:()=>({

			loading : true,

			hasDataPopulated : false,

			company_name : "",

			uselogo : false,

			website : '',

			phone : '',

			address : '',

			admin_header_color : '', 

			agent_header_color : '', 

			header_colors : [
				{ id:'skin-blue', name:'Blue'},
				{ id:'skin-black', name:'Black'},
				{ id:'skin-green', name:'Green'},
				{ id:'skin-purple', name:'Purple'},
				{ id:'skin-red', name:'Red'},
				{ id:'skin-yellow', name:'Yellow'},
			],

			client_header_color: '',

			client_button_color : '',

			client_button_border_color : '',

			client_input_field_color : '',

			icon : '',

			logo : '',

			clientlogo : '',

			logoStyle : { visibility : 'hidden' },

			radioOptions:[{name:'yes',value:1},{name:'no',value:0}],

			defaulticon : 0,

			defaultlogo : 0,

			header_default : true,

			input_default : true,

			button_default : true,

			border_default : true,	

			is_enabled_breadcrumb : false
		}),

		watch : {

			defaultlogo(newValue,oldValue){
				
				this.defaultlogo = boolean(newValue)

				return newValue
			},

			defaulticon(newValue,oldValue){
				
				this.defaulticon = boolean(newValue)

				return newValue
			}
		},

		beforeMount(){

			this.getInitialValues()
		},

		created(){

			this.defaulticon = boolean(this.defaulticon)

			this.defaultlogo = boolean(this.defaulticon)
		},

		methods :{

			getInitialValues(){

				this.loading = true

				axios.get('/getCompanyDetails').then(res=>{

					this.loading = false

					this.hasDataPopulated = true

					this.updatesStateWithData(res.data.message)

				}).catch(error=>{

					this.loading = false;

					this.hasDataPopulated = true;
				})
			},

			updatesStateWithData(company){

				const self = this

				const stateData = this.$data

				Object.keys(company).map(key=>{

					if(stateData.hasOwnProperty(key)){

						self[key] = company[key];
					}
				})
				
				this.border_default = this.colorMethod('client_button_border_color',this.client_button_border_color,'#00c0ef')

				this.button_default = this.colorMethod('client_button_color',this.client_button_color,'#009aba')

				this.header_default = this.colorMethod('client_header_color',this.client_header_color,'#009aba')

				this.input_default = this.colorMethod('client_input_field_color',this.client_input_field_color,'#d2d6de')
			},

			colorMethod(key,value,def){

				this[key] = value;

				return value === def ? true : false
			},

			isValid(){

				const { errors , isValid } = companySettingsValidation(this.$data)

				if(!isValid){

					return false
				}
				return true
			},

			validUrl(){

				if(this.website){

					let regex = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/;

					if(this.website.match(regex)) {  return true } 

					else {
			    	
			    	this.$store.dispatch('setValidationError',{website: "Invalid URL"})	
			    }
			    
				} else {

					return true
				}
			},

			onChange(value,name){

				this[name] = value
			},

			onSubmit(){
				
				if(this.isValid() && this.validUrl()){
					
					this.loading = true 

					var fd = new FormData();
					
					fd.append('company_name', this.company_name)

					fd.append('address', this.address)

					fd.append('phone', this.phone)

					fd.append('website', this.website)

					fd.append('admin_header_color', this.admin_header_color)

					fd.append('agent_header_color', this.agent_header_color)

					fd.append('is_enabled_breadcrumb',this.is_enabled_breadcrumb ? 1 : 0)

					if(!this.defaulticon){

						fd.append('icon', this.icon)
					}

					if(!this.defaultlogo){

						fd.append('logo_admin_agent', this.logo)
					}

					fd.append('logo', this.clientlogo === '' ? null : this.clientlogo)

					fd.append('uselogo', this.uselogo === true ? 1 : 0)

					fd.append('client_header_color',this.header_default ? '#009aba' : this.client_header_color)
					
					fd.append('client_button_color', this.button_default ? '#009aba' : this.client_button_color)

					fd.append('client_button_border_color', this.border_default ? '#00c0ef' : this.client_button_border_color)

					fd.append('client_input_field_color', this.input_default ? '#d2d6de' : this.client_input_field_color)
				
					const config = { headers: { 'Content-Type': 'multipart/form-data' } }

					axios.post('/postcompany', fd,config).then(res => {

						this.loading = false
						
						this.hasDataPopulated = true

						successHandler(res,'company-settings')

						this.redirect('/getcompany')

					}).catch(err => {
						
						this.loading = false
						
						this.hasDataPopulated = true

						errorHandler(err,'company-settings')
					});
				}
			}
		},

		components :{

			'text-field' : require('components/MiniComponent/FormField/TextField.vue'),

			"number-field": require("components/MiniComponent/FormField/NumberField"),

			'static-select': require('components/MiniComponent/FormField/StaticSelect.vue'),

			'check-box': require('components/MiniComponent/FormField/CheckBoxComponent'),

			'alert': require("components/MiniComponent/Alert"),
					
			'custom-loader': require("components/MiniComponent/Loader"),

			"tool-tip": require("components/MiniComponent/ToolTip"),

			'color-picker': require('components/MiniComponent/FormField/ColorPicker.vue'),

			'image-upload': require('components/MiniComponent/FormField/ImageUpload.vue'),

			'radio-button':require('components/MiniComponent/FormField/RadioButton'),

			'status-switch':require('components/MiniComponent/FormField/Switch'),
		}
	};
</script>

<style type="text/css" scoped>

	.label_align {
		display: block; padding-left: 15px; text-indent: -15px; font-weight: 500 !important; padding-top: 6px;
	}
	.checkbox_align {
		width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
	}
</style>