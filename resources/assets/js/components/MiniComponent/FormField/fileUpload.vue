<template>

	<form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" :required="required"  :isInlineForm="isInlineForm" :key="counter">

		<div>
			
			<ValidationProvider :name="name" :rules="computedRules" ref="fileUpload">
        			
     			<template slot-scope="{ failed, errors, classes }">
					
					<input :id="'file-upload-'+name" :name="name" ref="files" type="file" :accept="accept" @change="onFileSelected" 
						:multiple="multiple"/>

					<button data-toggle="modal" @click="$refs.files.click()" class="btn-bs-file btn btn-sm btn-default btn-block" :class="classes">
						
						<span><i class="far fa-file"></i> {{ trans('choose_file') }}</span>
					</button>

					<div v-if="files && files.length">
						
						<div class="file-detail-box" v-for="(file, index) in files" :key="index">
							
							<div>
								
								{{ file.name || file.value }} ( {{ file.size }} KB)
								
								<div class="pull-right">
									
									<i class="fas fa-times" @click="removeFile(index)" :title="trans('remove')"></i>

									<i v-if="isImageTypeFile(file.name)" class="fas fa-eye" @click="displayPreviewImageIfAny(index)" :title="trans('preview')"
										v-popover.top="{ name: 'attach' + index }">
										
									</i>

               				<!-- todo: this is a temporary block and should be removed once attachment module is rewritten  -->
               				<a  v-if="file.link" class="fas fa-download" style="color: inherit" :href="basePath()+file.link"></a>
								</div>
							</div>

							<popover v-show="showPreview" :name="'attach'+index" :pointer="true" event="click">
		
								<template v-if="previewImgSrc">

									<div class="card m_4">
           	
           							<div class="card-header">
             	
               						<h3 class="card-title">{{trans('preview')}}</h3>

           							</div>
           
           							<div class="card-body pb-0">
             	
             							<faveo-image-element id="attach-img" :source-url="previewImgSrc" :classes="['preview_img']"/>
           							</div>
         						</div>
								</template>

								<template v-else>
			
									<div class="row" id="load_margin">
				
										<loader :animation-duration="4000" :size="30"/>
									</div>
								</template>
							</popover>
						</div>
					</div>

					<span v-show="failed" class="error-block is-danger">{{errors[0]}}</span>
     			</template>
			</ValidationProvider>

			<!-- <span v-else>No file choosen</span> -->

			<!-- <div v-if="showPreview" class="popover " id="preview">
				<div class="arrow" id="arrow"></div>
				<h6 class="popover-title preview-title">Preivew
					<i class="fas fa-times pull-right" @click="showPreview = false" :title="trans('close')"></i>
				</h6>
				<div class="popover-content preview-content">
					<img id="preview_img" v-if="previewImgSrc" :src="previewImgSrc"/>
					<span v-else>Loading...</span>
				</div>
			</div> -->

		</div>
	</form-field-template>

</template>

<script type="text/javascript">

import { getBytesInBinary, boolean } from 'helpers/extraLogics'

import Vue      from 'vue'

import Popover from 'vue-js-popover'

Vue.use(Popover)

export default {

	name: "file-upload",

	description: "file upload component along with error block",

	props: {

		label: { type: String, required: true },

		hint: { type: String, default: "" }, //for tooltip message

		name: { type: String, required: true },

		onChange: { type: Function, Required: true },

		classname: { type: String, default: "" },

		required: { type: Boolean, default: false },

		labelStyle: { type: Object },

		id: { type: String | Number, default: 'file-upload' },

		value: { type: Object | String | Array , default: '' },

		accept: { type: String, default: ''},

		multiple: { type: Boolean, default: false },

		fileMaxSize: { type: Number, default: 2 }, // Max size(in MB) allowed for a file

		isInlineForm: { type: Boolean, default: false },

		rules: { type: String, default: '' },

	},

	data () {
		return {
			files: [],
			previewImgSrc: '',
			showPreview: false,

      /**
       * This is a jugaad to get attachment validation to not be a blocker
       * This must be removed once attachment module is rewritten
       */
      computedRules: this.rules,
      counter : 0,
		};
	},

	mounted () {
		this.updatefiles(this.value)
	},

	watch: {
		value (newVal) {
			this.updatefiles(newVal)
      
      /*
       * JUGAAD. Whenever the value is non-empty, we remove the validation
       * REASON: attachment is handled here differently in create and edit. Value can be a file object OR
       * a normal json (containing ID iof the attachment, in case of Service desk assets).
       * @todo: this must be removed once attachment module is rewritten
       * */
      this.rules && (this.computedRules = boolean(newVal) ? "": "required");
		}
	},

	methods: {

		isImageTypeFile (name) {
			return /\.(jpe?g|png|gif)$/i.test(name)
		},

		updatefiles(newVal) {
			if (newVal) {
				if(Array.isArray(newVal)) {
					this.files = newVal;
				} else {
					this.files = [newVal];
				}
			} else {
				this.files = []
			}
		},

		async onFileSelected (e) {
		  const { valid } = await this.$refs.fileUpload.validate(e.target.files);

			if (valid) {
				const files = e.target.files;
				for ( var i = 0; i < files.length; i++ ) {
					if (this.multiple) {
						this.files.push( files[i]);
					} else {
            // emptying the array to clear all references
					  this.files.splice(0, this.files.length);
					  this.files.push(files[i]);
					}
				}
				this.updateParent(this.files, this.name, false);
				}
		},

		displayPreviewImageIfAny (index) {

			this.showPreview = true;

			this.previewImgSrc = '';

			setTimeout(()=>{

				let reader = new FileReader()
				reader.addEventListener('load', function() {
					this.previewImgSrc = reader.result
				}.bind(this), false)
				reader.readAsDataURL(this.files[index])
			},1)
		},

		distroyPreviewImageBlock() {
			this.previewImgSrc = ''
			this.showPreview = false;
		},

		updateParent (newValue, name, isRemoved) {
			this.onChange(newValue, name, isRemoved)
		},

		removeFile (index) {
			this.files.splice(index, 1);
			this.distroyPreviewImageBlock();
			this.updateParent(this.files, this.name, true);
			this.$refs.fileUpload.validate(this.files);
			this.counter++;
		}

	},

	components: {
		'form-field-template': require('./FormFieldTemplate'),
		'loader':require('components/Client/Pages/ReusableComponents/Loader'),
		'faveo-image-element': require('components/Common/FaveoImageElement')
	}
};
</script>

<style scoped>

.vue-popover{ width: 260px !important;height: 30%!important; top:auto !important; left:auto !important; }
	
	#load_margin { margin-top: 15px;margin-bottom: 15px; }

input[type="file"]{
	position: absolute;
  opacity: -999;
}
.preview_img {
	width: 216px;
    height: 180px;
    margin-bottom: 20px;
}
.file-detail-box {
	border: 1px solid #dedede;
   padding: .3rem;
   margin-top: .5rem;
   background: #f8f9fa;
   border-radius: 0.25rem;
}
.m_4{ margin: -4px !important; }
</style>