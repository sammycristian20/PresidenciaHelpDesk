<template>
	<div>
	<alert componentName="dataTableModal"/>
	<div class="card card-light">
		<div class="card-header">
			<h3 class="card-title">{{lang('directory_attributes')}}</h3>
			<tool-tip :message="lang('directory_attributes_description')" size="large"></tool-tip>

			<!--  clicking on this should call the API function instead of mount -->
			<a id="toggle-list-button" v-on:click="toggleList">
				<span v-if="minimized" class="pull-right glyphicon glyphicon-menu-down" :title="lang('expand')"></span>
				<span v-else class="pull-right glyphicon glyphicon-menu-up" :title="lang('collapse')"></span>
			</a>
		</div>
		<div class="card-body">
			<div :class="['toggle',{'toggle-expand' : hasDataPopulated}]">
				<div class="box-header">
					<div class="row">
						<div class="col-md-12" v-if="!minimized">
							<a class="btn btn-primary right" @click="showModal = true" href="javascript:;"><span class="glyphicon glyphicon-plus"> </span> {{lang('add_more')}}</a>
						</div>
					</div>
				</div>
				<data-table v-if="hasDataPopulated" :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
			</div>
		</div>
		<data-table-modal v-if="showModal" title="create" :onClose="onClose" :showModal="showModal" :apiUrl="'api/ldap/ldap-directory-attribute/' + this.ldapId" :data="data"></data-table-modal>
	</div>
</div>

</template>
<script>

import {
	errorHandler,
	successHandler
} from 'helpers/responseHandler';
import {
	lang
} from 'helpers/extraLogics';
import axios from 'axios';
import {
	mapGetters
} from "vuex";

export default {

	name: 'ldap-table',

	description: 'Ldap table component',

	props: {
		ldapId: {
			type: String,
			default: ''
		},
	},

	data() {
		return {

			showModal: false,

			data: {
				id: 0,
				name: ''
			},

			/**
			 * if the data is populated
			 * @type {Boolean}
			 */
			hasDataPopulated: false,

			/**
			 * If the list is minimized
			 * @type {Boolean}
			 */
			minimized: true,

			/**
			 * base url of the application
			 * @type {String}
			 */
			base: window.axios.defaults.baseURL,

			/**
			 * columns required for datatable
			 * @type {Array}
			 */
			columns: ['name', 'created_at', 'updated_at', 'is_default', 'action'],

			options: {},

			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl: '/api/dependency/ldap-directory-attributes/' + this.ldapId,
		}
	},

	beforeMount() {
		const that = this;
		this.options = {
			headings: {
				name: 'Name',
				created_at: 'Created At',
				updated_at: 'Updated At',
				is_default: 'Is default',
				action: 'Action'
			},
			texts: {
				filter: '',
				limit: ''
			},
			templates: {
				action: 'data-table-actions',
				is_default: 'data-table-is-default',
				created_at: (h, row) => {
					return this.formattedTime(row.created_at)
				},
				updated_at: (h, row) => {
					return this.formattedTime(row.updated_at)
				},
			},
			sortable: ['name', 'created_at', 'updated_at', 'is_default'],
			filterable: ['name', 'created_at', 'updated_at'],
			pagination: {
				chunk: 5,
				nav: 'scroll'
			},
			requestAdapter(data) {
				return {
					sort_field: data.orderBy ? data.orderBy : 'is_default',
					sort_order: data.ascending ? 'desc' : 'asc',
					search_query: data.query.trim(),
					page: data.page,
					limit: data.limit,
				}
			},
			responseAdapter({
				data
			}) {
				return {
					data: data.data.data.map(data => {

						data.edit_modal = 'api/ldap/ldap-directory-attribute/' + that.ldapId,

						data.delete_url = window.axios.defaults.baseURL + '/api/ldap/ldap-directory-attribute/' + data.id;

						data.active = (data.active == '1') ? 'active' : 'inactive';

						return data;
					}),
					count: data.data.total
				}
			},

		}
	},

	methods: {

		/**
		 * Toggles the list view
		 * @return {undefined}
		 */
		toggleList() {
			this.minimized = !this.minimized
			if (!this.minimized) {
				this.hasDataPopulated = true;
			} else {
				this.hasDataPopulated = false;
			}
		},

		onClose() {
			this.showModal = false;
			this.$store.dispatch('unsetValidationError');
		},

	},

	computed: {
		...mapGetters(['formattedTime'])
	},

	components: {
		"alert": require("components/MiniComponent/Alert"),
		"custom-loader": require("components/MiniComponent/Loader"),
		'data-table': require('components/Extra/DataTable'),
		'data-table-modal': require('components/MiniComponent/DataTableComponents/DataTableModal'),
		"tool-tip": require("components/MiniComponent/ToolTip")
	}
};
</script>
<style scoped>
	.right{
		float: right;
	}
	#toggle-list-button {
    cursor: pointer;
  }
</style>
