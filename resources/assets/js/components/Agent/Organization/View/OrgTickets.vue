<template>
	
	<div id="org-tickets-table">
		
		<div class="row">
			
			<div class="col-md-12">
				
				<alert componentName="OrgTickets"/>
			</div>
		</div>

		<div class="card card-light ">

			<div class="card-header">
				
				<h3 class="card-title">{{lang('tickets_of')}} {{subString(name,40)}}</h3>	
			</div>
			
			<div class="card-body">

				<div v-if="hasDataPopulated" class="nav-tabs-custom">
					
					<ul class="nav nav-tabs">
						
						<li v-for="section in tabs" class="nav-item">
						
							<a  class="nav-link" :class="{ active: category === section.id }" href="javascript:;" data-toggle="pill" role="tab" 
								@click="tickets(section)" id="tickets_tab">
						
								{{lang(section.title)}} <span class="badge badge-primary"> {{section.count}}</span>
						
							</a>
						</li>
					</ul>

					<div class="tab-content">

						<div class="active tab-pane" id="activity">
							
							<div class="mt-3">
									<button type="button" @click="mergeTicket()" class="btn btn-xs btn-default" id="merge_ticket">
										
										<i class="fas fa-code-branch"> </i> {{lang('merge')}}
									</button>
						
									<div class="btn-group" :key="statusDiv">
												
										<button v-tooltip="lang('change_status')" type="button" class="btn btn-xs btn-default dropdown-toggle" 
											data-toggle="dropdown" @click="getStatusList()" id="status">
											
											<i class="fas fa-exchange-alt"> </i> {{lang('change_status')}}
										</button>
										
										<div class="dropdown-menu status-list" v-if="ticketId.length > 0">
												
											<div  v-if="statusLoader" class="row">
													
													<loader :animation-duration="4000" color="#1d78ff" :size="30"/>
											</div>

											<template v-if="!statusLoader">
													
												<template v-if="statusList && statusList.length > 0">
												
													<a v-for="status in statusList" class="dropdown-item" @click="getStatus(status)" href="javascript:;"
														id="change_status">
													
														<i :class="status.icon" :style="{color:status.icon_color}"> </i> &nbsp;{{status.name}}
													</a>
												</template>

												<a v-if="statusList && statusList.length === 0" class="dropdown-item" href="javascript:;">
													{{lang('no-records')}}
												</a>
											</template>
										</div>
									</div>

									<button v-if="ticketId.length > 0" type="button" @click="showAssignModal = true" 
										class="btn btn-xs btn-default" id="assign_ticket">
										
										<i class="far fa-hand-point-right"> </i> {{lang('assign')}}
									</button>

								</div>

								<div id="my_tic">
								
									<org-tickets-table :category="category" :id="id" :tickets="ticketsData" :key="category" 
										:apiUrl="apiUrl" :componentTitle="componentTitle" scroll_to="org-tickets-table" from="org">
									
									</org-tickets-table>
								</div>
							</div>				
						</div>
					</div>

					<div v-if="!hasDataPopulated && loading" class="row">

						<loader :animation-duration="4000" :size="60"/>
					</div>

				<transition  name="modal">

					<change-status-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :statusIds="statusIds" :ticketIds="ticketId" :status="change_status" :componentTitle="componentTitle">
						
					</change-status-modal>
				</transition>

				<transition  name="modal">
					<merge-ticket-modal v-if="showMergeModal" :onClose="onClose" :showModal="showMergeModal" :parent_tickets="parent_tickets" :ticketIds="ticketId" :componentTitle="componentTitle">

					</merge-ticket-modal>
				</transition>

				<transition  name="modal">
					<assign-ticket-modal v-if="showAssignModal" :onClose="onClose" :showModal="showAssignModal"
						:ticketIds="ticketId" :componentTitle="componentTitle">

					</assign-ticket-modal>
				</transition>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import { getSubStringValue, arrayUnique } from 'helpers/extraLogics';

	export default {

		name : 'org-tickets',

		description : 'Organization tickets page',

		props : { 

			id : { type : String|Number, default : ''},

			name : { type : String , default : 'Organization'}
		},

		data() {

			return {
			
				category:'',

				status_type : '',

				tabs:[],
				
				loading:true,

				hasDataPopulated : false,
				
				ticketId:[],

				showModal : false,

				statusList : '',

				showModal : false,

				showMergeModal : false,

				showAssignModal : false,

				parent_tickets : [],

				change_status : '',

				role_to : '',

				componentTitle : 'OrgTickets',

				apiUrl : '',

				statusDiv : 0,

				statusLoader  : false,
			}
		},

		beforeMount(){
			
			this.category = 'open';

			this.getCount();
		},

		methods :{
			
			subString(name,length = 15){
			
				return getSubStringValue(name,length)
			},

			getStatusList(){
				
				this.statusList = '';

				if(this.ticketId.length > 0){

					this.statusLoader = true;
				
					let params = {meta: true, supplements:arrayUnique(this.ticketId),limit : 'all'}
	        
					axios.get('/api/dependency/statuses',{params}).then(res =>{
				
						this.statusLoader  = false
				
						this.statusList=res.data.data.statuses;
				
					}).catch(err=>{
				
						this.statusLoader  = false
					})
				} else {

					this.statusDiv +=1;

					alert('Please select tickets.')
				}
			},

			getStatus(status){
				
				if(this.ticketId.length > 0){
				
					this.showModal = true
				
					this.change_status = status
				
					this.statusIds = this.tic_statuses
				
				}else {
					
					alert('Please select tickets.');

					this.showModal = false
				}
			},

			mergeTicket(){
			
				if(this.ticketId.length > 1){
			
					this.showMergeModal = true
			
					axios.get('/api/agent/tickets/get-merge-tickets',{ params : {'ticket-ids' : this.ticketId} }).then(res=>{
			
						this.parent_tickets = res.data.data
			
						for (var i in this.parent_tickets) {
			
							this.parent_tickets[i].id = this.parent_tickets[i]['ticket_id'];

							this.parent_tickets[i].subject = this.parent_tickets[i]['name'];
							
							this.parent_tickets[i].name = this.parent_tickets[i]['title'];
			
							delete this.parent_tickets[i].ticket_id;
			
							delete this.parent_tickets[i].title;
						}
					}) 
			
					this.parent_tickets = [];
				}else {
			
					this.showMergeModal = false
			
					alert('Please select 2 or more than 2 ticket.')
				}
			},

			tickets(category){
				this.category = category.id
				this.apiUrl = category.url
			},

			onClose(){
				
				this.showModal = false;
				
				this.showMergeModal = false;
				
				this.showAssignModal = false;
				
				this.getCount();
				
				this.$store.dispatch('unsetValidationError');
			},

			ticketsData(data){
		
				this.ticketId = data
			},

			getCount(){

				let params;
			
				let endPoint = 'api/agent/ticket-count?';

				params = 'organization-ids[]='+this.id;
				
				endPoint += params;
					  
				axios.get(endPoint).then(res=>{
					
					this.hasDataPopulated = true;
					
					this.loading = false;

					this.tabs = [];
				
					for (var key in res.data.data) {
					
						if (res.data.data.hasOwnProperty(key)) {

							let tabsBody = { id : key, title : key, count : res.data.data[key]}
							
							switch(tabsBody.title) {
								case 'open':
									tabsBody['url'] = 'api/agent/ticket-list?category=inbox&'+params;
									break;
								case 'closed':
									tabsBody['url'] = 'api/agent/ticket-list?category=closed&'+params;
									break;
								case 'deleted':
									tabsBody['url'] = 'api/agent/ticket-list?category=deleted&'+params;
									break;
								case 'unapproved':
									tabsBody['url'] = 'api/agent/ticket-list?category=unapproved&'+params;
									break;			
							}
							
							this.tabs.push(tabsBody)
						}
					}

					this.tickets(this.tabs[0])

				}).catch(error=>{

					this.hasDataPopulated = true;

					this.loading = false;
				})
			},

			updateStatus(status){

				this.showModal = true
				
				this.change_status = status;
			},
		},

		components : {
			
			'org-tickets-table' : require('components/Agent/TicketsComponents/TicketsTable'),

			'change-status-modal': require('components/Agent/TicketsComponents/ChangeStatusModal'),

			'merge-ticket-modal': require('components/Agent/TicketsComponents/MergeTicketModal'),

			'assign-ticket-modal': require('components/Agent/TicketsComponents/AssignTicketModal'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			"alert": require("components/MiniComponent/Alert"),	
		}
	};
</script>