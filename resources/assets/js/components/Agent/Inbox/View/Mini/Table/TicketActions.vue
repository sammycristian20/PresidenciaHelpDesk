<template>
	
	<div class="btn-group">
		
		<button type="button"  v-tooltip="lang('assign-ticket')" :style="buttonStyle" 
			class="btn btn-md btn-default" @click="modalMethod('assign_ticket','showAssignModal')" id="assign">

			<i  class="far fa-hand-point-right"></i>
		</button>

		<button type="button" v-tooltip="lang('merge-ticket')" class="btn btn-md btn-default" 
			:style="buttonStyle" @click="modalMethod('merge-ticket')" id="merge">

			<i  class="fas fa-cogs"></i>
		</button> 

		<button v-if="page === 'trash'" type="button" v-popover:tooltip="lang('delete-tickets')" 
			class="btn btn-sm btn-danger" @click="modalMethod('delete-ticket','showDeleteModal')" id="delete">

			<i class="fas fa-trash"></i>&nbsp;{{lang('delete_forever')}}
		</button>

		<div class="btn-group" :key="statusDiv">
					
			<button v-tooltip="lang('change_status')" type="button" class="btn btn-sm btn-default dropdown-toggle" 
				data-toggle="dropdown" :style="buttonStyle" @click="getStatusList()" id="status">
				<i class="fas fa-exchange-alt"> </i>
			</button>
			
			<div class="dropdown-menu status-list" v-if="selectedTickets.length > 0">
					
				<div  v-if="loading" class="row">
						
						<loader :animation-duration="4000" color="#1d78ff" :size="30"/>
				</div>

				<template v-if="!loading">
						
					<template v-if="statusList && statusList.length > 0">
					
						<a v-for="status in statusList" class="dropdown-item" @click="getStatus(status)" href="javascript:;">
						
							<i :class="status.icon" :style="{color:status.icon_color}"> </i> &nbsp;{{status.name}}
						</a>
					</template>

					<a v-if="statusList && statusList.length === 0" class="dropdown-item" href="javascript:;">
						{{lang('no-records')}}
					</a>
				</template>
			</div>
		</div>

		<transition name="modal">

			<change-status-modal v-if="showStatusModal" :onClose="onClose" :showModal="showStatusModal" 
				:ticketIds="selectedTickets" :status="change_status" componentTitle="inbox" :reloadTickets="loadTickets">
						
			</change-status-modal>
		</transition>

		<transition name="modal">

			<assign-ticket-modal v-if="showAssignModal" :onClose="onClose" :showModal="showAssignModal" 
				:ticketIds="selectedTickets" componentTitle="inbox" :reloadTickets="loadTickets">

			</assign-ticket-modal>
		</transition>

		<transition name="modal">
		
			<merge-ticket-modal v-if="showMergeModal" :onClose="onClose" :showModal="showMergeModal" 
				:parent_tickets="parent_tickets" :ticketIds="selectedTickets" componentTitle="inbox" :reloadTickets="loadTickets">

			</merge-ticket-modal>
		</transition>

		<transition name="modal">

			<delete-ticket-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal" 
				:ticketIds="selectedTickets" componentTitle="inbox" :reloadTickets="loadTickets">

			</delete-ticket-modal>
		</transition>
	</div>
</template>

<script>

	import {arrayUnique, lang} from 'helpers/extraLogics';

	import {errorHandler, successHandler} from 'helpers/responseHandler'
	
	import axios from 'axios';
	
	export default { 

		name : 'ticket-actions',

		description : 'Ticket actions component',

		props : {

			selectedTickets : { type : Array|String, default : ()=>[]},

			buttonStyle : { type : Object, default : ()=>{}},

			loadTickets : { type : Function },

			page : { type : String, default : '' }
		},

		data() {

			return {

				loading : false,

				parent_tickets : [],

				showStatusModal : false,

				change_status : '',

				statusList : '',

				showAssignModal : false,

				showMergeModal : false,

				showDeleteModal : false,

				statusDiv : 0,
			}
		},

		methods : {

			modalMethod(modal,name){

				if(modal !== 'merge-ticket'){

					if(this.selectedTickets.length > 0){
						
						this[name] = true;
					} else {

						alert('Please select tickets.')
					}

				} else {

					this.mergeTicket(modal);
				}
			},

			getStatusList(){
				
				this.statusList = '';

				if(this.selectedTickets.length > 0){

					this.loading = true;
				
					let params = {meta: true, supplements:arrayUnique(this.selectedTickets),limit : 'all'}
	        
					axios.get('/api/dependency/statuses',{params}).then(res =>{
						
						this.loading  = false
				
						this.statusList=res.data.data.statuses;
				
					}).catch(err=>{
				
						this.loading  = false
					})
				} else {

					this.statusDiv +=1;

					alert('Please select tickets.')
				}
			},

			getStatus(status){
				
				if(this.selectedTickets.length > 0){
				
					this.showStatusModal = true
				
					this.change_status = status
				
				}else {
					
					alert('Please select tickets.');

					this.showStatusModal = false
				}
			},

			mergeTicket(modal){

				if(this.selectedTickets.length > 1){
					
					this.showMergeModal = true
					
					axios.get('/api/agent/tickets/get-merge-tickets',{ params : {'ticket-ids' : this.selectedTickets} })
					.then(res=>{
					
						this.parent_tickets = res.data.data
					
						for (var i in this.parent_tickets) {
					
							this.parent_tickets[i].id = this.parent_tickets[i]['ticket_id'];

							this.parent_tickets[i].subject = this.parent_tickets[i]['name'];
					
							this.parent_tickets[i].name = this.parent_tickets[i]['title'];
					
							delete this.parent_tickets[i].ticket_id;
					
							delete this.parent_tickets[i].title;
						}
					}).catch(err=>{

						this.showMergeModal = false;

						errorHandler(err,'inbox');
					}) 
					
					this.parent_tickets = [];
				}else {
					
					this.showMergeModal = false
					
					alert(lang('please_select_more_than_2_tickets'));
				}
			},

			onClose(){
				
				this.statusDiv ++;

				this.showStatusModal = false;

				this.showAssignModal = false;

				this.showMergeModal = false

				this.showDeleteModal = false;
			
				this.$store.dispatch('unsetValidationError');
			},	
		},

		components : { 

			'change-status-modal': require('components/Agent/TicketsComponents/ChangeStatusModal'),

			'merge-ticket-modal': require('components/Agent/TicketsComponents/MergeTicketModal'),

			'assign-ticket-modal': require('components/Agent/TicketsComponents/AssignTicketModal'),

			'delete-ticket-modal' : require('components/Agent/TicketsComponents/DeleteTicketModal'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style scoped>
	
	#assign,#merge,#status,#delete{ margin: 2px; }

	#assign,#merge { padding-bottom: 8px;	}
	
	#status{ margin-left: 3px; }
</style>