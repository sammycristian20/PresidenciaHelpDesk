<template>

	<div style="min-height: 330px;">
		
		<h4 class="mt-1">Preview</h4>

		<div class="row">

			<div class="col-sm-6">                 

				<template v-if="mediaObj.thumbnail_url">
					
					<img :src="mediaObj.thumbnail_url" class="preview-img"  width="180px"/>
				</template>

				<template v-else>
					
					<img v-if="mediaObj.type=='image'" :src="mediaObj.base_64" class="preview-img"  width="180px"/>
				
					<img v-else-if="mediaObj.type=='application' || mediaObj.type=='text'" 
						:src="getApplicationFileImage(mediaObj.extension)" class="preview-img" width="180px"/>
					
					<img v-else-if="mediaObj.type=='audio'" :src="basePath()+'/themes/default/common/images/audio1.png'" 
						class="preview-img" width="180px"/>

					<img v-else-if="mediaObj.type=='video' || mediaObj.type=='inode'" class="preview-img"  width="180px"
						:src="basePath()+'/lb-faveo/media/images/video1.png'"/>

					<img v-else :src="basePath()+'/themes/default/common/images/common.png'" class="preview-img" width="180px"/>
		
				</template>
			</div>

			<div class="col-sm-6">

				<label class="image-details" style="display:block">{{mediaObj.filename}}</label>
				
				<p class="image-details">{{mediaObj.fileSize}}</p>
				
				<p class="image-details" v-if="mediaObj.showDimension">{{mediaObj.width}} &nbsp;&nbsp;{{mediaObj.height}}</p>
				
				<p style="word-wrap: break-word;">{{mediaObj.modified}}</p>
			</div>
		</div>

		<div class="row">

			<div class="col-sm-2" v-if="mediaObj.showDimension">

				<label style="line-height: 2">{{lang('Width')}}</label>
			</div>

			<div class="col-sm-10" id="info-field" v-if="mediaObj.showDimension">

				<input type="number" class="form-control" v-model="fileObj.image_width" min='0' @keyup="widthChange(fileObj.image_width)"> 
			</div>
		</div>

		<div class="row">

			<div class="col-sm-2" v-if="mediaObj.showDimension">

				<label style="line-height: 2">{{lang('Height')}}</label>
			</div>

			<div class="col-sm-10" id="info-field" v-if="mediaObj.showDimension">

				<input type="number" class="form-control" v-model="fileObj.image_height" min='0' @keyup="heightChange(fileObj.image_height)">
			</div>
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
			}
		},

		watch:{
				
			mediaObj(newval,oldval){
					
				this.fileObj.image_height=newval.image_height;
					
				this.fileObj.image_width=newval.image_width;
			}
		},

		methods:{
			
			getApplicationFileImage(ext){

        let extensions = ['text','zip','pdf','csv','xlsx','xls','doc','docx'];

        if(extensions.includes(ext)){

          return  this.basePath()+"/themes/default/common/images/" + ext + '.png';
        } else{

          return  this.basePath()+"/themes/default/common/images/common.png";
        }
      },

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
	};
</script>

<style scoped>
  .preview-img{
       width:100%;margin-bottom: 20px;height: 125px;background: gainsboro;
  }
  .image-details{
       word-wrap: break-word;
  }
  #info-field{

  	margin-bottom: 5px;
  }
</style>