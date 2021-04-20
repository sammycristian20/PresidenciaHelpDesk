<template>
  <div>
      <h1>{{ lang('1-hour') }}</h1>
      <h1>{{ total }}</h1>
    <h1>{{ timezone }}</h1>
    <h1>{{ time }}</h1>
      <button @click='updateRegistrations' ></button>
    <h1>{{users}}</h1>
    <faveo-datatable 
        v-bind:columns = "columns" 
        v-bind:rows = "tickets" 
        v-bind:noOfPages = "noOfPages"
        @update-page-number = "updatePageNumber"
        @update-search-query = "updateSearchQuery"
        @update-pagination = "updatePagination"
    />  
 
  </div>
</template>
<script>
  import FaveoDatatable from './../Extra/FaveoDatatable'
  //import {errorResponse} from './../../helpers/responseHandler'
  import { mapGetters } from 'vuex'
  export default {

    computed : {
      ...mapGetters({
          users : 'usersUnregistered',
          total:'totalRegistrations',
          timezone:'timezone'
        })
  },

      
    mounted(){
      this.columns = [{title: 'new field', field: 'uidss', sortable: true}, ...this.columns]
      this.fetchData();
    },

    components:{
      'faveo-datatable':FaveoDatatable
    },

    methods:{
        updateRegistrations(){
          this.$store.dispatch('register', 25)
        },

        modifyTicketsForRequiredView(ticketsFromServer){
          
          ticketsFromServer.map(ticket => {
              const fullNameOfTicketOwner = ticket['user']['first_name'] + ' ' + ticket['user']['last_name']
              const fullNameOfTicketAssignee = ticket['assigned']['first_name'] + ' ' + ticket['assigned']['last_name']

              ticket['from'] = ticket['user']['first_name'] ? fullNameOfTicketOwner : ticket['user']['user_name']

              ticket['assigned_to'] = ( ticket['assigned'].length == 0 ) ? "<label class = 'unassigned'>UNASSIGNED</label>" :
                                (ticket['assigned']['first_name'] ? fullNameOfTicketAssignee : ticket['assigned']['user_name'])
          })
          this.tickets = ticketsFromServer;
        },
  
        fetchData(){
            console.log('hello');
            axios.get('/api/agent/ticket-list?category=inbox').then(res=>(console.log(res)))
//            console.log(this.$store.getters.timezone)
              
        },

      //whenever a button for a new page is pressed, faveo-datatable component emits an event which is listened by this component and it updates its Current Page
      updatePageNumber(pageNumber){
        this.currentPage = pageNumber
      },

      //whenever a new pagination is requested, faveo-datatable component emits an event which is listened by this component and it updates its Pagination
      updatePagination(pagination){
        console.log('something');
        this.pagination = pagination
        this.fetchData();
      },

      //whenever a search query is entered, faveo-datatable component fires an event which is listened by this component and it updates the search query
      updateSearchQuery(searchQuery){
        this.searchQuery = searchQuery
      }


    },

    data:()=>({
      columns: [
          { title: 'Subject', field: 'title' },
          { title: 'Ticket Id', field: 'ticket_number', sortable: true },
          { title: 'From', field: 'from', sortable: true },
          { title: 'Assigned To', field: 'assigned_to', sortable: true },
          { title: 'Last Activity', field: 'updated_at', sortable: true },
      ],

      tickets: [],
      
      noOfPages: 0,
      
      searchQuery:'d',
      
      pagination:10,
    })
  } 
</script>
<style>
  .unassigned{
    color: red;
    font-weight: 500;
  }
</style>