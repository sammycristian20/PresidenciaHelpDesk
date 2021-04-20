<template>
	
		<div id="content" class="site-content col-md-12" :class="{'edit_pass align1' : language === 'ar'}">

			<div class="row">
				<text-field :label="lang('old_password')" :value="old_password" type="password" name="old_password" 
					:onChange="onChange" classname="col-sm-4" :required="true" :inputStyle="inputStyle">
									
				</text-field>
				
				<text-field :label="lang('new_password')" :value="new_password" type="password" name="new_password" 
					:onChange="onChange" classname="col-sm-4" :required="true" :inputStyle="inputStyle">
									
				</text-field>

				<text-field :label="lang('confirm_password')" :value="confirm_password" type="password" 
					name="confirm_password" :onChange="onChange" classname="col-sm-4" :required="true" :inputStyle="inputStyle">
									
				</text-field>
			</div>

			<div class="row" v-if="loading === true">

        <client-panel-loader :color="layout.portal.client_header_color" :size="60"></client-panel-loader>
      </div>

			<div>
				
				<hr/>
				
				<button @click="onUpdate()" class="btn btn-custom float-right" :class="{left : language === 'ar'}" 
					:style="buttonStyle">
					
					<i class="fas fa-sync"> </i> {{ lang('update') }}
				</button>
			</div>
		</div>
</template>

<script>
	
	import { errorHandler, successHandler } from 'helpers/responseHandler'

	import { validatePasswordSettings } from "helpers/validator/passwordSettings.js"

	export default{

		name : 'change-password',

		description : 'Channge password component',

		props : {

			layout : { type : Object, default : ()=>{}},
			
			auth : { type : Object, default : ()=>{}},
		},

		data() {

			return {

				old_password : '',

				new_password : '',

				confirm_password : '',

				loading : false,

				isDisabled : false,

				buttonStyle : {

					borderColor : this.layout.portal.client_button_border_color,

					backgroundColor : this.layout.portal.client_button_color
				},

				inputStyle : { borderColor : this.layout.portal.client_input_field_color },

				language : this.layout.language
			}
		},

		methods : {

			onChange(value, name) {
	      
	      this[name] = value;
	    },

	    isValid(){
				
				const {errors, isValid} = validatePasswordSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

			onUpdate(){

      	if(this.isValid()){

      		if(this.old_password !== this.new_password){

      			if(this.new_password === this.confirm_password){

							this.isDisabled=true;
							
							this.loading=true;

		      		this.$Progress.start();
							
							const data={};

							data['old_password']=this.old_password;
							
							data['new_password']=this.new_password;
							
							data['confirm_password']=this.confirm_password;

							axios.post('api/password/change',data).then(res=> {
								
								this.$Progress.finish();
								
								this.loading=false;

								this.isDisabled=false;
								
								successHandler(res,'edit_profile');

								this.$store.dispatch('deleteUser')

								this.$store.dispatch('updateUser')

							}).catch(error=>{

								this.$Progress.fail();

								this.loading=false;

								this.isDisabled=false;

								errorHandler(error,'edit_profile')
							})
		      	}else {

		      		this.$store.dispatch('setValidationError', {'confirm_password' : 'Password does not match'})
		      	}
      		}else {

		      	this.$store.dispatch('setValidationError', {'new_password' : 'new password is same as old. Please choose a different password'})
		      }
      	} 
      }
		},

		components : {

			"text-field": require("components/MiniComponent/FormField/TextField"),

			'client-panel-loader' : require('components/Client/ClientPanelLayoutComponents/ReusableComponents/Loader.vue'),
		}
	};
</script>

<style>
	
	#profile_tab {

		cursor: pointer;
	}

	#content {

		margin-top: 15px !important;
	}

	.edit_pass {
		direction : rtl;
	}
</style>