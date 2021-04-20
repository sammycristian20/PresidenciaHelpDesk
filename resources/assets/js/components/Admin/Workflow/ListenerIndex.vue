<template>

	<div>

		<alert componentName="dataTableModal" />

		<div class="card card-light">

			<div class="card-header">

				<h3 class="card-title">{{lang('listener_list')}}</h3>

				<div class="card-tools">

					<a id="create" class="btn btn-tool" :href="base+'/listener/create'" v-tooltip="lang('create_listener')">
						<span class="glyphicon glyphicon-plus"> </span> 
					</a>

						<a v-if="showTable && total_records > 1" class="btn btn-tool" href="javascript:;" @click="reorderMethod"
							v-tooltip="lang('reorder')">
							<span class="fas fa-bars"> </span></a>
					</div>

			</div>

			<div class="card-body">
				<data-table v-if="apiUrl !== '' && showTable" :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>

				<listener-reorder v-if="!showTable" :onClose="onClose" :url="apiUrl+'?type=listener&meta=true&sort=order&sort_order=asc'" reorder_type="listener">

				</listener-reorder>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import moment from 'moment';

	import momentTimezone from 'moment-timezone';
	import {mapGetters} from "vuex";

	import Vue from 'vue';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'listener-index',

		description : 'Listener table component',

		data: () => ({
			/**
			* base url of the application
			* @type {String}
			*/
			base:window.axios.defaults.baseURL,

			columns:[],

			options:{},

			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl:'api/get-enforcer-list/',

			total_records : 0,

			showTable: true,

		}),

		beforeMount(){

			const self = this;

			/**
			* columns required for datatable
			* @type {Array}
			*/
			this.columns= ['name', 'status', 'order', 'created_at', 'updated_at', 'action']

			this.options= {

				texts: {  filter: '', limit: ''},

				headings: { name: 'Name', status : 'Status',
							order : 'Order', created_at: 'Created',
							updated_at: 'Updated',action:'Action'
				},

				templates: {

					action: 'table-actions',

					status: function(createElement, row) {

			            let span = createElement('span', {
			              attrs:{
			                'class' : row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
			              }
			            }, row.status ? 'Active' : 'Inactive');

			            return createElement('a', {

			            }, [span]);
			        },

					name: 'datatable-name',
					created_at(h, row) {
						return self.formattedTime(row.created_at)
			        },
			        updated_at(h, row) {
						return self.formattedTime(row.updated_at)
					},
				},

				sortable:  ['name', 'status', 'order', 'rules', 'target','created_at', 'updated_at'],

				filterable: ['name', 'created_at', 'updated_at'],

				pagination:{chunk:5,nav: 'scroll'},

				requestAdapter(data) {
			        return {
			        	type : 'listener',
			          sort: data.orderBy ? data.orderBy : 'order',
			          sort_order: data.ascending ? 'asc' : 'desc',
			          search:data.query.trim(),
			          page:data.page,
			          limit:data.limit,
			        }
			    },

			 	responseAdapter({data}) {

			 		self.total_records = data.data.total;

					return {
						data: data.data.data.map(data => {
						data.edit_url = window.axios.defaults.baseURL + '/listener/'+data.id+'/edit' ;
						data.delete_url = window.axios.defaults.baseURL + '/api/delete-enforcer/listener/'+ data.id;
						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

		methods :{

			reorderMethod() {
	      this.showTable = false;
	      this.title = "reorder";
	    },

	    onClose() {
	      this.showTable = true;
	      this.title = "list_of_ticket_workflows";
	    }
		},

		computed:{
			...mapGetters(['formattedTime'])
		},

		components:{
			'data-table' : require('components/Extra/DataTable'),
			"listener-reorder": require("components/Admin/Workflow/Reorder.vue"),
    	'alert' : require('components/MiniComponent/Alert'),
		}

	};
</script>