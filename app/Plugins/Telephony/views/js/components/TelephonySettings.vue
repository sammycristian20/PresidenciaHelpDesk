<template>
	<div>
	<alert componentName="dataTableModal"/>

	<div class="card card-light">
		<div class="card-header">
			<h3 class="card-title">{{lang('telephony_providers')}}</h3>
			<tool-tip :message="lang('telephony_providers_attributes_description')" size="large"></tool-tip>
		</div>
		<div class="card-body">
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
		</div>
	</div>	
	</div>
</template>
<script type="text/javascript">
	import {errorHandler, successHandler} from 'helpers/responseHandler';
	import {lang} from 'helpers/extraLogics';
	import axios from 'axios';
	import Vue from 'vue';
	Vue.component('telephony-table-actions',require('./MiniComponents/TelephonyActions'))
	export default {
		data(){
			return {

				showModal: false,

				data : { id : 0, name : ''},

				/**
				* columns required for datatable
				* @type {Array}
				*/
				columns: ['name', 'action'],

				options: {
					headings: { name: 'Name', action:'Action'},
					templates: {
						action: 'telephony-table-actions',
					},
					filterable: false,
					sortable:  [],
					pagination:{chunk:5,nav: 'scroll'},
					requestAdapter(data) {
						return {
							page:data.page,
							limit:data.limit,
						}
					},
					responseAdapter({data}) {
						return {
							data: data.data.data.map(data => {
								data.edit_modal = 'api/get-providers-list';
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
				apiUrl:'/telephony/api/get-providers-list',
			}
		},

		components:{
			"alert": require("components/MiniComponent/Alert"),
			"custom-loader": require("components/MiniComponent/Loader"),
			'data-table' : require('components/Extra/DataTable'),
			"tool-tip": require("components/MiniComponent/ToolTip")
		}
	};
</script>