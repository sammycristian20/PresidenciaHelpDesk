<template>

  <div id="task-ticket-actions">

    <div v-if="ticketActions.has_calender" class="btn-group">
      
      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" id="task-action">
        
        <i class="far fa-calendar-plus"> </i> {{ lang('task') }} 

        <span class="badge badge-danger">{{taskCount}}</span>
        
      </button>
        
      <div class="dropdown-menu">
  
        <a class="dropdown-item" href="javascript:;" @click="showModal = true"> 

           <i class="fas fa-plus"> </i> {{ lang('create_task') }}
        </a>

        <a class="dropdown-item" href="javascript:;" @click="showTemplateChooserModal = true">

            <i class="fa fa-bars"> </i> {{ lang('task-plugin-template-ticket-action-button') }}
          </a>
          
        <a class="dropdown-item" href="javascript:;" v-scroll-to="'#timeline-display-box-tasks'" > 

          <i class="fas fa-eye"> </i> {{ lang('view') }}
        </a>
      </div>
    </div>

    <transition name="modal">

      <task-create-modal v-if="showModal" :onClose="onClose" :showModal="showModal"
        :ticketId="ticketId" componentTitle="timeline" :reloadDetails="reloadData">



      </task-create-modal>
    </transition>

    <transition name="modal">
        <task-template-modal v-if="showTemplateChooserModal" :onClose="onTemplateChooserClose"
           :showModal="showTemplateChooserModal" :ticketId="ticketId"
           componentTitle="timeline" :reloadDetails="reloadData"
        />
    </transition>

  </div>

</template>

<script>

  import {mapGetters} from 'vuex';

  export default {

      name: 'task-ticket-actions',

      description : 'Contains ticket actions on timline page, specific to Task Plugin',

      props :{
       
        data: {type: String|Object, required: true}
      },

      data(){
      
        return {
      
          showModal : false,
          showTemplateChooserModal: false,
        }
      },

      computed : {

        ticketId() {

           return JSON.parse(this.data).ticket_id;
        },

         taskCount() {

           return JSON.parse(this.data).task_count;
        },

        ...mapGetters({ticketActions : 'getTicketActions'}),
      },

      methods : {

        onClose(){
          
            this.showModal = false;

            this.$store.dispatch('unsetValidationError');
        },


        onTemplateChooserClose() {
          this.showTemplateChooserModal = false;

          this.$store.dispatch('unsetValidationError');
        },

          reloadData() {

            window.eventHub.$emit('updateTimelineActions')
          },
      },

      components : {

         'task-create-modal' : require('./TaskModal'),
         'task-template-modal' : require('./TicketTaskTemplateModal'),
      }
  };

</script>