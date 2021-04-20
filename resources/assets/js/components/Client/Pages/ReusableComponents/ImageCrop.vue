<template>

	<div :class="classname">
		
		<input ref="fileInput" type="file" @change="onFileSelected"  multiple style="display:none">
		
		<div class="user-block">

			<faveo-image-element id="image" :classes="['img-circle']" :source-url="value" alternative-text="User Image"/>
					
		</div> 

		<button data-toggle="modal" @click="$refs.fileInput.click()" class="btn-bs-file btn btn-sm btn-light"
		 id="change">
			<i class="fas fa-edit"></i>&nbsp;{{ lang('change_image') }}
		</button> 

		<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle" 
		:language="language">
		
			<div slot="title">
				<h4>{{lang('profile_pic')}}</h4>
			</div>
			
			<div slot="fields">
				<div class="">
				<vue-cropper ref='cropper' :guides="true" :view-mode="2"
					drag-mode="crop"
					:auto-crop="true"
					:auto-crop-area="0.5"
					:min-container-width="570"
					:min-container-height="300"
					:background="true"
					:rotatable="true"
					:src="imageSrc"
					:img-style="crop"
					:crop="cropImage">
				</vue-cropper></div>
			</div> 

			<div slot="fields">
				<button v-if="imageSrc" @click="rotateImage" id="rotate" :class="{rotate_btn : language === 'ar'}">Rotate</button>
									
			</div>

			<button slot="controls" type="button" @click = "onSubmit" class="btn btn-primary float-right" 
			:class="{left : language === 'ar'}"><i class="fa fa-check"></i> {{lang('proceed')}}</button>
	

		</modal>  
</div>
</template>

<script type="text/javascript">

import VueCropper from 'vue-cropperjs';

import canvastoBlob from 'canvas-toBlob';

export default {

	name: "image-crop",

	description: "Image cropper component",

	props: {

		name: { type: String, required: true },

		onChange: { type: Function, Required: true },

		value : { type: Object|String , default : '' },

		classname : { type: String | Object , default : '' },

		language : { type : String, default :''}

	},

	data() {
		return {

			base:window.axios.defaults.baseURL,
			
			selectedFile:this.value,
			
			showModal : false,

			containerStyle:{ width:'800px' },

			crop: { width: '400px', height: '300px' },

			imageSrc:'', 

			cropImg: '', 

			resultImage : ''
		};
	},

	mounted() {

		this.selectedFile = this.value;
	},

	/**A watcher metod has been added since at firt the changevalue is empty and fetch the data accordingly
	 * we need a watcher to update it with new value
	 * @type {String}
	 */
	watch: {
		value(newVal) {
			this.selectedFile = newVal;
		}
	},

	methods:{
		
		onFileSelected(e) {

			const file = e.target.files[0];

			if (!file.type.includes('image/')) {

				alert('Please select an image file');
				
				return;
			}

			if (typeof FileReader === 'function') {
				
				this.showModal = true;

				var element=this.$refs.fileInput;

				this.selectedFile = file;

				const reader = new FileReader();

				reader.onload = (event) => {

					this.imageSrc = event.target.result;

					this.$refs.cropper.replace(event.target.result);
				};

				reader.readAsDataURL(file);

				element.value="";

			} else {
				
				alert('Sorry, FileReader API not supported');
			}
		},

		cropImage() {

			this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
		},

		rotateImage() {
			
			this.$refs.cropper.rotate(90);
		},

		onClose(){

			this.showModal = false
		},

		onSubmit(){

			var ImageURL = this.cropImg;
			
			// Split the base64 string in data and contentType
			var block = ImageURL.split(";");
			
			// Get the content type
			var contentType = block[0].split(":")[1];// In this case "image/gif"
			
			// get the real base64 content of the file
			var realData = block[1].split(",")[1];// In this case "iVBORw0KGg...."

			// Convert to blob
			this.resultImage = this.b64toBlob(realData,contentType);

			const data = {};

			data['name'] = this.selectedFile.name;

			data['image'] = this.cropImg;

			data['file'] = this.resultImage;

			this.onChange(data,this.name);

			this.onClose();
		},

		b64toBlob(b64Data, filename,contentType, sliceSize) {
			
			contentType = contentType || '';
			
			sliceSize = sliceSize || 512;
			
			var byteCharacters = atob(b64Data);
			
			var byteArrays = [];

			for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
				
				var slice = byteCharacters.slice(offset, offset + sliceSize);

				var byteNumbers = new Array(slice.length);

				for (var i = 0; i < slice.length; i++) {

					byteNumbers[i] = slice.charCodeAt(i);
				}

				var byteArray = new Uint8Array(byteNumbers);

				byteArrays.push(byteArray);
			}
			
			var blob = new Blob(byteArrays, {type: contentType});
			
			return blob;
		},
	},

	components: {
		
		VueCropper,
		
		'modal':require('components/Common/Modal.vue'),

		'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		'faveo-image-element': require('components/Common/FaveoImageElement')
	}
};
</script>

<style>
	.img-circle{
		border: 5px solid #ccccc8 !important;
		width: 130px !important;
		height: 130px !important;
		border-radius: 50% !important;
	}
	.user-block{
		position: relative;
		top: 20px;
		left: 37px;
	}
	#change{
		margin-left: 36px !important;
		margin-top : 30px !important;
	}
	#rotate{
		margin-left: 15px;margin-top: 15px
	}
	.rotate_btn{
		margin-left: 0px !important;
		margin-right: 15px;	
	}
</style>
