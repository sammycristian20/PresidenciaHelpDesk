<template>
	
     <data-table :url="apiEndpoint" :dataColumns="columnArray"  :option="options" scroll_to="packages_title"></data-table>
	
</template>

<script>

  import { mapGetters } from 'vuex'

  import { lang } from 'helpers/extraLogics'

	export default {
		
    name : 'billing-packages-table',

		description : 'Billing packages table page',

		props : {

    	category : { type : String, default : ''},

    	columnArray : { type : Array | String, default : ''},

      apiEndpoint : { type:String , default : ''},

      optionsObj : { type:Object|String , default : ''},
    },

		data() {
			return {

      base:window.axios.defaults.baseURL,

      columns: this.columnArray,
      
      options : {},

      apiUrl: this.apiEndpoint,
			}
		},

		watch:{

			optionsObj(newValue,oldValue){
				this.updateTable();
				return newValue
			}
		},

    beforeMount(){

    	this.updateTable();

    },

		computed:{
      ...mapGetters(['formattedTime','formattedDate'])
		},

		methods :{

			updateTable(){

      const self = this;

      this.options = {

        sortIcon: {
          base : 'glyphicon',
          up: 'glyphicon-chevron-down',
          down: 'glyphicon-chevron-up'
        },

        headings: self.optionsObj.headings,

        texts : { 

          filter : '', 

          limit : ''
        },
        
        templates: self.optionsObj.templates,
        
        sortable: self.optionsObj.sortable,
        
        filterable:  self.optionsObj.filterable,
        
        pagination:{chunk:5,nav: 'scroll'},
        
        requestAdapter(data) {
          return {
        
            'sort-field': data.orderBy ? data.orderBy : 'id',
        
            'sort-order': data.ascending ? 'desc' : 'asc',
        
            'search-query':data.query.trim(),
        
            'page':data.page,
        
            'limit':data.limit,
          }
        },
        
        responseAdapter({data}) {

          return {
        
            data: data.data.data,
        
            count: data.data.total
          }
        },

      }
			}
		},

		components : {
			'data-table': require('components/Extra/DataTable')
		}
	};
</script>

<style type="text/css">
  .ticket-updated{
    width:20% !important;
    word-break: break-all;
  }
  .ticket-number{
    width:20% !important;
    word-break: break-all;
  }
  .ticket-title{
    width:30% !important;
    word-break: break-all;
  }
  .ticket-user{
    width:15% !important;
    word-break: break-all;
  }
  .ticket-assigned{
    width:15% !important;
    word-break: break-all;
  }  
</style>