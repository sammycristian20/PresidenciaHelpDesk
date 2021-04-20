<template>
	
	<tr :class="{'unanswered': ticket.isanswered == 0, 'active_ticket' : getStoredTicketId == ticket.id && timelineId}" v-bs-popover>

		<td class="priority-border" :style="{background: ticket.priority.priority_color+' !important'}" 
			v-tooltip="lang('priority_is')+ticket.priority.name">

		</td> 

		<td id="ticket_user">
			
			<div class="user-image">

				<faveo-image-element :id="'user_img_'+ticket.id" :source-url="ticket.from.profile_pic" alternative-text="User"/>

				<input :id="ticket.id" type="checkbox" name="check-row" class="checkbox" v-model="checked"> 
													
				<label :for="ticket.id" class=""></label>
			</div>
		</td> 

		<td class="name td-ticket-details" @click="onRowClick(ticket.id)" @click.ctrl="redirectToTab(ticket.id)">

			<div class="ticket_subject">

				<h3 class="ticket-description-tip overdue_ticket break-word">
					
					<ticket-popover :details="ticket" :tableHeader="color" :onTicketClick="ticketClicked" ref="ticketPopover">
						
					</ticket-popover>									
				</h3> 

				<div class="info-data">

					<div class="information ellipsis">

						<span>{{lang('from')}}:</span>

						<user-info :from="ticket.from" :tableHeader="color"></user-info>&nbsp;

						<span>{{lang('assigned_to')}}:</span> 

						<user-info v-if="ticket.assigned_team.length === 0" :from="ticket.assigned" :tableHeader="color">
							
						</user-info>
						
						<a v-else style="color:#3c8dbc">{{lang('team')+' '+ticket.assigned_team.name}}</a>&nbsp;

						<span> Department:</span> 

						<span style="color:#3c8dbc">{{ticket.department.name}}</span> 

						<div class="ticket-list-custom-box">
							<!-- custom details -->
							<span style="display:block"  v-for="field in ticket.extra_fields">
							
							<span>{{ field.label }}: </span>
							
							<span style="color:#3c8dbc" v-html="field.value"/></span>

						</div>
					</div>
				</div>
			</div>
		</td> 
		
		<td class="td-ticket-status hideForList">

			<span class="badge badge-danger text-xs" v-show="ticket.is_overdue">{{ lang('overdue') }}</span>

			<span class="badge badge-warning text-xs" v-show="ticket.due_today">{{ lang('due_today') }}</span>
		</td> 

		<td class="source source-icon hideForList">

			<div class="status-source source-detailed-phone" v-tooltip="lang('ticket_is_generated_via')+ticket.source.name">
				
				<i :class="ticket.source.css_class" id="ticket-source-icon"></i>
			</div>
		</td> 
		
		<td class="source-icon hideForList">
												
			<div class="status-source source-detailed-phone" v-tooltip="lang('ticket_is')+ticket.status.name">

				<i :class="ticket.status.icon" id="ticket-source-icon" :style="{color:ticket.status.icon_color}"></i>
			</div>
		</td> 
		
		<td  class="assign hideForList">
												
			<ul style="padding: 0px; margin: 0px; list-style: none; font-size: medium;">
													
				<li>{{ lang('created') }}:
					
					<span class="emphasize">{{countUpForCreated}}</span>
				</li>

				<li>{{ lang('updated') }}:
					
					<span class="emphasize">{{countUpForUpdated}}</span>
				</li>

				<li v-if="ticket.is_overdue">{{ lang('overdue') }}:
					
					<span class="emphasize">{{countUpForOverdue}}</span>
				</li>

				<li v-if="!ticket.is_overdue">{{ lang('due_in') }}
					
					<span class="emphasize">{{countdownForDuedate ? countdownForDuedate : '---'}}</span>
				</li>
			</ul>
		</td>
	</tr>
</template>

<script>
	
	import { mapGetters } from 'vuex';

	import { Popover }  from 'components/directive/popover.js';

	export default {

		name : 'ticket-details',

		description : 'Ticket details component',

		props : {

			color : { type : String, default :''},

			ticket : { type : Object, default : ()=>{}},

			appendSelect : { type : Function},

			onSelect : { type : Function },

			timelineId : { type : String | Number, default : '' }
		},

		data() {

			return {

				countdownForDuedate:'',

				countUpForCreated:'',
				
				countUpForUpdated:'',
				
				countUpForOverdue:'',

				checked: false,
			}
		},

		computed : {

			...mapGetters(['formattedTime','timeDiffForPast','timeDiffForFuture','getStoredTicketId']),
		},

		watch : {

			checked:function(val){
				
				this.appendSelect(this.ticket.id, val);
			},
		},

		beforeMount(){

			window.eventHub.$on('selectTicket', this.selectAllTicket);

			window.eventHub.$on("unCheckTicket", this.unCheck);
		},

		mounted() {
			this.timerActions()
		},

		methods : {

			onRowClick(id) {

				this.$refs.ticketPopover.onClick(id)
			},

			redirectToTab(id) {
				this.$refs.ticketPopover.redirectMethod(id)
			},

			timerActions() {
				if(this.ticket.is_overdue) {
					this.updateCountUp('duedate');
				}
				else {
					this.updateCountDownForDueDate();
				}
				this.updateCountUp('created_at');
				this.updateCountUp('updated_at');
			},

			ticketClicked(id) {

				this.onSelect(id);
			},

			selectAllTicket(x){
				
				this.checked=x;
			},
			
			unCheck(){
				 
				this.checked = false;
			},

			updateCountDownForDueDate(){

				this.countdownForDuedate = this.timeDiffForFuture(this.ticket.duedate);
								
				if(this.countdownForDuedate === 0){
					
					this.ticket.is_overdue = true
					
					this.ticket.due_today = false
					
					this.updateCountUp('duedate');
				}
				else{

					const timer = setTimeout(() => { this.updateCountDownForDueDate(this.ticket.duedate); }, 1000);
				}
			},

			updateCountUp(dateType){
				
				switch(dateType){
					
					case 'duedate':
						
						this.countUpForOverdue = this.timeDiffForPast(this.ticket[dateType]);
						break;

					case 'created_at':
					
						this.countUpForCreated = this.timeDiffForPast(this.ticket[dateType]);
						break;

					case 'updated_at':
					
						this.countUpForUpdated = this.timeDiffForPast(this.ticket[dateType]);
						break;

					default:
					
						return;
				}
				var self = this;
					
				setTimeout(
						
					function(){
							
						self.updateCountUp(dateType);
				}, 10000);
			},
		},

		components :{

			'user-info' : require('./UserInfo'),

			'ticket-popover' : require('./TicketPopover'),
			
			'faveo-image-element': require('components/Common/FaveoImageElement')
		}
	};
</script>

<style scoped>

	.inbox-subject{
		font-weight: 600;
	}

	.unanswered td{
		
		background: #f4f4f4 !important;
	}

	.active_ticket td { box-shadow: 8px 1px 5px 5px #bbbbbb;position: relative; }
	
	.unanswered .ticket-description-tip{
		
		font-weight: 600;
	}

	#ticket_user{
		width : 7%;
	}

	.fs-unset { font-size: unset !important; cursor: text; }
</style>