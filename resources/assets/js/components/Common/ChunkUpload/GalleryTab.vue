<template>
  
  <div id="gallery">
    
    <div class="row col-sm-12">

      <div :class="[mediaObj.filename!=undefined ? 'col-sm-8' : 'col-sm-12' ]">
        
        <div class="row" id="media-filter">
        
          <div class="col-sm-3 top-filter">

            <type-filter :search="search" v-on:filtering="dropdownFilter"></type-filter>
          
          </div>
          
          <div class="col-sm-3 top-filter">
            
            <day-filter :search="search" v-on:filtering="dropdownFilter"></day-filter>
          
          </div>
          
          <div class="col-sm-3 top-filter" >
            
            <month-filter :search="search" v-on:filtering="dropdownFilter"></month-filter>
          
          </div>

          <div class="col-sm-3 top-filter">
          
            <year-filter :search="search" v-on:filtering="dropdownFilter"></year-filter>
          
          </div>
        
        </div>

        <div class="image-view" v-scroll-ajax="loadMoreGallery">

          <ul class="list-inline row col-sm-12">
            
            <li class="col-sm-12 no-cont" v-if="mediaGallery.length==0">{{lang('no_media_file')}}</li>
            
            <li class="gallery-list" :class="[mediaObj.filename!=undefined ? 'col-sm-4' : 'col-sm-3' ]" 
              v-if="mediaGallery.length>0" v-for="(object,index) in mediaGallery" @click="insert(object,index)">
            
              <input type="radio" name="selection" :id="getId(index)" v-model="selection" class="input-hidden" :value="index+1"/>
              
              <label :for="getId(index)">
                
                <template v-if="object.thumbnail_url">
                  
                  <img class="media-image" :src="object.thumbnail_url" width="130px"  height="130px"/>
                </template>
                
                <template v-else>
                  
                  <img v-if="object.type=='image'" class="media-image" :src="object.base_64" width="130px" 
                  height="130px"/>
              
                <img v-else-if="object.type=='application' || object.type=='text'" class="media-image" 
                  :src="getApplicationFileImage(object.extension)" width="130px" height="130px"/>
              
                <img v-else-if="object.type=='audio'" class="media-image" width="130px" height="130px"
                    :src="basePath()+'/themes/default/common/images/audio1.png'"/>
              
                <img v-else-if="object.type=='video' || object.type=='inode'" class="media-image" 
                  :src="basePath()+'/themes/default/common/images/video1.png'" width="130px" height="130px"/>
              
                <img v-else class="media-image" :src="basePath()+'/themes/default/common/images/common.png'" 
                  width="130px" height="130px"/>
                </template>
              </label>
            </li>
            
            <li class="col-sm-12">
              
              <div v-if="loading">
              
                <loader :animation-duration="4000" :size="40"/>
              </div>
            </li>
          </ul>
        </div>
      </div>
    
      <div v-if="mediaObj.filename!=undefined" class="col-sm-4 media-info">
        
        <!-- media info -->
        <media-info :mediaObj="mediaObj"  :page="page"></media-info>
      </div>
    </div>
  </div>
</template>

<script>
  
  import { ScrollAjax }  from 'components/directive/scrollAjax.js';
  
  import { mapGetters } from 'vuex'

  import axios from 'axios'
  
  export default{
    
    props : {

      allgallery : { type : Array | Object },

      showloader : { type : Boolean },

      commonObj : { type : Object },

      page : { type : Number | String },

      apiUrl : { type : String, default : '/media/files/public'},

    },
    
    data(){
      
      return{
        
        search:{month:'',year:'',day:'',type:''},
        
        mediaObj:{},
        
        selection:false,
        
        mediaGallery:this.allgallery,
        
        baseObj:this.commonObj,

        loading : false
      
      }
    
    },
       
    directives: {
      
      ScrollAjax
    
    },
    
    computed:{
      
      loaderOption:{
        
        get(){
          
          return this.showloader;
        
        },
        
        set(value){
          
          return value;
        
        }
      
      },
    },
    
    components: {
      
      'type-filter': require('./Filters/TypeFilter.vue'),
      
      'day-filter': require('./Filters/DayFilter.vue'),
      
      'month-filter': require('./Filters/MonthFilter.vue'),
      
      'year-filter': require('./Filters/YearFilter.vue'),
      
      'media-info': require('./MediaInfo.vue'),

      'loader':require('components/Client/Pages/ReusableComponents/Loader'),
    
    },
    
    created(){
      
      window.eventHub.$on('showGallery', this.getNewGallery);
    
    },  
    
    methods:{
      
      //assign Id
      getId(x){
        
        return "media"+x;
      
      },

      getApplicationFileImage(ext){

        let extensions = ['text','zip','pdf','csv','xlsx','xls','doc','docx'];

        if(extensions.includes(ext)){

          return  this.basePath()+"/themes/default/common/images/" + ext + '.png';
        } else{

          return  this.basePath()+"/themes/default/common/images/common.png";
        }
      },
      
      //get new gallery
      getNewGallery(x){
        
        this.mediaGallery=x.data;
        
        this.insert(this.mediaGallery[0],'newUpload');
      
      },
      
      //get image size
      bytesToSize(bytes) {
        
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        
        if (bytes == 0) return '';
        
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
      
      },
      
      //ratio convertion
      gcd (a, b) {
        
        return (b == 0) ? a : this.gcd (b, a%b);
      
      },
      
      insert(x,y){

        this.$emit('disable',x.type,x.extension);
        
        if(x.type=="image"){
          
          var r = this.gcd (x.width,x.height);
          
          x['ratio']={};
          
          x['image_height']=x.height;
          
          x['image_width']=x.width;
          
          x.ratio['width']=x.width/r;
          
          x.ratio['height']=x.height/r;
          
          x['showDimension']=true;
          
          x['png'] = x.base_64;
        
        } else if(x.type=="text"){
                    
          x['png'] = this.getApplicationFileImage(x.type);
          
          x['showDimension']=false;         
        } else{
              
          x['png'] = this.getApplicationFileImage(x.extension);  
          
          x['showDimension']=false;
        }
                  
        var h=this.bytesToSize(x.size);
                  
        x['fileSize']=h;
                  
        this.mediaObj=x;
                  
        this.$emit('getSize',x);
                  
        if(y=='newUpload'){
                    
          setTimeout(()=>{
                      
            $('#media0').prop( "checked", true );      
          },200)
        }
      },
      
      //load more gallery
      loadMoreGallery(){

        var vm=this;
        
        vm.loaderOption=true;

        vm.loading=true;
        
        if(vm.baseObj.next_page_url==null){
          
          vm.loaderOption=false; 

          vm.loading=false; 
        
        } else{
          
          axios.get(this.baseObj.next_page_url).then(function(resp){
            
            vm.mediaGallery=vm.mediaGallery.concat(resp.data.data);
            
            vm.baseObj=resp.data;
            
            vm.loaderOption=false;

            vm.loading=false;
          
          }).catch(function(resp){
            
            console.log(resp);
            
            vm.loaderOption=false;

            vm.loading=false;
          
          })
        
        }
      
      },
      
      //filter media via dropdown
      dropdownFilter(filter){
        
        var vm=this;
        
        this.$emit('disable','disable');
        
        if((filter.type!=""||filter.type=="")&&filter.year==""&&filter.month==""&&filter.day!=""){
          
          alert('Please Select a Particular Month and Year')
        
        }
        
        else if((filter.type!=""||filter.type=="")&&filter.year==""&&filter.month!=""&&(filter.day!=""||filter.day=="")){
          
          alert('Please Select a Particular Year')
        
        }
        
        else if((filter.type!=""||filter.type=="")&&filter.year!=""&&filter.month==""&& filter.day!=""){
          
          alert('Please Select a Particular month')
        
        }
        
        else{
          
          var params=filter;
          
          vm.loaderOption=true;
          
          vm.loading=true;

          this.mediaObj={};
          
          axios.get(this.apiUrl,{params}).then(function(resp){
            
            vm.mediaGallery=resp.data.data;
            
            vm.baseObj=resp.data;
            
            vm.loaderOption=false;

            vm.loading=false;
            
            $('#media0').prop( "checked", false );
          
          }).catch(function(resp){
            
            console.log(resp);
            
            vm.mediaGallery=[];
            
            vm.loaderOption=false;

            vm.loading=false;
          
          })
        
        }
      
      }
    
    }
  
  };
</script>

<style scoped>
.input-hidden {
  position: fixed;left: -9999px;
}
input[type=radio]:checked + label>img {
  border: 1px solid #fff;box-shadow: 0 0 0 4px #0073aa;
}
.media-image{
    vertical-align: inherit;object-fit: cover;background-color: gainsboro;border: 1px solid #c1c1c1;
}
.gallery-list{
   margin-left: 0px;width: 147px;height: 150px;
}
.list-inline{
   margin-top: 40px;
}
.media-info{
   border-left:1px solid #ddd;
}
.top-filter{
  padding: 10px;
}
.image-view{
   height: 330px;;overflow-y: auto;overflow-x: hidden
}
.gellery-teb{
   padding-left: 0px;padding-right: 0px;
}
.no-cont{
    margin: 15px;
    text-align: center;
}
#media-filter{
  margin-left: -23px !important
}
</style>