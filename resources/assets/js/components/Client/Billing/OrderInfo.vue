<template>
  
  <div> 
  
    <modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">
    
      <div slot="title">
        
        <h4>{{lang('order_details')}}</h4>

        <alert componentName="OrderInfo"/>
      </div>
      
      <div v-if="!renderingData" slot="fields">
        
        <div class="pad-left-right">

          <div class="well table-responsive">

            <h4>{{lang('package_info')}}</h4>
            
            <table class="table">

              <thead class="bg-light">
                
                <tr>
                  <th>{{lang('package_name')}}</th> <th>{{lang("validity")}}</th> <th>{{lang("credit_type")}}</th>

                  <th>{{lang("credit_left")}}</th> <th>{{lang("expiry_date")}}</th>
                </tr>
              </thead>
              
              <tbody>
              
                <tr class="pack_data">
              
                  <td>{{orderDetail.package.name}}</td>
                  
                  <td>{{orderDetail.package.validity ? orderDetail.package.validity :lang('one_time')}}</td>
                  
                  <td>{{orderDetail.credit_type}}</td>
                  
                  <td>{{orderDetail.credit}}</td>
                  
                  <td>{{orderDetail.expiry_date ? formattedTime(orderDetail.expiry_date) : '--'}}</td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="well table-responsive" v-if="orderDetail.order_tickets && orderDetail.order_tickets.length > 0">
              
            <h4>{{lang('ticket-details')}}</h4>
            
            <div class="ticket_table">

              <table class="table">

                <thead class="bg-light">
                  
                  <tr>
                    <th>{{lang("title")}}</th> <th>{{lang("ticket_number")}}</th> <th>{{lang("creation-date")}}</th>
                  </tr>
                </thead>
                <tbody>

                  <tr class="ticket_data" v-for="item in orderDetail.order_tickets" :key="item.id">
                  
                    <td>{{item.ticket.first_thread.title}}</td>
                  
                    <td>
                      <router-link :to="'/check-ticket/'+item.ticket.encrypted_id" target="_blank"> 

                        {{item.ticket.ticket_number}}
                      </router-link>
                    </td>
                  
                    <td>{{formattedTime(item.ticket.created_at)}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>    
        </div>
      </div> 
    
      <div v-else class="row" slot="fields" >
        
        <loader :animation-duration="4000" :size="60" :color="layout.portal.client_header_color"/>
      </div>
    </modal>
  </div>
</template>

<script>
  
  import axios from 'axios';
  
  import { errorHandler } from "helpers/responseHandler";
  
  import { mapGetters } from 'vuex'

  import { successHandler } from '../../../helpers/responseHandler';

  export default {

    props : {

      id : { type : String | Number, default : ''},

      showModal : { type : Boolean, default : false},

      onClose:{type: Function},

      layout : { type : Object, default : ()=>{}},
    },

    data(){
      
      return {
      
        selectedId: this.id,
      
        renderingData: true,
      
        orderDetail: {},

        containerStyle:{ width:'800px' },

        lang_locale : this.layout.language
      }
    },

    computed:{
    
      ...mapGetters(['formattedTime','formattedDate'])
    },

    beforeMount(){

      this.$Progress.start();

      this.getDataFromAPi();
    },

    methods: {
      
      getDataFromAPi(){
      
        axios.get('bill/package/user-order-info/' + this.selectedId).then(res => {
          
          this.orderDetail = res.data.data;
          
          this.renderingData = false;

          this.$Progress.finish();
        }).catch(err => {
          
          this.renderingData = false;
          
          errorHandler(err)

          this.$Progress.fail();
        })
      },
    },

    components: {
    
     'modal':require('components/Common/Modal.vue'),
      
      'alert' : require('components/MiniComponent/Alert'),
      
      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
    },
  };
</script>

<style scoped>
  
  .pack_data td {
    width: 20%;
    word-break: break-all;
  }

  .ticket_data td {
    width: 33%;
    word-break: break-all;
  }

  .pad-left-right {
    padding-left: 15px;
    padding-right: 15px;
  }

  #pack_details { 
    display: flex;
    padding-bottom: 9px !important;
  }

  .ticket_table{
    max-height: 200px; overflow-y: scroll; overflow-x: hidden;
  }
</style>

