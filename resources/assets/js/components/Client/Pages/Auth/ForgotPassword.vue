<template>
	
		<div :class="{forgot_align : language === 'ar'}">

			<meta-component :dynamic_title="lang('password-email-title')" :dynamic_description="lang('password-email-description')" :layout="layout" >
				
			</meta-component>

			<div  id="content" class="site-content col-md-12">
			
				<alert componentName="password"/>

				<widget-component :layout="layout" :auth="auth"></widget-component>
		
				<form-helper>

					<h3 id="h3" slot="title">{{ lang(title) }}</h3>

					<div slot="fields" :class="{ align1 : language === 'ar'}">

						<text-field v-if="!loading" label="Email" :value="email_address" 
							type="email" name="email_address" :keyupListener="triggerEvent" :onChange="onChange" classname="" :required="true">
							
						</text-field>
					</div>
					<div v-if="!loading" slot="controls" class="row">
						
						<div class="col-xs-6 col-sm-6 col-md-8" :class="{align1 : language === 'ar'}">
						
							<router-link to="/auth/login" :style="linkStyle">{{ lang('i_know_my_password') }}</router-link>
						</div>
						
						<div class="col-xs-6 col-sm-6 col-md-4 float-right" :class="{left : language === 'ar'}">
						
							<button class="btn btn-custom btn-block btn-flat" @click="onSubmit()" :style="buttonStyle">
								{{ lang('send') }}

							</button>
						</div>
					</div>

					<div v-if="loading" slot="fields">
						
						<loader :animation-duration="4000" :color="layout.portal.client_header_color" :size="50"/>
					</div>
				</form-helper>
			</div>         
		</div>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { validateResetPasswordSettings } from "helpers/validator/passwordResetRules";

	import axios from 'axios'

	export default {

		name : 'forgot-password',

		description : 'Forgot password component',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data() {
			return {
			
				title:'forgot_password',
				
				loading:false,
				
		    email_address: "", 
				
				buttonStyle: {
					
					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color
				},

				linkStyle : { color : this.layout.portal.client_header_color },
				
				language : this.layout.language,

				isDisabled:false
			}
		},

		beforeMount(){

			if(!Array.isArray(this.auth.user_data)){
				this.$router.push({name:'Home'})
			}
		},

		methods: {

    	onChange(value, name) {

      	this[name] = value;
    	},

    	isValid() {

      	const { errors, isValid } = validateResetPasswordSettings(this.$data);

      	if (!isValid) {

        	return false;
      	}
      	return true;
    	},

    	onSubmit() {
      	
      	if (this.isValid()) {
      	
      		this.$Progress.start();
					
					this.isDisabled=true;
					
					this.loading=true;
					
					const data = 'email=' + this.email_address;
					
					axios.post('api/password/email',data).then(response =>{
					
						this.$Progress.finish();
						
						this.initialState();
						
						successHandler(response,'password');
						
						setTimeout(()=>{
							
							this.$router.push({ path:'/auth/login',name: 'Login'});
						},3000)
					
					}).catch(error=>{
					
						this.$Progress.fail();
					
						this.initialState();
						
						errorHandler(error,'password')
					})
	      }
	    },

	    initialState(){
	    		
	    	window.scrollTo(0, 0);
					
				this.isDisabled=false;
					
				this.loading=false;
	    },
				
			triggerEvent(event) {
				
				var key = event.which || event.keyCode;
					
				if (key === 13) {

					this.onSubmit()
				}
			}
		},

		components:{

			'form-helper': require('components/Client/Pages/ReusableComponents/FormHelper'),
			
			"text-field": require("components/MiniComponent/FormField/TextField"),

			'widget-component': require('components/Client/ClientPanelLayoutComponents/WidgetBoxComponent'),
			
			'alert' : require('components/MiniComponent/Alert'),
		}
	};
</script>

<style scoped>
	#h3 {
		margin-top: 0px;
		margin-bottom: 20px;
	}
	#wbox{
		margin-right:-5px !important;
	}
	.wid {
		margin-top: 2em !important;
		margin-bottom: 1.5em !important;
	}
	.forgot_align{
		direction: rtl;
	}
</style>