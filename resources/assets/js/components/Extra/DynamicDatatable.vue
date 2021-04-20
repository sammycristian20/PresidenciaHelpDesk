<template>
  <div id="dynamic-datatable-container">
    <data-table
      id="dynamic-datatable"
      :url="dataUrl"
      :dataColumns="columnNames"
      :option="options"
      scroll_to="dynamic-datatable">
    </data-table>
  </div>
</template>

<script>

import Vue from 'vue';
import { carbonToMomentFormatter} from 'helpers/extraLogics';
import { mapGetters } from 'vuex';

export default {

  data(){
    return {

      /**
       * Name of the columns in array format
       * @type {Array}
       */
      columnNames: [],

      /**
       * Datatable options
       * @type {Object}
       */
      options: {},

      /**
       * Contains the key and component of the data
       * for eg. for priority, we dynamically create a component with name priority-hyperlink,
       * not the mapping of this, will be stored in this variable. Like so :
       * { "priority" : "priority-hyperlink" }
       * @type {Object}
       */
      templateObject: {},
    }
  },

  props : {

    /**
     * Api endpoint for getting table data
     */
    dataUrl: {
      type: String,
      required: true
    },

    /**
     * Object with key and label of a column
     * for eg. { ticket_number : "Ticket Number" }
     * @type {Object}
     */
    columns: {type: Object, required: true},

    /**
     * Array of objects of column
     * @type {Object}
     */
    columnsMeta: {type: Array, required: true},

    /**
     * ticket filter paramaters
     * @type {Object}
     */
    filterParams: {type: Object, required: true},
  },

  beforeMount() {

    this.prepareDynamicComponents();

    this.updateColumnNames();

    this.updateOptions();
  },

  methods: {

    /**
     * Prepares component dynamically so that additional files should not be written for that
     * @return {Void}
     */
    prepareDynamicComponents()
    {
        // loop over all columns and whichever is html,
        // render it like html
        // whichever is timestamp, render it like timestamp
        this.columnsMeta.map(column => {

          // making "." replace with a "-", so that it can be a valid component name
          let key = column.key.replace(/\./g, '-');

          if(column.is_html){
            this.getHyperlinkInstance(key, column.key);
          }

          // formatting columns which are non custom, custom columns will be formatted from backend
          if(column.is_timestamp){

            let timestampFormat = carbonToMomentFormatter(column.timestamp_format);
            this.getTimestampInstance(key, column.key, timestampFormat);
          }
        })
    },

    /**
     * takes column keys and assign to columnNames state
     * @return {void}
     */
    updateColumnNames(){
      this.columnNames = Object.keys(this.columns);
    },

    sortableFields(){
      let sortables = [];

      this.columnsMeta.map(column => {
        if(column.is_sortable){
          sortables.push(column.key);
        }
      })
      return sortables;
    },

    /**
     * create hyperlink vue instance of dependencies dynamically
     * @param  {string} componentName
     * @param  {string} hyperlinkPath path of the dependency in the API
     *                                for eg. in {priority: {id:1, name:'hyperlink'}},
     *                                it will be priority.name
     * @return {void}
     */
    getHyperlinkInstance(componentName, keyName){

      // mapping keyname with componentName in template so that datatable can mount it as a component
      this.templateObject[keyName] = componentName;

      let absoluteHyperlinkPath = `data.${keyName}`;

      return Vue.component(componentName, { props: ['data'],
        template: "<span>" +
          "<span v-if=data."+ keyName +" v-html=" + absoluteHyperlinkPath + "></span>" +
          "<span v-else>--</span>"+
        "</span>"
      });
    },

    /**
     * create hyperlink vue instance of dependencies dynamically
     * @param  {string} componentName
     * @param  {string} hyperlinkPath path of the dependency in the API
     *                                for eg. in {priority: {id:1, name:'hyperlink'}},
     *                                it will be priority.name
     * @return {void}
     */
    getTimestampInstance(componentName, keyName, timestampFormat){
      
      // mapping keyname with componentName in template so that datatable can mount it as a component
      this.templateObject[keyName] = componentName;

      let value = `data.${keyName}`;
      let timestampFormatVariable = `timestampFormat`;

      Vue.component('report-timestamp-'+componentName, { props: ['data', 'timestampFormat'],
        template: "<span v-html=customFormattedTime("+value+","+timestampFormatVariable+")></span>",
        computed: {
          ...mapGetters(['customFormattedTime'])
        }
      });

      return Vue.component(componentName, { props: ['data'],
        template: "<report-timestamp-"+componentName+" :data='data' timestampFormat='"+timestampFormat+"'></report-timestamp-"+componentName+">"
      });
    },

    updateOptions(){
      this.options =  {

        headings: this.columns,

        perPageValues : [10,25,50,100,200,500],

        sortIcon: {

          base : 'glyphicon',

          up: 'glyphicon-chevron-down',

          down: 'glyphicon-chevron-up'
        },

        templates: this.templateObject,

        texts : {
          'filter': '',
          'limit': ''
        },

        responseAdapter({data}) {
          window.eventHub.$emit('dataCount',data.data.total)
          return {

            data: data.data.data,

            count: data.data.total
          }
        },

        requestAdapter: (data) => {

          let defaultParams = {
            "sort-field": data.orderBy,

            "sort-order": data.ascending ? 'desc' : 'asc',

            "search-query": data.query.trim(),

            "page": data.page,

            "limit": data.limit,
          };

          return { ...defaultParams, ...this.filterParams }
        },
        // we need this only for body of the ticket. For others its width can
        // be taken as max content
        columnsClasses: {
					ticket_number: 'ticket_number',
          subject: 'subject',
  			},

        sortable: this.sortableFields()
      }
    },
  },

  watch : {
    filterParams(){
      window.eventHub.$emit('refreshData');
    },
    columns(){
      this.prepareDynamicComponents();
      this.updateColumnNames();
      this.updateOptions();
      window.eventHub.$emit('refreshData');
    }
  },

  components : {
    'data-table' : require('components/Extra/DataTable'),
  }
}
</script>

<style>

  #dynamic-datatable-container .ticket_number {
    min-width: 130px;
  }

  #dynamic-datatable-container .subject {
    min-width: 300px;
  }

  #dynamic-datatable-container td{
    min-width: max-content !important;
  }

  #dynamic-datatable-container .VueTables__sortable {
    min-width: 100px;
  }

  /* overwriting datatable loader class  */
  .faveo-datatable-loader {
		margin-top: 0px !important;
		margin-bottom: 0px !important;
	}
  #dynamic-datatable {
    padding-bottom: 45px;
  }
</style>
