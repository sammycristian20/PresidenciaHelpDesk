<template>

  <div class="card-tools">

    <loader v-if="loading" :animation-duration="4000" color="#1d78ff" :size="60"/>

    <div class="btn-group">

      <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">

        <i class="fas fa-cog"> </i> {{lang('actions')}} 
      </button>

      <div class="dropdown-menu dropdown-menu-right" id="more_actions">

        <a v-if="!notEdit" :href="basePath()+'/tasks/task/'+task.id+'/edit'" class="dropdown-item">
            <i class="fas fa-edit" style="color: #0d6aad"> </i> {{lang('edit')}}
          </a>
        
         <a href="javascript:;" @click="showDeleteModal = true" class="dropdown-item">
          <i class="fas fa-trash" style="color: red"> </i> {{lang('delete')}}
        </a>
        
      </div>
    </div>

     <div class="btn-group">

      <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown">

        <i class="fas fa-exchange-alt"> </i> {{lang('status')}} 
      </button>

      <div class="dropdown-menu dropdown-menu-right" id="more_actions">

        <a  href="#" :class="task.status == 'Open' ? 'disabledLink' : ''" class="dropdown-item" 
             @click.prevent="changeTaskStatus('open')">
             <i class="far fa-clock" style="color: limegreen"> </i> {{lang('open')}}
          </a>

          <a  href="#" :class="task.status == 'Closed' ? 'disabledLink' : ''" class="dropdown-item"
             @click.prevent="changeTaskStatus('close')">
             <i class="fas fa-minus-circle" style="color: orangered"> </i> {{lang('closed')}}
          </a>

          <a  href="#" :class="task.status == 'In-progress' ? 'disabledLink' : ''" class="dropdown-item"
             @click.prevent="changeTaskStatus('inprogress')">
             <i class="fas fa-exclamation-triangle" style="color: orange"></i>{{lang('inprogress')}}
          </a>
      </div>
    </div>


     <transition name="modal">

      <delete-modal v-if="showDeleteModal" :onClose="onClose" :showModal="showDeleteModal"
          alertComponentName="tasks-view" :deleteUrl="'/tasks/task/' + task.id"
          redirectUrl="/tasks/task">

      </delete-modal>

		</transition> 

  </div>
</template>

<script>

  import { getSubStringValue } from 'helpers/extraLogics'

  import {errorHandler, successHandler} from 'helpers/responseHandler'

  import axios from 'axios'

  export default {

    props : {

      task : { type : Object, default : ()=> {}},
      notEdit: {type: Boolean,default: false}
    },

    data() {

      return {

        showDeleteModal : false,

        showTaskStatus : false,

        actions : '',

        loading: false
      }
    },


    methods : {

      subString(value,length = 15){

        return getSubStringValue(value,length)
      },


      onClose(){

        this.showDeleteModal = false;

        this.showTaskStatus = false;

      },

      changeTaskStatus(status) {
          this.loading = true;
          axios.get('/tasks/api/change-task/'+this.task.id+'/'+status)
          .then((res) => {
            this.loading = false;
              successHandler(res,'taskDetails');
              setTimeout(()=>location.reload(),1350);
          })
          .catch((err)=> {
            this.loading = false;
              errorHandler(err,"taskDetails");
          })

      }
    },

    components : {

      'delete-modal': require('components/MiniComponent/DataTableComponents/DeleteModal'),
      'loader' : require('components/MiniComponent/Loader')

    }
  };
</script>

<style scoped>

  .wrapper {
    cursor: not-allowed !important;
  }

  a.disabledLink {
      pointer-events: none !important;
  }
</style>
