<template>
  <div>
    <!-- Alert -->
    <alert componentName="report-entry-page" />

    <div class="card card-light  report-box-primary" v-if="reportConfigObj">

      <div class="card-header">

        <h3 class="card-title">{{reportConfigObj.name}}
              <a v-if="reportConfigObj.helplink" :href="reportConfigObj.helplink" target="__blank">
                <tool-tip slot="headerTooltip" :message="lang('click_to_see_how_to_read_this_report')" size="medium"></tool-tip></a>
        </h3>

        <div class="card-tools">

          <button type="button" class="btn btn-tool" v-tooltip="lang('Report configuration/filter')" @click="toggleFilterView()">

            <i class="fas fa-filter"></i>
          </button>

            <!-- Export -->
            <button v-if="showExportButton && dataCount" id="export-report"  class="btn btn-tool" @click="exportReport" 
              v-tooltip="lang('export')">

              <i class="fas fa-paper-plane" aria-hidden="true"></i>
            </button>
      </div>
    </div>

      <div class="card-body">

        <!-- Filter -->
        <ticket-filter  v-if="isShowFilter" v-on:filter="setFilter" :isApplyOnlyMode="true" :prefilled-filter-object="filterParams"
          filter-dependencies-api-endpoint="/api/agent/filter-dependencies" :showFilter="isShowFilter"
          :closeFilterView="toggleFilterView">

        <!-- filter operation button group -->
          <span slot="filter-operation-btn-group">
            <button class="btn btn-primary" v-if="!reportConfigObj.is_default" @click="forkUpdateAction('update')">
              <i class="fas fa-pencil-alt" aria-hidden="true"></i>
              &nbsp;{{lang('update')}}
            </button>
            <button class="btn btn-primary" @click="forkUpdateAction('fork')">
              <i class="fas fa-code-branch" aria-hidden="true"></i>
              &nbsp;{{lang('fork')}}
            </button>
          </span>

        </ticket-filter>

      <section v-for="(report, reportIndex) in reportConfigObj.sub_reports" :key="report.id">

        <!-- DATATABLE -->
        <tabular-report-layout v-if="report.data_type === 'datatable'" :data-url="report.data_url"
          :table-columns="report.columns" :sub-report-id="report.id" :export-url="reportConfigObj.export_url"
          :add-custom-column-url="report.add_custom_column_url" delete-custom-column-url="api/delete-custom-column"
          :short-code-url="'api/report-shortcodes/' + reportConfigObj.type" :filterParams="filterParams"
          :report-index="reportIndex">
        </tabular-report-layout>

        <!-- CATEGORY CHART -->
        <category-based-report v-if="report.data_type === 'category-chart'" :category-chart-data-api="report.data_url"
          :widget-data-api="report.data_widget_url" :categories="report.list_view_by"
          :default-category="report.selected_view_by" :default-chart-type="report.selected_chart_type"
          category-prefix="view_by" :layout-class="getLayoutClass(report.layout)" :filterParams="filterParams"
          :report-index="reportIndex" @updateChangedValue="updateChangedValue">
        </category-based-report>

        <!-- TIME SERIES CHART -->
        <time-series-chart v-if="report.data_type === 'time-series-chart'" :chart-data-api="report.data_url"
          :data-widget-api="report.data_widget_url" :categories="report.list_view_by"
          :default-category="report.selected_view_by" :filterParams="filterParams" :report-index="reportIndex"
          @updateChangedValue="updateChangedValue">
        </time-series-chart>

      </section>

    </div>
</div>
    <save-report-modal v-if="openSaveReportModal" :onClose="closeSaveReportModal"
      :reportDataObj="clonedReportConfigOnj" :modal-mode="modalMode" />

  </div>
</template>

<script>

import axios from 'axios';
import { getIdFromUrl } from 'helpers/extraLogics';
import { errorHandler, successHandler } from 'helpers/responseHandler';
import { getFilterObjectToArray, getValidFilterObject, getColumnClass } from '../helpers/utils';

export default {

  name: 'report-entry-page',

  components: {
    'ticket-filter': require('components/Agent/tickets/filters/TicketFilter.vue'),
    'tabular-report-layout': require('./Common/TabularReportLayout'),
    'time-series-chart': require('./Common/TimeSeriesChart'),
    'category-based-report': require('./Common/CategoryBasedReport'),
    'save-report-modal': require('./SaveReportModal'),
    'alert': require("components/MiniComponent/Alert"),
    "tool-tip": require("components/MiniComponent/ToolTip"),
  },

  data: () => {
    return {
      reportConfigObj: null, 
      clonedReportConfigOnj: null, // usded to save/update report
      isLoading: true,
      openSaveReportModal: false,
      modalMode: '',
      filterParams: {},
      isShowFilter : false,
      dataCount : 0
    }
  },

  beforeMount() {
    this.dashboardInit();
  },

  created() {
    window.eventHub.$on('onColumnUpdate', this.onColumnUpdate);
    window.eventHub.$on('refreshReportEntryPage', this.dashboardInit);
    window.eventHub.$on('dataCount',this.getCount)
  },

  computed: {

    /**
     * function to show export button only in tabular report
     * remove this logic once export in chart will be implemented
     */
    showExportButton: function() {
      if(this.reportConfigObj && Array.isArray(this.reportConfigObj.sub_reports)) {
        for(let i = 0; i < this.reportConfigObj.sub_reports.length; i++) {
          if(this.reportConfigObj.sub_reports[i].data_type === 'datatable') {
            return true;
          }
        }
      }
      return false;
    }
  },

  methods: {

    getCount(value) {

      this.dataCount = value;
    },

    toggleFilterView() {

      this.isShowFilter = !this.isShowFilter;
    },

    dashboardInit() {
      this.getReportConfiguration(getIdFromUrl(window.location.pathname));
    },

    getLayoutClass(layout) {
      return getColumnClass(layout);
    },

    /** Get report configuration object from server */
    getReportConfiguration(reportId) {
      this.isLoading = true;
      let params = getValidFilterObject(this.filterParams);
      params.include_filters = 1;

      axios.get('api/agent/report-config/' + reportId, { params: params })
      .then(res => {
        this.reportConfigObj = res.data.data;
        this.clonedReportConfigOnj = JSON.parse(JSON.stringify(this.reportConfigObj));
        this.updateFilterObj();
      }).catch(err => {
        errorHandler(err, 'report-entry-page');
      }).finally(res => {
        this.isLoading = false;
      });
    },

    /** Update local copy of filter object with the filter-object recieved form api response */
    updateFilterObj() {
      let filterObj = {};
      if(Array.isArray(this.reportConfigObj.filters)) {
        this.reportConfigObj.filters.forEach(element => {
          filterObj[element.key] = element.value
        });
      }
      this.filterParams = filterObj;
    },

    /** Export report */
    exportReport() {
      this.isLoading = true;
      axios.post(this.reportConfigObj.export_url, this.filterParams)
        .then(res => {
          successHandler(res, 'tabular-report-layout');
        })
        .catch(err => {
          errorHandler(err, 'tabular-report-layout');
        }).finally(() => {
          this.isLoading = false;
        })
    },

    /** Emit event for forking the report */
    forkUpdateAction(actionType) {
      window.eventHub.$emit('performApplyAction');
      this.modalMode = actionType;
      this.openSaveReportModal = true;
    },

    /** close save report modal */
    closeSaveReportModal() {
      this.openSaveReportModal = false;
    },

    /** set filter values to report-config-object */
    setFilter(payload) {
      this.filterParams = JSON.parse(JSON.stringify(payload));
      this.clonedReportConfigOnj.filters = getFilterObjectToArray(this.filterParams);
    },

    /** Update local copy of `key` property with the updated one */
    updateChangedValue(newValue, reportIndex, key) {
      this.clonedReportConfigOnj.sub_reports[reportIndex][key] = newValue;
    },

    /** Update the local copy of the column list in case of tabular report */
    onColumnUpdate(columns, reportIndex) {
      this.clonedReportConfigOnj.sub_reports[reportIndex].columns = columns;
    },
  }
}
</script>

<style lang="css" scoped>

</style>