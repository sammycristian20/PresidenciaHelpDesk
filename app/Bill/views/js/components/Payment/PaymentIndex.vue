<template>
	<div>
		
		<alert componentName="dataTableModal"/>

		<div class="card card-light">

			<div class="card-header">
				
				<h3 class="card-title" id="payment-title">{{lang('list_of_payment_gateways')}}</h3>
			</div>
			
			<div class="card-body">
				
				<data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to ="payment-title"></data-table>
			</div>

			<transition name="modal">
		
		 	<payment-settings-modal v-if="showModal" title="settings" :onClose="onClose" :showModal="showModal" :data="data">
		 	</payment-settings-modal>
		</transition>

		</div>	
	</div>
</template>

<script>
	
	import axios from 'axios';

	export default {

		name : 'payments',

		description : 'Payments data table component',

		data(){

			return {

				base:window.axios.defaults.baseURL,
				
				columns: ['name', 'gateway_name', 'is_default', 'status', 'action'],

				options : {},
		
				apiUrl:'/bill/get-gateways-list',

				showModal : false,

				data : {}
			}
		},

		beforeMount(){
			
			const self = this;

			this.options = {
					
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
						
					action: function(createElement, row) {
            
            let i = createElement('i', {
              attrs:{
               	'class' : 'fa fa-cogs'
              }
            });
            
            return createElement('button', {
              attrs:{
                class : 'btn btn-primary btn-xs'
              },
              on: {
				        click: function() {
				         	self.onClick(row)
				        }
				      }
            }, [i]);
          },
					
					is_default : 'data-table-is-default'
				},
					
				sortable:  [ 'name', 'gateway_name', 'is_default', 'status' ],
					
				filterable:  [ 'name', 'gateway_name', 'is_default', 'status' ],
					
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
							
							count: data.data.data.length
						}
					},
				}
		},

		methods : {
			onClick(data){

				this.data = data;
				
				this.showModal = true
			},

			onClose(){
				this.showModal = false;
				this.$store.dispatch('unsetValidationError');
			}
		},

		components : {

			'data-table' : require('components/Extra/DataTable'),

			"alert": require("components/MiniComponent/Alert"),

			'payment-settings-modal' : require('./PaymentSettingsModal')
		}
	};
</script>