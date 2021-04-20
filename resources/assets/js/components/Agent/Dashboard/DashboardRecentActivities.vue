<template>
  <div class="card card-light dashboard-widget-box">
    <div class="card-header">
      <h3 class="card-title">
        <i class="far fa-bell" aria-hidden="true"> </i> {{lang('recent_activities')}}
      </h3>
      <div class="card-tools">
                  
          <button type="button" class="btn btn-tool" data-card-widget="refresh" @click="refreshData()" v-tooltip="lang('refresh')">
            
            <i class="fas fa-sync-alt"></i>
          </button>
      </div>
    </div>
    <div class="card-body scrollable-area" v-if="notificationData.length > 0">
      <ul class="products-list product-list-in-card">
        <li class="item" v-for="activity in notificationData" :key="activity.id">
          <div class="product-img">
            <faveo-image-element v-if="activity.by === 'System'" :id=" 'user_img_' + activity.id" :source-url="activity.requester ? activity.requester.profile_pic: ''" default-image="system.png" :classes="['img-circle']" alternative-text=""/>
            <faveo-image-element v-else :id=" 'user_img_' + activity.id" :source-url="activity.requester.profile_pic" :classes="['img-circle']" :alternative-text="activity.requester.changed_by_user_name"/>
          </div>
          <div class="product-info fw-400" style="padding-top: 1rem;">
            <a v-if="activity.by === 'System'" class="product-title" :href="activity.url" target="_blank">{{activity.by + ' ' + activity.message}}</a>
            <a v-else class="product-title  fw-400" :href="activity.url" target="_blank">{{ activity.requester.changed_by_first_name + ' ' + activity.requester.changed_by_last_name + ' ' + activity.message}}</a>
            <span class="float-right">{{activity.created_at}}</span>
          </div>
        </li>
        <infinite-loading @infinite="getDataFromServer">
          <div slot="spinner"></div>
          <div slot="no-results"></div>
          <div slot="no-more"></div>
        </infinite-loading>
      </ul>
    </div>
    <div v-if="hasDataFetched && notificationData.length === 0" class="no-data-section">{{lang('no_data_available')}}</div>
  </div>
</template>

<script type="text/javascript">
import { errorHandler } from 'helpers/responseHandler';
import axios from 'axios';

	export default {
		
    name : 'dashboard-recent-activities',
  
		data: () => {
			return {
				notificationData: [],
        page: 1,
        hasDataFetched: false
      }
		},

		beforeMount() {
			this.getDataFromServer();
		},
		
		methods: {
  
			getDataFromServer($state, isRefresh = false) {
        this.$store.dispatch('startLoader', this.$options.name);
				axios.get('notification/api', { params: { page: this.page } })
        .then(response => {
          this.updateData(response.data.data, $state, isRefresh);
        })
        .catch(error => {
          errorHandler(error, 'dashboard-page');
				})
				.finally(() => {
          this.$store.dispatch('stopLoader', this.$options.name);
          $state && $state.loaded();
				})
      },

      updateData(data, $state, isRefresh) {
        if(isRefresh) {
          this.notificationData = data;
        } else {
          this.notificationData.push(...data);
        }
        if(data.length === 0) {
          // mark infinite loader as complete if data length is 0
          $state && $state.complete();
        } else {
          this.page += 1;
        }
        this.hasDataFetched = true;
      },
      
      refreshData() {
        this.hasDataFetched = false;
        this.page = 1;
        this.getDataFromServer(undefined, true);
      }
			
    },
    
    components: {
      'faveo-image-element': require('components/Common/FaveoImageElement'),
      'dashboard-help': require('./DashboardHelp')
    }

	};
</script>

<style type="text/css" scoped>

.fw-400{ font-weight: 400; }
</style>