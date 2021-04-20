<template>
		
	<div id="canned_response">
		
		<alert componentName="dataTableModal"/>

		<div class="card card-light ">
		
			<div class="card-header">
			
				<h3 class="card-title">{{lang('canned_response')}}</h3>

				<div class="card-tools">
					
					<a :href="basePath()+'/canned/create'" class="btn-tool" v-tooltip="lang('create_canned_response')">
          
          			<i class="fas fa-plus"></i>
        			</a>
				</div>
			</div>
			
			<div class="card-body" id="my_canned">
			
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="canned_response"></data-table>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { mapGetters } from 'vuex';

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'canned-list',

		description : 'Canned response list',

		data() {

			return {

				columns: ['title', 'created_at', 'updated_at', 'action'],

				options: {},

				apiUrl:'api/canned/list',
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
					
					title: 'Name', 

					created_at : 'Created at', 

					updated_at : 'Updated at',
					
					action:'Action'
				},

				columnsClasses : {
          
         	title: 'canned-name', 

					created_at : 'canned-created', 

					updated_at : 'canned-updated', 

					action : 'canned-action'
        },
				
				texts: { filter: '', limit: '' },

				templates: {
						
          updated_at : function(h,row){

          	return self.formattedTime(row.updated_at)
          },

          created_at : function(h,row){

          	return self.formattedTime(row.created_at)
          },
					
					action: 'table-actions'
				},

				sortable:  ['title', 'created_at', 'updated_at'],

				filterable:  ['title', 'created_at', 'updated_at'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},
				
				requestAdapter(data) {
	      
	        return {
	      
	          'sort-field': data.orderBy ? data.orderBy : 'updated_at',
	      
	          'sort-order': data.ascending ? 'desc' : 'asc',
	      
	          'search-query':data.query.trim(),
	      
	          page:data.page,
	      
	          limit:data.limit,
	        }
	      },

			 	responseAdapter({data}) {

					return {
					
						data: data.data.data.map(data => {

							data.edit_url = self.basePath() + '/canned/' + data.id + '/edit';
							
							data.delete_url = self.basePath() + '/api/canned/delete/' + data.id ;

						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		watch : {

		},

		methods : {

		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style>
	 
	.canned-name,.canned-created,.canned-updated, .canned-action{
		max-width: 250px; word-break: break-all;
	}

	#my_canned .VueTables .table-responsive {
		overflow-x: auto;
	}

	#my_canned .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>