<template>
  
  <div class="media-lib">

    <button type="button" class="btn btn-default media-btn" data-toggle="modal" data-target="#myModal" 
      @click="getGallery()">

      <i class="far fa-caret-square-right"></i>&nbsp;{{lang('add-media')}}&nbsp;

    </button>

    <slot name="templateBtn"></slot>

    <div id="myModal" class="modal fade" role="dialog">

      <div class="modal-dialog modal-xl" >

        <div class="modal-content">

          <div class="modal-header">
            
            <h4 class="modal-title">{{lang('insert-media')}}</h4>

            <button type="button" class="close" data-dismiss="modal">&times;</button>
          
          </div>

          <div class="modal-body">
            
            <alert componentName="MediaLibrary" />
            
            <div class="row">
              
              <div class="col-sm-12 media-tab">
                      
                <ul class="nav nav-tabs" id="mytabs">
                  
                  <li class="nav-item">

                    <a class="nav-link" :class="{ 'active' : category == 'upload' }" @click="category = 'upload'" href="javascript:;">  

                      {{lang('upload-files')}}
                    </a>
                  </li>
                        
                  <li class="nav-item">

                    <a class="nav-link" :class="{ 'active' : category == 'gallery' }" @click="category = 'gallery'" href="javascript:;">

                      {{lang('media-library')}}
                    </a>
                  </li>
                </ul>

                <div class="tab-content">
                  
                  <div class="active tab-pane" role="tabpanel">
                    
                    <gallery-tab v-if="showGallery && category == 'gallery'" :allgallery="gallery" :showloader="loader"  
                      :commonObj="mediaObj" v-on:disable="checkDisable" v-on:getSize="setImageSize" :page="page" 
                      :apiUrl="apiUrl">
                        
                    </gallery-tab>

                    <!-- Upload Tab -->
                    <upload-tab v-if="category == 'upload'" :apiUrl="apiUrl" :target_url="target_url"></upload-tab>
                  </div>
                          
                    <!-- Gallery Tab -->
                    <div v-if="simpleLoader" class="row" id="loader">

                      <loader :animation-duration="4000" :size="40"/>
                    </div>                    
                </div>
              </div>
                  
            </div>
            
          </div>

          <div class="modal-footer">
        
            <button v-if="page != 'canned' && page!= 'reply' && page != 'ticket'  && page != 'internal_note'" type="button" 
              class="btn btn-primary btn-md" :disabled="inlineBtnDisable" data-dismiss="modal" @click="addImgeToEditor()">
              <i class="fas fa-clone"> </i> 
              {{lang('inline')}}</button>
                  
            <button v-if="page != 'kb'" type="button" class="btn btn-primary btn-md" :disabled="disableBtn"  
              @click="addAttachment()" data-dismiss="modal">
              <i class="fas fa-paperclip"> </i> 
            {{lang('attach')}}</button>
              
            <button type="button" class="btn btn-danger btn-md" :disabled="disableBtn" @click="showModal = true">
              <i class="fas fa-trash"> </i> 
              {{lang('delete')}}
            </button>
          </div>
        </div>
      </div>
    </div>

    <delete-popup v-if="showModal" :showModal="showModal" :path="inlineImage.pathname" :onClose="onClose">
      
    </delete-popup>
  </div>
</template>

<script>
  
  import axios from 'axios'

  export default{

    props : {

      inlineFiles : { type : Object | Array },

      attachmentFiles : { type : Object | Array },

      apiUrl : { type : String, default : '/media/files'},

      target_url : { type : String, default : '/chunk/upload'},

      page : { type : String | Number },

      onInlinePdf : { type : Function }

    },
          
    data(){
      
      return{
        
        gallery:[],
                 
        loader:true,
                 
        mediaObj:{},
                 
        inlineBtnDisable:true,
                 
        disableBtn:true,
        
        inlineImage:{},        

        allMedia:[],
        
        simpleLoader:true,
                 
        showGallery:false,

        showModal : false,

        category : 'gallery'
      }
    
    },
    
    created() {

      window.eventHub.$on('showGallery',()=>{

        this.category = 'gallery';

        this.showGallery = false;

        this.getGallery();
      });       
    },

    watch : {

      category(newValue,oldValue){

        if(newValue == 'upload') {

          this.inlineBtnDisable=true;
        
          this.disableBtn=true;
        }
      }
    },

    components: {
            
      'upload-tab':require('./UploadTab.vue'),
            
      'gallery-tab':require('./GalleryTab.vue'),
            
      'delete-popup':require('./DeletePopup.vue'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),

      "alert": require("components/MiniComponent/Alert"),
    },
        
    methods:{
      
      getGallery(){
        
        this.simpleLoader=true;
        
        this.gallery=[];
        
        axios.get(this.apiUrl).then(res=>{
          
          this.simpleLoader=false;
          
          this.gallery=res.data.data;
                       
          this.mediaObj=res.data;
          
          this.loader=false;
          
          this.showGallery=true;
        
        }).catch(error=>{
                       
          this.simpleLoader=false;
          
          this.loader=false;
          
          this.showGallery=true;
        
        })
      
      },
      
      //add imgae to editor
      addImgeToEditor(){
        
        if(this.inlineImage.extension === 'pdf'){
         
          var link = this.inlineImage.base_64.replace(/\s/g,'%20');

          var value = "<a  target='_blank' href=" + link + ">"+this.inlineImage.filename +"</a>";

          this.onInlinePdf(value);
        }
      },
            
      //add attachment
      addAttachment(){
        
        this.allMedia.push(this.inlineImage);
                   
        this.$emit('getAttach',this.inlineImage);
      
      },
            
      //set inline image size
      
      setImageSize(payload){
        
        this.inlineImage=payload;
      
      },
            
      //disable button
      checkDisable(x,y){
        
        if(x=="disable"){
          
          this.inlineBtnDisable=true;
        
          this.disableBtn=true;
        
        }else{
                  
          this.disableBtn=false;
                  
          if (y == "pdf") {
            
            this.inlineBtnDisable=false;
          
          }else {
            
            this.inlineBtnDisable=true;     
          
          }

        }
                 
      },

      onClose(){

        this.showGallery=false;
      
        this.showModal = false;

        this.getGallery();
      }
    }
  };
</script>

<style scoped>
  .modal-dialog{
    width: 1000px;
    overflow: hidden !important;
  }
  .media-lib{
      margin-bottom: 9px;
  }
  #loader{
    margin-top: 20px;
    margin-bottom: 20px;
  }
  #link{
    cursor: pointer;
  }
  .modal-title {
    font-weight: 400 !important;
  }
</style>
  