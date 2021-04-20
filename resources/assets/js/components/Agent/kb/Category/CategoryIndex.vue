<template>
	
	<div id="category-index">
	
		<alert componentName="dataTableModal"/>

		<div class="card card-light ">
	
			<div class="card-header">
		
				<h3 class="card-title">{{lang('all_category')}}</h3>

				<div class="card-tools">
	
					<a class="btn-tool" :href="basePath()+'/category/create'" v-tooltip="lang('create_category')">
						
						<i class="fas fa-plus"> </i>		
					</a>
				</div>
			</div>
			
			<div class="card-body" id="my_category">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="category-index"></data-table>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import {getSubStringValue} from 'helpers/extraLogics';

	import axios from 'axios';

	import Vue from 'vue';

  Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {
		
		name : 'category-index',

		description : 'Category table component',


		data: () => ({

			columns: ['name', 'description', 'status', 'display_order','action'],
				
			options: {},
	
			apiUrl:'/api/get-category-data',			
		}),

		beforeMount(){

			const self = this;

			this.options = {

				texts : { 'filter' : '', 'limit': ''},

				headings: { name: 'Name', description : 'Description', status: 'Status', display_order : 'Display Order', 
										action:'Actions'},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},
				
				templates: {
					
					description: function(createElement, row) {
						
						return createElement('div', {
							
							attrs:{
								title : row.description.replace(/(<([^>]+)>)/g, "")
							},

							domProps: {
						    innerHTML: row.description.length > 100 ? row.description.substring(0, 100)+'...' : row.description
						  },
						});
					},

					status: 'data-table-status',
				
					action: 'table-actions'
				},

				columnsClasses : {

					name: 'category-name',

					description: 'category-desc',

					action : 'category-action',

					display_order: 'category-order',

					status: 'category-status'
				},
				
				sortable:  ['name', 'description', 'status','display_order'],
				
				filterable:  ['name', 'description', 'status','display_order'],
				
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

						data.view_url = data.status ? self.basePath() + '/category-list/' + data.slug : 'javascript:;';
						
						data.edit_url = self.basePath() + '/category/' + data.id + '/edit';

						data.delete_url = self.basePath() + '/category/delete/' + data.id;

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

<style>

	.category-name,.category-desc,.category-order,.category-status,.category-action{ max-width: 250px; word-break: break-all;}

	#my_category .VueTables .table-responsive {
		overflow-x: auto;
	}

	#my_category .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>