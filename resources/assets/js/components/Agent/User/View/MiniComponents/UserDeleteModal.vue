<template>
	
	<div> 
	
		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
			
			<div slot="alert">

				<alert componentName="user-delete"/>
			</div>

			<div slot="title">

				<h4>{{lang('delete')}}</h4>
			</div>
			
			<div slot="fields">

				<template v-if="!loading">

					<div slot="fields">

						<b id="bold">{{lang('what_should_happen_to_tickets_owned_by')}} {{userData.user_name ? userData.user_name : userData.email }}?</b><br>

						<radio-button :options="reqOptions" :label="lang('Options')" name="condition" :value="condition"
							:onChange="onChange" classname="form-group col-xs-12" :labelStyle="labelStyle">
								
						</radio-button>

						<dynamic-select v-if="condition == 'change_owner'"  :multiple="false" name="assign" 
							:label="lang('select_owner')" classname="col-xs-12" :value="assign" :onChange="onChange"
							apiEndpoint="/api/dependency/users?meta=true" :required="true" :clearable="assign ? true : false"
							strlength="50" :labelStyle="labelStyle">

						</dynamic-select>
					</div>
				</template>

				<div v-if="loading" class="row" slot="fields" >
				
					<loader :animation-duration="4000" :size="60"/>
				</div>			
			</div>
						
			<div slot="controls">
				
				<button type="button" @click="onSubmit" class="btn btn-danger" :disabled="isDisabled">

					<i class="fas fa-trash"></i> {{lang('delete')}}
				</button>
			</div>
		</modal>
	</div>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'
	
	import { validateUserDeleteSettings } from "helpers/validator/userDeleteRules.js"
	
	import axios from 'axios'
	
	export default {
		
		name : 'user-delete-modal',
	
		description : 'User Delete Modal component',
	
		props:{
	
			showModal:{type:Boolean,default:false},
	
			userData : { type : Object, default : ()=>{} },
	
			onClose:{type: Function}
		},
	
		data(){
	
			return {
	
				isDisabled:false,
	
				containerStyle : { width:'600px' },
				
				loading:false,
				
				labelStyle : { display:'none' },
								
				data : {},

				condition : 'nothing',

				assign : '',
				
				reqOptions:[
				
					{ name : 'do_nothing' , value : 'nothing' },

					{ name : 'delete_permanently' , value : 'delete' },
				
					{ name : 'change_requester' , value : 'change_owner'}
				]
			}
		},

		methods:{
		
			onChange(value,name){
		
				this[name]=value;

				this.assign = this.condition == 'change_owner' ? this.assign : '';
			},
			
			isValid(){
				
				const {errors, isValid} = validateUserDeleteSettings(this.$data);
		
				return isValid
			},
			
			onSubmit(){
				
				if(this.isValid()){
					
					this.loading = true
			
					this.isDisabled = true

					this.data['action_on_owned_tickets'] = this.condition;
					
					if(this.assign){

						this.data['set_owner_to'] = this.assign.id;
					}

					axios.delete('/api/account/delete/'+this.userData.id,{ data : this.data }).then(res=>{
					
						window.eventHub.$emit('refreshUserData');
						
						successHandler(res,'user-view')
					
						this.loading = false;
			
						this.isDisabled = false;
						
						this.onClose();
			
					}).catch(err => {
			
						this.loading = false;
					
						this.isDisabled = false;
			
						errorHandler(err,'user-delete');
		
					})
				}
			}
		},
		
		components:{
		
			'modal':require('components/Common/Modal.vue'),
		
			'alert' : require('components/MiniComponent/Alert'),
		
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			'radio-button':require('components/MiniComponent/FormField/RadioButton'),
			
			'dynamic-select':require('components/MiniComponent/FormField/DynamicSelect'),
		}
	};
</script>

<style scoped>
 
	#bold{
		line-height: 2;
		font-size: 16px;
		margin-left: 16px;
	}
</style>