<style scoped>
	.preview-img{
			 margin-bottom: 20px;max-height: 250px;background: gainsboro;
	}
	.image-details{
			 word-wrap: break-word;
	}
</style>
<template>
              <div style="min-height: 330px;">
                                                <div ng-show="preview" class="col-sm-12">
                                                    <h4>Preview</h4>
                                                  <div class="col-sm-6">                            
                                                    <img v-if="mediaObj.type=='image'" :src="mediaObj.base_64" class="preview-img"  width="180px"/>
                                                    <img v-else-if="mediaObj.type=='application' || mediaObj.type=='text'" 
                                                    :src="base+'/themes/default/common/images/'+mediaObj.extension+'.png'" 
                                                    class="preview-img"  width="180px"/>
                                                    <img v-else-if="mediaObj.type=='audio'" 
                                                    :src="base+'/themes/default/common/images/audio1.png'" 
                                                    class="preview-img"  width="180px"/>
                                                    <img v-else-if="mediaObj.type=='video' || mediaObj.type=='inode'" 
                                                    :src="base+'/lb-faveo/media/images/video1.png'" 
                                                    class="preview-img"  width="180px"/>
                                                         
                                                  <img  v-else :src="base+'/themes/default/common/images/common.png'" class="preview-img"  width="180px"/>
                                                  </div>
                                                  <div class="col-sm-6">
                                                    <label class="image-details" style="display:block">{{mediaObj.filename}}</label>
                                                    <p class="image-details">{{mediaObj.fileSize}}</p>
                                                    <p class="image-details" v-if="mediaObj.showDimension">{{mediaObj.width}} &nbsp;&nbsp;{{mediaObj.height}}</p>
                                                    <p style="word-wrap: break-word;">{{mediaObj.modified}}</p>
                                                  </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label style="line-height: 2">{{lang('PATH')}}</label>
                                                  </div>
                                                <div class="col-sm-9">
                                                     <input type="text" v-bind:value="mediaObj.pathname" class="form-control" v-on:input="mediaObj.pathname = $event.target.value" readonly="true">
                                                </div>
                                                <div class="col-sm-2" v-if="mediaObj.showDimension">
                                                    <label style="line-height: 2">{{lang('Width')}}</label>
                                                </div>
                                                <div class="col-sm-9" v-if="mediaObj.showDimension">
                                                    <input type="number" class="form-control" v-model="fileObj.image_width" min='0' @keyup="widthChange(fileObj.image_width)">
                                                </div>
                                                <div class="col-sm-2" v-if="mediaObj.showDimension">
                                                    <label style="line-height: 2">{{lang('Height')}}</label>
                                                </div>
                                                <div class="col-sm-9" v-if="mediaObj.showDimension">
                                                  <input type="number" class="form-control" v-model="fileObj.image_height" min='0' @keyup="heightChange(fileObj.image_height)">
                                                </div>
            </div>
</template>
<script>
    import { mapGetters } from 'vuex'
  export default{
      props:['mediaObj'],
       data(){
            return{
                fileObj:{image_height:this.mediaObj.image_height,image_width:this.mediaObj.image_width},
                base : ''
            }
       },

       computed : {
        ...mapGetters(['getUserData'])
      },

       watch:{
           mediaObj(newval,oldval){
                this.fileObj.image_height=newval.image_height;
                this.fileObj.image_width=newval.image_width;
           }
       },

       beforeMount(){
            this.base = this.getUserData.base_url;
      },

       methods:{
         widthChange(x){
              var v=(x/this.mediaObj.ratio.width)*this.mediaObj.ratio.height;
              this.fileObj = Object.assign({}, this.fileObj, { image_height: Math.round(v)});
              this.mediaObj.image_height=this.fileObj.image_height;
              this.mediaObj.image_width=this.fileObj.image_width;
              this.$emit('getOn',this.mediaObj);
         },
         heightChange(x){
              var v=(x/this.mediaObj.ratio.height)*this.mediaObj.ratio.width;
              this.fileObj = Object.assign({}, this.fileObj, { image_width: Math.round(v)});
              this.mediaObj.image_width=this.fileObj.image_width;
              this.mediaObj.image_height=this.fileObj.image_height;
              this.$emit('getOn',this.mediaObj);
         }
       }
  }
</script>