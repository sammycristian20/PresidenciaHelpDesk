<template>
	
	<div class="mt-10">

		<div v-if="getTicketActions && !getTicketActions.block_ticket_actions" 
			class="ticket-actions">

			<button type="button" class="btn btn-default" @click="showEditModal = true">
				
				<i class="far fa-edit"> </i> {{lang('edit')}}
			</button>

			<button  v-if="getTicketActions.assign"  type="button" class="btn btn-default" @click="showAssignModal = true">
				
				<i  class="far fa-hand-point-right"> </i> {{lang('assign')}}
			</button>

			<transition name="modal">

				<assign-ticket-modal v-if="showAssignModal" :onClose="onClose" :showModal="showAssignModal" 
					:ticketIds="[id]" componentTitle="timeline" :reloadTickets="reloadData">

				</assign-ticket-modal>
			</transition>

			<button  v-if="getTicketActions.change_duedate"  type="button" class="btn btn-default" 
				@click="showDueModal = true">
				
				<i  class="far fa-calendar"> </i> {{lang('change_due_date')}}
			</button>

			<transition name="modal">

				<due-date-modal v-if="showDueModal" :onClose="onClose" :showModal="showDueModal" 
					:ticketId="id" componentTitle="timeline" :reloadDetails="reloadData">

				</due-date-modal>
			</transition>

			<button  v-if="getTicketActions.surrender"  type="button" class="btn btn-default" 
				@click="showSurrenderModal = true">
				
				<i  class="fas fa-arrows-alt"> </i> {{lang('surrender')}}
			</button>

			<transition name="modal">

				<surrender-modal v-if="showSurrenderModal" :onClose="onClose" :showModal="showSurrenderModal" 
					:ticketId="id" componentTitle="timeline" :reloadDetails="reloadData">

				</surrender-modal>
			</transition>
		 	
		 	<button  v-if="getTicketActions.transfer"  type="button" class="btn btn-default" 
				@click="showDeptModal = true">
				
				<i  class="far fa-hand-point-right"> </i> {{lang('change_department')}}
			</button>

			<transition name="modal">

				<change-dept-modal v-if="showDeptModal" :onClose="onClose" :showModal="showDeptModal" 
					:ticketId="id" componentTitle="timeline" :reloadDetails="reloadData">

				</change-dept-modal>
			</transition>

			<!-- labels -->
	     	<ticket-label :ticketObj="data" :reloadDetails="reloadData"></ticket-label>

		   <!-- tags -->
		   <ticket-tag :ticketObj="data" :reloadDetails="reloadData"></ticket-tag>

		    <a :href="basePath() + '/ticket/print/' + id" target="_blank" class="btn btn-default">
		    	
		    	<i class="far fa-file-pdf"> </i> {{ lang('generate_pdf') }}
		    </a>

		   <div class="btn-group">
					
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" 
					@click="getStatusList()" id="status">
					
					<i class="fas fa-exchange-alt"> </i> {{lang('change_status')}}
				</button>
				
				<div class="dropdown-menu status-list">
						
					<div  v-if="loading" class="row">
							
							<loader :animation-duration="4000" color="#1d78ff" :size="30"/>
					</div>

					<template v-if="!loading">
						
						<template v-if="statusList && statusList.length > 0">
						
							<a v-for="status in statusList" class="dropdown-item"  id="change_status" @click="getStatus(status)" 
								href="javascript:;">
							
								<i :class="status.icon" :style="{color:status.icon_color}"> </i> &nbsp;{{status.name}}
							</a>
						</template>

						<a v-if="statusList && statusList.length === 0" class="dropdown-item"  href="javascript:;">
							{{lang('no-records')}}
						</a>
					</template>
				</div>
			</div>

			<transition name="modal">

				<change-status-modal v-if="showStatusModal" :onClose="onClose" :showModal="showStatusModal" 
					:ticketIds="[id]" :status="change_status" componentTitle="timeline" :reloadTickets="reloadData">
							
				</change-status-modal>
			</transition>

			<button  v-if="getTicketActions.allowed_enforce_approval_workflow"  type="button" class="btn btn-default" 
				@click="showApprovalModal = true">
				
				<i  class="fas fa-pause"> </i> {{lang('apply_approval_workflow')}}
			</button>

			<transition name="modal">

				<approval-modal v-if="showApprovalModal" :onClose="onClose" :showModal="showApprovalModal" 
					:ticketId="id" componentTitle="timeline" :reloadDetails="reloadData">

				</approval-modal>
			</transition>

			<button  type="button" class="btn btn-default"  @click="showForwardModal = true">
				
				<i  class="fas fa-forward"> </i> {{lang('forward_ticket')}}
			</button>

			<transition name="modal">

				<forward-modal v-if="showForwardModal" :onClose="onClose" :showModal="showForwardModal" 
					:ticketId="id" componentTitle="timeline" :reloadDetails="reloadData">

				</forward-modal>
			</transition>

			<more-events :actions="getTicketActions" :ticket="data" :updateDetails="reloadData">
				
			</more-events>

			<div id="timeline-action-bar" class="inline-block">{{timelineActionBarMounted()}}</div>

			<div v-if="getTicketActions.has_calender" id="timeline-action-div" class="inline-block">{{timelineActionDivMounted()}}

			</div>

		</div>

		
		<transition name="modal">

			<edit-ticket-modal v-if="showEditModal" :onClose="onClose" :showModal="showEditModal" 
				:ticketId="id" componentTitle="timeline" :reloadDetails="reloadData">

			</edit-ticket-modal>
		</transition>
	</div>
</template>

<script>

	import { mapGetters } from 'vuex';

	import axios from 'axios';

	import {arrayUnique} from 'helpers/extraLogics';
	
	export default {

		name : 'ticket-actions',

		description : 'Tickets Actions Component',

		props : {

			id : { type : String | Number, default : '' },

			data : { type : Object, default : ()=> {} },

			afterAction : { type : Function },
		},

		data() {

			return {

				showEditModal : false,

				showAssignModal : false,

				showDueModal : false,

				showSurrenderModal : false,

				showTaskModal : false,

				showDeptModal : false,

				showStatusModal : false,

				change_status : '',

				statusList : '',

				loading : false,

				showApprovalModal : false,

				showForwardModal : false,
			}
		},

		computed : {

			...mapGetters(['getTicketActions'])
		},

		created() {

			 window.eventHub.$on('updateTimelineActions',this.reloadData)
		},

		methods : {

			getStatusList(){
				
				this.loading = true;
				
				let params = {meta: true, supplements:arrayUnique([this.id]),limit : 'all'}
	        
				axios.get('/api/dependency/statuses',{params}).then(res =>{
				
					this.loading  = false
				
					this.statusList=res.data.data.statuses;
				
				}).catch(err=>{
				
					this.loading  = false
				})
			},

			getStatus(status){
				
				this.showStatusModal = true
				
				this.change_status = status
			},

			timelineActionBarMounted(){
				
				this.data['alertName'] = 'timeline';
            // if i could pass data to it
            window.eventHub.$emit('timeline-action-bar-mounted', this.data);
          },

          timelineActionDivMounted(){
				
				this.data['alertName'] = 'timeline';

				this.data['ticket_id'] = this.id;
            // if i could pass data to it
            window.eventHub.$emit('timeline-action-div-mounted', this.data);
          },

          reloadData(from) {
          	
          	this.afterAction(from);
          },

         onClose() {

         	this.showEditModal = false;

				this.showAssignModal = false;

				this.showDueModal = false;

				this.showSurrenderModal = false;

				this.showDeptModal = false;

				this.showStatusModal = false;

				this.showApprovalModal = false;

				this.showForwardModal = false;

				this.$store.dispatch('unsetValidationError');
         }
		},

		components : {

			'ticket-label' : require('./Mini/TicketLabel.vue'),
          
         'ticket-tag' : require('./Mini/TicketTags.vue'),

         'edit-ticket-modal' : require('./Mini/EditTicket'),

         'surrender-modal' : require('./Mini/SurrenderModal'),

         'change-dept-modal' : require('./Mini/DepartmentModal'),

         'approval-modal' : require('./Mini/ApprovalWorkflowModal'),

         'forward-modal' : require('./Mini/ForwardModal'),

         'assign-ticket-modal': require('components/Agent/TicketsComponents/AssignTicketModal'),

			'change-status-modal': require('components/Agent/TicketsComponents/ChangeStatusModal'),

         'due-date-modal' : require('./Mini/DueDateModal'),

         'more-events' : require('./Mini/MoreEvents.vue'),

         'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style>
	
	.smaller { font-size: smaller !important; }

	.ticket-actions .btn { margin-bottom: 5px; font-size: 13px !important;}

	.ticket-actions .btn-group .btn { margin-bottom: 5px; font-size: 13px !important; }

	.mt-10 { margin-top: 10px; }

	#timeline-action-bar .btn { margin-bottom: 5px; }

	.inline-block{ display: inline-block; }
</style>