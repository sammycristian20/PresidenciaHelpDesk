<template>
	
	<transition name="page" mode="out-in">
	
		<div :class="{fa_align : language === 'ar'}">

			<div  id="content" class="site-content col-md-12 " >
			
				<alert componentName="2fa"/>
		
				<form-helper>

					<h3 id="head3" slot="title">{{ lang('2_factor_auth') }}</h3>

					<text-field slot="fields" :label="lang('enter_code')" :value="otp" 
						type="text" name="otp" :onChange="onChange" classname="" :required="true">
							
					</text-field>

					<p slot="fields"> 

						<i class="fa fa-mobile-phone" style="font-size:18px"></i> {{lang('2fa-message')}}
					</p>

					<div slot="controls" class="row">
						
						<div class="col-sm-4 offset-sm-4" :class="{left : language === 'ar'}">
						
							<button class="btn btn-custom btn-block btn-flat" @click="onSubmit()" :style="buttonStyle" :disabled="otp ? false : true">
								<i class="fa fa-check"> </i> {{ lang('verify') }}

							</button>
						</div>
					</div>

					<div v-if="loading" slot="fields">
						
						<custom-loader :animation-duration="4000" :color="layout.portal.client_header_color" :size="50"/></custom-loader>
					</div>
				</form-helper>
			</div>         
		</div>
	</transition>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {

		name : 'verify-2fa',

		description : 'Two-factor Authentication component',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}},

			pp : { type : Object | String, default : ''},

			remember : { type : Boolean, default : false}
		},

		data() {
			return {
				
				loading:false,
				
		    otp: "", 
				
				buttonStyle: {
					
					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color
				},
				
				language : this.layout.language,

				isDisabled:false
			}
		},

		beforeMount(){

			if(!Array.isArray(this.auth.user_data)){
		
				this.$router.push({name:'Home'})
			
			} else if(!this.pp){
		
				this.$router.push({name:'Login'})
			}
		},

		methods: {

    	onChange(value, name) {

      	this[name] = value;
    	},

    	onSubmit() {
      	
      	this.$Progress.start();
					
				this.isDisabled=true;
					
				this.loading=true;
					
				const data = {};
				
				data['totp'] = this.otp;

				data['PPAuth'] = this.pp ? this.pp : '';

				data['remember'] = this.remember;

				axios.post('/2fa/loginValidate',data).then(response =>{
					
					this.$Progress.finish();
						
					this.isDisabled=false;
					
					this.loading=false;

					this.redirectUrl=response.data.data.redirect_url;
						
					this.redirectPath = localStorage.getItem('redirectPath');
				
					if(this.redirectPath){

						const base = this.$store.getters.getLayoutData.system.url;
				
						window.location.href = base + '/mytickets';
						
					} else {
				
						if(this.redirectUrl == '/'){
						
							window.location.href=this.auth.system_url;
						
						} else{
							
							this.redirect(this.redirectUrl);
						}
					}

				}).catch(error=>{
					
					if(error.response.status === 422){

						setTimeout(()=>{

							this.$router.push({name:'Login'})
						},2000);
					} else {

						this.$store.dispatch('setValidationError', {'otp' : 'Wrong code. Try again'})
					}

					this.$Progress.fail();
					
					this.isDisabled=false;
					
					this.loading=false;
						
					errorHandler(error,'2fa');

					
				})
	    }
	  },

		components:{

			'form-helper': require('components/Client/Pages/ReusableComponents/FormHelper'),
			
			"text-field": require("components/MiniComponent/FormField/TextField"),
			
			'alert' : require('components/MiniComponent/Alert'),

			'custom-loader' : require('components/MiniComponent/Loader'),
		}
	};
</script>

<style scoped>
	
	.fa_align{
		direction: rtl;
	}

	#head3{
		margin-top: 0px !important;
	}
</style>