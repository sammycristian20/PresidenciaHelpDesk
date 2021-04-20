<template>
	
	<div id="pages-index">
	
		<alert componentName="dataTableModal"/>

		<div class="card card-light ">
	
			<div class="card-header">
				
				<h3 class="card-title">{{lang('list_of_pages')}}</h3>

				<div class="card-tools">
	
					<a class="btn-tool" :href="basePath()+'/page/create'" v-tooltip="lang('create_pages')">
						
						<i class="fas fa-plus"> </i>
					</a>		
				</div>
			</div>
			
			<div class="card-body" id="my_pages">

				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="pages-index"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import Vue from 'vue';

	import { mapGetters } from 'vuex'
  
  Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {
		
		name : 'pages-index',

		description : 'Pages table component',


		data: () => ({

			columns: ['name', 'status', 'created_at','action'],
			
			options: {},
			
			apiUrl:'api/get-pages-data',	
		}),

		computed:{
			
			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount(){
			
			const self = this;

			this.options = {
				
				texts : { 'filter' : '', 'limit': ''},

				headings: { name: 'Name', status: 'Status', created_at : 'Created at', action:'Actions'},
				
				columnsClasses : {

					name: 'page-name',

					action : 'page-action',

					created_at: 'page-created',

					status: 'page-status'
				},

				templates: {
				
					status: function(createElement, row) {
					
						let span = createElement('span', {
							
							attrs:{
								'class' : row.status === 1 ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
							}
						}, row.status === 1 ? 'Published' : 'Draft');
						
						return createElement('a', {}, [span]);
					},
				
					action: 'table-actions',

					created_at(h, row) {
						
						return self.formattedTime(row.created_at)
					},
				
				},
				
				sortable:  ['name', 'status','created_at'],
				
				filterable:  ['name', 'status','created_at'],
				
				pagination:{chunk:5,nav: 'scroll'},

				requestAdapter(data) {
				
					return {
				
						sort: data.orderBy ? data.orderBy : 'id',
				
						order: data.ascending ? 'desc' : 'asc',
				
						search:data.query.trim(),
				
						page:data.page,
				
						limit:data.limit,

					}
				
				},
				
				responseAdapter({data}) {
				
					return {
				
						data: data.message.data.map(data => {

						data.view_url = data.status ? self.basePath() + '/pages/' + data.slug : 'javascript:;';
						
						data.edit_url = self.basePath() + '/page/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/page/delete/' + data.id;

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
		} 
	};
</script>

<style type="text/css">

	.page-name,.page-created,.page-status,.page-action{
		max-width: 250px; word-break: break-all;
	}
	
	#my_pages .VueTables .table-responsive {
		overflow-x: auto;
	}

	#my_pages .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>