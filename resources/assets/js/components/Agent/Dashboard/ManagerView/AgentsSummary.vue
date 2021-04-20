<template>
  
  <div class="card card-light dashboard-widget-box">
  
    <div class="card-header">
      
      <template v-if="hasDataFetched">
  
        <h3 class="card-title">
  
          <i class="fas fa-user" aria-hidden="true"> </i> {{agentsData.title}}
        </h3>
  
        <dashboard-help :helplink="agentsData.helpLink" :description="agentsData.description" />
      </template>

       <div class="card-tools">
                  
          <button type="button" class="btn btn-tool" data-card-widget="refresh" @click="refreshData()" v-tooltip="lang('refresh')">
            
            <i class="fas fa-sync-alt"></i>
          </button>
      </div>
    </div>
    <div class="card-body scrollable-area" v-if="agentsData && agentsData.data && agentsData.data.length > 0">
      <ul class="products-list product-list-in-card">
        <li class="item" v-for="(agent, aindex) in agentsData.data" :key="aindex">
          <div class="product-info">
            <div class="row">
              <div class="col-md-7">
                <div class="product-img">
                  <faveo-image-element :id=" 'user_img_' + aindex" :source-url="agent.picture" :classes="['img-circle']" alternative-text=""/>
                </div>
                <div style="padding-left: 5rem; padding-top: 1.0rem;"><span v-html="agent.title"></span></div>
              </div>
              <div class="pull-right col-md-5">
                <div v-for="(attribute, index) in agent.attributes" :key="index">
                  <div style="padding: 0.3rem;">{{attribute.key}}: <span class="attribute-value-text pull-right" v-html="attribute.value"></span></div>
                </div>
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
    <div v-if="hasDataFetched && agentsData && agentsData.data && agentsData.data.length === 0" class="no-data-section">{{lang('no_data_available')}}</div>
  </div>
</template>

<script type="text/javascript">
import axios from 'axios';
import { errorHandler } from 'helpers/responseHandler';

	export default {
		
    name : 'dashboard-agent-summary',
  
		data: () => {
			return {
        agentsData: null,
        page: 1,
        hasDataFetched: false
      }
    },
    
    beforeMount() {
      this.getDataFromServer();
    },
		
		methods: {
			getDataFromServer($state) {
        this.$store.dispatch('startLoader', this.$options.name);
        axios.get('api/agent/dashboard-report/manager/agent-analysis', { params: { page: this.page } })
        .then(response => {
          if($state) {
            if(response.data.data.length) {
              this.agentsData.push(...response.data.data);
              this.page += 1;
            } else {
              $state.complete();
            }
          } else {
            this.agentsData = response.data.data;
            this.page += 1;
          }
          this.hasDataFetched = true;
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page', this.$options.name);
        })
        .finally(() => {
          $state && $state.loaded();
          this.$store.dispatch('stopLoader', this.$options.name);
        })
      },

      refreshData() {
        this.page = 1;
        this.notificationData = [],
        this.getDataFromServer();
      }
		},

    components: {
      'faveo-image-element': require('components/Common/FaveoImageElement'),
      'dashboard-help': require('../DashboardHelp')
    }
	};
</script>

<style type="text/css" scoped>
.products-list .product-info {
  margin-left: 0px !important;
}
.product-img { padding-top: 0.5rem; }
</style>