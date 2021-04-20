<template>
  <div>
    <form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" :required="required">

      <editor
          :init="options"
          v-model="editorValue"
          @onChange="onChange(editorValue,name)"
      />

    </form-field-template>

  </div>
</template>


<script>

import Editor from '@tinymce/tinymce-vue';
import { plugins, editorProps } from "./tinyMceDefaults";

export default {
  name: 'TinyMCE',

  components: {
    'editor': Editor,
    'form-field-template': require('components/MiniComponent/FormField/FormFieldTemplate')
  },

  props: {

    ...editorProps,

    name: {type: String, Required:true},

    onChange: { type: Function, Required: true },

    label: {type: String, default: ''},

    labelStyle:{type:Object, default: function () { return { }}},

    classname: { type: String, default: "" },

    hint: { type: String, default: "" },

    required: { type: Boolean, default: false },

    value: {type: String, default:""},

    page: {type: String, default:""}, //possible values kb,reply

  },

  data() {
    return {

      editorValue: this.value,

      options: {
        width:this.width,
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
        images_upload_url: this.basePath()+'/api/tiny-image-uploader?_token='+document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        plugins: (this.page !== 'kb') ? plugins.filter(element => element !== 'media') : plugins,
        toolbar:
            'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | \
            alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | \
            forecolor backcolor  | searchreplace removeformat | pagebreak charmap insertdatetime nonbreaking| \
            preview print | table image media link codesample hr code | help'
      }
    }
  },

  watch: {
    value: {
      immediate: true,
      handler (value, oldValue) {
        this.editorValue = value;
      }
    }
  }

}

</script>

<style>

.tox-dialog {
  z-index: 1650 !important;
}

.tox .tox-dialog-wrap__backdrop {
  z-index: 1600 !important;
}

</style>
