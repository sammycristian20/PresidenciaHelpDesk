<template>
    <div>

        <!-- loader -->
        <div class="row" v-if="loading">
            <custom-loader :duration="duration"></custom-loader>
        </div>

        <!-- alert -->
        <alert componentName="dataTableModal" />

        <faveo-box :title="lang('facebook_pages_list')">

            <div slot="headerMenu" class="card-tools">
                <button class="btn btn-tool" @click="addPage" v-tooltip="lang('facebook_add_page')">
                    <i class="fas fa-plus"></i>
                </button>

              <button class="btn btn-tool" @click.prevent="gotoSecurity" v-tooltip="lang('facebook_go_to_security_settings')">
                <i class="fas fa-shield-alt"></i>
              </button>

            </div>

            <div id="page-view">

                <data-table
                        v-if="dataTableVisible"
                        :url="apiUrl"
                        :dataColumns="columns" :option="options"
                        scroll_to ="page-view"
                />

            </div>

        </faveo-box>


    </div>
</template>

<script>

    import { mapGetters } from 'vuex';
    import {errorHandler, successHandler} from 'helpers/responseHandler';
    import moment from 'moment';
    import axios from 'axios';
    import Vue from 'vue';
    import FaveoBox from "components/MiniComponent/FaveoBox";

    Vue.component('switch-action', require('./SwitchAction')); //for implementing on-off switch
    Vue.component('table-actions-actual', require('components/MiniComponent/DataTableComponents/DataTableActions.vue'));

    export default {

        name: 'FacebookSettings',
        description: 'Component listing all facebook pages',

        computed : {
            ...mapGetters(['formattedTime','formattedDate'])
        },


        data() {
            return {

                apiUrl : 'facebook/api/integration',
                loading: false,
                columns: ['page_name', 'page_id', 'status', 'action'],
                options: {},
                dataTableVisible : true,
                duration: 4000,

            }
        },

        methods: {
            addPage() {
                this.redirect('/facebook/integration/create');
            },

          gotoSecurity() {
              this.redirect('/facebook/security-settings')
          }
        },

        beforeMount() {

            const self = this;

            this.options = {

                headings: { page_name: 'Name', page_id : 'Page ID', status : 'Status', action:'Actions'},

                columnsClasses : {

                    page_name: 'page-name',

                    page_id : 'page-id',

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

                            data.delete_url = self.basePath() + '/facebook/api/integration/' + data.id;
                            data.edit_url = self.basePath() + '/facebook/integration/edit/' + data.id;
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
            'faveo-box' : FaveoBox,

        }
    }
</script>


<style scoped>

    .mr-2 {
        margin-right: 5px;
    }

    .page-name{
        width:30% !important;
        word-break: break-all;
    }

    .page-id{
        width:30% !important;
        word-break: break-all;
    }

    .page-active {
        width:20% !important;
        word-break: break-all;
    }

    .page-action{
        width:20% !important;
        word-break: break-all;
    }

</style>

