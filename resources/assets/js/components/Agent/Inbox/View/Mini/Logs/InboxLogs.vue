<template>
	
	<div id="inbox-logs">

		<alert componentName="inbox-threads"></alert>

		<div class="card card-light card-tabs">

			<div class="card-header p-0 pt-1">
			
				<ul class="nav nav-tabs" role="tablist">
					
					<li v-for="section in tabs" class="nav-item">
						
						<a class="nav-link" :class="{ active: category === section.id }" data-toggle="pill" role="tab" href="javascript:;" 
							@click="associates(section.id)">
							
							<i :class="section.icon"> </i> {{lang(section.title)}}
						</a>
					</li>

					<li class="nav-item ml-auto mt-04">
						
						<a class="btn-tool" @click="updateLogs" v-tooltip="lang('refresh')" href="javascript:;">

							<i class="fas fa-sync-alt"></i>
						</a>
					</li>
				</ul>
			</div>

			<div class="card-body">
				
				<div class="tab-content" id="inbox-logs-tab">
					
					<div v-if="!loading" class="active tab-pane" role="tabpanel">
						
						<keep-alive>
						
							<component v-bind:is="currentComponent" :ticketId="ticketId" ref="inboxLogs">
						
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

		name : 'inbox-logs',

		description : 'Inbox Logs Component',

		props : {

			ticketId : { type : String | Number, default : '' },

			ticket : { type : Object, default : ()=> {} }
		},

		data() {

			return {

				tabs:[
		  
					 {id : 'conversation', title : 'ticket_conversation', icon : 'fa fa-comments'},
				  
					 {id : 'activity', title : 'ticket_activity', icon : 'fa fa-history' }
				],

				category : 'conversation',

				loading : false
			}
		},

		computed : {

			currentComponent(){
		  	
				let option = this.category;

				const componentsArr = { 

					conversation : 'inbox-threads', 

					activity : 'inbox-ticket-activity'
				} 

				return componentsArr[option]
			}
		},

		methods : {

			updateLogs() {

				this.$refs.inboxLogs.updateLogs();
			},

			associates(category){

				this.loading = true;

				setTimeout(()=>{

					this.loading= false;
				},1000)

				this.category = category;
			}
		},

		components : {

			'inbox-threads' : require('./InboxThreads'),

			'inbox-ticket-activity' : require('./InboxTicketActivity'),

			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'alert' : require('components/MiniComponent/Alert'),
		}
	};
</script>

<style scoped>

	.load_margin { margin-top: 70px; margin-bottom: 70px; }

	#inbox-logs { position: relative; }

	#inbox-logs-tab { min-height: 200px; }

	.mt-04 { margin-top: 4px !important; }
</style>