<template>
  <modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

    <div slot="title">

      <h4 class="modal-title">{{lang('task-plugin-template-modal-title')}}</h4>
    </div>

    <div  slot="fields" id="task-modal-container">

      <task-template-chooser ref="taskTemplateChooser" :ticketId="ticketId" :onComplete="onCompleted" />

    </div>

    <div v-if="loading" class="row" slot="fields" >

      <loader :animation-duration="4000" :size="60"/>
    </div>

    <div slot="controls">

      <button type="button" @click="onSubmit" class="btn btn-primary" :disabled="isDisabled">

        <i class="fas fa-check"></i> {{lang('task-plugin-template-selection-apply')}}
      </button>
    </div>
  </modal>
</template>

<script>

import TaskTemplateChooser from "./MiniComponents/TaskTemplateChooser";

export default {

  name: "TicketTaskTemplateModal",

  props:{

    showModal : { type : Boolean, default : false },

    onClose : { type : Function },

    ticketId : { type : String | Number, default : '' },

    reloadDetails : { type : Function }
  },

  data(){

    return {

      isDisabled:false,

      containerStyle:{ width:'500px' },

      loading:false,
    }
  },

  methods:{

    onSubmit(){

      this.$refs.taskTemplateChooser.apply();
    },

    onCompleted(){

      this.onClose();

      this.reloadDetails();
    }
  },

  components:{

    'modal':require('components/Common/Modal.vue'),

    'loader':require('components/Client/Pages/ReusableComponents/Loader'),

    'task-template-chooser' : TaskTemplateChooser
  }

}
</script>
