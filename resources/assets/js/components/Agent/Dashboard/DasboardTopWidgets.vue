<template>
	<div>
		<div v-if="!dataWidgetData" class="row">
			<div class="col-12 col-sm-6 col-md-3" v-for="i in topWidgetCount" :key="i">
				<div class="data-widget-box-skeleton">
				</div>
			</div>
		</div>
		<data-widget v-if="dataWidgetData && hasDataFetched" :data-widget-data="dataWidgetData" :widget-class="['col-md-3', 'col-sm-6', 'col-12']"/>
	</div>
</template>

<script type="text/javascript">

import axios from 'axios';
import { errorHandler } from 'helpers/responseHandler';

	export default {
		
    name: 'dashboard-top-widgets',
  
		data: () => {
			return {
				dataWidgetData: null,
				hasDataFetched: false,
      }
		},

		props: {
			topWidgetCount: {
        type: Number,
        default: 3
      }
		},

		beforeMount() {
			this.getDataFromServer();
		},
		
		methods: {

			getDataFromServer() {
				this.$store.dispatch('startLoader', this.$options.name);
				axios.get('api/agent/dashboard-report/top-widget')
        .then(res => {
					this.dataWidgetData = res.data.data;
        })
        .catch(err => {
          errorHandler(err, 'dashboard-page');
				})
				.finally(() => {
					this.hasDataFetched = true;
					this.$store.dispatch('stopLoader', this.$options.name);
				})
			}
		},

		components: {
			'data-widget': require('components/Common/DataVisualization/DataWidget'),
		}
	};
</script>

<style type="text/css" scoped>
.data-widget-box-skeleton {
	margin-bottom: 1rem;
  transition: box-shadow 280ms cubic-bezier(.4,0,.2,1);
  background: #fff;
	border-left: 3px solid #979393;
  box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
	height: 10vh;
}
</style>