<template>
	<faveo-box :title="lang('mail_logs')">

		<div slot="headerMenu" class="card-tools" style="cursor:pointer" id="mail_logs_table">
			<span><i class="fas fa-sync-alt" aria-hidden="true" v-tooltip="lang('refresh')" @click="refreshTable()"></i></span>
		</div>

		<div class="row">
			<dynamic-select :label="lang('sender')"
				id="sender-select-box"
				:multiple="true"
				name="sender_mails"
				:required="false"
				:prePopulate="false"
				classname="col-sm-6"
				apiEndpoint="/api/dependency/users?meta=true&supplements=true"
				:value="sender_mails"
				:onChange="onChange">
			</dynamic-select>

			<dynamic-select :label="lang('receiver')"
				id="reciever-select-box"
				:multiple="true"
				name="reciever_mails"
				:required="false"
				:prePopulate="false"
				classname="col-sm-6"
				apiEndpoint="/api/dependency/users?meta=true&supplements=true"
				:value="reciever_mails"
				:onChange="onChange">
			</dynamic-select>

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

		<div class="row mail-log-table">
			<logs-table
				id="mail_logs_table"
				v-if="!options.length"
				:category_ids="category_ids"
				:created_at_start="created_at_start"
				:created_at_end="created_at_end"
				:sender_mails="sender_mails"
				:reciever_mails="reciever_mails"
				:columns="columns"
				:options="options"
				:apiUrl="apiUrl"
				componentTitle="mailLogs">
			</logs-table>
		</div>
	</faveo-box>
</template>

<script>

	import Vue from 'vue'

	import { mapGetters } from 'vuex';
	import FaveoBox from 'components/MiniComponent/FaveoBox';


   	 Vue.component('mail-hover', require('./ReusableComponent/MailHover'));

	 Vue.component('referee-id', require('./ReusableComponent/RefereeIdComponent'));

	 Vue.component('mail-subject', require('./ReusableComponent/MailSubject'));

	 Vue.component('log-status', require('./ReusableComponent/LogStatus'));

	 import moment from 'moment';

	export default {

		data() {

			return {

				columns : [],

				options : {},

				apiUrl : '/api/logs/mail' ,

				columns: ['category', 'sender_mail', 'reciever_mail', "collaborators", 'subject', 'referee_id', 'referee_type', 'created_at', 'updated_at','status'],

				options : {},

				reciever_mails : [],

				sender_mails : [],

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

			this.declareMailComponent("sender", "sender_mail");
			this.declareMailComponent("receiver", "reciever_mail");
			this.declareMailComponent("collaborators", "collaborators");

			const self = this;

			this.options = {

				headings: {

					category: 'Category',

					subject : 'Subject',

					sender_mail: 'Sender mail',

					reciever_mail: 'Reciever mail',

					collaborators: "Collaborators",

					referee_id : 'Referee id',

					referee_type : 'Referee type',

					created_at: 'Created At',

					updated_at: 'Updated At',

					status: 'Status',
				},

				perPageValues : [10,25,50,100,200,500,1000,2000],

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},

				headingTooltips:{
				    category:'Category'
				},

				columnsClasses : {

					category: 'mail-category',

					subject : 'mail-subject',

					sender_mail: 'mail-sender',

					reciever_mail: 'mail-receiver',

					referee_id: 'mail-refree',

					referee_type: 'mail-refree-type',

					created_at: 'mail-created',

					updated_at: 'mail-updated',

					status: 'log-status'
				},

				texts: { filter: '', limit: '' },

				templates: {

					category(h,row){

						return row.category.name
					},

					created_at(h, row) {

						return self.formattedTime(row.created_at);
					},

					updated_at(h, row) {

						return self.formattedTime(row.updated_at);
					},

					sender_mail : 'sender',

					reciever_mail : 'receiver',

					referee_id : 'referee-id',

					status : 'log-status',

					subject: 'mail-subject',

					collaborators: "collaborators"
				},

				sortable:  ['category', 'referee_id', 'referee_type', 'sender_mail', 'reciever_mail', 'subject', 'source', 'created_at', 'updated_at','status'],

				filterable:  ['category', 'referee_id', ,'referee_type', 'sender_mail', 'reciever_mail', 'subject', 'source', 'created_at'],

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

		computed:{
			...mapGetters(['formattedTime','formattedDate'])
		},

		methods : {

			onChange(value,name){

				this[name] = value;

				if(name === 'created_date'){
					this.created_at_start = value[0] !== null ? moment(value[0]).format('YYYY-MM-DD+HH:mm:ss') : '';
					this.created_at_end =  value[1] !== null ? moment(value[1]).format('YYYY-MM-DD+HH:mm:ss') : '';
				}

			},

			declareMailComponent(componentName, key){
				return Vue.component(componentName, {
					props: ["data"],
					template: '<mail-hover :data="data" objectKey='+ key + '></mail-hover>'
				});
			},

			refreshTable() {
				window.eventHub.$emit('mailLogsrefreshData');
			}
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

	.mail-category{
		min-width: 110px;
		word-break: break-all;
	}
	.mail-subject{
		width:300px;
		word-break: break-all;
	}
	.mail-sender,.mail-receiver{
		max-width: 200px;
		word-break: break-all;
	}

	.mail-refree-type{
    width: 120px;
		word-break: break-all;
	}
	.mail-created{
		word-break: break-all;
	}

	.mail-refree{
		max-width: 250px;
		word-break: break-all;
	}

	.log-status{
		word-break: break-all;
	}

	.mail-log-table #logs_table .VueTables .table-responsive {
		overflow-x: scroll;
	}

	.mail-log-table #logs_table .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}



</style>
