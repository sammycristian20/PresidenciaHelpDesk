<template>
    <form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" :required="required" :isInlineForm="isInlineForm">

      <file-manager-container v-if="mediaOption && panel != 'client'" 
        v-on:filesChosen="getAttachmentInfo"
      />

    <ValidationProvider :name="name" :rules="rules">
      <template slot-scope="{ failed, errors, classes }">

        <editor :init="options" v-model="editorValue" @onChange="onChange(editorValue, name)" :name="name" :class="classes" />

        <span v-show="failed" class="error-block is-danger">{{errors[0]}}</span>

      </template>
    </ValidationProvider>

      <template v-if="loading">
        <custom-loader duration="4000" />
      </template>

    <template v-for="(attachment,index) in attachmentsArr">
      
        <div id='hidden-attach' contenteditable='false' v-if="attachment.disposition !== 'inline'">
          
          {{attachment.name}}({{attachment.size ? attachment.size : attachment.file_size}} bytes)
          
          <i class='fa fa-times close-icon' aria-hidden='true' @click='removeAttachment(index)'></i>
        </div>
      </template>

  </form-field-template>
</template>

<script>

  import Editor from '@tinymce/tinymce-vue';

  import { plugins, editorProps } from "./tinyMceDefaults";

  export default {

    name: "tiny-editor-with-validation",

    description: 'TinyMCE Editor With Validations',

    props: {

      ...editorProps,

      label: { type: String, default: '' },

      name: { type: String, required: false },

      value: { type: String, required: false },

      isInlineForm: { type: Boolean, default: false },

      required: { type: Boolean, default: false },

      rules: { type: String, default: '' },

      hint: { type: String, default: '' },

      /**
      * for show labels of the fields
      * @type {Object}
      */
      labelStyle:{type:Object},

      /**
       * classname of the form field. It can be used to give this component any bootstrap class or a custom class
       * whose css will be defined in parent class
       * @type {String}
       */
      classname: { type: String, default: "" },

      /**
       * The function which will be called as soon as value of the field changes
       * It should have two arguments `value` and `name`
       *     `value` will be the updated value of the field
       *     `name` will be thw name of the state in the parent class
       *
       * An example function :
       *         onChange(value, name){
       *             this[name]= selectedValue
       *         }
       *
       * @type {Function}
       */
      onChange: { type: Function, Required: true },

      mediaOption: { type: Boolean | Number, default: false },

      attachments : { type : Array, default : ()=> []},

      getAttach : { type : Function },

      panel : { type : String, default : '' }

    },

    created () {
      this.getCsrfToken();
    },

    data() {
      return {
        loading: false,
        attachmentsArr : this.attachments,
        editorValue : this.value,
        options: {
          max_height: this.height,
          min_height: this.height,
          menubar: (this.showMenubar) ? 'file edit view insert format tools table help' : false,
          toolbar_sticky: this.toolbarSticky,
          autosave_ask_before_unload: this.autoSaveAskBeforeUnload,
          autosave_interval: this.autoSaveInterval,
          autosave_prefix: 'tinymce-autosave-{path}{query}-{id}-',
          autosave_restore_when_empty: this.autoSaveRestoreWhenEmpty,
          autosave_retention: this.autoSaveRetention,
          image_advtab: this.imageAdvancedTab,
          template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
          template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
          image_caption: this.imageCaption,
          quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
          noneditable_noneditable_class: 'mceNonEditable',
          toolbar_mode: this.toolbarMode,
          contextmenu: false,
          content_style: this.contentStyle,
          draggable_modal: this.draggableModal,
          elementpath: this.elementPath,
          height: this.height,
          resize: this.resize,
          branding: this.branding,
          browser_spellcheck: this.browserSpellcheck,
          custom_undo_redo_levels: this.customUndoRedoLevels,
          paste_data_images: this.pasteDataImages,
          statusbar: this.statusbar,
          auto_focus:this.autoFocus,
          automatic_uploads: this.automaticUploads,
          relative_urls: this.relativeUrls,
          remove_script_host: this.removeScriptHost,
          document_base_url: this.basePath(),
          images_upload_url: '',
          plugins: (this.page !== 'kb') ? plugins.filter(element => element !== 'media') : plugins,
          toolbar:
              'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | \
              alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | \
              forecolor backcolor  | searchreplace removeformat | pagebreak charmap insertdatetime nonbreaking| \
              preview print | table image media link codesample hr | help'
        }
      }
    },

    watch: {
      value: {
        immediate: true,
        handler (value, oldValue) {
          this.editorValue = value;
        }
      },
    },

    methods: {

      getCsrfToken() {
        this.options.images_upload_url = this.basePath() + '/api/tiny-image-uploader?_token=' + document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      },

      removeAttachment(x){

        this.attachmentsArr.splice(x,1);

        this.getAttach(this.attachmentsArr);
      },

      async getAttachmentInfo(filesInfo) {
         let files = JSON.stringify(filesInfo.files);
         this.loading = true
        try {
          let response = await axios.get('file-manager/files-info', {
            params: {names: files, disk: filesInfo.disk}
          })

          let data = response.data.data;

          data.forEach((file) => {
            this.attachmentsArr.push(file);
          })

          this.getAttach(this.attachmentsArr);

        } catch (e) {
          await this.$store.dispatch('setAlert', {
            type: 'danger',
            message: this.lang('file-manager-modal-error-message'),
            component_name: 'faveo-form'
          })
        } finally {
          this.loading = false;
        }
      },
    },

    components: {
      editor: Editor,
      'form-field-template': require('components/MiniComponent/FormField/FormFieldTemplate'),
      "custom-loader": require("components/MiniComponent/Loader"),
    },
  }

</script>

<style scoped>
  #hidden-attach{
    background-color: #f5f5f5;
    border: 1px solid #dcdcdc;
    font-weight: bold;
    margin-top:9px;
    overflow-y: hidden;
    padding: 4px 4px 4px 8px;
  }
  
  .close-icon{
    float:right;cursor: pointer;
  }
</style>