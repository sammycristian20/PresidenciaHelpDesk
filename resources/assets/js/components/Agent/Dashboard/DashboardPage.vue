<template>
  <div>
    <alert componentName="dashboard-page"/>
    <custom-loader :duration="4000" v-if="showLoader" />
    <div class="row">
      <div class="col-md-12">
        <dashboard-top-widgets :top-widget-count="topWidgetCount"></dashboard-top-widgets>
      </div>

      <div class="col-md-8">
        <dashboard-requires-immediate-action></dashboard-requires-immediate-action>
      </div>

      <div class="col-md-4">
        <dashboard-todo></dashboard-todo>
      </div>

      <template v-if="userRoles.includes('manager') || userRoles.includes('admin')">
        <div class="col-md-6">
          <dashboard-agent-summary></dashboard-agent-summary>
        </div>
        <div class="col-md-6">
          <dashboard-department-summary></dashboard-department-summary>
        </div>
      </template>

      <div class="col-md-8">
        <dashboard-recent-activities></dashboard-recent-activities>
      </div>
      <div class="col-md-4">
        <dashboard-agent-performance-widget></dashboard-agent-performance-widget>
      </div>

      <div class="col-md-12" v-if="userRoles.includes('admin')">
        <dashboard-system-analysis></dashboard-system-analysis>
      </div>

    </div>
  </div>
</template>

<script type="text/javascript">
import { mapGetters } from "vuex";

	export default {
		
    name: 'dashboard-page',

    props: {
      /** Array of user roles -- used to decide which widget to show/hide */
      userRoles: {
        type: Array,
        required: true
      },

      topWidgetCount: {
        type: Number,
        default: 3
      }
    },
  
		data: () => {
			return {
      }
		},
		
		computed: {
    ...mapGetters(['showLoader'])
    },

    beforeDestroy() {
      this.$store.dispatch('forceStopLoader');
    },

		components: {
      'alert': require("components/MiniComponent/Alert"),
      'custom-loader': require("components/MiniComponent/Loader"),
      'dashboard-top-widgets': require('./DasboardTopWidgets'),
      'dashboard-recent-activities': require('./DashboardRecentActivities'),
      'dashboard-todo': require('./DashboardTodo'),
      'dashboard-system-analysis': require('./DashboardSystemAnalysis'),
      'dashboard-requires-immediate-action': require('./DashboardRequiresImmediateAction'),
      'dashboard-agent-summary': require('./ManagerView/AgentsSummary'),
      'dashboard-department-summary': require('./ManagerView/DepartmentSummary'),
      'dashboard-agent-performance-widget': require('./DashboardAgentPerformanceWidget')
		}
	};
</script>

<style type="text/css" scoped>

</style>