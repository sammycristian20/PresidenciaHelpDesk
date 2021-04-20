
<template>
  <div id="main-div">
    <alert componentName="dataTableModal"/>

    <faveo-box :title="lang('list_of_approval_workflow')">

      <div class="card-tools" slot="headerMenu" >
        <a class="btn btn-tool" :href="base+'/approval-workflow/create'" v-tooltip="lang('create_approval_workflow')">
        	<span class="glyphicon glyphicon-plus"> </span>
        </a>
      </div>

      <div id="apply-workflow-table">
        <data-table v-if="apiUrl !== ''" :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
      </div>

    </faveo-box>
  </div>
</template>

<script type="text/javascript">

	import {lang} from 'helpers/extraLogics';

	import axios from 'axios';

	import moment from 'moment';

	import momentTimezone from 'moment-timezone';

	import FaveoBox from 'components/MiniComponent/FaveoBox';

    import {mapGetters} from "vuex";

	export default {

		name : 'approval-workflow-index',

		description : 'Approval workflow table component',

		data: () => ({
			/**
			* base url of the application
			* @type {String}
			*/
			base:window.axios.defaults.baseURL,

			columns:[],

			options:{},

			/**
			 * api url for ajax calls
			 * @type {String}
			 */
			apiUrl:'/api/admin/approval-workflow/',

		}),

		beforeMount(){

            /**
			* columns required for datatable
			* @type {Array}
			*/
			this.columns= ['name', 'created_at', 'updated_at', 'action']

			this.options= {
				texts: {
		          filter: '',
		          limit: ''
		        },
				headings: { name: 'Name', CreatedAt: 'created_at', UpdatedAt: 'created_at', action:'Action'},
				columnsClasses : {
				  name: "apply-workflow-index-name",
				  created_at: "apply-workflow-index-created",
				  updated_at: "apply-workflow-index-updated",
				  action: "apply-workflow-index-action"
			  },
				templates: {
					action: 'data-table-actions',
					created_at: (h, row) => {
                      return this.formattedTime(row.created_at)
			        },
			        updated_at: (h, row) => {
                      return this.formattedTime(row.updated_at)
			        },
				},
				sortable:  ['name', 'created_at', 'updated_at'],
				filterable: ['name', 'created_at', 'updated_at'],
				pagination:{chunk:5,nav: 'fixed',edge:true},
				requestAdapter(data) {
			        return {
			          sort: data.orderBy ? data.orderBy : 'name',
			          order: data.ascending ? 'asc' : 'desc',
			          search:data.query.trim(),
			          page:data.page,
			          limit:data.limit,
			        }
			    },
			 	responseAdapter({data}) {
					return {
						data: data.data.data.map(data => {
						data.edit_url = window.axios.defaults.baseURL + '/approval-workflow/' + data.id + '/edit/';
						data.delete_url = window.axios.defaults.baseURL + '/api/admin/approval-workflow/' + data.id + '/workflow/';
						return data;
					}),
						count: data.data.total
					}
				},
			}
		},

        computed:{
          ...mapGetters(['formattedTime'])
        },

        components:{
			'data-table' : require('components/Extra/DataTable'),
			"alert": require("components/MiniComponent/Alert"),
			'faveo-box': FaveoBox,
		}

	};
</script>

<style type="text/css">
	.apply-workflow-index-name, .apply-workflow-index-created, .apply-workflow-index-updated, .apply-workflow-index-action{
    max-width: 250px; word-break: break-all;
  }
  
  #apply-workflow-table .VueTables .table-responsive {
    overflow-x: auto;
  }

  #apply-workflow-table .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
  }
</style>
