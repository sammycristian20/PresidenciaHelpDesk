<template>
    <div>
      <alert componentName="task-template-settings" />
        <faveo-box :title="lang('task-plugin-task-templates')">

            <div slot="headerMenu" class="card-tools">

                <a :href="createPage" class="btn btn-tool" v-tooltip="lang('task-plugin-add-task-template')">
                    <i class="fas fa-plus"> </i> 
                </a>

            </div>

            <div>

                <data-table :url="apiUrl" :dataColumns="columns"
                    :option="options" componentTitle="TaskTemplate"
                    scroll_to="task-templates"
                />

            </div>

        </faveo-box>

    </div>
</template>

<script>

    import { mapGetters } from 'vuex'

    import Vue from 'vue';

    import FaveoBox from 'components/MiniComponent/FaveoBox';

    Vue.component('task-table-actions', require('./MiniComponents/TaskTableActions.vue'));

    export default {
        name: "TaskTemplate",

        components : {

            'data-table' : require('components/Extra/DataTable'),

            'task-modal' : require('./TaskModal'),

            'faveo-box' : FaveoBox,

            "alert": require("components/MiniComponent/Alert"),
        },

        computed : {

            ...mapGetters(['formattedTime']),

            createPage() {
                return this.basePath() + "/tasks/template/create";
            }
        },

        methods : {

            onClose(){

                this.$store.dispatch('unsetValidationError');

                this.showModal = false
            },

            gotoCreateTemplatePage() {

                this.redirect('/tasks/template/create');

            },
        },


        props : {

            category : { type : String, default : '' }
        },

        data() {

            return {

                apiUrl : '/tasks/api/template/index',

                columns: ['name','description','category','tasks','action'],

                showModal : false,

                style:{ width:'500px' },
            }
        },

        beforeMount() {

            const self = this;

            this.options = {

                columnsClasses : {

                    name: 'task-template-name',

                    description: 'task-template-description',

                    category: 'task-template-category',

                    tasks: 'task-template-tasks',

                    action: 'task-template-action',
                },

                sortIcon: {

                    base : 'glyphicon',

                    up: 'glyphicon-chevron-up',

                    down: 'glyphicon-chevron-down'
                },

                texts: { filter: '', limit: '' },

                templates: {

                   category: (h, row) => {
                     let category = row.category;
                     if (!category)
                       return '--';
                     return category.name;
                   },

                  tasks: 'table-list-elements',

                  action: 'task-table-actions'
                },

                sortable:  ['name'],

                filterable:  ['name'],

                pagination:{chunk:5,nav: 'fixed',edge:true},

                requestAdapter(data) {

                    return {

                        'sortField' : data.orderBy ? data.orderBy : 'id',

                        'sortOrder' : data.ascending ? 'desc' : 'asc',

                        'searchTerm' : data.query.trim(),

                        page : data.page,

                        limit : data.limit,
                    }
                },

                responseAdapter({data}) {

                    return {

                        data: data.data.data.map(data => {

                            data.edit_url = self.basePath() + '/tasks/template/edit/' +data.id;

                            data.delete_url = self.basePath() + '/tasks/api/template/delete/' + data.id;

                            data.alertComponentName = 'task-template-settings';

                            data.componentTitle = 'TaskTemplate';

                            data.listElementObj = {
                              key: 'template_tasks',
                            }

                            return data;
                        }),
                        count: data.data.total
                    }
                },
            }
        },
    }
</script>

<style>

  .task-template-name {
    width:15% !important;
    word-break: break-all;
  }

  .task-template-description {
    width:25% !important;
    word-break: break-all;
  }

  .task-template-tasks{
    width:30% !important;
    word-break: break-all;
  }

  .task-template-category {
    width:15% !important;
    word-break: break-all;
  }

  .task-template-action {
    width:15% !important;
    word-break: break-all;
  }

</style>