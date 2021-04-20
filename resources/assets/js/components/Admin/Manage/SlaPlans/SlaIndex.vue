<template>
	
	<div>
	
		<alert componentName="dataTableModal" />

		<div class="card card-light">
			
			<div class="card-header">
				
				<h3 class="card-title">{{lang(title)}}</h3>

				<div class="card-tools">
						
					<a class="btn btn-tool" :href="basePath()+'/sla/create'" v-tooltip="lang('create_SLA')">

						<span class="glyphicon glyphicon-plus"> </span> 
					</a>
						
					<a id="reorder" v-if="showTable && total_records > 1" class="btn btn-tool" href="javascript:;" 
						@click="reorderMethod" v-tooltip="lang('reorder')">

						<span class="fas fa-bars"> </span>
					</a>
				</div>
			</div>

			<div class="card-body" id="sla-table">
				
				<data-table v-if="apiUrl !== '' && showTable" :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="sla-table">
					
				</data-table>
				
				<sla-reorder v-if="!showTable" :onClose="onClose" :url="apiUrl+'?type=sla&meta=true&sort=order&sort_order=asc'" 
					reorder_type="sla">
			
				</sla-reorder>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from "axios";

	import { mapGetters } from 'vuex'

	import moment from 'moment';

	import Vue from 'vue'

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {
		
		name: "sla-index",

		description: "SLA table component",

		data(){

			return {

				columns: [ "name", "status", "order", "created_at", "updated_at", "action"],

				options: {},

				apiUrl: "api/get-enforcer-list/",

				title: "list_of_SLA_plans",

				showTable: true,

				total_records : 0,
			}
		},

		computed :{
		
			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount() {

			const self = this;

			this.options = {
			
				texts: { filter: "", limit: "" },
	
				headings: {
					
					created_at: "Created",
					
					updated_at: "Updated",
				},

				columnsClasses : {

					name: 'sla-name',

					status : 'sla-status',

					order : 'sla-order',

					updated_at : 'sla-updated',

					created_at : 'sla-created'
				},

				templates: {
				
					action: "table-actions",

					status: function(createElement, row) {
						
						let span = createElement('span', {
						
							attrs:{
							
								'class' : row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
							}
						}, row.status ? 'Active' : 'Inactive');
									
						return createElement('a', {}, [span]);
					},

					created_at(h, row) {

						return self.formattedTime(row.created_at)
					},

					updated_at(h, row) {
						
						return self.formattedTime(row.updated_at)
					},
				},
				
				sortable: [ "name", "status", "order", "created_at", "updated_at"],

				filterable: ["name", "created_at", "updated_at"],
				
				pagination: { chunk: 5, nav: "scroll" },
				
				requestAdapter(data) {
					
					return {
						
						type: "sla",
						
						sort: data.orderBy ? data.orderBy : "order",
						
						sort_order: data.ascending ? "asc" : "desc",
						
						search: data.query.trim(),
						
						page: data.page,
						
						limit: data.limit
					};
				},

				responseAdapter({ data }) {  

					self.total_records = data.data.total;      
				
					return {
						
						data: data.data.data.map(data => {
							
							data.edit_url = self.basePath() + "/sla/" + data.id + '/edit';
							
							data.delete_url = self.basePath() + "/api/delete-enforcer/sla/" + data.id;
							
							return data;
						}),
						
						count: data.data.total
					};
				}
			};
		},

		methods: {
			
			reorderMethod() {
				
				this.showTable = false;
				
				this.title = "reorder";
			},
			
			onClose() {
				
				this.showTable = true;
				
				this.title = "list_of_SLA_plans";
			}
		},

		components: {
			
			"data-table": require("components/Extra/DataTable"),
			
			"sla-reorder": require("components/Admin/Workflow/Reorder.vue"),
			
			'alert' : require('components/MiniComponent/Alert'),
		}
	};
</script>

<style>

	.sla-name,.sla-order,.sla-status,.sla-created,.sla-updated{
		max-width: 250px; word-break: break-all;
	}
	
	#sla-table .VueTables .table-responsive {
		overflow-x: auto;
	}

	#sla-table .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>
