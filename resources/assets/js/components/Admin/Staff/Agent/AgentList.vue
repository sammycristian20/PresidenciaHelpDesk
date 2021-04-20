<template>
  <div id="main-div">
    <alert componentName="agentList" />

    <faveo-box :title="lang('list_of_agents')" boxClass="card-light">

      <div class="card-tools" slot="headerMenu" >

        <a class="btn btn-tool" @click="toggleFilterView" data-toggle="tooltip" v-tooltip="lang('filter')">
          
          <i class="fas fa-filter" ></i>
        </a>
        
        <a class="btn btn-tool" :href="base+'/agents/create'" v-tooltip="lang('create_agent')" id="add-agent-link">
          
          <i class="fas fa-plus" aria-hidden="true"> </i>
        </a>
      </div>

      <div id="my_agents">
        <advance-filter id="filter-box" v-show="isShowFilter" :metaData="filterOptions"
          @selectedFilters="selectedFilters" />
        <data-table :url="apiUrl" :dataColumns="columns" :option="options" scroll_to="agents-list"></data-table>
      </div>

    </faveo-box>

  </div>
</template>

<script type="text/javascript">

	import { lang } from 'helpers/extraLogics';
	import axios from 'axios';
	import {errorHandler,successHandler} from "helpers/responseHandler";
  import Vue from 'vue';
  import FaveoBox from 'components/MiniComponent/FaveoBox';
  import { mapGetters } from 'vuex';
  Vue.component('user-table-actions', require('components/Agent/User/UserTableActions.vue'));

	export default {
		name : 'agent-list',
		description : 'Agent list table component',

		components:{
			'data-table' : require('components/Extra/DataTable'),
			'alert': require("components/MiniComponent/Alert"),
			'button-dropdown': require('components/MiniComponent/FormField/ButtonDropdown'),
      'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
      'advance-filter': require("components/Extra/AdvanceFilter"),
      'faveo-box': FaveoBox,
		},

		data: () => {
      return {

        isShowFilter: false,

        /**
        * base url of the application
        * @type {String}
        */
        base: window.axios.defaults.baseURL,
      
        /**
         * api url for ajax calls
         * @type {String}
         */
        apiUrl:'api/admin/get-users-list?roles[0]=admin&roles[1]=agent&active=1',

        /**
        * columns required for datatable
        * @type {Array}
        */
        columns: ['name', 'user_name', 'email', 'phone', 'info', 'last_login_at', 'action'],

        /* filterOptions object contains different filter option like
        *  1. usetType (from elements property)
        *  2. departments (from api call)
        *  3. teams (from api call)
        */
        filterOptions: [
          { name: 'userType',
            url: '',
            elements: [
              {id:'all', name:'All', queryPerm:'roles[0]=admin&roles[1]=agent'},
              {id:'active_admin_agent', name:'Active Admins and Agents', queryPerm:'roles[0]=admin&roles[1]=agent&active=1'},
              {id:'deactive_admin_agent', name:'Deactived Admins and Agents', queryPerm:'roles[0]=admin&roles[1]=agent&active=0'},
              {id:'active_admin', name:'Active Admin', queryPerm:'roles[0]=admin&active=1'},
              {id:'deactive_admin', name:'Deactived Admins', queryPerm:'roles[0]=admin&active=0'},
              {id:'all_admin', name:'All Admins', queryPerm:'roles[0]=admin'},
              {id:'active_agent', name:'Active Agents', queryPerm:'roles[0]=agent&active=1'},
              {id:'deactive_agent', name:'Deactived Agents', queryPerm:'roles[0]=agent&active=0'},
              {id:'all_agent', name:'All Agents', queryPerm:'roles[0]=agent'}
            ],
            isMultiple: false,
            isPrepopulate: false,
            label: 'user_type',
            value: {id: 'active_admin_agent', name: 'Active Admins and Agents', queryPerm: 'roles[0]=admin&roles[1]=agent&active=1'},
            className: 'col-sm-4'
          },
          { name: 'departments',
            url: 'api/dependency/departments',
            elements: [],
            isMultiple: true,
            isPrepopulate: false,
            label: 'departments',
            value: '',
            className: 'col-sm-4'
          },
          { name: 'teams',
            url: 'api/dependency/teams',
            elements: [],
            isMultiple: true,
            isPrepopulate: false,
            label: 'teams',
            value: '',
            className: 'col-sm-4'
          },
        ],
        
        /**
        * Options required for datatable
        * @type {Object}
        */
        options: {},
      }
    },

    beforeMount() {
      const thisRef = this;

      this.options = {
        headings: {
					name: 'Name',
					user_name: 'User Name',
					email: 'Email',
					phone: 'Phone',
					info: 'Account info',
          last_login_at: 'Last Login',
					action:'Actions'
          },
          
          columnsClasses: {
            name: 'name__agentList', 
            user_name: 'username__agentList', 
            email:'email__agentList',
            phone: 'phone__agentList',
            info: 'active__agentList',
            action: 'action__agentList'
				  },

          texts: { filter: '', limit: '' },

				templates: {

          name: function(createElement, row) {
            let a = createElement('a', {
              attrs:{
                'href': window.axios.defaults.baseURL + '/user/' + row.id,
                'target': "_blank"
              }
            }, row.name);
            return createElement('a', {
            }, [a]);
          },

          phone(h, row) {
            return row.phone === ' Not available' || row.phone === ' ' || !row.phone ? '--' : row.phone;
          },

          last_login_at(h, row) {
						return thisRef.formattedTime(row.last_login_at);
					},

					action: 'user-table-actions', // template for the ACTION column
					info: 'data-table-statuses', // template for the STATUS column
					type(h,row){
						return row.type.name
					},
				},

				sortable: ['name', 'user_name', 'email', 'last_login_at'], // sortable columns

				pagination: {
					chunk: 5,
					nav: 'scroll'
					},

				requestAdapter(data) {
      		return {
        		'sort-field': data.orderBy ? data.orderBy : 'name',
        		'sort-order': data.ascending ? 'asc' : 'desc',
        		'search-query': data.query.trim(),
        		page: data.page,
        		limit: data.limit,
      		}
    		},
			 	responseAdapter({data}) {
          if(data) {
					return {
  						data: data.data.users.map(data => {
  						data.edit_url = window.axios.defaults.baseURL + '/agents/' + data.id + '/edit';
  						data.view_url = window.axios.defaults.baseURL + '/user/' + data.id ;
              data.agent_view_url = window.axios.defaults.baseURL + '/agent/' + data.id ; 
  						return data;
  						}),
  						count: data.data.total
  					}
          }
         }
        }
    },

    computed:{
			...mapGetters(['formattedTime'])
		},

		methods: {
      selectedFilters(value){ //teams userType departments
        if(value === 'closeEvent') {
          this.isShowFilter = false;
          return;
        } else if(value === 'resetEvent') {
          this.resetFilter();
          return;
        }
        let baseUrlForFilter = 'api/admin/get-users-list?';
        let params = '';

        if(value.userType) { //userType
          this.filterOptions[0].value = value.userType;
          params = value.userType.queryPerm + '&';
        } else {
          params = "roles[0]=admin&roles[1]=agent&";
        }

        if(value.departments) { //departments
          this.filterOptions[1].value = value.departments;
          value.departments.forEach(function(element, index) {
               params +=  'dept-ids[' + index + ']=' + element.id + '&'
            });
        }

        if(value.teams) { //teams
          this.filterOptions[2].value = value.teams;
          value.teams.forEach(function(element, index) {
               params +=  'team-ids[' + index + ']=' + element.id + '&'
            });
        }
        if(params[params.length-1] === '&') {
          params = params.slice(0, -1);
        }
        this.apiUrl = baseUrlForFilter + params;
        this.isShowFilter = false;
      },


      resetFilter() {
        this.filterOptions.forEach(function(element){
          element.value = '';
        });
        this.apiUrl = 'api/admin/get-users-list?roles[0]=admin&roles[1]=agent';
      },

      toggleFilterView() {
        this.isShowFilter = !this.isShowFilter;
      },
		},
	};

</script>

<style type="text/css">
.name__agentList,.username__agentList,.email__agentList,.phone__agentList,.active__agentList{ 
  max-width: 250px; word-break: break-all;
}
.role__agentList { text-transform: capitalize; }

#my_agents .VueTables .table-responsive {
  overflow-x: auto;
}
#my_agents .VueTables .table-responsive > table{
  width : max-content;
  min-width : 100%;
  max-width : max-content;
  overflow: auto !important;
}
</style>