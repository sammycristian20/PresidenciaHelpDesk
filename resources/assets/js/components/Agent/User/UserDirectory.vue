<template>

	<div id="user_list">

		<alert componentName="dataTableModal" />

		<div class="card card-light ">

			<div class="card-header">

				<h3 class="card-title">{{ trans('list_of_users') }}</h3>

				<div class="card-tools">
					
					<a  class="btn btn-tool" v-tooltip="trans('create_user')" :href="basePath()+'/user/create'">

						<i class="fas fa-plus"></i>
					</a>

					<a class="btn btn-tool" :href="basePath()+'/user-export'" v-tooltip="trans('export')">

						<i class="fas fa-paper-plane"></i>
					</a>

					<button class="btn btn-tool" @click="toggleFilterView" href="javascript:;" v-tooltip="trans('filter')">
							
						<i class="fas fa-filter"></i>
					</button>
				</div>
			</div>

			<div class="card-body" id="my_user">

				<advance-filter id="filter-box" v-show="isShowFilter" :metaData="filterOptions"
					@selectedFilters="selectedFilters">
				</advance-filter>

				<data-table :url="apiUrl" :dataColumns="columns" :option="options" scroll_to="user_list"></data-table>
			</div>
		</div>
	</div>
</template>

<script>

	import Vue from 'vue'

	import { mapGetters } from 'vuex'

	Vue.component('user-table-actions', require('./UserTableActions.vue'));

	export default {

		name : 'user-directory',

		description : 'User list component',

		props : {

		},

		data() {

			return {

				isShowFilter: false,

				apiUrl:'api/admin/get-users-list?roles[0]=user&roles[1]=agent&active=1',

				columns: ['name', 'user_name', 'email', 'phone', 'info', 'last_login_at', 'organizations', 'action'],

				options : {},

				filterOptions: [
					{ name: 'userType',
						url: '',
						elements: [
							{id: 'all', name: 'All', queryPerm: 'roles[0]=user&roles[1]=agent'},
							{id: 'active_users', name: 'Active users', queryPerm: 'roles[0]=user&active=1'},
							{id: 'deactivated_users', name:'Deactivated users', queryPerm:'roles[0]=user&active=0'},
							{id: 'all_users', name: 'All users', queryPerm: 'roles[0]=user'},
							{id: 'active_agents', name: 'Active agents', queryPerm: 'roles[0]=agent&active=1'},
							{id: 'deactivated_agents', name: 'Deactivated agents', queryPerm: 'roles[0]=agent&active=0'},
							{id: 'all_agents', name:'All agents', queryPerm:'roles[0]=agent'},
							{id: 'all_active', name: 'Activate Users and Agents', queryPerm: 'roles[0]=user&roles[1]=agent&active=1'},
							{id: 'all_deactive', name: 'Dectivated Users and Agents', queryPerm: 'roles[0]=user&roles[1]=agent&active=0'},
						],
						isMultiple: false,
						isPrepopulate: false,
						label: 'user_type',
						value: {id: 'active', name: 'Active users', queryPerm: 'roles[0]=user&roles[1]=agent&active=1'},
						className: 'col-sm-3',
						strlength : 25
					},
					{ name: 'departments',
						url: 'api/dependency/departments',
						elements: [],
						isMultiple: true,
						isPrepopulate: false,
						label: 'departments',
						value: '',
						className: 'col-sm-3',
						strlength : 25
					},
					{ name: 'teams',
						url: 'api/dependency/teams',
						elements: [],
						isMultiple: true,
						isPrepopulate: false,
						label: 'teams',
						value: '',
						className: 'col-sm-3',
						strlength : 25
					},
					{ name: 'organizations',
						url: 'api/dependency/organizations',
						elements: [],
						isMultiple: true,
						isPrepopulate: false,
						label: 'organizations',
						value: '',
						className: 'col-sm-3',
						strlength : 25
					},
				],

			}
		},

		computed:{

			...mapGetters(['formattedTime','formattedDate'])
		},

		beforeMount(){

			const self = this;

			this.options = {

				headings: {

					name: 'Name',

					user_name : 'User name',

					email: 'Email',

					phone: 'Phone',

					info : 'Account info',

					last_login_at : 'Last login',

					// role: 'Role',

					organizations: 'Organization(s)',

					action: 'Action',
				},

				sortIcon: {

					base : 'glyphicon',

					up: 'glyphicon-chevron-down',

					down: 'glyphicon-chevron-up'
				},

				columnsClasses : {

					name: 'user-name',

					user_name : 'user-user',

					email: 'user-email',

					phone: 'user-phone',

					info : 'user-status',

					last_login_at : 'user-login',

					// role: 'user-role',

					action: 'user-action',
				},

				texts: { filter: '', limit: '' },

				templates: {

					last_login_at(h, row) {
						return self.formattedTime(row.last_login_at);
					},

					phone(h, row) {

						return row.phone === ' Not available' || row.phone === ' ' || !row.phone ? '--' : row.phone;
					},

					email(h, row) {

						return row.email ? row.email : '--';
					},

					// role: function(createElement, row) {

					// 	let span = createElement('span', {
					// 		attrs:{
					// 			'class' : 'fas fa-user',
					// 			'style' : row.role === 'user' ? 'color:red': 'color:green',
					// 		}
					// 	}, ' '+row.role);

					// 	return createElement('a', {
					// 		attrs:{
					// 			'class' : 'btn btn-xs btn-default text-capitalize'
					// 		}
					// 	}, [span]);
					// },

					name: function(createElement, row) {

						return createElement('a', {
							attrs: {
								href: self.basePath() + '/user/' + row.id,
							}
						}, row.name);
					},

					user_name: function(createElement, row) {

						return createElement('a', {
							attrs: {
								href: self.basePath() + '/user/' + row.id,
							}
						}, row.user_name);
					},

					info : 'data-table-statuses',

					action: 'user-table-actions',

					organizations: 'table-list-elements'
				},

				sortable: ['name', 'user_name', 'email', 'last_login_at', 'role'],

				filterable: ['name', 'user_name', 'email', 'phone', 'last_login_at', 'role','organizations'],

				pagination:{chunk:5,nav: 'fixed',edge:true},

				requestAdapter(data) {

					return {

						'sort-field': data.orderBy,

						'sort-order': data.ascending ? 'desc' : 'asc',

						'search-query':data.query.trim(),

						page:data.page,

						limit:data.limit,

					}
				},
				responseAdapter({data}) {
					return {

						data: data.data.users.map(data => {

							data.edit_url = self.basePath() + '/user/' + data.id + '/edit';

							data.view_url = self.basePath() + '/user/' + data.id ;

							data.listElementObj = {
								key: 'organizations',
								redirectUrl: self.basePath() + '/organizations/'
							}
							
							if(data.role !== 'user'){

								data.agent_view_url = self.basePath() + '/agent/' + data.id ;	
							}

							return data;
						}),

						count: data.data.total
					}
				},
			}
		},

		methods: {

			selectedFilters(value){

				if(value === 'closeEvent') {

					return this.isShowFilter = false;
				}
				if(value === 'resetEvent') {

					return this.resetFilter();
				}

				return this.applyFilter(value)
			},

			resetFilter(){

				this.apiUrl = 'api/admin/get-users-list?roles[0]=user&roles[1]=agent';

				this.filterOptions = this.filterOptions.map(function (obj) {

					obj.value = '';

					return obj;
				});
			},

			applyFilter(value){

				let baseUrlForFilter = '/api/admin/get-users-list?';

				let params = '';

				if(value.userType) {

					this.filterOptions[0].value = value.userType;

					params = value.userType.queryPerm + '&';
				} else {
					params = 'roles[0]=user&roles[1]=agent&'
				}

				if(value.departments) {

					params = this.commonMethod(1,'dept-ids',value.departments,params);
				}

				if(value.teams) {

					params = this.commonMethod(2,'team-ids',value.teams,params);
				}

				if(value.organizations) {

					params = this.commonMethod(3,'org-ids',value.organizations,params);
				}

				if(params[params.length-1] === '&') {

					params = params.slice(0, -1);
				}

				this.apiUrl = baseUrlForFilter + params;

				this.isShowFilter = false;
			},

			commonMethod(i,key,value,param){

				var params;

				params = param;

				this.filterOptions[i].value = value;

				value.forEach(function(element, index) {

					params +=  key + '[' + index + ']=' + element.id + '&'
				});

				return params
			},

			toggleFilterView() {

				this.isShowFilter = !this.isShowFilter;
			},
		},

		components : {

			"data-table" : require("components/Extra/DataTable"),

			"alert": require("components/MiniComponent/Alert"),

			'advance-filter': require("components/Extra/AdvanceFilter")
		},
	};
</script>

<style>

	.user-name,.user-user,.user-name,.user-email,.user-phone,.user-login{ max-width: 250px; word-break: break-all;}

	.user-role { text-transform: capitalize; }
	
	#my_user .VueTables .table-responsive {
		overflow-x: auto;
	}

	#my_user .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>