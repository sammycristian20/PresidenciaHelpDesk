<template>
  <div class="card card-light dashboard-widget-box">
    <div class="card-header">
      <template v-if="hasDataFetched">
        <h3 class="card-title"><i class="fas fa-desktop" aria-hidden="true"> </i> {{sysAnalysisData.title}}</h3>
        <dashboard-help :helplink="sysAnalysisData.helpLink" :description="sysAnalysisData.description" />
      </template>
      <div class="card-tools">
                  
          <button type="button" class="btn btn-tool" data-card-widget="refresh" @click="refreshData()" v-tooltip="lang('refresh')">
            
            <i class="fas fa-sync-alt"></i>
          </button>
      </div>
    </div>
    <div class="card-body scrollable-area" v-if="sysAnalysisData">
      <div v-for="(sysAnalysis, index) in sysAnalysisData.data" :key="index">
        <div class="system-analysis-list">
          <ul class="nav flex-column">
            <li v-for="(attribute, index) in sysAnalysis.attributes" :key="index">{{sysAnalysis.title}}
              <span class="float-right badge bg-blue" v-html="attribute.value"></span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>   
</template>

<script type="text/javascript">
import axios from 'axios';
import { errorHandler } from 'helpers/responseHandler';

	export default {
		
    name: 'dashboard-system-analysis',
  
		data: () => {
			return {
        sysAnalysisData: null,
        hasDataFetched: false
      }
    },
    
    beforeMount() {
      this.getDataFromServer();
    },
		
		methods:{
			getDataFromServer() {
        this.$store.dispatch('startLoader', this.$options.name);
        axios.get('api/agent/dashboard-report/system-analysis')
        .then(response => {
          this.sysAnalysisData = response.data.data;
          this.hasDataFetched = true;
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page');
        })
        .finally(() => {
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
      'dashboard-help': require('./DashboardHelp')
    }
	};
</script>

<style type="text/css" scoped>
.products-list .product-info {
  margin-left: 0px !important;
}
.system-analysis-list {
  border-bottom: 1px solid #f4f4f4;
  padding: 10px;
}
</style>