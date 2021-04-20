<template>
  
  <div class="card card-light dashboard-widget-box">
  
    <div class="card-header">
      
      <template v-if="hasDataFetched">
  
        <h3 class="card-title">
          <i class="fas fa-sitemap" aria-hidden="true"> </i> {{departmentSummary.title}}
        </h3>
        
        <dashboard-help :helplink="departmentSummary.helpLink" :description="departmentSummary.description" />
      </template>
      
      <div class="card-tools">
                  
          <button type="button" class="btn btn-tool" data-card-widget="refresh" @click="refreshData()" v-tooltip="lang('refresh')">
            
            <i class="fas fa-sync-alt"></i>
          </button>
      </div>
    </div>
    
    <div class="card-body scrollable-area" v-if="departmentSummary && departmentSummary.data && departmentSummary.data.length > 0">

      <div class="card card-widget widget-user-2 shadow-0" v-for="(department, aindex) in departmentSummary.data" :key="aindex">
        
        <div class="widget-user-header" style="padding: 0px;">
        
          <h3 class="widget-user-username widget-name">{{department.title}}</h3>
        </div>
        
        <div class="card-footer p-0">
        
          <ul class="nav flex-column bg-white">
        
            <li  class="nav-item" v-for="(attribute, index) in department.attributes" :key="index">

              <a class="nav-link">{{attribute.key}} 

                <span class="float-right badge bg-primary font-14 pad-0"  v-html="attribute.value"></span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div v-if="hasDataFetched && departmentSummary && departmentSummary.data && departmentSummary.data.length === 0" class="no-data-section">{{lang('no_data_available')}}</div>
  </div>
</template>

<script type="text/javascript">
import axios from 'axios';
import { errorHandler } from 'helpers/responseHandler';

	export default {
		
    name : 'dashboard-department-summary',
  
		data: () => {
			return {
        departmentSummary: null,
        hasDataFetched: false
      }
    },
    
    beforeMount() {
      this.getDataFromServer();
    },
		
		methods: {
			getDataFromServer() {
        this.$store.dispatch('startLoader', this.$options.name);
        axios.get('api/agent/dashboard-report/manager/department-analysis')
        .then(response => {
          this.departmentSummary = response.data.data;
          this.hasDataFetched = true;
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page', this.$options.name);
        })
        .finally(() => {
          this.$store.dispatch('stopLoader', this.$options.name);
        })
      },

      refreshData() {
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
.attribute-value-text {
  font-size: 16px;
}
.shadow-0{ box-shadow: none !important; }

.widget-name {
  margin-left: 0 !important;
  padding-bottom: 5px;
  font-size: 20px !important;
  border-bottom: 1px solid #dfdfdf;
}
.font-14 { font-size: 14px !important; }

.pad-0 { padding: 0px; }
</style>