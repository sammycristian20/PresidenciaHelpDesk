<template>
  <div>
    <!-- Alert  -->
    <alert componentName="tabular-report-layout" />

    <div class="column-list-right">
      <!-- Columns -->
      <column-list :table-columns="columns" :sub-report-id="subReportId" :add-custom-column-url="addCustomColumnUrl"
        :delete-custom-column-url="deleteCustomColumnUrl" :short-code-url="shortCodeUrl" :report-index="reportIndex" />
    </div>

    <div>
      <dynamic-datatable v-if="columns.length" :data-url="dataUrl" :columns="visibleColumns" :columnsMeta="columns"
        :filterParams="filterParams" />
    </div>

  </div>
</template>

<script>

import axios from 'axios';
import {errorHandler, successHandler} from 'helpers/responseHandler';
import {lang, boolean} from 'helpers/extraLogics';
import FaveoBox from 'components/MiniComponent/FaveoBox';

export default {

  name: 'tabular-report-layout',

  description: 'Common layout component for tabular reports',

  props: {

    // Api endpoint for getting table data
    dataUrl: {
      type: String,
      required: true
    },

    // Api endpoint for getting table column list
    tableColumns: {
      type: Array,
      required: true
    },

    // Api endpoint for export call
    exportUrl: {
      type: String,
      required: true
    },

    // Api endpoint for saving cloumns order, visibility etc
    subReportId: {
      type: Number,
      required: true
    },

    /**
     * Url endpoint for adding custom column
     */
    addCustomColumnUrl: {
      type: String,
      required: true
    },

    /**
     * Url endpoint for deleting custom column
     */
    deleteCustomColumnUrl: {
      type: String,
      required: true
    },

    /**
     * Url endpoint for getting short codes
     */
    shortCodeUrl: {
      type: String,
      required: true
    },

    // Defualt filter field value objec
    filterParams: {
      type: Object,
      default: () => {}
    },

    reportIndex: {
      type: Number,
      required: true
    }

  },

  data(){
    return {
      isLoading: false,
      columns: [],
      visibleColumns: {},
    }
  },

  created() {
    window.eventHub.$on('onColumnUpdate', this.onColumnUpdate);
  },

  beforeMount() {
    this.columns = this.tableColumns;
    this.updateVisibleColumns();
  },

  methods: {

    onColumnUpdate(columns) {
      this.columns = columns;
      this.updateVisibleColumns();
    },

    updateVisibleColumns() {
      // setting visibleColumns to empty so that old values can be removed
      this.visibleColumns = {};

      this.columns.map(column => {
        if(boolean(column.is_visible)){
          this.visibleColumns[column.key] = column.label;
        }
      })
    }
  },

  components : {
    'ticket-filter': require('components/Agent/tickets/filters/TicketFilter.vue'),
    'dynamic-datatable' : require('components/Extra/DynamicDatatable'),
    'alert': require("components/MiniComponent/Alert"),
    'custom-loader': require("components/MiniComponent/Loader"),
    'column-list': require('../Common/ColumnList'),
  }
}
</script>

<style scoped>
.tabular-report-layout-box {
  padding: 0px !important;
}
.column-list-right { float : right; }
</style>
