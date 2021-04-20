<template>
	<div>
		<alert componentName="dataTableModal"/>
	<div class="card card-light ">
		<div class="card-header">
			<h3 class="card-title">{{lang('reports')}}</h3>
		</div>
		
		<div class="card-body">
			
			<data-table :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
		</div>
	</div>
</div>
</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';
	import axios from 'axios';
	import Vue from 'vue'
	import {mapGetters} from "vuex";

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));
	export default {
		
		name : 'report-exports',

		description : 'Exports table component',


		data(){
		    const self = this;

		    return {

			/**
			* base url of the application
			* @type {String}
			*/
			base:window.axios.defaults.baseURL,

			/**
			* columns required for datatable
			* @type {Array}
			*/
			columns: ['file', 'ext', 'type', 'user', 'created_at', 'action'],
				
			options: {
				texts : { filter : '', limit : ''},
				headings: { 
					file: 'Filename', ext: 'Format', type:'Type', user :'User', created_at:'Created At', action:'Action'
				},
				templates: {
					action: 'table-actions',
					user : 'data-table-user',
					created_at : (h, row) => {
						return self.formattedTime(row.created_at)
					},
				    ext(h,row){
				    	return row.ext.toUpperCase() ;
				    }
				},
				sortable:  ['file', 'ext', 'type', 'created_at'],
				filterable:  ['file', 'ext', 'type', 'created_at'],
				pagination:{chunk:5,nav: 'scroll'},
				requestAdapter(data) {
	        return {
	          sort_by: data.orderBy ? data.orderBy : 'file',
	          order: data.ascending ? 'desc' : 'asc',
	          search:data.query.trim(),
	          page:data.page,
	          per_page:data.limit,

	        }
	      },
			 	responseAdapter({data}) {
					return {
						data: data.data.data.map(data => {
						
						data.delete_url = window.axios.defaults.baseURL + '/report/api/agent/export/delete/' + data.id;

						data.download_url = window.axios.defaults.baseURL + '/report/api/agent/export/download/' + data.hash;

						return data;
					}),
						count: data.data.total
					}
				},

			},
			
			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl:'/report/api/agent/exports/',

				
		}},

		computed:{
			...mapGetters(['formattedTime'])
		},

		components:{
			'data-table' : require('components/Extra/DataTable'),
			"alert": require("components/MiniComponent/Alert"),
		} 

		
	};
</script>

<style type="text/css" scoped>
	.box-header h3{
		font-family: Source Sans Pro !important
	}
	.box.box-primary {
		padding: 0px !important;
	}
	.right{
		float: right;
	}
</style>