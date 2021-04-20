<template>

	<div>
		<div class="login100-form-avatar mb-4">
					<div class="logo_presidencia"></div>
				</div>

		<div v-if="!pageLoad" class="col-md-6 offset-md-3 form-helper">
		
			<div class="login-box" style="width:auto;" valign="center">

				<div class="form-border">
					
					<h3 class="login_title text-white" slot="title" >{{ lang('login_to_start_your_session') }}</h3>

					<form-with-captcha btnClass="login100-form-btn" 
						:btnStyle="buttonStyle" btnName="login" iconClass="" :formSubmit="onSubmit" page="login_page" 
						:componentName="componentName" btn_id="default-login-button">

						<div slot="fields" class="row">
			
							<div class="col-sm-12">
								
								<text-field :labelStyle="labelStyle" label="Username" :value="user_name" 
									type="text" name="user_name" :keyupListener="triggerEvent" :onChange="onChange" 
									placehold="Email/Username" classname="" :required="true" id="user_name">
								
								</text-field>

								<text-field :labelStyle="labelStyle" label="Password" :value="password" 
									type="password" name="password" :keyupListener="triggerEvent" :onChange="onChange" 
									placehold="Password" classname="" :required="true" id="password">
										
								</text-field>

								<div :class="[ 'pull-left  text-white',{float1 : locale === 'ar'}]" id="remember_me">
										
									<input type="checkbox" v-model="remember" name="remember"> {{ lang('remember') }}
								</div>
							</div>

							<div v-if="loading" id="login_loader">
								
								<custom-loader :duration="4000" :color="layout.portal.client_header_color" :size="60"/>
							</div>
						</div>

						<div class="form-group" slot="actions">

							<div id="default-login">

								<router-link to="/password/email" id="default-forgot-password" :style="linkStyle" 
									:class="[ 'text-white',{float1 : locale === 'ar'}]">{{ lang('forgot_password') }}?
								</router-link>
							</div>
								
							<div id="login-box">{{loginBoxVisible()}}</div>

							<button v-if="allow_register == 1" id="default-register-button"
								class="login100-form-btn red" @click="onRegister" :style="buttonStyle" :disabled="isDisabled">
								{{ lang('create_an_account') }}
							</button><br>

							<div v-if="!loading" v-for="provider in providerData.providers" :style="socialLoginBoxStyle">
								
								<ul class="list-unstyled" :class="{padd : locale === 'ar'}" style="width: 100%;">
							
									<li id="social">

										<button :class="'btn btn-block btn-social btn-flat btn-'+provider" @click="socialRedirect(provider)">
										
											<span :class="'fab fa-'+provider"></span> {{lang('sign_in_with')}} {{provider}}
										</button>
									</li>
								</ul>
							</div>
						</div>
					</form-with-captcha>
				</div>
			</div>
		</div>

		<div v-if="pageLoad">
				
			<custom-loader :duration="4000" :color="layout.portal.client_header_color" :size="60"/>
		</div>
	</div>
</template>

<script>

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { boolean } from 'helpers/extraLogics'

	import { validateLoginSettings } from "helpers/validator/loginRules";

	import axios from 'axios'

	import { mapGetters } from 'vuex'

	export default {

		name:'login-component',

		description:'Login fields component',

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},

		data () {

			return {
			
				isDisabled:false,

				base: this.auth.system_url,

				providerData:[],

				user_name: '',

				password: '',

				redirectUrl:'',

				buttonStyle: {
					
					borderColor : this.layout.portal.client_button_border_color,
					
					backgroundColor : this.layout.portal.client_button_color
				},

				linkStyle : { color : this.layout.portal.client_header_color },

				loads:'',

				loading:false,

				pageLoad : false,

				allow_register : this.layout.user_registration.status,

				locale : this.layout.language,

				redirectPath:'',

				componentName : 'page-login',

				labelStyle:{ display:'none' },

				remember: true,

				socialLoginBoxStyle: {}
			}
		},

		beforeMount() {

			this.$Progress.start();
			
			this.loading=true;

			this.pageLoad = true;
			
			axios.get('api/active-providers').then(response => {
			
				this.providerData = response.data.data;

				this.loading=false;

				this.pageLoad = false;
			
				this.$Progress.finish();

			}).catch(error=>{
				
				this.loading=false;

				this.pageLoad = false;
				
				this.$Progress.fail();
			});

			// listen to an event, whenever that is fired witha value, change the value of `loading` acccordingly
			// this gives an external event a power to change the loading value by emitting and event
			window.eventHub.$on("login-box-loader", (loading) => {
				this.loading = loading;
			});

		},

		methods : {

			/**
			 * For any external script to be able to inject some javascript on loginBox visiblity
			 * @return {undefined}
			 */
			loginBoxVisible(){
			  window.eventHub.$emit('login-box-mounted', this.providerData);
			},

			/**
			 * Redirects to register page
			 * @return {undefined}
			 */
			onRegister(){
				this.$router.push('/auth/register');
			},

			/**
			* populates the states corresponding to 'name' with 'value'
			* @param  {string} value
			* @param  {[type]} name
			* @return {void}
			*/
			onChange(value, name) {
				this[name] = value;
			},

			/**
			* checks if the given form is valid
			* @return {Boolean} true if form is valid, else false
			*/
			isValid() {
				const { errors, isValid } = validateLoginSettings(this.$data);
				
				return isValid;
			},

			socialRedirect(provider){

				window.location.href=this.base+'/social/login/redirect/' + provider;
			},

			/**
			 * sending ajax call for login
			 */
			onSubmit(key,value) {

				if (this.isValid()) {

					this.$Progress.start();
					
					this.isDisabled=true;

					this.loading=true;

					const params = { email: this.user_name, password: this.password, remember: this.remember}
					
					params[key] = value;
					// emitting event that login has been initialised so that plugins can respond to it
					// currently ldap plugin is using it to insert one extra parameter to the request
					// as ldap = true
					window.eventHub.$emit('login-data-submitting', params);

					axios.post('login', params).then(
						res=>{
							
							this.redirectUrl=res.data.data.redirect_url;
					
							this.isDisabled=true;

							this.isLoginDisabled = true;
					
							this.loading=false;

							this.redirectPath = localStorage.getItem('redirectPath');

							// redirect to 2fa page if redirect url is 
							if(this.redirectUrl == 'verify-2fa'){
								this.$Progress.finish();
								this.$router.push({ path:'/verify-2fa',name: 'Verify2FA', params : { pp:res.data.data.PPAuth, remember: this.remember}});
							}

							else if(this.redirectPath) {
								window.location.href=this.base+'/mytickets';
							}
					
							else {
					
								if(this.redirectUrl == '/'){
							
									window.location.href=this.base;
								
								}else if(this.redirectUrl == 'verify-email'){
								
									this.$store.dispatch('setEmail', res.data.data.email);
									
									this.$Progress.finish();
									
									this.$router.push({ path:'/verify-email',name: 'EmailVerify'});

								} else{
								
								this.redirect('/'+this.redirectUrl);
							}
						}

						window.eventHub.$emit('login-success', res);

						}).catch(error=>{

							this.isDisabled=false;

							this.loading=false;
							
							errorHandler(error,this.componentName);
							
							this.$Progress.fail();

							if(error.response.data.message['g-recaptcha-response']) {

								this.$store.dispatch('setAlert',{
									type:'danger',message:'Invalid ReCaptcha', component_name : this.componentName
								})
							}

							window.eventHub.$emit('login-failure', error);
					})
				}
			},

			/**
			* calling validateForm method when press enter while filling login credentials in input fields
			* @return {Void}
			*/
			triggerEvent(event) {
				var key = event.which || event.keyCode;
				if (key === 13) { // 13 is enter

					window.eventHub.$emit('logging-in-with-enter-key');

					// if default button is visble then press it
					let defaultLogin = document.getElementById('default-login-button');
					if(defaultLogin.style.display != 'none'){
						defaultLogin.click();
					}
				}
			},
		},

		components:{

			"text-field": require("components/MiniComponent/FormField/TextField"),

			"form-with-captcha": require("components/Common/Form/FormWithCaptcha.vue"),

			'custom-loader': require("components/MiniComponent/Loader"),
		},
	};
</script>

<style scoped>

	#login_loader{
		margin: auto !important;
		margin-bottom: 20px !important;
	}

	#default-login-button{
		margin-top: 5px;
		margin-bottom: 3px;
	}

	#batan{
		color: #fff !important;
		background-color: #009aba !important;
		border-color: #00c0ef !important;
	}

	#remember_me {
		margin-bottom: 10px;
	}

	#default-register-button{
		margin-top: 20px;
	}

	#default-login{
		text-align : left;
		margin-bottom: 10px;
	}
	
	.log_nav_align{
		direction: rtl;
	}

	.social_log{
		line-height: 5px;
	}

	.form-helper {
		margin-bottom: 50px !important;
		display: inline-block;
	}

	.login_title {
		margin-top:0px;margin-bottom: 21px;text-align: center;
	}
</style>