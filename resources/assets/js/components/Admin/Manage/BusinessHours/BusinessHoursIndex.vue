<template>
	
	<div>
		
		<alert componentName="dataTableModal"/>

		<div class="card card-light">
		
			<div class="card-header">

				<h3 class="card-title">{{lang('list_of_business_hours')}}</h3>
				
				<div class="card-tools">

					<a class="btn btn-tool" :href="basePath()+'/sla/business-hours/create'" v-tooltip="lang('create_business_hour')">

						<i class="glyphicon glyphicon-plus"> </i>
					</a>
				</div>
			</div>

			<div class="card-body">
			
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios';

	export default {

		name : 'business-hours-index',

		description : 'Business hours table component',


		data: () => ({

			/**
			* columns required for datatable
			* @type {Array}
			*/
			columns: ['name', 'status', 'action'],

			options: {
				headings: { name: 'Name', status: 'Status', action:'Action'},
				texts: {
		          filter: '',
		          limit: ''
		        },
				templates: {
					status: 'data-table-status',
					action: 'data-table-actions'
				},
				sortable:  ['name', 'status'],
				filterable:  ['name', 'status'],
				pagination:{chunk:5,nav: 'fixed',edge:true},
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

						data.edit_url = window.axios.defaults.baseURL + '/sla/business-hours/edit/' + data.id;

						data.delete_url = window.axios.defaults.baseURL + '/sla/business-hours/delete/' + data.id;

						data.active = (data.active == '1') ? 'active' : 'inactive';

						return data;
					}),
						count: data.message.total
					}
				},

			},

			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl:'/sla/business-hours/getindex/',


		}),

		components:{
			'data-table' : require('components/Extra/DataTable'),
			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>
