<template>
		
	<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="org_members" 
		:color="layout.portal.client_header_color">
			
	</data-table>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import { mapGetters } from 'vuex';

	import Vue from 'vue';

	Vue.component('org-member-actions', require('components/Client/Pages/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'org-members-index',

		description : 'Organization members table component',

		props : {

			org_id : { type : String | Number, default : ''},

			layout : { type : Object, default : ()=>{}},

			auth : { type : Object, default : ()=>{}},
		},

		data() {

			return {

				base:window.axios.defaults.baseURL,

				columns: ['full_name', 'user_name', 'email', 'phone_number', 'mobile', 'updated_at', 'action'],

				options: {},

				apiUrl:'/organization/userdata/'+this.org_id,
			}
		},

		computed :{

			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount(){

			this.$Progress.start();

			const self= this;

			this.options = {

				sortIcon:{
				   
				  base:'glyphicon',
				   
				  up: 'glyphicon-chevron-down',
          
          down: 'glyphicon-chevron-up'
				},

				headings: { 
					
					user_name: 'Username',

					full_name: 'Name',

					email : 'Email', 
					
					phone_number : 'Phone', 

					mobile : 'Mobile',
					
					updated_at : 'Last activity',
					
					action:'Action'
				},

				columnsClasses : {
          
         	user_name: 'org-name', 

					email : 'org-email', 
					
					phone_number : 'org-phone',

					mobile : 'org-mobile',
        },
				
				texts: { filter: '', limit: '' },

				templates: {

          updated_at : function(h,row){

          	return self.formattedTime(row.updated_at)
          },
					
					mobile : function(h,row){

          	return row.mobile === 'Not available' || !row.mobile ? '---' : row.mobile
          },

          phone_number : function(h,row){

          	return row.phone_number ? row.phone_number : '---'
          },

          email : function(h,row){

          	return row.email ? row.email : '---'
          },

					action: 'org-member-actions'
				},

				sortable:  ['user_name', 'email', 'phone_number', 'mobile', 'updated_at'],

				filterable:  ['user_name', 'email', 'phone_number', 'mobile','updated_at'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
	      
	        return {
	      
	          'sort-field': data.orderBy ? data.orderBy : 'updated_at',
	      
	          'sort-order': data.ascending ? 'desc' : 'asc',
	      
	          'search-query':data.query.trim(),
	      
	          'page':data.page,
	      
	          'limit':data.limit,
	        }
	      },

			 	responseAdapter({data}) {

			 		self.$Progress.finish();

					return {
					
						data: data.message.data.map(data => {

							if(self.auth.user_data.id !== data.id){

								data.edit_url = { name : 'EditUser', params : { id : data.id}};

								data.deactivate_url = true;

								data.layout = self.layout;

								data.auth = self.auth
							}
						return data;
					}),
						count: data.message.total
					}
				},
			}
		},
		components:{
			
			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),
		},


	};
</script>

<style type="text/css">

	 .org-name,.org-email{ 
	 	width: 200px; 
	 	word-break: break-word;
	 }

	 .org-phone,.org-mobile{ 
	 	width: 150px; 
	 	word-break: break-word;
	 }


	 #org_members .VueTables .table-responsive {
		overflow-x: auto;
	}

	#org_members .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
</style>
