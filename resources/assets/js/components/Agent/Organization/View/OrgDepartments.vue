<template>
		
	<div id="org_departments">
		
		<alert componentName="OrgDepartmentsdataTableModal"/>

		<div class="card card-light ">
		
			<div class="card-header">
			
				<h3 class="card-title">{{lang('list_of_org_department')}}</h3>

				<div class="card-tools">
					
					<a class="btn-tool" @click="showModal = true" href="javascript:;" v-tooltip="lang('add_department')">
          
		          	<i class="fas fa-plus"> </i>
		        </a>
				</div>
			</div>
			
			<div class="card-body">
			
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="org_departments" 
				componentTitle="OrgDepartments">
					
				</data-table>
			</div>

			<transition name="modal">

				<org-dept-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :orgId="id">

				</org-dept-modal>
			</transition>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import Vue from 'vue';

	Vue.component('org-dept-actions', require('./MiniComponents/OrgDeptActions.vue'));

	export default {

		name : 'org-departments',

		description : 'Organization departments list',

		props : { 

			id : { type : String|Number , default : ''},
		},

		data() {

			return {

				columns: ['org_deptname', 'business_hours_id', 'org_dept_manager', 'action'],

				options: {},

				apiUrl:'/org-dept-list/'+this.id,

				showModal : false,
			}
		},

		beforeMount(){


			const self= this;

			this.options = {

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				headings: { 
					
					org_deptname : 'Organization department name', 

					business_hours_id : 'Business hours',

					org_dept_manager : 'Organization department manager',
					
					action:'Actions'
				},

				columnsClasses : {
          
         	org_deptname : 'org-dept', 

					business_hours_id : 'org-hours',

					org_dept_manager : 'org-dept-manger',
        },
				
				texts: { filter: '', limit: '' },

				templates: {
					
					business_hours_id(h,row){

						return row.business_hour ? row.business_hour.name : '---'
					},

					org_dept_manager(createElement,row){

						if(row.manager){

							return createElement('a', {
								
								attrs: {
									
									href: self.basePath()+'/user/' + row.manager.id,
								}
							}, row.manager.full_name);
						} else {

							return '---'
						}
					},

					action: 'org-dept-actions'
				},

				sortable:  ['org_deptname', 'business_hours_id', 'org_dept_manager'],

				filterable:  ['org_deptname', 'business_hours_id', 'org_dept_manager'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
	      
	        return {
	      
	          'sort-by': data.orderBy ? data.orderBy : 'updated_at',
	      
	          order: data.ascending ? 'desc' : 'asc',
	      
	          'search-query':data.query.trim(),
	      
	          page:data.page,
	      
	          limit:data.limit,
	        }
	      },

			 	responseAdapter({data}) {

					return {
					
						data: data.message.data.map(data => {
							
							data.delete_url = self.basePath()+'/delete-org-dept/' + data.id ;

							data.orgId = self.id;

						return data;
					}),
						count: data.message.total
					}
				},
			}
		},

		methods : {

			onClose(){

				this.showModal = false;

				this.$store.dispatch('unsetValidationError');
			},
		},

		components : {

			'org-dept-modal' : require('./MiniComponents/OrgDeptModal.vue'),

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style>
	 .org-dept,.org-hours,.org-dept-manager{ min-width: 150px;; word-break: break-all;}

	 #org_departments .VueTables .table-responsive {
		overflow-x: auto;
	}

	#org_departments .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
</style>