<template>
	<div class="box box-primary">

		<div class="box-header">
				
			<h2 class="box-title">{{lang('order')}}</h2>
			
		</div>

		<div class="box-body">

			<div v-if="!loading" slot="fields">
				
				<div class="callout" style="background-color: rgb(0, 191, 239); color: rgb(249, 249, 249);">

					<div class="row">

						<div class="col-md-3"><b>{{lang('package')}} : </b> {{orderData.package.name}} </div> 

						<div class="col-md-3"><b>{{lang('credit_type')}} : </b> {{orderData.credit_type}} </div> 

		        <div class="col-md-3"><b>{{lang('credit_left')}} : </b> {{orderData.credit}} </div> 

		        <div class="col-md-3"><b>{{lang('expiry_date')}}: </b> {{formattedTime(orderData.expiry_date)}} </div>
		      </div>
		    </div>
				
				<div class="row">
				
				<div class="col-md-12">
					
					<div class="invoice_box box-solid">
					
						<div class="box-body">
								
							<table class="table table-striped">
			
								<thead>
						
									<tr>
										<th>{{lang('title')}}</th>
										<th>{{lang('ticket_no')}}</th>
										<th>{{lang('created_at')}}</th>
										<th>{{lang('updated_at')}}</th>
									</tr>
								</thead>
						
								<tbody>
									<tr v-for="ticket in orderData.order_tickets">
										<td>{{ticket.ticket.first_thread.title}}</td>
										<td>{{'#'+ticket.ticket.ticket_number}}</td>
										<td>{{formattedTime(ticket.ticket.created_at)}}</td>
										<td>{{formattedTime(ticket.ticket.updated_at)}}</td>		
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div v-if="loading" class="row" slot="fields">
			
		<loader :animation-duration="4000" color="#1d78ff" :size="size"/>
	</div>
</div>
</template>

<script>
	
	import axios from 'axios' 

	import {getIdFromUrl} from 'helpers/extraLogics';

	import {mapGetters} from 'vuex';

	export default {
		
		name : 'order-details',

		description : 'Order details component',

		data:()=>({

			loading:false,

			size: 60,

			order_id : 0,

			orderData : {},
		}),

		computed : {

			...mapGetters(['formattedTime'])
		},

		beforeMount(){

			const path = window.location.pathname;

			this.order_id = getIdFromUrl(path);

			this.getData()
		},

		methods:{

			getData(){

				this.loading = true;

				axios.get('/bill/package/user-order-info/'+this.order_id).then(res=>{

					this.loading = false;

					this.orderData = res.data.data;
				}).catch(error=>{

					this.loading = false;
				})
			}
		},

		components:{
			
			'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		}
	};
</script>

<style type="text/css" scoped>
	.has-feedback .form-control {
		padding-right: 0px !important;
	}
	#H5{
		margin-left:16px; margin-bottom:18px !important;
	}
	.margin {
		margin-right: 16px !important;margin-left: 0px !important;
	}
	.label_align {
		display: block; padding-left: 15px; text-indent: -15px; font-weight: normal !important; padding-top: 6px;
	}
	.checkbox_align {
		width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
	}
	#align{
		margin-left: 15px !important
	}
</style>