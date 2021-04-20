<template>
		
		<div id="content" class="site-content col-md-12">
				
			<div class="row">
			
				<div class="col-sm-3" :class="{float1 : language === 'ar'}">

        	<image-crop :value="profile_pic" name="profile_pic" :onChange="onChange" 
        		:classname="{float1 : language === 'ar'}" :language="language">
					
					</image-crop>
				</div>
				
				<div class="col-md-9" :class="{'edit_pro align1' : language === 'ar'}">
					
					<div class="row">
						
						<text-field :label="lang('first_name')" :value="first_name" type="text" name="first_name" 
							:onChange="onChange" classname="col-sm-6" :required="true" :inputStyle="inputStyle">
								
						</text-field>

						<text-field :label="lang('last_name')" :value="last_name" type="text" name="last_name" 
							:onChange="onChange" classname="col-sm-6" :inputStyle="inputStyle">
								
						</text-field>
					</div>

					<div class="row">
						
						<text-field :label="lang('email')" :value="email" type="text" name="email" 
							:onChange="onChange" classname="col-sm-6" :disabled="true" :inputStyle="inputStyle">
								
						</text-field>

						<phoneWithCountryCode id="mobile"  classname="col-sm-6" name="mobile" :language="language"
			        :onChange="onChange" :value="mobile" :countryCode="country_code" @countCode="getCountCode"
			        :inputStyle="inputStyle" :countryIso="iso" @countIso="getCountIso">
			      
			      </phoneWithCountryCode>
			    </div>

			    <div class="row">
						
						<number-field :label="lang('ext')" :value="ext" name="ext" classname="col-sm-2"
			        :onChange="onChange" type="number" :formStyle="inputStyle">
			            
			      </number-field>

			     	<number-field :label="lang('work_phone')" :value="phone_number" name="phone_number" classname="col-sm-4"
			        :onChange="onChange" type="number" :formStyle="inputStyle">
			            
	      		</number-field>

					</div>
				</div>
			</div>

			<div class="row" v-if="loading === true">

            <client-panel-loader :color="layout.portal.client_header_color" :size="60"></client-panel-loader>
        </div>

			<div>
				
				<hr/>
				
				<button @click="onUpdate()" class="btn btn-custom float-right" :class="{left : language === 'ar'}" :style="buttonStyle">
					
					<i class="fas fa-sync"> </i> {{ lang('update') }}
				</button>
			</div>
		</div>
</template>

<script>

	import { errorHandler, successHandler } from 'helpers/responseHandler'

	import { validateProfileSettings } from "helpers/validator/profileSettings.js"

	import axios from 'axios'

	export default{

		name : 'edit-profile',

		description : 'Profile edit component',

		props : {

			layout : { type : Object, default : ()=>{}},
			
			auth : { type : Object, default : ()=>{}},
		},

		data() {

			return {

				loading : true,

				first_name : '',

				last_name : '',

				email : '',

				country_code: 91,
     		
     		mobile: '',

     		phone_number : '',

     		ext : '',

     		profile_pic : '',

     		profile_pic_name : '',

     		buttonStyle : {
     			
     			borderColor : this.layout.portal.client_button_border_color,

     			backgroundColor : this.layout.portal.client_button_color
     		},

     		inputStyle : { borderColor : this.layout.portal.client_input_field_color },

     		selectedImage : '',

     		language : this.layout.language,

     		iso : ''
			}
		},

		watch : {

		},

		beforeMount(){

			this.$Progress.start();

			this.updateStatesWithData(this.auth.user_data)

		},

		methods : {

			updateStatesWithData(data){

				const self = this;
				
				const stateData = this.$data;
				
				Object.keys(data).map(key => {
					
					if (stateData.hasOwnProperty(key)) {
					
						self[key] = data[key];
					
					}

				});

				this.mobile = this.mobile === 'Not available' ? '' : this.mobile;

				this.country_code = this.country_code === '' ? 91 : this.country_code;

				this.loading = false

				this.$Progress.finish();
			},

			onChange(value, name) {

	      if(name === 'profile_pic'){

	      		this.profile_pic = value.image;

	      		this.selectedImage = value
	      } else {

	      	this[name] = value;
	      }
	    },

	    getCountCode(value){

        this.country_code = value;
      },

      getCountIso(value){
          	
          	this.iso = value;
        	},

      isValid(){
				
				const {errors, isValid} = validateProfileSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

      onUpdate(){

      	if(this.isValid()){

					this.isDisabled=true;
					
					this.loading=true;

      		this.$Progress.start();
				
					var fd = new FormData();
					
					fd.append('first_name', this.first_name);
					
					fd.append('last_name', this.last_name);
					
					fd.append('country_code', this.country_code ? this.country_code : 91);

					fd.append('iso', this.iso);
					
					fd.append('mobile', this.mobile);

					fd.append('ext', this.ext ? this.ext : '');
					
					fd.append('phone_number', this.phone_number ? this.phone_number : '');
					
					if(this.selectedImage){

						fd.append('profile_pic', this.selectedImage.file,this.selectedImage.name);
					}
					
					fd.append('_method', "PATCH");
					
					axios.post('api/profile/edit', fd).then(res=> {
						
						this.$Progress.finish();
						
						successHandler(res,'edit_profile');

						this.updateUserData()
							
					}).catch(error=>{

						this.$Progress.fail();

						this.loading=false;

						this.isDisabled=false;

						errorHandler(error,'edit_profile')
					})
      	}
      },

      updateUserData(){

      	this.$Progress.start();

      	axios.get('/api/get-auth-info').then(res=>{

      		this.loading=false;

					this.isDisabled=false;

					this.updateStatesWithData(res.data.data.user_data);

					window.eventHub.$emit('updateUserData',res.data.data.user_data)
					
					window.eventHub.$emit('updateProfileData',res.data.data)
      		
      		this.$Progress.finish();

      	}).catch(error=>{

      		this.loading=false;

					this.isDisabled=false;

					this.$Progress.fail();
      	})
      }
		},

		components : {

			"text-field": require("components/MiniComponent/FormField/TextField"),

			"radio-button": require("components/MiniComponent/FormField/RadioButton"),

			"number-field": require("components/MiniComponent/FormField/NumberField"),

			'phoneWithCountryCode': require('components/MiniComponent/FormField/PhoneWithCountryCode.vue'),

			'image-crop': require('components/Client/Pages/ReusableComponents/ImageCrop'),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		}
	};
</script>

<style scoped>
	
	#profile_tab {

		cursor: pointer;
	}

	#content {

		margin-top: 15px !important;
	}
	.edit_pro{
		float: left !important;
		direction: rtl;
	}
</style>