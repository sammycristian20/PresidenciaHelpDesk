<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
	
		<div slot="title">
		
			<h4 class="modal-title">{{lang('assign_manager')}}</h4>
		</div>
		
		<div v-if="!loading" slot="fields" class="row">

			<dynamic-select :label="lang('select_manager')" :multiple="true" name="managers"  
				classname="col-sm-12" :apiEndpoint="'/search/organization/manager/'+orgId" :value="managers" :onChange="onChange">
			
			</dynamic-select>
		</div> 
		
		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" :size="60" />
		</div>
					
		<div slot="controls">
			
			<button type="button" @click="onSubmit()" class="btn btn-primary" id="submit_btn" :disabled="isDisabled">

				<i class="fa fa-check"></i> {{lang('assign')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'org-manager-modal',

		description : 'Assign Manger to Organization Modal component',

		props:{
			
			showModal:{type:Boolean,default:false},

			orgId : { type : String|Number, default : '' },

			onClose:{type: Function},

			managerList : { type : Array, default : ()=>[] },
		},

		data(){
			
			return {

				isDisabled:true,

				containerStyle:{ width:'600px' },

				loading:false,

				managers : this.managerList
			}	
		},

		methods:{

		onChange(value, name){
			
			this[name]= value;

			this.isDisabled = value === '' ? true : false;
		},

		onSubmit(){

			this.loading = true
			
			this.isDisabled = true

			let ids = [];

			for(var i in this.managers){

				ids.push(this.managers[i].id)
			}
			var obj={'user':ids};

			axios.post('/add/organization/manager?org_id='+this.orgId,obj).then(res=>{
				
				this.loading = false;
				
				this.isDisabled = true
			
				successHandler(res,'org-view');
				
				window.eventHub.$emit('refreshOrgData');
				
				this.onClose();
				
			}).catch(err => {
				
				this.loading = false;
				
				this.isDisabled = false
				
				errorHandler(err,'org-view');

				this.onClose();
			})
		},
	},

	components:{
		
		'modal':require('components/Common/Modal.vue'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
		'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		
		"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
	}
};
</script>