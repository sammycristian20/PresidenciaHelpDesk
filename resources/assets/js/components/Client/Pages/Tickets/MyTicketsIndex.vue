<template>
	
	<div id="client-tickets-table" :class="{align1: lang_locale == 'ar'}">

		<meta-component :dynamic_title="lang('mytickets-title')"  :dynamic_description="lang('mytickets-description')" 
			:layout="layout" >
		  
	 	</meta-component>
		
		<alert componentName="dataTableModal"/>

		<div v-if="hasDataPopulated" class="nav-tabs-custom">
				
			<ul class="nav nav-tabs" :class="{tabs_align: lang_locale == 'ar'}">
					
				<li v-for="tab in tabs" class="nav-item">
					
					<a id="tickets_tab"  class="nav-link text-dark" :class="{ active: currentTabId === tab.id, float1: lang_locale === 'ar' }" 	
						href="javascript:;" data-toggle="tab" @click="tickets(tab)">
					
						<b>{{lang(tab.name)}}</b>
					
						<span class="badge badge-pill" :style="badgeStyle">{{tab.tickets_count}}</span>
					
					</a>
				</li>

				<li :class="[(lang_locale === 'ar') ? 'mr-auto' : 'ml-auto']">
							
					<ticket-status v-if="tic_status == 1" :layout="layout" :updateStatus="updateStatus" :ticketIds="ticketId">
								
					</ticket-status>
				</li>
			</ul>

			<div class="tab-content">

				<div class="active tab-pane" id="activity">
						
					<div id="my_tic">
							
						<my-tickets-table :tickets="ticketsData" :layout="layout" :key="currentTabId"
							:apiUrl="apiUrl" :loggedInId="auth.user_data.id" alert-component-name="dataTableModal">
								
						</my-tickets-table>
					</div>
				</div>			
			</div>
		</div>

		<div v-if="!hasDataPopulated || loading" class="row">

			<loader :color="layout.portal.client_header_color" :animation-duration="4000" :size="60"/>
		</div>

		<transition  name="modal">
				
			<change-status-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :ticketIds="ticketId" 
				:status="change_status" :layout="layout">
							
			</change-status-modal>
		</transition>
	</div>
</template>

<script>
	
	import axios from 'axios'

	export default {

		name : 'client-tickets',

		description : 'Client panel tickets page',

		props : { 

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}},

			ticketTabData : { type : Object , default : ()=>{}},

			from : { type : String, default : '' },

			orgId : { type : String | Number, default : '' },
		},

		data() {

			return {
			
				currentTabId : 1,

				tabs:[],
				
				loading:true,

				hasDataPopulated : false,
				
				statusList : [],
				
				ticketId:[],

				lang_locale : this.layout.language,

				tic_status : this.layout.user_set_ticket_status.status,

				badgeStyle: { backgroundColor : this.layout.portal.client_header_color },

				change_status : '',

				showModal : false,

				apiUrl : ''
			}
		},

		beforeMount(){

			this.getCount();

			this.updateApiUrl();
		},

		methods :{
			
			updateApiUrl() {

				if(this.ticketTabData && this.ticketTabData.id){

					this.tickets(this.ticketTabData)
				}
			},

			tickets(category){
				
				this.getCount();
				
				this.currentTabId = category.id;

				this.ticketId = [];

				this.apiUrl = category.api_end_point;
			},

			onClose(){
				
				this.showModal = false;
				
				this.getCount();
				
				this.$store.dispatch('unsetValidationError');
			},

			ticketsData(data){
		
				this.ticketId = data
			},

			getCount(){

				this.$Progress.start();

				let params = { limit: 'all' };
				if(this.from) {
					params['supplements[]'] = this.orgId;
				}
				
				axios.get('/api/dependency/ticket-status-tabs', { params: params }).then(res=>{

					this.hasDataPopulated = true;
					
					this.loading = false;

					this.tabs = [];
					
					this.tabs = res.data.data.status_tab_list;

					this.apiUrl = !this.apiUrl ? this.tabs[0].api_end_point : this.apiUrl;

					this.$Progress.finish();

				}).catch(error=>{

					this.hasDataPopulated = true;

					this.loading = false;

					this.$Progress.fail();
				})
			},

			updateStatus(status){

				this.showModal = true
				
				this.change_status = status;
			},
		},

		components : {
			
			'my-tickets-table' : require('./MiniComponents/ClientTicketsTable'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
			
			"alert": require("components/MiniComponent/Alert"),

			'ticket-status': require('./MiniComponents/ChangeStatus'),

			'change-status-modal' : require('./MiniComponents/ChangeStatusModal')
		}
	};
</script>

<style scoped>
	.tab-content {
		margin-top: 15px !important;
	}

	.badge{
		border-radius: 3px !important;
		color: white;
	}

	#tickets_tab {
		cursor: pointer;
	}
	#my_tic{
		padding: 10px;
	}
</style>