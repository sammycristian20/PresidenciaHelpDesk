<template>
  
  <div id="upload">
    
    <uploader :options="options" class="uploader-example drop" @file-complete="fileComplete(apiUrl)" ref="uploader" @file-error="errorHandling">
      
      <uploader-unsupport></uploader-unsupport>
      
      <uploader-drop>
      
        <h3>{{lang('Drop File Anywhere to Upload')}}</h3>
        
        <p>or</p>
        
        <uploader-btn type="button">{{lang('Upload files')}}</uploader-btn>
      </uploader-drop>
      
      <uploader-list></uploader-list> 
    </uploader>
  </div>
</template>

<script>
  
  import axios from 'axios'
  
  import {errorHandler, successHandler} from 'helpers/responseHandler'

  export default{
  
    props : {

      apiUrl : { type : String, default : '/media/files/public'},

      target_url : { type : String, default : '/chunk/upload/public'},

    },

    data(){
      
      return{
        
        options: {
         
          target: this.basePath()+this.target_url,
         
          testChunks: false,
         
          allowDuplicateUploads : true,
        
        },
      
      }
    
    },
    
    mounted(){

      $(function(){
        
        $('.uploader-btn').attr('type','button');
      });
    },
    
    methods:{

      errorHandling(rootFile, file, message, chunk){

        var error = JSON.parse(message)
        this.$store.dispatch('setAlert',{type:'danger',message:error.message, component_name : 'MediaLibrary'})
      },

      fileComplete(url){

        setTimeout(function(){

          $('.uploader-list').find('ul li').remove();
          
          axios.get(url).then(function(resp){

            window.eventHub.$emit('showGallery',resp.data);       
          }).catch(function(resp){

            console.log(resp);
          })
        },1000);
      }
    },
  };
</script>

<style scoped>
  .drop {
      width: 170px;
      text-align: center;
      padding: 50px 10px 0px 10px;
      margin: auto;
      width:100%;
      min-height: 250px;
  }
  .uploader-drop{
    border: none;
      background-color: #ffffff;
      min-height: 250px;
  }
  .btn:active{
      box-shadow: none;
  }
</style>

<style scoped>
.drop {
    width: 170px;
    text-align: center;
    padding: 10px 10px 0px 10px;
    margin: auto;
    width:100%;
    min-height: 150px;
}
.uploader-drop{
  border: none;
    background-color: #ffffff;
    min-height: 150px;
}
.btn:active{
    box-shadow: none;
}
</style>


        