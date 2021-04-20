<template>
	
	<div class="row">
		
		<div class="col-md-12">

			<alert componentName="edit_agent_profile"/>
		</div>

		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>
			
		</div>

		<div class="col-md-6" v-if="hasDataPopulated">

			<div class="card card-light ">
				
				<div class="card-header">
					
					<h3 class="card-title">Profile</h3>
						
				</div>
				
				<div class="card-body">
					
					<div class="text-center">
						
						<image-upload :label="lang('profile_pic')" :value="profile_pic" name="profile_pic" :onChange="onChange"
							:labelStyle="labelStyle" :labelCss="labelCss" buttonName="change">

						</image-upload>
					</div>

					<text-field :label="lang('first_name')" :value="first_name" type="text" name="first_name" 
						:onChange="onChange" :required="true">
								
					</text-field>

					<text-field :label="lang('last_name')" :value="last_name" type="text" name="last_name" 
						:onChange="onChange">
								
					</text-field>

					<dynamic-select v-if="types.length > 0" :label="lang('type')" :multiple="true" name="types" :required="false" 
						 apiEndpoint="/api/dependency/types" :value="types" :onChange="onChange" :clearable="types ? true : false"
						:disabled="true">

					</dynamic-select>

					<text-field :label="lang('email')" :value="email" type="text" name="email" :onChange="onChange" :disabled="true">
								
					</text-field>

					<dynamic-select v-if="location" :label="lang('location')" :multiple="false" name="location" 
						apiEndpoint="/api/dependency/locations" :value="location" :onChange="onChange" :clearable="location ? true : false"
						:disabled="true">

					</dynamic-select>

					<div class="row">
						
						<number-field :label="lang('ext')" :value="ext" name="ext" classname="col-sm-3"
							:onChange="onChange" type="number">
									
						</number-field>

						<number-field :label="lang('work_phone')" :value="phone_number" name="phone_number" classname="col-sm-9"
							:onChange="onChange" type="number">
									
						</number-field>

					</div>

					<phoneWithCountryCode id="mobile" name="mobile" :onChange="onChange" :value="mobile" :countryCode="country_code" 
						@countCode="getCountCode" :countryIso="iso" @countIso="getCountIso">
						
					</phoneWithCountryCode>


					<tiny-editor :value="agent_sign" type="text" :onChange="onChange" name="agent_sign" :label="lang('agent_sign')"
						classname="" :lang="'en'">
						
					</tiny-editor>

					<radio-button :options="radioOptions" :label="lang('for_auto_assign_accept_tickets')" name="not_accept_ticket" 
						:value="not_accept_ticket" :onChange="onChange" classname="form-group">
							
					</radio-button>

				</div>
				
				<div class="card-footer">
					
					<button class="btn btn-primary" @click="onSubmit()" :disabled="isDisabled">
						
						<i class="fas fa-sync"></i> {{lang('update')}}
					</button>
				</div>
			</div>
		</div>
		
		<div class="col-md-6" v-if="hasDataPopulated">
			
			<div class="card card-light ">
				
				<div class="card-header">
					
					<h3 class="card-title">Change password</h3> 
				</div>
				
				<div class="card-body">
				
					<text-field :label="lang('old_password')" :value="old_password" type="password" name="old_password" 
						:onChange="onChange" :required="true">
										
					</text-field>
					
					<text-field :label="lang('new_password')" :value="new_password" type="password" name="new_password" 
						:onChange="onChange" :required="true">
										
					</text-field>

					<text-field :label="lang('confirm_password')" :value="confirm_password" type="password" 
						name="confirm_password" :onChange="onChange" :required="true">
										
					</text-field>
				</div>
				
				<div class="card-footer">
					
					<button class="btn btn-primary" @click="onUpdatePassword()" :disabled="passDisabled">
						
						<i class="fas fa-sync"></i> {{lang('update')}}
					</button>
				</div>
			</div>

			<div class="card card-light ">
				
				<div class="card-header">
					
					<h3 class="card-title">{{lang('2fa_setup')}}</h3>
				</div>
				
				<div class="card-body">
					
					<div class="row">
						
						<div class="col-md-9">

							<span> 

								<img class="img-responsive img-circle img-sm" src="themes/default/common/images/authenticator.png" alt="A"
									id="auth_img">&nbsp;{{two_factor ? '2-Step Verification is ON since '+formattedTime(profileData.google2fa_activation_date)  : lang('authenticator_app')}}
							</span>
						</div>
						
						<div class="col-md-3">

							<button v-if="!two_factor" type="button" class="btn btn-primary float-right" @click="showModal = true">

								<i class="fas fa-toggle-on"></i> {{lang('turn_on')}}

							</button>

							<button v-if="two_factor" type="button" class="btn btn-danger float-right" @click="removeModal = true">

								<i class="fas fa-power-off"></i> {{lang('turn_off')}}

							</button>
						</div>
					</div>	
				</div>
			</div>

			<transition name="modal">
				
				<barcode-modal v-if="showModal" :onClose="onClose" :showModal="showModal">
					
				</barcode-modal>
			</transition>

			<transition name="modal">
				
				<remove-modal v-if="removeModal" :onClose="onClose" :showModal="removeModal" alertName="edit_agent_profile">
					
				</remove-modal>
			</transition>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { errorHandler, successHandler } from 'helpers/responseHandler'

	import { validateAgentProfileSettings } from "helpers/validator/agentProfileSettings.js"

	import { validatePasswordSettings } from "helpers/validator/passwordSettings.js"

	import { mapGetters } from 'vuex';

	export default {

		name : 'agent-profie-edit',

		description  : 'Agent Profile Edit Page',

		data(){

			return {

				loading : false,

				hasDataPopulated : false,

				isDisabled : false,

				passDisabled : false,

				first_name : '',

				last_name : '',

				email : '',

				country_code: 91,
				
				mobile: '',

				phone_number : '',

				ext : '',

				profile_pic : '',

				not_accept_ticket : 0,

 				types : [],

				agent_sign : '',

				location : '',

				radioOptions:[{name:'yes',value:0},{name:'no',value:1}],

				labelStyle : { display : 'none' },

				labelCss : { visibility : 'hidden', margin : 'auto'},

				old_password : '',

				new_password : '',

				confirm_password : '',

				profileData : '',

				two_factor : false,

				showModal : false,

				removeModal : false,

				iso : '',
			}
		},

		created() {

			window.eventHub.$on('updateEditData',this.getData);
		},

		beforeMount(){

			this.getData()

		},

		computed : {

			...mapGetters(['formattedDate','formattedTime'])
		},

		methods : {

			getData(){

				this.loading = true;

				axios.get('/api/profile/info').then(res=>{

					this.profileData = res.data.data;

					this.updateStatesWithData(res.data.data);

					this.loading = false;

					this.hasDataPopulated = true;

				}).catch(error=>{

					this.loading = false;

					this.hasDataPopulated = true;

				});
			},

			updateStatesWithData(data){

				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(data).map(key => {
					
					if (stateData.hasOwnProperty(key)) {
					
						self[key] = data[key];
					
					}

				});

				this.two_factor = data.is_2fa_enabled;

				this.mobile = this.mobile === 'Not available' ? '' : this.mobile;

				this.country_code = this.country_code === '' ? 91 : this.country_code;

			},

			onChange(value, name) {

				this[name] = value;
			},

			getCountCode(value){

				this.country_code = value;
			},

			getCountIso(value){
          	
          	this.iso = value;
        	},

			isValid(){
				
				const {errors, isValid} = validateAgentProfileSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

			isPasswordValid(){
				
				const {errors, isValid} = validatePasswordSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

			onSubmit(){

				if(this.isValid()){

					this.isDisabled=true;
					
					this.loading=true;
				
					var fd = new FormData();
					
					fd.append('first_name', this.first_name);
					
					fd.append('last_name', this.last_name);
					
					fd.append('country_code', this.country_code ? this.country_code : null);

					fd.append('iso', this.iso ? this.iso : null);
					
					fd.append('mobile', this.mobile);
					
					fd.append('ext', this.ext ? this.ext : '');
					
					fd.append('phone_number', this.phone_number ? this.phone_number : '');

					let pic_type = typeof this.profile_pic;

					fd.append('profile_pic', pic_type === 'object' ? this.profile_pic : null);

					fd.append('location', this.location ? this.location.id : '');

					fd.append('agent_sign', this.agent_sign ? this.agent_sign : '');

					fd.append('not_accept_ticket', this.not_accept_ticket);

					if(this.types !== ''){
						
						for(var i in this.types){
							
							fd.append('type['+i+']', this.types[i].id);
						}
					}
					
					fd.append('_method', "PATCH");
					
					axios.post('/agent-profile', fd).then(res=> {
						
						successHandler(res,'edit_agent_profile');

						this.isDisabled=false;
					
						this.loading=false;

						this.redirect('/profile');
							
					}).catch(error=>{

						this.loading=false;

						this.isDisabled=false;

						errorHandler(error,'edit_agent_profile')
					})
				}
			},

			onUpdatePassword(){

				var regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{6,20}$/;

				var message = regex.exec(this.new_password);

				if(this.isPasswordValid()){

					if(this.old_password !== this.new_password){

						if(message){

							if(this.new_password === this.confirm_password){

								this.passDisabled=true;
								
								this.loading=true;
								
								var fd = new FormData();;

								fd.append('old_password',this.old_password);
								
								fd.append('new_password',this.new_password);
								
								fd.append('confirm_password',this.confirm_password);

								fd.append('_method', "PATCH");

								axios.post('/agent-profile-password/'+this.profileData.id,fd).then(res=> {
									
									this.old_password = '';
									
									this.new_password = '';

									this.confirm_password = '';
									
									this.loading=false;

									this.passDisabled=false;
									
									successHandler(res,'edit_agent_profile');

								}).catch(error=>{

									this.loading=false;

									this.passDisabled=false;

									errorHandler(error,'edit_agent_profile')
								})
							}else {

								this.$store.dispatch('setValidationError', {'confirm_password' : 'Password does not match'})
							}
						} else {

							this.$store.dispatch('setValidationError', {'new_password' : 'Password should contain atleast one number, one uppercase, one lowercase and one special character'})
						}
					}else {

						this.$store.dispatch('setValidationError', {'new_password' : 'new password is same as old. Please choose a different password'})
					}
				} 
			},

			onClose(){
				
				this.showModal = false;

				this.removeModal = false; 

				this.$store.dispatch('unsetValidationError');
			},
		},

		components : {

			"text-field": require("components/MiniComponent/FormField/TextField"),

			"radio-button": require("components/MiniComponent/FormField/RadioButton"),

			"number-field": require("components/MiniComponent/FormField/NumberField"),

			'phoneWithCountryCode': require('components/MiniComponent/FormField/PhoneWithCountryCode.vue'),

			'dynamic-select' : require('components/MiniComponent/FormField/DynamicSelect'),

			'image-upload': require('components/MiniComponent/FormField/ImageUpload.vue'),

			'custom-loader' : require('components/MiniComponent/Loader'),

			'alert' : require('components/MiniComponent/Alert'),

			'barcode-modal' : require('./BarcodeModal'),

			'remove-modal' : require('./RemoveVerification'),
		}
	};
</script>

<style>
	#setup_fa{
		background: #eee !important;
		background-color: #eee !important;
	}
	#fa_switch{
		margin-top: 4px;
	}
	#auth_img{
		width: 25px!important;
		height: 25px!important;
	}
</style>