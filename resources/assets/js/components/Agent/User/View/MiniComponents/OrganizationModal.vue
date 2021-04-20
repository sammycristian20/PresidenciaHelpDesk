<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">
		
			<h4 class="modal-title">{{lang(title)}}</h4>
		
		</div>
		
		<!-- removing organization -->

		<div slot="fields" v-if="title === 'remove' && !loading">

			<span>{{lang('are_you_sure_you_want_to_remove')}}</span>

		</div>

		<!-- assign existing organization -->

		<div slot="fields" v-if="title === 'assign' && !loading" class="row">

			<dynamic-select :label="lang('organizations')" :multiple="true" name="organizations" 
				classname="col-sm-12" apiEndpoint="/api/dependency/organizations" :value="organizations" 
				:onChange="onChange" :required="true">

			</dynamic-select>	

			<dynamic-select v-if="deptCondition" :label="lang('organization_department')" :multiple="false" name="org_dept" 
				 classname="col-sm-12" :apiEndpoint="org_dept_api" :value="org_dept" 
				:onChange="onChange" :required="false">

			</dynamic-select>	
		</div>

		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" :size="60"/>
		</div>
					
		<div slot="controls">

			<button type="button" id="submit_btn" @click = "onSubmit" :class="btnClass" :disabled="isDisabled">
				<i :class="iconClass"></i> {{lang(btnName)}}
			</button>
		</div>
	</modal>

</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import {validateAssignOrgSettings} from "helpers/validator/validateAssignOrgRules.js"

	import { mapGetters } from 'vuex'

	import axios from 'axios'

	export default {
		
		name : 'org-modal',

		description : 'Organization Modal component',

		props:{

			showModal : { type : Boolean, default : false},

			title : { type : String, default : '' },

			orgId : { type : String | Number, default : '' },

			userId : { type : String | Number, default : '' },

			onClose : { type : Function},

			deptCondition : { type : Boolean | Number}
		},

		data(){
			
			return {

				isDisabled:false,

				containerStyle:{ width:'800px' },

				loading:false,
				
				organizations  : [],

				org_dept : '',

				org_dept_api : '',

				btnName : this.title === 'assign' ? 'assign' : 'remove',

				btnClass : this.title === 'assign' ? 'btn btn-primary' : 'btn btn-danger',
				
				iconClass : this.title === 'assign' ? 'fas fa-hand-point-right' : 'fas fa-unlink',
			}
		},

		methods:{
			
			onSubmit(){
			
				if(this.title === 'assign'){
					
					if(this.isValid()){

						this.loading = true;

						this.isDisabled = true;

						const data = {};

						var orgs = [];

						var org_depts = [];
						
						for(var i in this.organizations){
						
							orgs.push(this.organizations[i].id)						
						}
						
						data['org'] = orgs;

						data['org_dept'] = this.org_dept ? this.org_dept.id : '';

						axios.post('/api/user-org-assign/'+this.userId,data).then(res=>{

							successHandler(res,'user-view')
					
							this.afterSuccess();

						}).catch(error=>{

							errorHandler(error,'user-view')
							
							this.afterResponse();
						})
					}
				}else{
				
					this.loading = true;
					
					this.isDisabled = true;

					axios.delete('/api/remove-user-org/'+this.userId+'/'+this.orgId).then(res=>{
				
						successHandler(res,'user-view')
				
						this.afterSuccess();
					
					}).catch(error=>{
						
						errorHandler(error,'user-view')
						
						this.afterResponse();
					})
				}	
			},

			afterSuccess(){

				this.afterResponse();

				window.eventHub.$emit('refreshUserData');
			},

			afterResponse(){

				this.loading = false;

				this.isDisabled = false;
				
				this.onClose();
			},

			isValid(){
				
				const {errors, isValid} = validateAssignOrgSettings(this.$data);
				
				if(!isValid){
				
					return false
				}
				return true
			},

			onChange(value,name){
			
				this[name] = value;

				if(name === 'organizations') {

					this.org_dept = '';

					var ids = [];

					if(value.length > 0){

						for(var i in value){

							ids.push(value[i].id)
						}
						
						this.org_dept_api = '/org/department/search?org_id='+ids;
					}
				}
			}
		},

		components:{

			'modal':require('components/Common/Modal.vue'),
		
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		
			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		}
	};
</script>