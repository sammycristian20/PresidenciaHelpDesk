<template>
	<faveo-box :title="lang('exception_logs')">

		<div slot="headerMenu" class="card-tools" style="cursor:pointer">
			<span><i class="fas fa-sync-alt" aria-hidden="true" v-tooltip="lang('refresh')" @click="refreshTable()"></i></span>
		</div>

		<div class="row">
			<dynamic-select :label="lang('category')"
				:multiple="true"
				name="category_ids"
				:required="false"
				:prePopulate="false"
				classname="col-sm-6"
				apiEndpoint="/api/log-category-list"
				:value="category_ids"
				:onChange="onChange">
			</dynamic-select>

			<date-time-field :label="lang('created_date')"
				:value="created_date"
				type="datetime"
				:time-picker-options="timeOptions"
				name="created_date"
				:required="false"
				:onChange="onChange" range
				:currentYearDate="false"
				format="YYYY-MM-DD HH:mm:ss" classname="col-sm-6"
				:clearable="true" :editable="true" :disabled="false">
			</date-time-field>
		</div>
		
		<div class="row">
			<logs-table
				id="exception_logs_title"
				v-if="!options.length"
				:category_ids="category_ids" 
				:created_at_end="created_at_end"
				:created_at_start="created_at_start" 
				:columns="columns"
				:options="options"
				:apiUrl="apiUrl"
				componentTitle="exceptionLogs">
			</logs-table>
		</div>
	</faveo-box>
</template>

<script>

	import axios from 'axios';
	import { mapGetters } from 'vuex';
	import FaveoBox from 'components/MiniComponent/FaveoBox';
	import moment from 'moment';

	export default {

		data() {

			return {

				columns : [],

				options : {},

				apiUrl : '/api/logs/exception' ,

				columns: ['category', 'file', 'line', 'message', 'trace', 'created_at'],

				options : {},

				category_ids : [],

				timeOptions: {
					start: '00:00',
					step: '00:30',
					end: '23:30'
				},

				created_date : '',
				created_at_start : '',
				created_at_end : '',
				moment : moment,
			}
		},

		beforeMount(){
			const self = this;
			this.options = {

				headings: { 

					category: 'Category', 

					file: 'File', 

					line:'Line',

					message: 'Message',
					
					trace: 'Trace',

					created_at: 'Created At'
				},

				columnsClasses : {

					category: 'log-category', 

					file: 'log-file', 

					line:'log-line',

					trace: 'log-trace',

					message: 'log-message',

					created_at: 'log-created'
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				texts: { filter: '', limit: '' },

				templates: {

					category(h,row){

						return row.category.name
					},

					created_at(h, row) {
						
						return self.formattedTime(row.created_at)
					},

					trace: 'logs-trace',
				},

				sortable:  ['category', 'file', 'line', 'trace', 'message', 'created_at'],

				filterable:  ['category', 'file', 'line', 'trace', 'message', 'created_at'],
				
				pagination:{chunk:5,nav: 'fixed',edge:true},

				requestAdapter(data) {

					return {
					
						sort_field: data.orderBy,
					
						sort_order: data.ascending ? 'desc' : 'asc',
					
						'search-query':data.query.trim(),
					
						page:data.page,
					
						limit:data.limit,

					}
				},
				responseAdapter({data}) {
					return {
						
						data: data.data.data,

						count: data.data.total
					
					}
				},
			}
		},

		methods: {
			refreshTable() {
				window.eventHub.$emit('exceptionLogsrefreshData');
			},

			onChange(value,name){
				this[name] = value;
				if(name === 'created_date'){
					this.created_at_start = value[0] !== null ? moment(value[0]).format('YYYY-MM-DD+HH:mm:ss') : '';
					this.created_at_end =  value[1] !== null ? moment(value[1]).format('YYYY-MM-DD+HH:mm:ss') : '';
				}
			}
		},

		computed:{
			...mapGetters(['formattedTime','formattedDate'])
		},

		components : {

			'logs-table': require('./ReusableComponent/LogsTable.vue'),
			'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
			'date-time-field': require('components/MiniComponent/FormField/DateTimePicker'),
			'faveo-box' : FaveoBox
		}
	};
</script>

<style>
	
	.log-category{
		width:10% !important;
		word-break: break-word;
	}
	.log-file{
		width:30% !important;
		word-break: break-all;
	}
	.log-trace{
		width: 20% !important;
    	word-break: break-all;
	}
	.log-line{
		width: 8% !important;
    	word-break: break-all;
	}
	.log-created{
		width: 12% !important;
    	word-break: break-all;
	}
	.log-message{
		width:20% !important;
		word-break: break-all;
	}

</style>