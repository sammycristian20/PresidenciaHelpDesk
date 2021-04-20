<template>
	<div>
		
		<div class="row" v-if="hasDataPopulated === false || loading === true">

			<custom-loader :duration="loadingSpeed"></custom-loader>

		</div>

		<alert componentName="invoice"/>

		<section class="invoice" v-if="hasDataPopulated === true">
			
			<div class="invoice p-3 mb-3">
				
				<div class="row">
					
					<div class="col-12">
						
						<h4>
						  	
						  	<i class="fas fa-globe"></i> {{company_name}}

						  		<div class="dropdown float-right">
									
									<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
							 
							 			<i class="fas fa-bars"></i>
									</button>
					
									<div class="dropdown-menu dropdown-menu-right">
										
										<a id="pay" v-if="total_amount - paid_amount > 0"  href="javascript:;" class="dropdown-item" 
											@click="onSubmit()">
						            	
						            	<i class="fas fa-credit-card"></i> {{lang('add_payment')}}
						          	</a>

										<a id="unpaid" v-if="payable_amount - paid_amount <= 0" @click="showUnpaidModal = true" 
											href="javascript:;" class="dropdown-item"> 
										
											<i class="fas fa-thumbs-down"></i> {{lang('mark_as_unpaid')}} 
										</a>

										<a id="print" class="dropdown-item" @click="printInvoice('print-invoice')"> 

											<i class="fas fa-print"></i> {{lang('print')}} 
										</a>
										
										<a id="pdf" :href="base+'/invoice/pdf/' + invoice_id" class="dropdown-item"> 
											
											<i class="fas fa-download"></i> {{lang('generate_pdf')}} 
										</a>
										
										<a id="mail" @click="sendMail()" class="dropdown-item"> 

											<i class="fas fa-reply"></i> {{lang('send_mail')}} 
										</a>
									</div>
				</div>
						</h4>
					</div>
				</div>
				
				<div id="print-invoice" class="row invoice-info">
					
					<div class="col-sm-4 invoice-col">
						{{lang('from')}}
						<address>
						  <strong>{{from.name}}</strong><br>
						 <template v-if="from.address" v-html="from.address"> </template><br v-if="from.address">
						  {{lang('phone')}}: {{from.phone}}<br>
						  {{lang('website')}}: {{from.website}}
						</address>
					 </div>
					 <!-- /.col -->
					 <div class="col-sm-4 invoice-col">
						{{lang('to')}}

						<address>
						  <strong>{{to.first_name}}{{to.last_name}}</strong><br>
						  {{lang('phone')}}: {{to.phone}}<br>
						  {{lang('email')}}: {{to.email}}
						</address>
					 </div>
					 <!-- /.col -->
					 <div class="col-sm-4 invoice-col">
						<b>{{lang('invoice_details')}}</b><br>
						<br>

						<b>{{lang('invoice')}}:</b> #{{invoice_id}}<br>
						<b>{{lang('order_id')}}:</b> {{order_id}}<br>
						<b>{{lang('paid_on')}}:</b> {{formattedTime(paid_date)}}
					 </div>
					 <!-- /.col -->
				  </div>

           			<div class="card card-light">
           				
           				<div class="card-header">
           					
           					<h3 class="card-title">{{lang('product')}}</h3>
           				</div>

           				<div class="card-body">
           					
           					<div class="table-responsive">
			                  <table class="table table-striped">
			                    <thead>
			                    <tr>
			                      <th>{{lang('name')}}</th>
										<th>{{lang('description')}}</th>
										<th>{{lang('validity')}}</th>
										<th>{{lang('price')}}</th>
			                    </tr>
			                    </thead>
			                    <tbody>
			                    <tr>
			                     <td>{{packages.name}}</td>
										<td>{{packages.description}}</td>
										<td>{{packages.validity === null ? 'One time' : packages.validity}}</td>
										<td id="product_price">
											
											<number-field :label="lang('price')" :value="payable_amount" 
												name="payable_amount" classname="" :labelStyle="labelStyle"
										:onChange="onChange" type="number" :required="true">
										
									 </number-field>
										</td>	
			                    </tr>
			                    </tbody>
			                  </table>
			                </div>
           				</div>
           			</div>

           			<div class="card card-light">
					
						<div class="card-header">
					
							<h3 class="card-title">{{lang('transactions')}}</h3>
						</div>
					
						<div class="card-body">
							
							<div class="table-responsive">
							<table class="table table-striped">
			
								<thead>
						
									<tr>
										<th>{{lang('id')}}</th>
										<th>{{lang('payment_method')}}</th>
										<th>{{lang('amount_paid')}}</th>
										<th>{{lang('transacted_by')}}</th>
										<th>{{lang('pay_date')}}</th>
										<th>{{lang('status')}}</th>
									</tr>
								</thead>
						
								<tbody>
									<tr v-for="transaction in transactions">
										<td>{{transaction.transactionId}}</td>
										<td>{{transaction.payment_method}}</td>
										<td>{{transaction.amount_paid}}</td>
										<td><a :href="base+'/user/'+transaction.paid_by.id">{{transaction.paid_by.full_name}}</a></td>
										<td>{{formattedTime(transaction.created_at)}}</td>
										<td>
											<a id="status">

												<span title="Status" 
													:class="{'btn btn-success btn-xs':transaction.status !== 0,'btn btn-danger btn-xs':transaction.status === 0}" id="edit_btn">{{transaction.status ? lang('successful') : lang('fail')}}
												</span>
											</a>
										</td>		
									</tr>
								</tbody>
							</table>
						</div>
						</div>
					</div>

						<div class="card card-light">
					
						<div class="card-header">
					
							<h3 class="card-title">{{lang('payment_details')}} {{formattedTime(paid_date)}}</h3>
						</div>
					
						<div class="card-body">
							
							<div class="table-responsive">
							<table class="table">
					
							<tbody>
								
								<tr>
									<th style="width:50%">{{lang('paid_amount')}} : </th>
									<td>{{getFormattedCurrency(paid_amount, currency)}}</td>
								</tr>

								<tr>
									<th style="width:50%">{{lang('balance')}} : </th>
									<td>{{getFormattedCurrency(paid_amount - total_amount, currency)}}</td>
								</tr>
			
								<tr>
									<th>Total:</th>
									<td>{{getFormattedCurrency(total_amount, currency)}}</td>
								</tr>
								</tbody>
						</table>
					</div>

		</div>

		<div class="card-footer">
						
								<button type="button" id="update_btn" class="btn btn-primary" @click="onUpdate()">
									<i class="fas fa-sync"></i> {{lang('update')}}
								</button>
							</div>
	</div>
				</div>
	
		
							
						
		</section>

		<transition name="modal">
			
			<invoice-modal v-if="showModal" title="add_payment" :onClose="onClose" :showModal="showModal" :id="invoice_id">
				
			</invoice-modal>
		</transition>

		<transition name="modal">
			
			<unpaid-modal v-if="showUnpaidModal" title="Mark as Unpaid" :onClose="onClose" :refreshPage="refreshPage" :showModal="showUnpaidModal" :id="invoice_id">
				
			</unpaid-modal>
		</transition>
	</div>
</template>

<script>
	
	import axios from 'axios';

	import {getIdFromUrl, currencyFormatter} from 'helpers/extraLogics';

	import { errorHandler, successHandler } from "helpers/responseHandler";
	
	import { validateInvoiceSettings } from "faveoBilling/helpers/validator/invoiceRules";

	import { mapGetters } from 'vuex';

	export default{

		name : 'package-invoice',

		description : 'Package invoice page',

		props :{

		},

		data(){

			return {

				hasDataPopulated: false,

				loading : false,

				loadingSpeed: 4000,

				company_name : '',

				from : {},

				to : {},

				invoice_id : 0,

				order_id : 0,

				paid_date : '',

				packages : {},

				transactions : [],

				total_amount : 0,

				paid_amount : 0,

				labelStyle : { display : 'none'},

				showModal : false,

				base : '',

				payable_amount : '',

				showUnpaidModal : false,

				currency: null
			}
		},

		computed:{
			...mapGetters(['formattedTime','formattedDate','getUserData'])
		},

		watch :{

			getUserData(newValue,oldValue){
				this.base = newValue.system.url;
				return newValue
			}
		},

		beforeMount(){

			const path = window.location.pathname;

			this.invoiceId = getIdFromUrl(path);

			this.getData();
		},

		created(){

			window.eventHub.$on('reloadInvoiceData', this.getData);

			if(this.getUserData.system){
				this.base = this.getUserData.system.url
			}
		},

		methods :{

			getData(){

				this.loading = true;			
				
				axios.get('/bill/package/user-invoice-info/'+this.invoiceId).then(res => {

					let result = res.data.data;

					this.company_name = result.company_name;

					this.from = result.from;

					this.to = result.order.user;

					this.invoice_id = result.invoice_id;

					this.order_id = result.order_id;

					this.paid_date = result.paid_date;

					this.payable_amount = result.payable_amount;

					this.packages = result.order.package;

					this.transactions = result.transactions;

					this.total_amount = result.payable_amount;

					this.paid_amount = result.amount_paid;

					this.hasDataPopulated = true;
					
					this.currency = result.currency;

					this.loading = false;


				}).catch(err => {
					
					errorHandler(err)
					
					this.hasDataPopulated = true;
					
					this.loading = false;
				
				});
			},

			isValid() {

				const { errors, isValid } = validateInvoiceSettings(this.$data);
			
				if (!isValid) {
			
					return false;
			
				}
			
					return true;
			
			},

			onClose(){
			
				this.showModal = false;

				this.showUnpaidModal = false;
			
				this.$store.dispatch('unsetValidationError');
			},

			refreshPage(){
				console.log('claed')
				this.getData();
			},


			onChange(value,name){

				this.payable_amount = value
			},


			printInvoice(divID){
				
				var divElements = document.getElementById(divID).innerHTML;
		  
		  var newWindow=window.open('','','width=auto,height=auto');

		  newWindow.document.write("<html><head><title></title></head><body>" + divElements + "</body>");
				
				newWindow.document.close();
				
				newWindow.focus();
				
				newWindow.print();
				setTimeout(function(){
					newWindow.close();
				}, 5)
		 },

		 sendMail(){

			this.loading = true;

			axios.get('/invoice/send/'+this.invoice_id).then(res=>{
				
				this.loading = false;

				successHandler(res,'invoice');
			}).catch(error=>{
		 
				errorHandler(error,'invoice');
		 
				this.loading = false;
			})
		 },
		 
			onSubmit(){

				if(this.isValid()){

					this.showModal = true;
				}
			},

			onUpdate(){

				if(this.isValid()){

					this.loading = true;			

					const data = {};

					data['invoice_id'] = this.invoice_id;

					data['payable_amount'] = this.payable_amount;

					axios.put('/invoice/update',data).then(res=>{
					
						this.loading = false;

						this.refreshPage();

						successHandler(res,'invoice');

					}).catch(err => {
						
						this.loading = false;

						errorHandler(err,'invoice')
					})
				}
			},

			getFormattedCurrency(value, currency) {
					return currencyFormatter(value, currency, localStorage.getItem("LANGUAGE"),)
			},
		},

		components :{

			'alert' : require('components/MiniComponent/Alert'),
		
			"custom-loader": require("components/MiniComponent/Loader"),

			'number-field': require('components/MiniComponent/FormField/NumberField'),

			'invoice-modal' : require('./Tables/MiniComponents/InvoiceModal.vue'),

			'unpaid-modal' : require('./Tables/MiniComponents/UnpaidModal.vue')
		}
	};
</script>

<style scoped>

	#pdf,#print,#pay,#mail, #unpaid{
		cursor: pointer;
	}

	#product_price{
		width : 25%;
	} 

	.invoice {
    	border: unset !important;
 	}
</style>