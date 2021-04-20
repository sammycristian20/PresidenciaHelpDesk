<template>
	
	<div>
		
		<div class="row">
			
			<div class="col-md-12">
				
				<alert componentName="org-view"/>
			</div>
		</div>

		<div v-if="!loading" class="row">

			<div class="col-md-4">
	
				<org-details :orgData="orgData" :managerList="orgManagers"></org-details>

				<template v-if="orgManagers.length > 0">
					
					<org-managers v-for="manager in orgManagers"  :key="manager.id" :manager="manager">
						
					</org-managers>
				</template>
			</div>

			<div class="col-md-8">

				<org-members v-if="orgId" :id="orgId" :name="orgData.name"></org-members>

				<org-tickets v-if="orgId" :id="orgId" :name="orgData.name"></org-tickets>

				<org-departments v-if="orgData.OrganizationDepartmentStatus" :id="orgId"></org-departments> 

				<div id="org-page-table">{{orgPageVisible()}}</div>

				<org-report v-if="orgId" :id="orgId" :name="orgData.name"></org-report>
			</div>
		</div>

		<div v-else class="row">
				
			<loader :animation-duration="4000" :size="60"/>
		</div>

	</div>
</template>

<script>

	import {getIdFromUrl} from 'helpers/extraLogics';

	import axios from 'axios'

	export default {
		
		name : 'org-view',

		description : 'Organization view page',

		data() {
			return {

				orgId : '',

				orgData : '',

				orgManagers : [],
				
				loading : true,
			}
		},

		created(){

			window.eventHub.$on('refreshOrgData',this.refreshOrgDetails)
		},

		beforeMount(){

			const path = window.location.pathname;
			
			this.orgId = getIdFromUrl(path);

			this.getInitialValues(this.orgId)
		},

		methods :{

			refreshOrgDetails(from = ''){
				
				if(from){
				
					this.loading = true;
				}
				
				this.getInitialValues(this.orgId);
			},

			getInitialValues(id){

				axios.get('/api/organization/view/'+id).then(res=>{

					this.orgData = res.data.data.organization;

					this.orgManagers = res.data.data.manager;

					this.loading = false

				}).catch(error=>{

					this.loading = false
				})
			},

			orgPageVisible(){
				
				window.eventHub.$emit('org-page-mounted',{org_id : this.orgId,'from' : 'org'});
			}
		},

		components : {

			'org-details' : require('./View/OrgDetails.vue'),

			'org-managers' : require('./View/OrgManagers.vue'),

			'org-members' : require('./View/OrgMembers.vue'),

			'org-departments' : require('./View/OrgDepartments.vue'),
			
			'org-tickets' : require('./View/OrgTickets.vue'),
			
			'org-report' : require('./View/OrgReport.vue'),
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),

			'alert' : require('components/MiniComponent/Alert'),
		}
	};
</script>

<style scoped>

	#dept_mgr{
		min-height: 120px;
	}
	.pad_0 {
		padding: 0 !important;
	}
</style>