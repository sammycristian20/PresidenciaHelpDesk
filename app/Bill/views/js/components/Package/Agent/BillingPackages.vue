<template>

	<div  id="billing-packages" class="card card-light ">
		
		<div class="card-header">
			
			<h3 class="card-title">{{ lang('packages') }}</h3>

			<div v-if="category === 'invoice'" class="card-tools">
				
				<a class="btn-tool" @click="addInvoice()" v-tooltip="lang('create_invoice')" href="javascript:;">
					
					<i class="fas fa-plus"> </i> 
				</a>
			</div>
		</div>
	
		<div class="card-body">
			
			<ul class="nav nav-tabs" role="tablist">
					
				<li v-for="section in tabs" class="nav-item">
					
					<a class="nav-link" :class="{ active: category === section.category }" data-toggle="pill" role="tab" href="javascript:;" 
						@click="packages(section.category)">

						{{lang(section.title)}} <span class="badge badge-primary">{{section.count}}</span>
					</a>
				</li>
			</ul>

			<div class="tab-content">
			
				<div class="active tab-pane">
					
					<component v-bind:is="currentTableComponent" :id="user_id" :category="category" :apiEndpoint="apiUrl">
					  
				 </component>
			
				</div>			
			</div>
		</div>

		<transition name="modal">
				 
			<order-modal v-if="showModal" :showModal="showModal" :onClose="onClose" :userId='user_id' 
				:title="lang('create_invoice')">
						
			</order-modal>
		</transition>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import { lang } from 'helpers/extraLogics';

	import {getIdFromUrl} from 'helpers/extraLogics';

	export default {

		name : 'billing-packages',

		description : 'Billing packages datatable',

		props : {

			id : { type : String | Number, default :''},
		},

		data(){

			return {

				category:'orders',

				tabs:[],
				
				user_id : '',
				
				loading:true,

				apiUrl : '',
				
				showModal : false
			}
		},

		watch : {

			category(newValue,oldValue){
				return newValue
			},
		},

		computed : {
			currentTableComponent(){
				return this.category === 'orders' ? 'orders-table' : 'invoices-table';
				},
		},	

		beforeMount(){

			const path = window.location.pathname;

			this.user_id = getIdFromUrl(path);

			this.getCount();

			this.getTableData(this.category);
		},

		methods :{

			getTableData(category){

				this.apiUrl = category === 'orders' ? '/bill/package/get-user-packages?user_id='+this.user_id : '/bill/package/get-user-invoice?users[0]='+this.user_id
			},

			packages(category){
				this.category = category
				this.getCount();
				this.getTableData(category);
			},

			getCount(){

				axios.get('/bill/package/get-all-count/'+this.user_id).then(res=>{
					this.tabs = [
						{category:'orders',title:'orders',b_class:'badge bg-orange',count:res.data.data.userpackage},
						{category:'invoice',title:'invoice',b_class:'badge bg-red',count:res.data.data.invoice},
					]
				}).catch(err=>{ this.tabs = [] })
			},
			
			onClose(){
				this.showModal = false;
				this.packages('invoice');
				this.$store.dispatch('unsetAlert');
			},

			addInvoice()
			{
				this.showModal = true;
			}
		},

		components : {

			'billing-packages-table' : require('./BillingPackagesTable'),
			
			'orders-table' : require('./Tables/Orders'),

			'invoices-table' : require('./Tables/Invoices'),

			'order-modal': require('./Tables/MiniComponents/OrdersModal'),
		},
	};
</script>