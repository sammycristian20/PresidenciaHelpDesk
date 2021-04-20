<template>
		
	<div id="org_members">
		
		<alert componentName="OrgMembers"/>

		<div class="card card-light ">
		
			<div class="card-header">
			
				<h3 class="card-title" v-tooltip="lang('users_of') +' '+ name">{{lang('users_of')}} {{subString(name,40)}}</h3>

				<div class="card-tools">
					
					<a :href="basePath()+'/user/create'" class="btn-tool" v-tooltip="lang('create_user')">
		          
		          <i class="fas fa-plus"></i>
		        </a>
				</div>
			</div>
			
			<div class="card-body">
			
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="org_members"
					componentTitle="OrgMembers">
					
				</data-table>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { getSubStringValue } from 'helpers/extraLogics';

	import { mapGetters } from 'vuex';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'org-members',

		description : 'Organization memebers list',

		props : { 

			id : { type : String|Number , default : ''},

			name : { type : String , default : 'Organization'}
		},

		data() {

			return {

				columns: ['name','user_name', 'email', 'phone_number', 'status', 'updated_at', 'action'],

				options: {},

				apiUrl:'/org-user-list/'+this.id,
			}
		},

		computed : {

			...mapGetters(['formattedTime','formattedDate'])
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
					
					name: 'Name', 

					user_name: 'User name',

					email : 'Email', 
					
					phone_number : 'Phone', 
					
					status: 'Status', 

					updated_at : 'Last activity',
					
					action:'Actions'
				},

				columnsClasses : {
          
         	user_name: 'org-name', 

					email : 'org-email', 
					
					phone_number : 'org-phone',
        },
				
				texts: { filter: '', limit: '' },

				templates: {
					
					name: function(createElement, row) {
						
						return createElement('a', {
							attrs: {
								href: self.basePath()+'/user/' + row.id,
							}
						}, row.full_name);
					},

					user_name: function(createElement, row) {
						
						return createElement('a', {
							attrs: {
								href: self.basePath()+'/user/' + row.id,
							}
						}, row.user_name);
					},

					phone_number(h, row) {

						return row.phone_number === ' Not available' || !row.phone_number ? '---' : row.phone_number;
					},

					email(h, row) {

						return row.email ? row.email : '---';
					},

          updated_at : function(h,row){

          	return self.formattedTime(row.updated_at)
          },
					
					status : 'data-table-statuses',

					action: 'table-actions'
				},

				sortable:  ['user_name', 'email', 'phone_number', 'active', 'ban', 'updated_at'],

				filterable:  ['user_name', 'email', 'phone_number', 'active', 'ban','updated_at'],
				
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

							data.edit_url = self.basePath()+'/user/' + data.id + '/edit';
							
							data.view_url = self.basePath()+'/user/' + data.id ;

						return data;
					}),
						count: data.message.total
					}
				},
			}
		},

		methods : {

			subString(name,length = 15){
			
				return getSubStringValue(name,length)
			},
		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style>
	 .org-name,.org-email,.org-phone{ min-width: 150px;; word-break: break-all;}

	 #org_members .VueTables .table-responsive {
		overflow-x: auto;
	}

	#org_members .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
</style>