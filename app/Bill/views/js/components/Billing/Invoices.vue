<template>
	<div>
		
		<alert componentName="dataTableModal"/>

		<div class="card card-light">

			<div class="card-header">

				<h3 class="card-title" id="pack-title">{{lang('invoice_list')}}</h3>

				<div class="card-tools">

					<a id="advance-filter-btn" class="btn btn-tool" @click="toggleFilterView">
						
						<i class="glyphicon glyphicon-filter"></i>
					</a>

				</div>
			</div>	

			<!-- datatable -->
			<div class="card-body">
				
				<div :style="filterStyle">
					
					<invoices-filter id="filter-box" v-if="isShowFilter" :apiChange="apiChange" :hideFilter="hideFilter" :data="hideData"> </invoices-filter>
				</div>

					<data-table v-if="apiUrl" :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="pack-title"  :tickets="packages"></data-table>
			</div>

			<transition name="modal">

				<delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="deleteUrl" ></delete-modal>
			</transition>

		</div>	
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { mapGetters } from 'vuex'

	import moment from 'moment';

	import { lang } from 'helpers/extraLogics';

	export default {

		name : 'pacakges',

		description : 'Pacakges data table component',

		props : {

		},

		data(){

			return {

				base:window.axios.defaults.baseURL,
				
				columns: ['name', 'user', 'payment_mode', 'total_amount', 'payable_amount', 'amount_paid','due_by', 'status'],

				options : {},
		
				apiUrl:'',

				selectedData : [],

				showModal : false,

				deleteUrl : '',

				isShowFilter : false,

				hideData : '',

				show : false,

				filterStyle : {}
			}
		},

		computed :{
			...mapGetters(['formattedTime','formattedDate'])
		},

		watch : {

		},

		beforeMount(){

			if(window.location.search.substring(1) === 'status=0'){
			
				this.isShowFilter = !this.isShowFilter;
			} else {

				this.apiUrl = '/bill/package/get-user-invoice?meta='+true+'&all-users='+1
			}
			const self = this;

			this.options = {
				
				headings: { 

					name: 'Name', 

					validity : 'Validity', 

					allowed_tickets : 'Incident credit',

					price : 'Price', 

					status: 'Status', 

					action:'Action'
				},
					
				texts: {
					
					filter: '',
					
					limit: ''
				},

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-down',
						
					down: 'glyphicon-chevron-up'
				},
					
				templates: {
					name: function(createElement, row) {
						
						return createElement('a', {
							
							attrs:{
								href : self.base+'/bill/package/'+row.id+'/user-invoice',
								target : '_blank'
							},
							}, 'Invoice#'+row.id);
						},
							 
						due_by(h,row) {
							return self.formattedTime(row.due_by)
						},
	
						payment_mode(h,row){
							return lang(row.payment_mode)
						},
						status: function(createElement, row) {
						
							let span = createElement('span', {
								attrs:{
								'class' : row.order.status === 1 ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
							}
						}, row.order.status === 1 ? 'Paid' : 'Unpaid');
						
						return createElement('a', {}, [span]);
						},

						user: function(createElement, row) {
						
							return createElement('a', {
								attrs:{
									href : self.base+'/user/'+row.order.user.id,
									target : '_blank'
								},
							}, row.order.user.full_name);
						},
				},
					
				sortable:  [ 'name', 'payment_mode', 'total_amount', 'payable_amount', 'amount_paid', 'due_by', 'user', 'status'],
					
				filterable:  [ 'name', 'validity', 'allowed_tickets', 'price', 'status' ],
					
				pagination:{chunk:5,nav: 'fixed',edge:true},
					
				requestAdapter(data) {
					
					return {

						'sort-field': data.orderBy ? data.orderBy : 'id',
				
						'sort-order': data.ascending ? 'desc' : 'asc',
				
						'search-query':data.query.trim(),
				
						'page':data.page,
				
						'limit':data.limit,
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

		methods : {
			packages(data){

				this.selectedData = data;
			},

			deletePackage(){

				this.deleteUrl = 'bill/package/delete?package_ids=' + this.selectedData

				this.showModal = true
			},

			onClose(){
				
				this.showModal = false;
				
				this.$store.dispatch('unsetValidationError');
			},

			toggleFilterView() {
				
				this.isShowFilter = true;

				this.show = !this.show;

				if(this.show){
					
					this.filterStyle = { display : 'block'}
				
				} else {
					
					this.filterStyle = { display : 'none'}
				}
			},

			hideFilter(data){

				this.isShowFilter = false;

				this.show = false;

				this.hideData = data;
			},

			apiChange(value){

				let baseUrlForFilter = '/bill/package/get-user-invoice?meta='+true+'&all-users='+1+'&';
				
				let params = '';	

				if(value.status){ 
					
					let val = value.status === 'unpaid' ? 0 : 1;

					params += 'status=' + val + '&';
				}

				if(value.payable_amount){

					params += 'payable_amount=' + value.payable_amount + '&';
				}

				if(value.payment_mode) { 

					value.payment_mode.forEach(function(element, index) {
						
						params +=  'payment_mode[' + index + ']=' + element.id + '&'
					});
				}

				if(value.users.length > 0) { 

					baseUrlForFilter = '/bill/package/get-user-invoice?meta='+true+'&';

					value.users.forEach(function(element, index) {
					
						params +=  'users[' + index + ']=' + element.id + '&'
					});
				}

				if(value.amount_paid){

					params += 'amount_paid=' + value.amount_paid + '&';
				}

				if(value.created_date){

					let create = value.created_date;

					let created_at_start = create[0] !== null ? moment(create[0]).format('YYYY-MM-DD+HH:mm:ss') : '';

					let created_at_end =  create[1] !== null ? moment(create[1]).format('YYYY-MM-DD+HH:mm:ss') : '';

					params += 'created_at_start=' + created_at_start + '&created_at_end=' + created_at_end + '&';
				}

				if(value.due_date){

					let due = value.due_date;

					let due_by_start = due[0] !== null ? moment(due[0]).format('YYYY-MM-DD+HH:mm:ss') : '';

					let due_by_end =  due[1] !== null ? moment(due[1]).format('YYYY-MM-DD+HH:mm:ss') : '';

					params += 'due_by_start=' + due_by_start + '&due_by_end=' + due_by_end + '&';

				}

				if(params[params.length-1] === '&') {
				
					params = params.slice(0, -1);
				}

				this.apiUrl = baseUrlForFilter + params;
			},
		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),

			'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),

			'invoices-filter': require("./InvoicesFilter")
		}
	};
</script>

<style scoped>

</style>