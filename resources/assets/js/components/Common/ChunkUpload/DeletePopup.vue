<template>

  <div>
  
    <modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

      <div slot="title">

        <h4>{{lang('delete')}}</h4>
      </div>

      <div v-if="!loading" slot="fields">

        <span>{{lang('media_delete')}}</span>
      </div>

      <div v-if="loading" class="row" slot="fields" >
       
        <loader :animation-duration="4000" color="#1d78ff" :size="60"/>
      </div>

      <div slot="controls">
       
        <button type="button" @click = "onSubmit" class="btn btn-danger" :disabled="isDisabled">

          <i class="fas fa-trash" aria-hidden="true"></i> {{lang('delete')}}
        </button>
      </div>

    </modal>
  </div>
</template>

<script>
  
  import axios from 'axios'
  
  import {errorHandler, successHandler} from 'helpers/responseHandler'

  export default {

    name : 'delete-modal',

    description : 'Delete Modal component',

    props:{

      showModal:{type:Boolean,default:false},

      onClose:{type: Function},

      path : { type : String, default : ''}
    },

    data:()=>({

      isDisabled:false,

      containerStyle:{ width:'500px' },

      loading:false,

    }),

    methods:{

    onSubmit(){

      this.loading = true

      this.isDisabled = true

      const obj = {};

      obj['file']=this.path;

      axios.post('/media/files/delete',obj).then(res=>{

        this.$store.dispatch('setAlert',{type:'success',message:'Deleted successfully',component_name:'MediaLibrary'})

        this.onClose();

        this.loading = false;

        this.isDisabled = true

      }).catch(err => {

        this.onClose();

        this.loading = false;

        this.isDisabled = true
      })
    }
  },

  components:{

    'modal':require('components/Common/Modal.vue'),
    
    'alert' : require('components/MiniComponent/Alert'),
    
    'loader':require('components/Client/Pages/ReusableComponents/Loader'),
  }

};
</script>
