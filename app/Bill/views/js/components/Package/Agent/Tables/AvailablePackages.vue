<template>
	<div class="avail-pack">
     <data-table :url="apiEndpoint" :dataColumns="columns"  :option="options" scroll_to="packages_title"></data-table>
	</div>
</template>

<script>

  import { mapGetters } from 'vuex'

  import { lang } from 'helpers/extraLogics'

	export default {
		
    name : 'available-packages',

		description : 'Available packages table page',

		props : {

    	category : { type : String, default : ''},

      apiEndpoint : { type:String , default : ''},
    },

		data() {
			return {

      base:window.axios.defaults.baseURL,
      
      options : {},

      apiUrl: this.apiEndpoint,

      columns : ['name','validity','allowed_tickets','price','status']
			}
		},

    beforeMount(){

      const self = this;

      this.options = {

        sortIcon: {
          
          base : 'glyphicon',
          
          up: 'glyphicon-chevron-down',
          
          down: 'glyphicon-chevron-up'
        },

        headings: { 

					due_by : 'Due date',
		    },

		    columnsClasses : {

          name: 'pack-name',

          validity : 'pack-valid',

          allowed_tickets : 'pack-limit',

          price : 'pack-amount',

          status: 'order-status'
        },

        texts : { 

          filter : '', 

          limit : ''
        },
        
        templates: {
								
					status: 'data-table-status',

					validity(h,row){
						
						return row.validity === null ? lang('one_time') : lang(row.validity);
					}
				},
        
        sortable:  ['name','validity','allowed_tickets','price','status'],
		        
		    filterable:  ['name','validity','allowed_tickets','price','status'],
        
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

    },

		computed:{
      ...mapGetters(['formattedTime','formattedDate'])
		},

		methods :{

		},

		components : {
			'data-table': require('components/Extra/DataTable')
		}
	};
</script>

<style type="text/css">
  .pack-name{
    width : 25%;
    word-break: break-all;
  }
  .pack-valid{
    width : 25%;
    word-break: break-all;
  }
  .pack-limit{
    width : 20%;
    word-break: break-all;
  }
  .pack-amount{
    width : 15%;
    word-break: break-all;
  }
  .pack-status{
    width : 15%;
    word-break: break-all;
  }  
</style>