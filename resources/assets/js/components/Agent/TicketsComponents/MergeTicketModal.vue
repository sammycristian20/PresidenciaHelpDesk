<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
		<div slot="title">
			
			<h4 class="modal-title">{{lang('merge-ticket')}}</h4>
		</div>
			
		<div v-if="!loading && parent_tickets.length > 0" slot="fields">
			
			<div class="row">
				
				<static-select :label="lang('select-pparent-ticket')" strlength="30"  :elements="parent_tickets"
					name="parentTicket" :value="parentTicket" classname="col-sm-6" :onChange="onChange" 
					:required="true">
				</static-select>

				<text-field :label="lang('title')" :value="title" type="text" name="title"
					:onChange="onChange" classname="col-sm-6" :required="true">
				</text-field>
			</div>

			<div class="row">
			
				<text-field :label="lang('merge-reason')" :value="merge_reason" type="textarea" name="merge_reason"
					:onChange="onChange" classname="col-sm-12">
				</text-field>
			</div>
		</div>
		
		<div v-if="loading || parent_tickets.length == 0" class="row" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="size" :class="{spin: lang_locale == 'ar'}" />
		</div>
						
		<div slot="controls">
			<button type="button" id="submit_btn" @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">

				<i class="fa fa-check"></i> {{lang('proceed')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {validateMergeTicketSettings} from "helpers/validator/mergeTicketSettingRules"

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'change-status-modal',

		description : 'Change status Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			parent_tickets : { type : Array, default : ()=>[] },

			ticketIds : { type : Array, default : ()=>[] },

			onClose:{type: Function, default : ()=>{}},

			componentTitle : { type : String, default :'UserTickets'},

			reloadTickets : { type : Function, default: () => {} }
		},

		data:()=>({
			
			isDisabled:false,

			containerStyle:{
				width:'600px'
			},

			loading:false,

			size: 60,

			lang_locale:'',

			merge_reason : '',

			title : '',

			parentTicket : ''
		}),

		created(){

			this.lang_locale = localStorage.getItem('LANGUAGE');
		},

		methods:{
			
			isValid(){
				
				const {errors, isValid} = validateMergeTicketSettings(this.$data)
				
				if(!isValid){
					
					return false
				}
				
				return true
			},
		
			onChange(value, name){

				this[name]= value;	

				if(name === 'parentTicket'){
				
					this.title = value ? this.parent_tickets.find(x => x.id === value).subject : '';
				}
			},
			
			onSubmit(){
				
				if(this.isValid()){
					
					this.loading = true
					
					this.isDisabled = true
					
					axios.post('/merge-tickets/'+this.ticketIds,{ p_id:this.parentTicket, title:this.title, reason:this.merge_reason, '_method':'PATCH' }).then(res=>{

						this.loading = false;
						
						this.isDisabled = true

						this.reloadTickets();

						successHandler(res,this.componentTitle);
				
						window.eventHub.$emit(this.componentTitle+'refreshData');
						
						window.eventHub.$emit(this.componentTitle+'uncheckCheckbox')

						window.eventHub.$emit('refreshUserReport')

						window.eventHub.$emit('refreshOrgReport')

						window.eventHub.$emit('closeTimelineView');
						
						this.onClose();
					
					}).catch(err => {
						
						this.loading = false;
						
						this.isDisabled = false
						
						errorHandler(err,this.componentTitle);

						this.onClose();
					})
				}
			},
		},

		components:{
		
			'modal':require('components/Common/Modal.vue'),
		
			'alert' : require('components/MiniComponent/Alert'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			'text-field': require('components/MiniComponent/FormField/TextField'),
			
			"static-select": require("components/MiniComponent/FormField/StaticSelect"),
		}
	};
</script>

<style scoped>
	
	#mt_10 { margin-top: 15px; }
</style>