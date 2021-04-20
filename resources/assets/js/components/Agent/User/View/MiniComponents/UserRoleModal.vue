<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">
		
			<h4 class="modal-title">{{lang('role_change')}} {{role}} to {{role_to}}</h4>
		
		</div>

		<div slot="fields" v-if="role_to === 'user' && !loading">

			<span>{{lang('changing_role_to_user')}} <b>{{user.user_name ? user.user_name : user.email }}</b>. {{lang('do_you_want_to_proceed_to_change_their_role_to_user')}} </span>

		</div>

		<div slot="fields" v-if="!loading && role_to !== 'user'" class="row">

			<dynamic-select :label="lang('departments')" :multiple="true" name="departments" 
				classname="col-sm-12" apiEndpoint="/api/dependency/departments" :value="departments" 
				:onChange="onChange" :required="true">

			</dynamic-select>	
		</div>
		
		<div v-if="loading" class="row" slot="fields" >
				
			<loader :animation-duration="4000" :size="60"/>
		</div>
						
		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit" class="btn btn-primary" :disabled="isDisabled">
					
				<i class="fas fa-check"></i> {{lang('proceed')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import {validateUserRoleSettings} from "helpers/validator/validateUserRoleRules.js"

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {
		
		name : 'org-modal',

		description : 'Organization Modal component',

		props:{

			role : { type : String, default : ''},

			role_to : { type : String, default : ''},
			
			showModal : { type : Boolean, default : false},

			userId : { type : String | Number, default : '' },

			onClose : { type : Function},

			dept : { type : Array, default:()=>[]},

			user : { type : Object, default : ()=>{}}
		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle:{ width:'800px' },

				loading:false,
				
				departments  : this.dept
			}
		},


		computed : {

			apiUrl(){

				return '/role-change-' + this.role_to + '/';
			}
		},

		methods:{
			
			onSubmit(){
			
				if( (this.role_to == 'user' || this.isValid() ) ){
				    this.changeRole();
				}
			},

			changeRole(){

				this.loading = true;

				this.isDisabled = true;

				const data = {};

				if(this.role_to !== "user"){

					var depts = [];
								
					for(var i in this.departments){
								
						depts.push(this.departments[i].id)						
					}
								
					data['primary_department'] = depts;
				}

				axios.post(this.apiUrl+this.userId,data).then(res=>{

					successHandler(res,'user-view')
						
					this.afterSuccess();

				}).catch(error=>{

					errorHandler(error,'user-view')
								
					this.afterResponse();
				})
			},

			afterSuccess(){

				this.afterResponse();

				window.eventHub.$emit('refreshUserData');

				window.eventHub.$emit('UserTicketsrefreshData');
				
				window.eventHub.$emit('UserTicketsuncheckCheckbox')

				window.eventHub.$emit('refreshUserReport')
			},

			afterResponse(){

				this.loading = false;

				this.isDisabled = false;
				
				this.onClose();
			},

			isValid(){
				
				const {errors, isValid} = validateUserRoleSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

			onChange(value,name){
			
				this[name] = value;
			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
		
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		
			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		}
	};
</script>