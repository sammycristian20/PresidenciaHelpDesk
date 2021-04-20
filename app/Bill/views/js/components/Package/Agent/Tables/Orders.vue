<template>
	<div class="order-pack">
    
    <!-- <div v-if="orderIds.length > 0" class="row" id="delete_btn">
     
      <button class="btn btn-danger pull-right" @click="deleteInvoices()"> 

        <i class="fa fa-trash"> </i> {{lang('delete')}}
      </button>
    </div> -->

    <data-table :url="apiEndpoint" :dataColumns="columns"  :option="options" scroll_to="packages_title" :tickets="orderData"></data-table>

    <transition name ="modal" >
      
      <orders-modal v-if="showModal" title="order_details" :onClose="onClose" :showModal="showModal" :id="order_id">
        
      </orders-modal>
    </transition>

	</div>
</template>

<script>

  import { mapGetters } from 'vuex'

  import { lang } from 'helpers/extraLogics'

	export default {
		
    name : 'orders-table',

		description : 'Orders table page',

		props : {

    	category : { type : String, default : ''},

      apiEndpoint : { type:String , default : ''},
    },

		data() {
			return {

        base:window.axios.defaults.baseURL,
        
        options : {},

        apiUrl: this.apiEndpoint,

        columns : ['name','invoice','credit_type','credit','total_amount','expiry_date'],

        orderIds : [],

        order_id : 0,

        showModal :false
			}
		},

    beforeMount(){

      const self = this;

      this.options = {

        sortIcon: {
          
          base : 'glyphicon',
          
          up: 'glyphicon-chevron-down',
          
          down: 'glyphicon-chevron-up'
        },

        headings: { 

					due_by : 'Due date',
		    },

        columnsClasses : {

          invoice : 'order-invoice',

          credit_type : 'order-type',

          credit: 'order-credit',

          total_amount: 'order-amount',

          name: 'order-name',

          expiry_date: 'order-date',

        },

        texts : { 

          filter : '', 

          limit : ''
        },
        
        templates: {
						
          invoice: function(createElement, row) {
            
            return createElement('a', {
              attrs:{

                href : self.base+'/bill/package/'+row.id+'/user-invoice',
                target : '_blank'
              },
            }, 'Invoice#'+row.id);
          },

          credit_type(h,row){
          
            return row.credit_type
          },

          credit(h,row){
            
            return row.credit
          },

          name(createElement,row){
            
            return createElement('a', {
              attrs:{

                href : self.base+'/bill/order/'+row.id,
                
                target : '_blank'
              }
            }, row.package.name);
          },

          expiry_date(createElement,row){
            
            let currentDate = new Date();

            let expiry = self.formattedTime(row.expiry_date)

            let date;
            
            if(currentDate > expiry){

              date = createElement('span', {
                attrs:{
                   class : 'text-red'
                },
              }, self.formattedTime(row.expiry_date));
            } else {

              date = createElement('span', {
                attrs:{
                  class : 'text-green'
                },
              }, self.formattedTime(row.expiry_date));
            }

            return date
          },

          total_amount(h,row){

            return row.invoice.total_amount
          }
				},
        
        sortable:  ['credit_type','credit','total_amount','expiry_date','status'],
            
        filterable:  ['credit_type','credit','total_amount','expiry_date','status'],
        
        pagination:{chunk:5,nav: 'scroll'},

        headings: { 
          'name' : lang('package_name')
        },
        
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

		computed:{
      ...mapGetters(['formattedTime','formattedDate'])
		},

		methods :{

      orderData(data){
        
        this.orderIds = data
      },

      onClick(row){

        this.order_id = row.id;

        this.showModal = true;
      },

      onClose(){
      
        this.showModal = false;
      
        this.$store.dispatch('unsetValidationError');
      },

      deleteInvoices(){

        prompt('Are you sure to delete this','yes ')
      }
		},

		components : {
			'data-table': require('components/Extra/DataTable'),

      'orders-modal' : require('./MiniComponents/OrdersModal.vue')
		}
	};
</script>

<style type="text/css">
  .order-type{
    /*width:15% !important;*/
    word-break: break-all;
  }
  .order-credit{
    /*width:10% !important;*/
    word-break: break-all;
  }
  .order-amount{
    /*width:15% !important;*/
    word-break: break-all;
  }
  .order-name{
    /*width:20% !important;*/
    word-break: break-all;
  } 
  .order-date{
     /*width:15% !important;*/
    word-break: break-all;
  }
  .order-status{
     /*width:10% !important;*/
    word-break: break-all;
  }

  .order-pack .VueTables .table-responsive {
    overflow-x: scroll;
  }

  .order-pack .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
  }
  #delete_btn{
    margin-right: 10px !important;
  }
</style>