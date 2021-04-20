<template>
	
  <data-table :url="apiUrl" :dataColumns="columns"  :option="options" :tickets="tickets" 
    :scroll_to="scroll_to" :componentTitle="componentTitle">
        
  </data-table>
	
</template>

<script>

  import { mapGetters } from 'vuex'

	export default {
		
    name : 'agent-tickets-table',

		description : 'Agent panel tickets page',

		props : {
    
      id : { type: Number|String , default : ''},

      category : { type:String , default : 'inbox'},
    
      tickets : { type : Function, default : ()=>[]},

      apiUrl : { type : String, default : ''},

      componentTitle : { type : String, default :''},

      scroll_to : { type : String, default :''},

      from : { type : String, default : ''}
    },

		data() {

			return {

      selectedTickets : this.tickets,
      
      options : {},

      tableData : [],
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

          id:'Id',

          title: 'Subject', 

          ticket_number : 'Ticket Id',

          user: 'User', 

          assign_to : 'Assign to',

          updated_at:'Last activity'
        },

        columnsClasses : {

          id : 'ticket-id',

          title: 'ticket-title',

          ticket_number : 'ticket-number',

          user: 'ticket-user',

          assign_to: 'ticket-assign',

          updated_at: 'ticket-updated',
        },
        
        texts : { 

          filter : '', 

          limit : ''
        },
        
        templates: {

          title: function(createElement, row) {
              return createElement('a', {

              attrs: {
              
                href: self.basePath()+'/thread/' + row.id,
              }
            },(row.title ? row.title : " ")+ '('+row.thread_count+')');
            
          },
          
          updated_at(h,row){

            return self.formattedTime(row.updated_at)
          },
          
          user: function(createElement, row) {
              
            return createElement('a', {

              attrs: {
              
                href: self.basePath()+'/user/' + row.from.id,
              }
            }, row.from.full_name ? row.from.full_name : row.from.user_name);
          },

          assign_to: function(createElement, row) {
            
             return createElement('p', {

            }, row.assigned ? row.assigned.full_name : !Array.isArray(row.assigned_team) ? row.assigned_team.name : '---');
          },

          ticket_number: function(createElement, row) {
            
             return createElement('a', {

              attrs: {
              
                href: self.basePath()+'/thread/' + row.id,
              }
            }, '#'+row.ticket_number);
          }
        },
        
        sortable:  ['ticket_number','updated_at'],
        
        filterable:  ['ticket_number','updated_at'],
        
        pagination:{chunk:5,nav: 'fixed',edge:true},
        
        requestAdapter(data) {

          return {
        
            'sort-field': data.orderBy ? data.orderBy : 'updated_at',
        
            'sort-order': data.ascending ? 'desc' : 'asc',
        
            'search-query':data.query.trim(),
        
            'page':data.page,
        
            'limit':data.limit,
          }
        },
        
        responseAdapter({data}) {

          self.tableData = data.data;

          return {
        
            data: data.data.tickets.map(data => {
        
              return data;
        
            }),
        
            count: data.data.total
          }
        },

      }

    },

		computed:{

      columns(){

        return this.tableData.length === 0 ? ['ticket_number', 'title', 'user', 'assign_to', 'updated_at'] : 
        ['id', 'ticket_number', 'title', 'user', 'assign_to', 'updated_at']
      },

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

	.ticket-title,.ticket-user,.ticket-assign,.ticket-reply{ max-width: 250px; word-break: break-all;}

	#my_tic .VueTables .table-responsive {
		overflow-x: auto;
	}

	#my_tic .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
</style>