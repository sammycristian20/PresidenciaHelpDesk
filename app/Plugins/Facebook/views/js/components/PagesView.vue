<template>
    <div>

        <!-- loader -->
        <div class="row" v-if="loading">
            <custom-loader :duration="duration"></custom-loader>
        </div>

        <!-- alert -->
        <alert componentName="dataTableModal" />

        <!-- box -->
        <div class="card card-light">

            <div class="card-header">
                <h3 class="card-title">
                    {{ lang('pages_list') }}
                </h3>
                <div class="card-tools">
                <button class="btn btn-tool" @click="redirectToApp" v-tooltip="lang('app_settings')">
                    <i class="fas fa-cog"> </i>
                </button>

                <button class="btn btn-tool refresh" @click="refreshFbPages(true)" v-tooltip="lang('refresh')">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>

            </div> <!--box-header-->

            <div class="card-body" id="page-view">

                <data-table 
                    v-if="dataTableVisible" 
                    :url="apiUrl" 
                    :dataColumns="columns" :option="options" 
                    scroll_to ="page-view" 
                />

                <div v-if="!dataTableVisible" class="emptyDiv">
                    
                </div>
                
            </div> <!--body-->

        </div>

    </div>
</template>

<script>

import { mapGetters } from 'vuex';
import {errorHandler, successHandler} from 'helpers/responseHandler';
import moment from 'moment';
import axios from 'axios';
import Vue from 'vue';

Vue.component('switch-action', require('./SwitchAction')); //for implementing on-off switch
Vue.component('table-actions-actual', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

export default {
    name: 'pages',
    description: 'Component listing all facebook pages',

    computed : {
		...mapGetters(['formattedTime','formattedDate'])
    },


    data() {
        return {

            apiUrl : 'facebook/api/pages/view',
            loading: false,
            columns: ['page_name', 'page_id', 'status', 'action'],
            options: {},
            dataTableVisible : true,
            duration: 4000,

        }
    },

    methods : {

        refreshFbPages(sync=false) {
            this.loading = true;
            if(!sync) this.dataTableVisible = false;
			axios.get('facebook/api/pages/refresh')
			.then((response) => {
                if(!sync) this.dataTableVisible = true;
                else successHandler(response,'dataTableModal');
                setTimeout(()=>window.eventHub.$emit('refreshData'),10);
                this.loading = false;

            })
            .catch((err) => {
                errorHandler(err,'pages-view');
                if(!sync) this.dataTableVisible = true;
                this.loading = false;
            })
        },
        
        fetchPageCount() {
            axios.get(this.apiUrl)
            .then((res) => {
                if(parseInt(res.data.data.total) == 0) this.refreshFbPages();
            })
        },

        redirectToApp() {
            this.redirect("/facebook/settings");
        },

    },

    beforeMount() {

        this.fetchPageCount();

        const self = this;
        this.options = {
            headings: { page_name: 'Name', page_id : 'Page ID', status : 'Status', action:'Action'},

            columnsClasses : {

                page_name: 'page-name', 

                page_name: 'page-used', 

                status: 'page-active',

                action: 'page-action',
            },
            
            sortIcon: {
						
                base : 'glyphicon',
                    
                up: 'glyphicon-chevron-up',
                    
                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            templates: {

                status: 'switch-action',
                action: 'table-actions-actual'
            },

            requestAdapter(data) {
	      
                return {
                
                    'sort-field' : data.orderBy ? data.orderBy : 'id',
                    
                    'sort-order' : data.ascending ? 'desc' : 'asc',
                    
                    'search-query' : data.query.trim(),
                    
                    page : data.page,
                    
                    limit : data.limit,
                }
            },
            
            responseAdapter({data}) {

                return {
                    data: data.data.pages.map(data => {

                        data.delete_url = self.basePath() + '/facebook/api/pages/delete/' + data.id;
                        return data;

                    }),

                    count: data.data.total
                }
            },

            sortable:  ['page_name', 'page_id'],
				
            filterable:  ['page_name', 'page_id'],
            
            pagination:{chunk:5,nav: 'fixed',edge:true},
        }
    },

    components : {

        "alert": require("components/MiniComponent/Alert"),
        "data-table" : require('components/Extra/DataTable'),

    }
}
</script>


<style>

.mr-2 {
    margin-right: 5px;
}

.page-name{
    width:52% !important;
    word-break: break-all;
}

.page-id{
    width:30% !important;
    word-break: break-all;
}

.page-active {
    width:9% !important;
    word-break: break-all;
}

.page-action{
    width:9% !important;
    word-break: break-all;
}
    
.emptyDiv {
    padding: 130px;
}

</style>

