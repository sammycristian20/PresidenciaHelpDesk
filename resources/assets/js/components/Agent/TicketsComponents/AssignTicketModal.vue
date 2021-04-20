<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
	
		<div slot="title">
		
			<h4 class="modal-title">{{lang('assign_tickets')}}</h4>
		</div>
		
		<div v-if="!loading" slot="fields" class="row">

			<radio-button :options="radioOptions" :label="lang('assign_to')" name="assign" :value="assign"
				:onChange="onChange" classname="form-group col-sm-6" ></radio-button>

			<div v-if="assignChange" id="assign_change">
            <loader :size="30" :duration="4000" :color="'#1d78ff'"></loader>
         </div>

      	<dynamic-select v-if="!assignChange && assign != 'assign_to_me'" 
      		:label="lang('whome_do_you_want_to_assign_ticket')" :multiple="false" 
				name="assign_to"  classname="col-sm-6" :api-endpoint="'api/dependency/' + assign" 
				:api-parameters="getApiParameters"
				:value="assign_to" :onChange="onChange" strlength="30">

			</dynamic-select>		
		</div> 
		
		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="size" />
		</div>
					
		<button slot="controls" type="button" @click="onSubmit()" class="btn btn-primary" id="submit_btn" 
			:disabled="isDisabled">

			<i class="far fa-hand-point-right"></i> {{lang('assign')}}
		</button>
	</modal>
</template>

<script type="text/javascript">
	
	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'assign-ticket-modal',

		description : 'Assign ticket Modal component',

		props:{
			
			showModal:{type:Boolean,default:false},

			ticketIds : { type : Array, default : ()=>[] },

			onClose:{type: Function, default : ()=>{} },

			componentTitle : { type : String, default :'UserTickets'},

			reloadTickets : { type : Function, default: () => {} }
		},

		data:()=>({
			
			isDisabled:true,

			containerStyle:{ width:'750px' },

			loading:false,

			size: 60,
	
			assign_to : '',

			assign : 'agents',

			radioOptions:[{name:'agent',value:'agents'},{name:'team',value:'teams'},{name:'assign_to_me',value:'assign_to_me'}],

			assignChange : false,
		}),

		computed :  {

			getApiParameters(){

				return {
					'supplements[ticket_id]': this.ticketIds,
					'supplements[no_email]': 1,
				};
			}
		},

		methods:{

		onChange(value, name){
			
			this[name]= value;

			if(name === 'assign'){

				this.assignChange = true;

	        	setTimeout(()=>{
	          
	          	this.assignChange = false;
	        	},1)

				this.assign_to = value != 'assign_to_me' ? '' : { id : sessionStorage.getItem('user_id') };

				this.isDisabled = value == 'assign_to_me' ? false : true;
			}

			if(name === 'assign_to'){
				
				this.isDisabled = value ? false : true
			}
		},

		onSubmit(){

			this.loading = true
			
			this.isDisabled = true

			var obj={
				'assign_to' : this.assign === 'teams' ? 'team_' + this.assign_to.id : 'user_' + this.assign_to.id, 
				'_method' : 'PATCH'
			};

			axios.post('/ticket/assign?ticket_id='+this.ticketIds,obj).then(res=>{
				
				this.loading = false;
				
				this.isDisabled = true

				this.reloadTickets();

				successHandler(res,this.componentTitle);
				
				window.eventHub.$emit(this.componentTitle+'refreshData');
				
				window.eventHub.$emit(this.componentTitle+'uncheckCheckbox')

				window.eventHub.$emit('refreshUserReport')

				window.eventHub.$emit('refreshOrgReport')
				
				this.onClose();
				
			}).catch(err => {
				
				this.loading = false;
				
				this.isDisabled = false
				
				errorHandler(err,this.componentTitle);

				this.onClose();
			})
		},
	},

	components:{
		
		'modal':require('components/Common/Modal.vue'),
		
		'alert' : require('components/MiniComponent/Alert'),
		
		'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		
		"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),

		'radio-button':require('components/MiniComponent/FormField/RadioButton'),
	}
};
</script>

<style type="text/css">
	
	#assign_change{
	  padding: 10px;
	}
</style>