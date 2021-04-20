0<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
		
		<div slot="title">
			
			<h4 class="modal-title">{{lang('merge-ticket')}}</h4>
		</div>
			
		<div v-if="!loading" slot="fields" id="merge_content">
			
			<div class="row">
				
				<static-select :label="lang('select-pparent-ticket')" strlength="30"  :elements="parentTickets"
					name="parentTicket" :value="parentTicket" classname="col-sm-4" :onChange="onChange" 
					:required="true">
				</static-select>

				<dynamic-select name="childTicket" :label="lang('select_tickets')" :value="childTicket" :elements="childTickets"
					:onChange="onChange" classname="col-sm-4" apiEndpoint=""  :multiple="true"
					:required="true" strlength="30">
			 	</dynamic-select>

				<text-field :label="lang('title')" :value="title" type="text" name="title"
					:onChange="onChange" classname="col-sm-4" :required="true">
				</text-field>
			</div>

			<div class="row">
			
				<text-field :label="lang('merge-reason')" :value="merge_reason" type="textarea" name="merge_reason"
					:onChange="onChange" classname="col-sm-12">
				</text-field>
			</div>
		</div> 	
		
		<div v-if="loading" class="row" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="size"/>
		</div>
						
		<div slot="controls">
			<button type="button" id="submit_btn" @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">

				<i class="fas fa-check"></i> {{lang('proceed')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import {validateTimelineMergeSettings} from "helpers/validator/timelineMergeSettingRules"

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import axios from 'axios'

	export default {
		
		name : 'timeline-merge-modal',

		description : 'Timeline merge ticket Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			ticketId : { type : Number | String, default : '' },

			onClose:{type: Function},

			componentTitle : { type : String, default :'timeline'},

			reloadTickets : { type : Function }
		},

		data:()=>({
			
			isDisabled:false,

			containerStyle:{ width:'800px' },

			loading:false,

			size: 60,

			merge_reason : '',

			title : '',

			parent_tickets : '',

			parentTicket : '',

			childTicket : ''
		}),

		computed : { 

			parentTickets() {

				if(this.childTicket && this.childTicket.length > 0) {
					
					return this.parent_tickets.filter(i => !this.childTicket.includes(i));

				} else {

					return this.parent_tickets
				}
			},

			childTickets() {

				if(this.parentTicket) {
					
					return this.parent_tickets.filter(i => i.id != this.parentTicket );
				} else {

					return this.parent_tickets
				}
			}
		},

		beforeMount() {

			this.mergeTicket();
		},

		methods:{

			mergeTicket(){

				this.loading = true;

				axios.get('/api/agent/tickets/get-merge-tickets',{ params : {'ticket-id' : this.ticketId} }).then(res=>{
					
					this.loading = false;

					this.parent_tickets = res.data.data
				
					for (var i in this.parent_tickets) {
					
						this.parent_tickets[i].value = this.parent_tickets[i]['name'];

						this.parent_tickets[i].id = this.parent_tickets[i]['ticket_id'];
				
						this.parent_tickets[i].name = this.parent_tickets[i]['title'];
				
						delete this.parent_tickets[i].ticket_id;
				
						delete this.parent_tickets[i].title;
					}
				}).catch(err=>{

					this.parent_tickets = [];

					this.loading = false;

					this.onClose();

					errorHandler(err,this.componentTitle)
				})
				
				this.parent_tickets = [];
			},
			
			isValid(){
				
				const {errors, isValid} = validateTimelineMergeSettings(this.$data)
				
				if(!isValid){
					
					return false
				}
				
				return true
			},
		
			onChange(value, name){

				this[name]= value;	

				if(name === 'parentTicket'){

					this.title = value ? this.parent_tickets.find(x => x.id == value).value : '';
				}
			},
			
			onSubmit(){
			
				if(this.isValid()){
					
					this.loading = true
					
					this.isDisabled = true

					const data = {};

					data['title'] = this.title;
					
					data['p_id'] = this.parentTicket;

					data['reason'] = this.merge_reason;
					
					data['_method'] = 'PATCH';
					
					data['t_id'] = this.childTicket.map(a => a.id);
					
					axios.post('/merge-tickets/'+this.ticketId,data).then(res=>{

						this.loading = false;
						
						this.isDisabled = true

						this.reloadTickets();

						successHandler(res,this.componentTitle);
						
						this.onClose();
					
					}).catch(err => {
						
						this.loading = false;
						
						this.isDisabled = false
						
						errorHandler(err,this.componentTitle)

						this.onClose();
					})
				}
			},
		},

		components:{
		
			'modal':require('components/Common/Modal.vue'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			'text-field': require('components/MiniComponent/FormField/TextField'),
			
			"static-select": require("components/MiniComponent/FormField/StaticSelect"),

			"dynamic-select": require("components/MiniComponent/FormField/DynamicSelect"),
		}
	};
</script>

<style>
	
	#merge_content {
		max-height: 350px;
    	overflow-y: auto;
    	overflow-x: hidden;
	}
</style>