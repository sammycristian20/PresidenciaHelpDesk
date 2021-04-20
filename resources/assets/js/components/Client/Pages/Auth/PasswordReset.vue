<template>

	<div :class="{reset_align : language === 'ar'}">

		<meta-component :dynamic_title="lang('password-reset-title')" :dynamic_description="lang('password-reset-description')" :layout="layout" >
				
		</meta-component>

		<div v-if="loading === true">

      <client-panel-loader :size="60"></client-panel-loader>
    </div>

		<div id="content" class="site-content col-sm-12">
			
			<alert componentName="reset"/>

			<widget-component :layout="layout" :auth="auth"></widget-component>

			<form-helper>
				
				<h3 id="h3" slot="title">{{ lang('reset_password') }}</h3>

				<div slot="fields" :class="{ align1 : language === 'ar'}">

					<text-field :label="lang('new_password')" :value="password" type="password" name="password" 
						:onChange="onChange" :required="true" :keyupListener="triggerEvent">
													
					</text-field>

					<text-field :label="lang('confirm_password')" :value="repeat" type="password" 
						name="repeat" :onChange="onChange" :required="true" :keyupListener="triggerEvent">
													
					</text-field>
				</div>

				<div slot="controls">
						
					<button class="btn btn-custom btn-block btn-flat" @click="onSubmit()" :style="buttonStyle" :disabled="loading">
						
						<i class="fas fa-sync"> </i> {{ lang('reset_password') }}
					</button>
				</div>
			</form-helper>
		</div>
	</div>
</template>

<script>

	import { errorHandler, successHandler } from 'helpers/responseHandler'

	import { validateResetPasswordSettings } from "helpers/validator/resetPasswordRules.js"
	
	export default {

		props : {

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}}
		},
		
		data() {
			
			return {
				
				token:'',
				
				path:'',
				
				password: '',
				
				repeat:'',
				
				user:'',
				
				userEmail:'',

				loading : true,

				buttonStyle : {

					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color
				},

				language : this.layout.language,
			}
		},

		beforeMount() {

			if(!Array.isArray(this.auth.user_data)){
			
				this.$router.push({name:'Home'})
			}
		},
	
		mounted(){

			this.userEmail = location.search.split('=');
			
			this.user = decodeURIComponent(this.userEmail[this.userEmail.length-1]);

			this.loading = false;
		},

		methods: {
			
			onChange(value, name) {
	      
	      this[name] = value;
	    },

	    isValid(){
				
				const {errors, isValid} = validateResetPasswordSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

			triggerEvent(event) {
				
				var key = event.which || event.keyCode;
					
				if (key === 13) {

					this.onSubmit()
				}
			},

			onSubmit() {
				
				if(this.isValid()){

					if(this.password === this.repeat){

						this.$Progress.start();

						this.loading = true;
							
						this.path= location.pathname.split('/');
							
						this.token = this.path[this.path.length-1];
							
						const data = {token : this.token, password : this.repeat}
							
						axios.post('api/password/reset',data).then(res=>{
							
							this.loading = false;

							successHandler(res,'reset');

							this.$Progress.finish();

							setTimeout(()=>{
							
								this.$router.push({ path:'/auth/login',name: 'Login'});
							},3000)

						}).catch(error=>{

							errorHandler(error,'reset');

							this.$Progress.fail();

							this.loading = false;
						})
					} else {

						this.$store.dispatch('setValidationError', {'repeat' : 'Password does not match'})
					}
				}
			}
		},

		components : {

			"text-field": require("components/MiniComponent/FormField/TextField"),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),

			'alert' : require('components/MiniComponent/Alert'),

			'widget-component': require('components/Client/ClientPanelLayoutComponents/WidgetBoxComponent'),

			'form-helper': require('components/Client/Pages/ReusableComponents/FormHelper'),
		}
	};
</script>

<style scoped>
		.reset_align{
			direction: rtl;
		}
</style>