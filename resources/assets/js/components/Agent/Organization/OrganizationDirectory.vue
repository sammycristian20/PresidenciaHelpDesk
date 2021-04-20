<template>
	
	<div id="org-list">

		<alert componentName="dataTableModal"/>
		
		<div class="card card-light ">
		
			<div class="card-header">
				
				<h3 class="card-title ">{{lang('organization_list')}}</h3>

				<div class="card-tools">
					
					<a  class="btn-tool" v-tooltip="lang('create_organization')" :href="basePath()+'/organizations/create'">

						<i class="fas fa-plus"></i>
					</a>
				</div>
			</div>
				
			<div class="card-body" id="my_orgs">
						
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="org-list"></data-table>
			</div>
		</div>
	</div>
</template>

<script>
	
	import Vue from 'vue'

	import { mapGetters } from 'vuex'

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'organizations-directory',

		description : 'Organization list component',

		props : { 

		},

		data() {

			return {

				apiUrl : '/org-list-data' ,

				columns: ['name', 'phone', 'active_users_count', 'created_at', 'updated_at', 'action'],

				options : {},
			}
		},

		computed:{
    
      ...mapGetters(['formattedTime','formattedDate'])
    },

		beforeMount(){

			const self = this;
			
			this.options = {

				headings: {

					name: 'Name',

					phone: 'Phone',

					active_users_count : 'Members count',

					created_at : 'Created at',

					updated_at : 'Updated at',

					action: 'Actions',
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				columnsClasses : {

					name: 'organization-name',

					phone: 'organization-phone',

					active_users_count : 'organization-members',

					created_at : 'organization-created',

					updated_at : 'organization-updated',

					action: 'organization-action',
				},

				texts: { filter: '', limit: '' },

				templates: {

					created_at(h, row) {

						return self.formattedTime(row.created_at);
					},

					updated_at(h, row) {

						return self.formattedTime(row.updated_at);
					},

					phone(h, row) {

						return row.phone === ' Not available' ? '---' : row.phone;
					},

					name: function(createElement, row) {
						
						return createElement('a', {
							attrs: {
								href: self.basePath()+'/organizations/' + row.id,
							}
						}, row.name);
					},

					member_count: function(h, row) {
						return row.active_users_count
					},

					action : 'table-actions',
				},

				sortable: ['name', 'phone', 'active_users_count', 'created_at', 'updated_at'],

				filterable: ['name', 'phone','created_at', 'updated_at'],

				pagination:{chunk:5,nav: 'fixed',edge:true},

				requestAdapter(data) {

					return {

						'sort-by': data.orderBy,

						'order': data.ascending ? 'desc' : 'asc',

						'search-query':data.query.trim(),

						page:data.page,

						limit:data.limit,

					}
				},
				responseAdapter({data}) {
					return {

						data: data.message.data.map(data => {
							
							data.edit_url = self.basePath()+'/organizations/' + data.id + '/edit';
							
							data.view_url = self.basePath()+'/organizations/' + data.id ;

							data.delete_url = self.basePath()+'/org/delete/' + data.id;
							
							return data;
						}),
						
						count: data.message.total
					}
				},
			}
		},

		components : { 

			"data-table" : require("components/Extra/DataTable"),

			"alert": require("components/MiniComponent/Alert"),
		},
	};
</script>

<style>
	
	.organization-name,.organization-phone,.organization-created,.organization-updated,.organization-action{
		max-width: 250px; word-break: break-all;
	}
	
	#my_orgs .VueTables .table-responsive {
		overflow-x: auto;
	}

	#my_orgs .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>