<template>
	<div>
		
		<alert componentName="dataTableModal"/>

		<div class="card card-light">

			<div class="card-header">

				<h3 class="card-title" id="pack-title">{{lang('list_of_packages')}}</h3>

				<div class="card-tools">

					<a class="btn btn-tool" :href="base+'/bill/package/create'" v-tooltip="lang('create-package')">

						<span class="glyphicon glyphicon-plus"> </span> 
					</a>
					
					<a v-if="selectedData.length > 0" class="btn btn-tool" @click="deletePackage()" v-tooltip="lang('delete-package')">

						<span class="fas fa-trash"> </span> 
					</a>
				</div>
			</div>
			
			<div class="card-body">
				<!-- datatable -->
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="pack-title"  :tickets="packages"></data-table>
			</div>

			<transition name="modal">

			 	<delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="deleteUrl" ></delete-modal>
			</transition>

		</div>	
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { lang } from 'helpers/extraLogics';

	export default {

		name : 'pacakges',

		description : 'Pacakges data table component',

		props : {

		},

		data(){

			return {

				base:window.axios.defaults.baseURL,
				
				columns: ['id', 'name', 'validity', 'allowed_tickets', 'price', 'status', 'action'],

				options : {},
		
				apiUrl:'/bill/package/get-inbox-data',

				selectedData : [],

				showModal : false,

				deleteUrl : '',
			}
		},

		computed :{

		},

		watch : {

		},

		beforeMount(){
			
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
						
					status: 'data-table-status',
						
					action: 'data-table-actions',

					validity(h,row){
						
						return row.validity === null ? lang('one_time') : lang(row.validity);
					}
				},
					
				sortable:  [ 'name', 'validity', 'allowed_tickets', 'price', 'status' ],
					
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
							
							data: data.message.data.map(data => {

								data.edit_url = window.axios.defaults.baseURL + '/bill/package/'+data.id+'/edit';

								return data;
							}),
							
							count: data.message.total
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
		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),

			'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),
		}
	};
</script>