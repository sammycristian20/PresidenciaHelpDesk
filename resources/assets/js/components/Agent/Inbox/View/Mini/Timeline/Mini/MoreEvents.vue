<template>
	
	<div class="btn-group">

		<button type="button" class="btn btn-default dropdown-toggle btn_more" data-toggle="dropdown">

			<i class="fas fa-cogs"> </i> {{ lang('more') }}
		</button>

		<div class="dropdown-menu">

			<a class="dropdown-item" href="javascript:;" @click="showRequesterModal = true">

				<i class="far fa-user"> </i> {{ lang('change_requester') }} 
			</a>
			
			<a class="dropdown-item" href="javascript:;" @click="showMergeModal = true">

				<i class="fas fa-cog"> </i> {{ lang('merge-ticket') }}
			</a>
			
			<a class="dropdown-item" href="javascript:;" @click="showForkModal = true">

				<i class="fas fa-code-branch"> </i> {{ lang('fork') }}
			</a>
		
			<a href="javascript:;" class="dropdown-item" @click="requestFeedback">

				<i class="far fa-star"></i>{{ lang('request-feedback') }}
			</a>
			
			<a v-if="actions.time_track_enabled" class="dropdown-item" href="javascript:;" @click="showTimeModal = true">

				<i class="far fa-clock"> </i> {{ lang('record_time') }}
			</a>
		</div>

		<transition name="modal">

			<change-requester-modal v-if="showRequesterModal" :onClose="onClose" :showModal="showRequesterModal" 
				:ticketId="ticket.id" componentTitle="timeline" :reloadDetails="reloadData">

			</change-requester-modal>
		</transition>

		<transition name="modal">
		
			<timeline-merge-modal v-if="showMergeModal" :onClose="onClose" :showModal="showMergeModal" 
				:ticketId="ticket.id" componentTitle="timeline" :reloadTickets="reloadData">

			</timeline-merge-modal>
		</transition>

		<transition name="modal">
		
			<ticket-fork-modal v-if="showForkModal" :onClose="onClose" :showModal="showForkModal" 
				:ticketId="ticket.id" componentTitle="timeline" :reloadDetails="reloadData">

			</ticket-fork-modal>
		</transition>

		<transition name="modal">
		
			<record-time-modal v-if="showTimeModal" :onClose="onClose" :showModal="showTimeModal" 
				:ticketId="ticket.id" componentTitle="timeline" :updateData="updateTimeTrack">

			</record-time-modal>
		</transition>

		<div  v-if="loading" class="row">
						
						<loader :size="60"/>
					</div>
	</div>
</template>

<script>

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import axios from 'axios';
	
	export default {
		
		props : {

			actions : { type : Object, default : ()=> {} },

			ticket : { type : Object, default : ()=> {} },

			user : { type : Object, default : ()=> {} },

			updateDetails : { type : Function }
		},

		data() {
			
			return {

				showRequesterModal : false,

				showMergeModal : false,
				
				showForkModal : false,
				
				showTimeModal : false,

				loading : false
			}
		},

		methods: {

			reloadData() {

				this.updateDetails();
			},

			updateTimeTrack() {

				window.eventHub.$emit('refreshTimeTrack')
			},

			onClose() {

				this.showRequesterModal = false;

				this.showMergeModal = false;
				
				this.showForkModal = false;
				
				this.showTimeModal = false;
			},

			requestFeedback()
			{	
				this.loading = true;

				axios.post('/api/request/rating/'+this.ticket.id).then(res=>{

					this.loading = false;

					successHandler(res,'timeline');
				}).catch(err=>{
					
					this.loading = false;

					errorHandler(err,'timeline')
				})
			}
		},

		components : {

         'change-requester-modal' : require('./ChangeRequesterModal'),

         'ticket-fork-modal' : require('./TicketFork'),

         'record-time-modal' : require('./RecordTimeModal'),

         'timeline-merge-modal': require('./TimelineMergeModal'),

         'loader': require('components/MiniComponent/Loader'),
		}
	};
</script>

<style>
	
</style>
