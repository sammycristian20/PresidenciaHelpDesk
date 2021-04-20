<template>
    
    <div>

        <alert componentName="dataTableModal" />

        <div class="card card-light">

            <div class="card-header">
                
                <h3 class="card-title"> {{ lang('chat_settings') }}</h3>
            </div>

            <div class="card-body" id="chat-view">

                <data-table 
                    :url="apiUrl" 
                    :dataColumns="columns" 
                    :option="options" 
                    scroll_to ="chat-view" 
                />
            </div>
        </div>
    </div>
</template>

<script>

import { mapGetters } from 'vuex';
import {errorHandler, successHandler} from 'helpers/responseHandler';
import axios from 'axios';
import { getSubStringValue } from 'helpers/extraLogics';
import Vue from 'vue';

Vue.component('table-actions-clone', require('./ChatSwitchHolder')); //for implementing on-off switch
Vue.component('table-actions-actual', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));
Vue.component('click-to-copy', require('components/MiniComponent/ClickToCopy.vue'))

export default {

    data() {
        return {
            apiUrl : 'chat/api/chats',
            loading: false,
            columns: ['name', 'url', 'department', 'helptopic', 'status', 'action'],
            options: {},
            duration: 4000,
        }
    },

     computed : {
		...mapGetters(['formattedTime','formattedDate'])
    },

    methods: {

        subString(value,length = 30){

            return getSubStringValue(value,length)
        },

    },

    beforeMount() {

        const self = this;
        this.options = {

            headings: { name: 'Name', url : 'URL', status : 'Status', action:'Action', helptopic: "HelpTopic", department: "Department"},

            columnsClasses : {

                name: 'name', 
                url: 'url', 
                status: 'status',
                action: 'action',
                department: 'department',
                helptopic: 'helptopic'

            },
            
            sortIcon: {
						
                base : 'glyphicon',
                    
                up: 'glyphicon-chevron-up',
                    
                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            templates: {
                
                url: 'click-to-copy',

                department: function(h,row,index) {
                    if(!row.department) return "--";
                    return row.department.name
                },

                helptopic: function(h,row,index) {
                    if(!row.helptopic) return "--";
                    return row.helptopic.name
                },

                status: 'table-actions-clone',
                action: 'table-actions-actual'

            },
            requestAdapter(data) {
	      
                return {
                
                    'sort_field' : data.orderBy ? data.orderBy : 'id',
                    
                    'sort_order' : data.ascending ? 'desc' : 'asc',
                    
                    'search_term' : data.query.trim(),
                    
                    page : data.page,
                    
                    limit : data.limit,
                }
            },
            
            responseAdapter({data}) {
                return {
                    data: data.data.chats.map(data => {
                        data.edit_url = self.basePath() + '/chat/edit/' + data.id;
                        data.textToCopy = data.url;
                        return data;
                    }),
                    count: data.data.total
                }
            },
            sortable:  ['name','department','helptopic'],
				
            filterable:  ['name','department','helptopic'],
            
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
.name{
    width:15% !important;
    word-break: break-all;
}
.url{
    width:39% !important;
    word-break: break-all;
}
.status {
    width:10% !important;
    word-break: break-all;
}
.action{
    width:10% !important;
    word-break: break-all;
}

.helptopic {
    width: 13% !important;
    word-break: break-all;
}

.department {
    width: 13% !important;
    word-break: break-all;
}
    
</style>