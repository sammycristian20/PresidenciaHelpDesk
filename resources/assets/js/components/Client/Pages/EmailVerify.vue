<template>
	
	<div>

		<alert componentName="email"/>

		<div v-if="getVerifyEmail" :class="{verify_align : lang_locale === 'ar'}">

			<meta-component :dynamic_title="lang('verify-email-title')" :layout="layout"
				:dynamic_description="lang('verify-email-description')" >
				
			</meta-component>

			<div id="content" class="site-content col-md-12 " >
		
				<alert componentName="updateEmail"/>
		
				<widget-component :layout="layout" :auth="auth"></widget-component>
		
				<form-helper v-if="!updateEmail">
		
					<h4 v-if="!loading" slot="title" class="box-title" align="center"><p>Hello!</p>
		
						<span id="message-resend" style="font-size: .8em; display: none;">
		
							Please wait we are sending an activation link to sakthi.m***@gmail.com
						</span>
		
						<span id="message" style="font-size: .8em">
		
							We have sent an activation link to {{email_address}}. Please check your mailbox and click on the link to activate your account then try again.
						</span>
					</h4>
		
					<div v-if="!loading" slot="controls" align="center">
		
						<button id="resend" class="btn btn-custom btn-block btn-flat" @click="resendLink" :style="buttonStyle">
						{{lang('resend_activation_link') }}</button>					
					
						<p id="or">or</p>
					
						<a id="update_email" :style="linkStyle" href="javascript:void(0)" @click="showField()">
						{{ lang('update-email-address') }}</a>
					</div>
			
					<div v-if="loading" slot="fields">
				
						<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="50"/>
					</div>
				</form-helper>

				<!-- =============================================== -->

				<form-helper v-if="updateEmail">
				
					<h3 id="h3" slot="title">{{ lang(title) }}
					</h3>
					<p v-if="!loading" slot="title">You must provide a distinct email address and your current password to verify this update request.</p>
					<div slot="fields" :class="{align1 : lang_locale === 'ar'}">
						<text-field v-if="!loading" label="New Email Address" :value="email_address" type="email" name="email_address" :onChange="onChange" classname="" :required="true"></text-field>

						<text-field v-if="!loading" label="Current Password" :value="password" type="password" name="password" :keyupListener="triggerEvent" :onChange="onChange" classname="" :required="true"></text-field>
					</div>
					
					<div v-if="!loading" slot="controls" class="row">
				
						<div class="col-xs-6 col-sm-6 col-md-8" :class="{align1 : lang_locale === 'ar'}">
				
							<router-link to="/auth/login" :style="linkStyle">{{ lang('account_activated') }}</router-link>
						</div>
				
						<div class="col-xs-6 col-sm-6 col-md-4 pull-right" :class="{left : lang_locale === 'ar'}">
				
							<button id="update_btn" class="btn btn-custom btn-block btn-flat" @click="onSubmit" :style="buttonStyle">{{ lang('update') }}</button>
						</div>
					</div>

					<div v-if="loading" slot="fields">
				
						<loader :animation-duration="4000" :color="layout.portal.client_header_color" :size="50"/>
					</div>
				</form-helper>
			</div>         
		</div>
	</div>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { validateResetPasswordSettings } from "helpers/validator/passwordResetRules";

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {

		name : 'verify-email',

		description : 'Email verification Component',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data() {
			return {

				loading:false,
				
				buttonStyle: {
				
					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color
				},
				
				linkStyle : {
					color : this.layout.portal.client_header_color 
				},

				lang_locale : this.layout.language,

				isDisabled:false,

				email_address : '',

				password : '',

				updateEmail : false,

				title : 'Update Email'
			}
		},

		computed:{

			...mapGetters(['getVerifyEmail'])
		},

		beforeMount(){

			this.email_address = this.getVerifyEmail;

			if(!this.email_address){

				this.$store.dispatch('setAlert',{type:'danger',message:'Your account does not have an email address. Contact admin.', component_name : 'email'});

				setTimeout(()=>{

					this.$router.push('/auth/login/');
				},3000)
			}
		},

		methods: {
			
	    onChange(value, name) {
	      
	      this[name] = value;
	    },

			showField(){
				this.updateEmail = true
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
					
					const data = { oldEmail: this.getVerifyEmail, email_address: this.email_address, password: this.password }
					
					axios.post('api/update-email-verification',data).then(response =>{
					
						this.$Progress.finish();
						
						this.initialState();
						
						successHandler(response,'updateEmail');
						
						setTimeout(()=>{
							
							this.$router.push({ path:'/auth/login',name: 'Login'});
						},4000)
					}).catch(error=>{
						
					this.$Progress.fail();
						
					this.initialState();
						
					errorHandler(error,'updateEmail')
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
					
				if (key === 13) { // 13 is enter
					this.onSubmit()
				}
			},

			resendLink(){
				
				this.$Progress.start();
					
				this.isDisabled=true;
					
				this.loading=true;
					
				const data = { email: this.getVerifyEmail }
					
				axios.post('api/send-email-verification-link',data).then(response =>{
						
					this.$Progress.finish();
						
					this.initialState();
						
					successHandler(response,'updateEmail');
						
					setTimeout(()=>{
							
						this.$router.push({ path:'/auth/login',name: 'Login'});
					},4000)
				}).catch(error=>{
					
					this.$Progress.fail();
						
					this.initialState();
						
					errorHandler(error,'updateEmail')
				})
			}
		},

		components:{

			'form-helper': require('./ReusableComponents/FormHelper'),

			'widget-component': require('components/Client/ClientPanelLayoutComponents/WidgetBoxComponent'),
				
			'alert' : require('components/MiniComponent/Alert'),

			"text-field": require("components/MiniComponent/FormField/TextField"),
				
		},
	};
</script>
<style scoped>
#h3 {
	margin-top: 0px;
	margin-bottom: 20px;
}
#or{
	margin :auto !important;
}
.verify_align{
		direction: rtl;
	}
	#message{
		word-break: break-word;
	}
</style>