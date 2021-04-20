<template>
		
	<div id="list_of_recur">
		
		<alert componentName="dataTableModal"/>

		<div class="card card-light ">
		
			<div class="card-header">
			
				<h3 class="card-title">{{lang('list_of_recur')}}</h3>

				<div class="card-tools">
					
					<a :href="basePath()+panel+'/ticket/recur'" class="btn-tool" v-tooltip="lang('create_recur')">
          
		          	<i class="fas fa-plus"> </i> 
		        </a>
				</div>
			</div>
			
			<div class="card-body" id="my_recurs">

				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="list_of_recur"></data-table>
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

		name : 'recurring-tickets',

		description : 'Recurring tickets list',

		data() {

			return {

				columns: ['name', 'interval', 'start_date', 'end_date', 'action'],

				options: {},

				apiUrl:null,

				panel:null,
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

					interval : 'Interval', 

					start_date : 'Start Date',

					end_date : 'End Date',
					
					action:'Action'
				},

				columnsClasses : {
          
         	name: 'recur-name', 

					interval : 'recur-interval', 

					start_date : 'recur-start',

					end_date : 'recur-end', 
					
					action : 'recur-action', 
        },
				
				texts: { filter: '', limit: '' },

				templates: {
						
          start_date : function(h,row){

          	return self.formattedDate(row.start_date)
          },

          end_date : function(h,row){

          	return self.formattedDate(row.end_date)
          },
					
					action: 'table-actions'
				},

				sortable: ['name', 'interval', 'start_date', 'end_date'],

				filterable: ['name', 'interval', 'start_date', 'end_date'],
				
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

							data.edit_url = self.basePath() + self.panel + '/recur/' + data.id + '/edit';
							
							data.delete_url = self.basePath() + '/api/recur-delete/' + data.id ;

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
		},
		created(){
			this.panel = window.location.pathname.split('/').includes('agent') ? '/agent' : '';
			this.apiUrl = 'api' + this.panel + '/recur-list';
		}
	};
</script>

<style>

	.recur-name,.recur-start,.recur-end,.recur-interval,.recur-action{
		max-width: 250px; word-break: break-all;
	}
	
	#my_recurs .VueTables .table-responsive {
		overflow-x: auto;
	}

	#my_recurs .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>