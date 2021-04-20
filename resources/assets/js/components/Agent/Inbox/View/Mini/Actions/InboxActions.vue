<template>
	
	<div id="inbox-actions" v-if="getTicketActions">

		<div class="card card-light card-tabs">
		
			<div class="card-header p-0 pt-1">
					
				<ul class="nav nav-tabs" role="tablist">
					
					<li v-for="section in tabs" class="nav-item">
						
						<a class="nav-link" :class="{ active: category === section.id }" data-toggle="pill" role="tab" href="javascript:;" 
							@click="associates(section.id)">
							
							<i :class="section.icon"> </i> {{lang(section.title)}}
						</a>
					</li>
				</ul>
			</div>
			
			<div class="card-body">
				
				<div class="tab-content" id="inbox-actions-tab">
					
					<div v-if="!loading" class="active tab-pane" role="tabpanel">
						
						<keep-alive>
							<component v-bind:is="currentComponent" :actions="getTicketActions" :id="ticketId"
								:updateThreads="refreshThreads" :user="user" :ccArray="ticket.collaborators" :ticket="ticket">
								
							</component>
						</keep-alive>
					</div>

					<div v-if="loading" class="load_margin">

	            	<loader :size="60" :duration="4000"></loader>
	        		</div>				
				</div>
			</div>
		</div>
	</div>
</template>

<script>

	import { mapGetters } from 'vuex';

	export default {

		name : 'inbox-actions',

		description : 'Inbox Actions Component',

		props : {

			ticketId : { type : String | Number, default : '' },

			refreshThreads : { type : Function },

			ticket : { type : Object, default : ()=> {} }
		},

		data() {

			return {

				tabs:[
		  
					 {id : 'reply', title : 'reply', icon : 'fas fa-reply-all'},
				  
					 {id : 'internal', title : 'internal_notes', icon : 'far fa-file-code' }
				],

				category : 'reply',

				user : this.ticket.user,

				loading : false
			}
		},

		created() {

			window.eventHub.$on('threadReply', this.showReply);

			window.eventHub.$on('updateActionsTab',this.forceUpd);
		},

		computed : {

			currentComponent(){
		  	
				let option = this.category;

				const componentsArr = { 

					reply : 'inbox-reply', 

					internal : 'inbox-notes', 

					progress : 'inbox-approval',  

					time : 'inbox-recorded-time'
				} 

				return componentsArr[option]
			},

			...mapGetters(['getTicketActions'])
		},

		beforeMount() {

			this.checkActions(this.getTicketActions)
		},

		watch : { 

			getTicketActions(newValue,oldValue){

				if(newValue){

					this.checkActions(newValue);
				}
			}
		},

		methods : {

			forceUpd(){

				this.$forceUpdate();
			},

			checkActions(actions) {

				if(actions){

					if(!this.tabs.some(tab => tab.id === "progress") && actions.view_approval_progress){

						this.tabs.push({id : 'progress', title : 'approval_progress', icon : 'fas fa-tasks'})
					
					} 

					if(!this.tabs.some(tab => tab.id === "time") && actions.time_track_enabled){

						this.tabs.push({id : 'time', title : 'time-track', icon : 'far fa-clock'})
					}
				}
			},

			associates(category){

				this.category = category;
			},

			showReply(thread) {

				var elmnt = document.getElementById('inbox-actions-tab');
	  			
	  			elmnt.scrollIntoView({ behavior : "smooth"});

				this.category = 'reply';

				this.user = thread.user;
			}
		},

		components : {

			'inbox-reply' : require('./Mini/InboxReply'),

			'inbox-notes' : require('./Mini/InboxNotes'),

			'inbox-approval' : require('./Mini/InboxApproval'),
			
			'inbox-recorded-time' : require('./Mini/InboxRecordedTime'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style scoped>

	.load_margin { margin-top: 70px; margin-bottom: 70px; }

	#inbox-actions { position: relative; }

	#inbox-actions-tab { min-height: 200px; }
</style>