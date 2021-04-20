<style scoped>
.btn-bs-file {
  position: relative;
}
.btn-bs-file input[type="file"] {
  position: absolute;
  top: -9999999;
  filter: alpha(opacity=0);
  opacity: 0;
  width: 0;
  height: 0;
  outline: none;
  cursor: inherit;
}
.upload-files {
  list-style-type: none;
  background-color: #f5f5f5;
  border: 1px solid #dcdcdc;
  font-weight: bold;
  margin: 8px;
  overflow-y: hidden;
  padding: 4px 4px 4px 8px;
  max-width: 448px;
  margin-left: 0px;
}
.file-actions {
  float: right;
  cursor: pointer;
  margin: 3px;
}
.fa-warning {
  color: red;
  font-size: 14px;
}

</style>
<template>
	    <div id="file-upload__attachment">
        <slot name="attachmentList"></slot>
	    	<!-- display attachments -->
	    	<div class="upload-files" contenteditable="false" v-if="attachmentArray.length>0" v-for="(attachment,index) in attachmentArray" :key="index">
	    		 {{attachment.name}} ({{attachment.size}})
	    		 <i class="fa fa-times file-actions" aria-hidden="true"  @click="removeAttachment(index)"></i>
        </div>

        <img v-if="imageSource" :src="imageSource">

        <label v-if="showFileUploader" :class="{'btn-bs-file btn btn-sm btn-default btn-light border_2': true, 'field-danger': errors.has(unique)}">
           <i class="fa fa-plus"></i>&nbsp;Attach files
           <input type="file" @change="onChange" v-validate="validate" :name="unique" :accept="accept" :multiple="true"><br>
        </label>

        <span v-show="errors.has(unique)" class="help is-danger">
            {{ errors.first(unique) }}
        </span>
      </div>
</template>
<script>
import {boolean, lang} from 'helpers/extraLogics';
import {mapGetters} from 'vuex';

export default {
  props: {

    /**
     * If multiple uploads are allowed
     */
    isMultiple : {type: Boolean, default: true},

    /**
     * Form field object
     */
    node: {type: Object, default : null},

    /**
     * Key to which value has to be bind
     */
    objKey : {type: String, default : 'value'},

    /**
     * Validation rules
     */
    validate: {type : Object, default: null },

    /**
     * The type of extensions that are acceptable
     */
    accept : {type: String, default: '*'},

    /**
     * unique identifier for the field
     */
    unique : {type: String, required: true},
  },

  data() {
    return {
      attachmentArray: [],
      imageSrc: [],
      errorFromServer : false,
    };
  },
  watch: {
    "node.value"(newvalue) {
      if (Array.isArray(newvalue) && newvalue.length > 0) {
        this.convertBase64ToFileObject(newvalue);
      }

      // when value is emptied from outside
      if(newvalue == ""){
        this.attachmentArray = [];
        this.imageSrc = "";
      }
    },

    attachmentArray(newValue){
      // don't validate if attachmentArray is non-empty
      if(newValue.length > 0){
        this.$validator.pause()
      }else{
        this.$validator.resume()
      }
    },
  },
  computed: {
    ...mapGetters({
      postMaxSize: 'getPostMaxSize',
      uploadMaxFilesize: 'getUploadMaxFilesize',
      maxFileUploads: 'getMaxFileUploads',
    }),

    showFileUploader(){
      if(!boolean(this.isMultiple) && this.attachmentArray.length >= 1){
        return false;
      }
      return true;
    },

    // show only in case where only single image is allowed and image is already uploaded
    imageSource(){
      if(!boolean(this.isMultiple) && this.attachmentArray.length >= 1 && this.accept == 'image/*'){
        return this.imageSrc;
      }
      return "";
    }
  },
  methods: {
    /**
     * Removes attachment from attachmentArray
     * @param  {Number} index index of the entry that has to be removed
     * @return {undefined}
     */
    removeAttachment(index) {
      if(this.errorFromServer){
        this.$validator.reset();
        this.errorFromServer = false;
      }

      this.attachmentArray.splice(index, 1);

      // NOTE: this has to be made avaiable for multile images too
      this.imageSrc = "";

      this.$emit("assignToModel", "value", this.attachmentArray);
    },

    onChange(event) {
      const mainDivElement = document.getElementById("file-upload__attachment");

      if(mainDivElement) {
         mainDivElement.scrollIntoView({behavior: "smooth"});
      }

      if(this.errorFromServer){
        this.$validator.reset();
        this.errorFromServer = false;
      }

      for (let i = 0; i < event.target.files.length; i++) {
        // converting file size into kilobyte
        if(event.target.files[i].size/1000 > this.uploadMaxFilesize){
          let message = lang('max_file_size_allowed_is')+' '+this.uploadMaxFilesize + ' kilobytes';
          this.$validator.errors.add({field: this.unique, msg: message});
        }else{
          let newOBject = event.target.files[i];
          this.attachmentArray.push(event.target.files[i]);
        }
      }

      this.$emit("assignToModel", "value", this.attachmentArray);
    },

    convertBase64ToFileObject(files){

        for(let i = 0; i < files.length; i++){
        if(files[i].hasOwnProperty('file') && files[i].hasOwnProperty('filename')){
            let mime = files[i]["file"].match(/:(.*?);/)[1];
            fetch(files[i]['file'])
            .then(res => res.blob())
            .then(blob => {
              this.attachmentArray.push(new File([blob], files[i]['filename'], {type: mime}));
              // NOTE: this has to be made avaiable for multile images too
              this.imageSrc = files[i]['file'];
              this.$emit("assignToModel", "value", this.attachmentArray);
          })
        }
      }
    }
  }
};
</script>
