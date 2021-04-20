<template>
  
  <div>
  
    <alert componentName="dataTableModal" />

	 <div class="card card-light">

		  <div class="card-header">
			   
         <h3 class="card-title">{{lang(title)}}</h3>

         <div class="card-tools">
        
          <a id="create" class="btn btn-tool" :href="base+'/workflow/create'" v-tooltip="lang('create_workflow')">

            <span class="glyphicon glyphicon-plus"> </span> 
          </a>
          
          <a v-if="showTable && total_records > 1" class="btn btn-tool" href="javascript:;" v-tooltip="lang('reorder')"
            @click="reorderMethod">
            <span class="fas fa-bars"> </span>
          </a>
        </div>
		</div>

    <div class="card-body" id="workflow-index-table">
		  <data-table v-if="apiUrl !== '' && showTable" :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
		<workflow-reorder v-if="!showTable" :onClose="onClose" :url="apiUrl+'?meta=true&sort=order&sort_order=asc'" 
      reorder_type="workflow">
      
    </workflow-reorder>

    </div>
	</div>
</div>
</template>

<script type="text/javascript">
import { lang } from "helpers/extraLogics";

import axios from "axios";

import moment from "moment";

import momentTimezone from "moment-timezone";
import {mapGetters} from "vuex";

import Vue from 'vue';

Vue.component('table-actions', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

export default {
  name: "workflow-index",

  description: "Workflow table component",

  data: () => ({
    /**
     * base url of the application
     * @type {String}
     */
    base: window.axios.defaults.baseURL,

    columns: [],

    options: {},

    /**
     * api url for ajax calls
     * @type {String}
     */
    apiUrl: "api/get-enforcer-list/",

    title: "list_of_ticket_workflows",

    showTable: true,

    total_records : 0,
  }),

  beforeMount() {

    const self = this;
    /**
     * columns required for datatable
     * @type {Array}
     */
    this.columns = [
      "name",
      "status",
      "order",
      "created_at",
      "updated_at",
      "action"
    ];

    this.options = {
      
      texts: {
        filter: "",
        limit: ""
      },
      headings: {
        name: "Name",
        status: "Status",
        order: "Order",
        created_at: "Created",
        updated_at: "Updated",
        action: "Action"
      },

	  columnsClasses : {
		  name: "workflow-index-name",
		  status: "workflow-index-status",
		  order: "workflow-index-order",
		  created_at: "workflow-index-created",
		  updated_at: "workflow-index-updated",
		  action: "workflow-index-action"
	  },

      templates: {
        
        action: "table-actions",

        status: function(createElement, row) {
            
          let span = createElement('span', {
            
            attrs:{
              
              'class' : row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
            }
          }, row.status ? 'Active' : 'Inactive');
                  
          return createElement('a', {
                    
          }, [span]);
        },

        created_at(h, row) {
            return self.formattedTime(row.created_at)
        },
        updated_at(h, row) {
			return self.formattedTime(row.updated_at)
        },
      },
      sortable: [
        "name",
        "status",
        "order",
        "created_at",
        "updated_at"
      ],
      filterable: ["name", "created_at", "updated_at"],
      pagination: { chunk: 5, nav: "scroll" },
      requestAdapter(data) {
        return {
          type: "workflow",
          sort: data.orderBy ? data.orderBy : "order",
          sort_order: data.ascending ? "asc" : "desc",
          search: data.query.trim(),
          page: data.page,
          limit: data.limit
        };
      },
      responseAdapter({ data }) {  

        self.total_records = data.data.total;      
        
        return {
          data: data.data.data.map(data => {
            data.edit_url =
              window.axios.defaults.baseURL + "/workflow/edit/" + data.id;
            data.delete_url =
              window.axios.defaults.baseURL +
              "/api/delete-enforcer/workflow/" +
              data.id;
            return data;
          }),
          count: data.data.total
        };
      }
    };
  },

  computed:{
	  ...mapGetters(['formattedTime'])
  },

  methods: {
    reorderMethod() {
      this.showTable = false;
      this.title = "reorder";
    },
    onClose() {
      this.showTable = true;
      this.title = "list_of_ticket_workflows";
    }
  },

  components: {
    "data-table": require("components/Extra/DataTable"),
    "workflow-reorder": require("components/Admin/Workflow/Reorder.vue"),
    'alert' : require('components/MiniComponent/Alert'),
  }
};
</script>

<style type="text/css">

.workflow-index-name, .workflow-index-created, .workflow-index-updated, .workflow-index-status, .workflow-index-action, .workflow-index-order {
    max-width: 250px; word-break: break-all;
  }
  
  #workflow-index-table .VueTables .table-responsive {
    overflow-x: auto;
  }

  #workflow-index-table .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
  }

</style>
