<template>
  
  <div class="card card-light dashboard-widget-box">
    
    <div class="card-header with-border">
      
      <template v-if="ticketData.title">
    
        <h3 class="card-title">
          
          <i class="fas fa-exclamation-triangle text-red" aria-hidden="true"> </i> {{ticketData.title}}
        </h3>
        
        <dashboard-help :helplink="ticketData.helpLink" :description="ticketData.description" />
      </template>

      <div class="card-tools">
                  
          <button type="button" class="btn btn-tool" data-card-widget="refresh" @click="refreshData()" v-tooltip="lang('refresh')">
            
            <i class="fas fa-sync-alt"></i>
          </button>
      </div>
    </div>

    <div class="card-body scrollable-area" v-if="ticketData.data.length > 0">
      
      <ul class="products-list product-list-in-card pl-2 pr-2">
    
        <li class="item" v-for="(ticket, aindex) in ticketData.data" :key="aindex">
    
          <div class="product-info">
  
            <span class="fw-500" v-html="ticket.title"></span> &nbsp;
            <span v-if="ticket.metaData.assigned_to_me" class="badge badge-info">Assigned to me</span>
            <span v-if="ticket.metaData.reopened" class="badge badge-warning">Reopened</span>

            <div class="float-right">
              <div v-for="(attribute, index) in ticket.attributes" :key="index" class="float-right">
                <div v-if="attribute.value" :class="ticket.metaData.overdue ? 'text-danger': ''">{{attribute.key}}: <span v-html="attribute.value"></span></div>
              </div>
            </div>
          </div>
        </li>
        <infinite-loading @infinite="getDataFromServer">
          <div slot="spinner"></div>
          <div slot="no-results"></div>
           <div slot="no-more"></div>
        </infinite-loading>
      </ul>
    </div>
    <div v-if="hasDataFetched && ticketData.data.length === 0" class="no-data-section">{{lang('no_data_available')}}</div>
  </div>    
</template>

<script type="text/javascript">
import axios from 'axios';
import { errorHandler } from 'helpers/responseHandler';
import FaveoBox from 'components/MiniComponent/FaveoBox';

	export default {
		
    name : 'dashboard-require-immediate-action',
  
		data: () => {
			return {
        ticketData: {
          title: '',
          data: []
        },
        page: 1,
        hasDataFetched: false
      }
    },
    
    beforeMount() {
      this.getDataFromServer();
    },
		
		methods: {
			getDataFromServer($state, isRefresh) {
        this.$store.dispatch('startLoader', this.$options.name);
        axios.get('api/agent/dashboard-report/require-immediate-action', { params: { page: this.page } })
        .then(response => {
          this.updateData(response.data.data, $state, isRefresh);
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page');
        })
        .finally(() => {
          $state && $state.loaded();
          this.$store.dispatch('stopLoader', this.$options.name);
        })
      },

      updateData(responseData, $state, isRefresh) {
        this.ticketData.title = responseData.title;
        this.ticketData.description = responseData.description;
        this.ticketData.helplink = responseData.helpLink;
        if(isRefresh) {            
          this.ticketData.data = responseData.data;
        } else {
          this.ticketData.data.push(...responseData.data);
        }
        if(responseData.data.length == 0) {
          // mark infinite loader as complete if data length is 0
          $state && $state.complete();
        } else {
          this.page += 1;
        }
        this.hasDataFetched = true;
      },

      refreshData() {
        this.page = 1;
        this.hasDataFetched = false;
        this.getDataFromServer(undefined, true);
      }
    },

    components: {
      'dashboard-help': require('./DashboardHelp')
    }
	};
</script>

<style type="text/css" scoped>
.products-list .product-info {
  margin-left: 0px !important;
}
.fw-500 { font-weight: 500!important }
</style>