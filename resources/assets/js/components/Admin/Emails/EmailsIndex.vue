<template>

	<div>

		<alert componentName="dataTableModal"/>

		<div class="card card-light ">

			<div class="card-header">

				<h3 id="emails-index" class="card-title">{{trans('list_of_emails')}}</h3>

				<div class="card-tools">

					<a class="btn btn-tool" :href="basePath()+'/emails/create'" v-tooltip="trans('create_email')">

						<i class="glyphicon glyphicon-plus"> </i> 
					</a>
				</div>
			</div>

			<div class="card-body" id="emails_index">

				<data-table
						url="/api/emails-list" :dataColumns="columns"
						:option="options" scroll_to ="emails-index"
				/>
			</div>
		</div>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import Vue from 'vue';

	import { mapGetters } from 'vuex';

	Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

	export default {

		name : 'emails-index',

		description : 'Emails lists table component',

		data(){

			return {

				columns: [ 'email_address', 'priority', 'department', 'help_topic', 'created_at', 'updated_at', 'actions'],

				options: {}
			}	
		},

		computed : {

			...mapGetters(['formattedTime'])
		},

		beforeMount() {

			const self = this;

			this.options = {

				sortIcon: {

					base : 'glyphicon',

					down: 'glyphicon-chevron-up',

					up: 'glyphicon-chevron-down'
				},

				columnsClasses : {

					email_address: 'email-address',

					priority: 'email-priority',

					department: 'email-dept',

					created_at: 'email-create',

					updated_at: 'email-update',

					help_topic : 'email-topic',

					actions: 'email-actions',
				},

				texts: { filter: '', limit: '', filterPlaceholder:"Search by email address" },

				templates: {

					priority(h,row) {

						return row.priority ? row.priority.name : '--'
					},

					department(h,row) {

						return row.department ? row.department.name : '--' 
					},

					help_topic(h,row) {

						return row.help_topic ? row.help_topic.name : '--' 
					},

					updated_at : function(h,row){

		          	return self.formattedTime(row.updated_at)
		         },

		         created_at : function(h,row){

		          	return self.formattedTime(row.created_at)
		         },

		         email_address: function(createElement, row) {
						
					if(row.is_default) {
						let span = createElement('span', {
							attrs:{
								'class' : 'badge badge-warning'
							}
						}, 'Default');
					
						return createElement('a', {
							attrs : {
								href : self.basePath() + '/emails/'+row.id+'/edit',
							}
						}, [row.email_address+'  ',span]);
					} else {
						
						return createElement('a', {
							attrs :{
								href : self.basePath() + '/emails/'+row.id+'/edit',
							}
						}, [row.email_address]);
					}
				},

					
					actions : 'table-actions'
				},

				sortable:  [ 'email_address', 'priority', 'department', 'help_topic', 'created_at', 'updated_at' ],

				filterable:  ['email_address'],

				pagination: { chunk:5,nav: 'fixed',edge:true },

				requestAdapter(data) {

					return {

						'sort-field' : data.orderBy ? data.orderBy : 'id',

						'sort-order' : data.ascending ? 'desc' : 'asc',

						'search-query' : data.query.trim(),

						page : data.page,

						limit : data.limit,
					}
				},

				responseAdapter({data}) {

					return {

						data: data.data.emails.map(data => {

							data.edit_url = self.basePath() + '/emails/' + data.id + '/edit';
							
							data.delete_url = self.basePath() + '/api/email-delete/' + data.id;
							
							return data;
						}),
						count: data.data.total
					}
				},
			}
		},

		components : {

			"data-table" : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),
		}
	};
</script>

<style type="text/css">
	.email-address,.email-priority,.email-dept,.email-create,.email-update,.email-actions, .email-topic
	{ max-width: 250px; word-break: break-all;}

	#emails_index .VueTables .table-responsive {
		overflow-x: auto;
	}

	#emails_index .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>
