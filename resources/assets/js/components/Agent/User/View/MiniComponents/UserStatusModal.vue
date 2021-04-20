<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
	
		<div slot="title">

			<h4 class="modal-title">{{ title == 'email_verified' ? lang('email_verify') : lang('mobile-verify') }}</h4>
		</div>
		
		<div v-if="!loading" slot="fields" class="row">
				
			<text-field v-if="title === 'email_verified'" :label="lang('email')" :value="email" type="email" name="email"
				:onChange="onChange" classname="col-sm-12" :required="true">
					
			</text-field>

			<phoneWithCountryCode v-if="title === 'mobile_verified'" id="mobile" classname="col-sm-12" name="mobile"
				 :onChange="onChange" :value="mobile" :countryCode="country_code" @countCode="getCountCode" 
				 :required="true" :countryIso="iso" @countIso="getCountIso">
		      
		    </phoneWithCountryCode>	 			
		</div>

		<div v-if="loading" slot="fields" class="row">
			
			<loader :animation-duration="4000" :size="60"/>
		</div>

		<div slot="controls">

			<button  type="button" @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">

				<i class="fas fa-check"></i> {{lang('update_and_verify')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { validateUserStatusSettings } from "helpers/validator/userStatusRules.js"

	import axios from 'axios'

	export default {
		
		name : 'user-modal',

		description : 'User Modal component',

		props:{
	
			showModal:{type:Boolean,default:false},

			userData : { type : Object, default : ()=>{} },

			onClose:{type: Function},

			title : { type : String, default : ''},

		},

		data(){

			return {

				isDisabled:false,

				containerStyle:{ width:'600px' },

				mobile : this.userData.mobile === 'Not available' ? '' : this.userData.mobile,

				email : this.userData.email,

				country_code: this.userData.country_code === '' || this.userData.country_code === 0 ? 91 : 
											this.userData.country_code,

				loading:false,

				modal : this.title,

				data : {},

				iso : this.userData.iso
			}
		},

		methods:{

			getCountCode(value){

        this.country_code = value;
      },

      getCountIso(value){
          	
          	this.iso = value;
        	},

      onChange(value, name) {

	      this[name] = value;
	    },

	    isValid(){

				const {errors, isValid} = validateUserStatusSettings(this.$data);
				
				if(!isValid){
					
					return false
				}
				
				return true
			},

			onSubmit(){
				
				if(this.isValid()){

					this.loading = true
					
					this.isDisabled = true

					this.data['id'] = this.userData.id;

					if(this.modal === 'email_verified'){

						this.data['email'] = this.email;
					}

					if(this.modal === 'mobile_verified'){

						this.data['country_code'] = this.country_code;

						this.data['iso'] = this.iso;

						this.data['mobile'] = this.mobile;
					}

					axios.post('/manual-verify',this.data).then(res=>{

						window.eventHub.$emit('refreshUserData');
							
						successHandler(res,'user-view')
						
						this.loading = false;

						this.isDisabled = false;
							
						this.onClose();

					}).catch(err => {

						this.loading = false;
						
						this.isDisabled = false;

						errorHandler(err,'user-view');

						this.onClose();
					})
				}
			}
		},

	components:{

		'modal':require('components/Common/Modal.vue'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
		'loader':require('components/Client/Pages/ReusableComponents/Loader'),

		'text-field': require('components/MiniComponent/FormField/TextField'),

		'phoneWithCountryCode': require('components/MiniComponent/FormField/PhoneWithCountryCode.vue'),
	}
};
</script>