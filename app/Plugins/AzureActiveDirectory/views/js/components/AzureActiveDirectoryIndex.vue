<template>
    <div>
        <alert componentName="dataTableModal"/>

        <faveo-box :title="trans('list_of_azure_active_directories')">
          <alert componentName="azure-list-page" />

          <loader :duration="4000" v-if="isLoading"/>

          <div class="card-tools d-flex mt-1" slot="headerMenu">

              <a class="btn btn-tool" :href="basePath()+'/azure-active-directory/create'" v-tooltip="trans('create_new_directory')">
                <i class="fas fa-plus" aria-hidden="true"></i>
             </a>

             <span class="btn-tool" v-tooltip="trans('hide_default_login')" >
                <input class="hide-checkbox"  type="checkbox" v-model="hideDefaultLogin" @change="updateHideDefaultLogin()">
             </span>
         </div>

            <data-table :url="apiUrl" :dataColumns="columns"  :option="options"></data-table>
       
        </faveo-box>
    </div>
</template>

<script type="text/javascript">

    import {mapGetters} from "vuex";
    import axios from "axios";
    import {successHandler, errorHandler} from "helpers/responseHandler";
    import FaveoBox from 'components/MiniComponent/FaveoBox';

    export default {

        name: "AzureActiveDirectoryIndex",

        data(){
            let self = this;
            return {

                hideDefaultLogin: false,

                columns: ['app_name', 'app_id', 'created_at', 'updated_at', 'action'],

                options: {
                    texts: { filter: '', limit: '' },

                    headings: {
                        app_name: 'App Name',
                        app_id: 'App Id',
                        created_at: 'Created At',
                        updated_at: 'Updated At',
                        action: 'Action'
                    },

                    templates: {
                        action: 'data-table-actions',

                        created_at(h, row) {

                            return self.formattedTime(row.created_at);
                        },

                        updated_at(h, row) {

                            return self.formattedTime(row.updated_at);
                        },

                    },

                    sortable: ['app_name', 'app_id', 'created_at', 'updated_at'],

                    filterable: ['app_name', 'app_id', 'created_at', 'updated_at'],

                    pagination: {chunk: 5, nav: 'fixed', edge: true},

                    requestAdapter(data) {
                        return {
                            sort_field: data.orderBy ? data.orderBy : 'id',
                            sort_order: data.ascending ? 'desc' : 'asc',
                            search_query: data.query.trim(),
                            page: data.page,
                            limit: data.limit,
                        }
                    },
                    responseAdapter({data}) {
                        self.hideDefaultLogin = data.data.hide_default_login;

                        return {
                            data: data.data.directories.data.map(data => {

                                data.edit_url = this.basePath() + '/azure-active-directory/' + data.id + '/edit';

                                data.delete_url = this.basePath() + '/api/azure-active-directory/settings/' + data.id;

                                return data;
                            }),
                            count: data.data.directories.total
                        }
                    },
            },

            /**
             * api url for ajax calls
             * @type {String}
             */
            apiUrl:'api/azure-active-directory/settings',
        }},

        methods:{

          updateHideDefaultLogin() {
            this.isLoading = true;
            const params = {
              hide_default_login: this.hideDefaultLogin
            };
            axios.post('api/azure-active-directory/hide-default-login', params)
                .then(response => {
                  successHandler(response, 'azure-list-page');
                })
                .catch(error => {
                  errorHandler(error, 'azure-list-page');
                })
                .finally(() => {
                  this.isLoading = false;
                })
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

<style type="text/css" scoped>
    .right{
        float: right;
    }
    .hide-checkbox{ height : 25px;}

</style>
