<template>
	
  <data-table :url="apiUrl" :dataColumns="columns"  :option="options" :tickets="tickets" 
    scroll_to="client-tickets-table" :color="layout.portal.client_header_color" 
    :alert-component-name="alertComponentName">
        
  </data-table>
	
</template>

<script>

  import { mapGetters } from 'vuex'

	export default {
		
    name : 'client-tickets-table',

		description : 'Client panel tickets page',

		props : {
    
      tickets : { type : Function, default : ()=>[]},

      layout : { type : Object, default : ()=>{}},

      apiUrl : { type : String, default : ''},

      loggedInId : { type : String | Number, default : ''},

      /**
			 * Alert component name to dispatch alert box
			 */
			alertComponentName: { type: String, default: '' },
  },

		data() {

			return {

      selectedTickets : this.tickets,

      columns: ['id', 'ticket_number', 'title', 'user', 'last_replier', 'updated_at'],
      
      options : {},
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

          last_replier : 'Last replier',

          updated_at:'Last activity'
        },

        columnsClasses : {

          id : 'ticket-id',

          title: 'ticket-title',

          ticket_number : 'ticket-number',

          user: 'ticket-user',

          last_replier: 'ticket-reply',

          updated_at: 'ticket-updated',
        },
        
        texts : { 

          filter : '', 

          limit : ''
        },
        
        templates: {

          title: function(createElement, row) {

            var cc_tag = '';

            if(self.loggedInId != row.user_id){

              if(row.collaborator.length > 0){

                let result = row.collaborator.map(a => a.user_id);

                let present = result.includes(self.loggedInId);

                cc_tag = present ? ' <span class="badge badge-info">CC</span>' : '';  
              }
            }

            return createElement('router-link', {

              attrs: {
              
                to: '/check-ticket/' + row.encrypted_id,
              
                style : `color:${self.layout.portal.client_header_color}`
              },
              domProps: {
                innerHTML: row.first_thread.title + '('+row.thread_count+')' + cc_tag
              },
            });
          },
          
          updated_at(h,row){

            return self.formattedTime(row.updated_at)
          },
          
          user: function(createElement, row) {
              
             return createElement('p', {

            }, row.user.full_name);
          },

          last_replier: function(createElement, row) {
            
             return createElement('p', {

            }, row.last_thread.user.full_name);
          },

          ticket_number: function(createElement, row) {
            
             return createElement('router-link', {

              attrs: {
              
                to: '/check-ticket/' + row.encrypted_id,
              
                style : `color:${self.layout.portal.client_header_color}`
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
          
          return { data : data.data.data, count: data.data.total }
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

	.ticket-title,.ticket-user,.ticket-reply{ max-width: 250px; word-break: break-all;}

	#my_tic .VueTables .table-responsive {
		overflow-x: auto;
	}

	#my_tic .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
	}
</style>