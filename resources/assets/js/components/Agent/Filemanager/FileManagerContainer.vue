<template>
  <div id="file-manager-container">

    <button v-on:click="showModalDialog()" type="button" class="btn btn-default" id="add-media-button-with-margin">
      <i class="far fa-caret-square-right"></i>&nbsp;{{lang('add-media')}}
    </button>

    <slot name="templateBtn"></slot>

    <modal v-if="displayModal"  :showModal="displayModal" :onClose="onClose" :containerStyle="containerStyle"
           modalBodyClass="fm_modal_body">

      <div slot="title">
        <h4>{{ lang('file-manager-modal-title') }} <tool-tip v-if="toolTipMessage" :message="toolTipMessage" size="large"></tool-tip></h4>
      </div>

      <div slot="fields" v-if="!loading" style="height: 100%">
        <alert componentName="fileManagerModal" />
        <file-manager :settings="settings"></file-manager>
      </div>

      <div class="row" slot="fields" v-if="loading">
        <loader :duration="4000" :size="60"></loader>
      </div>

      <button slot="controls" type="button"  @click="onSubmit" class="btn btn-primary" :disabled="buttonDisabled">
        <i class="fas fa-paperclip"> </i>  {{lang('file-manager-insert-media')}}
      </button>

    </modal>

  </div>
</template>

<script>

import axios from 'axios';

export default {
  name: "FileManagerContainer",

  components: {
    'modal':require('components/Common/Modal.vue'),
    'loader':require('components/Client/Pages/ReusableComponents/Loader'),
    'alert' : require('components/MiniComponent/Alert'),
    "tool-tip": require("components/MiniComponent/ToolTip"),
  },

  props: {
    page: {type: String, default: 'others'}, // takes 2 values 'kb' or 'others'
    showModal: {type: Boolean, default: false}
  },

  data() {
    return {
      settings: {
        baseUrl : this.basePath() + '/file-manager/',
        page: this.page,
        maxFileUploadCount: null,
        maxSingleFileSize: null,
        maxPostSize: null
      },
      toolTipMessage: null,
      selectedFiles: [],
      selectedDisk: null,
      displayModal : this.showModal,
      loading: false,
      containerStyle: {
        "width": "80vw",
      },
    }
  },

  computed: {
    fileSelected() {
      return this.$store.state.fm.left.selected.files;
    },
    folderSelected() {
      return this.$store.state.fm.left.selectedDirectory;
    },
    diskSelected() {
      return this.$store.state.fm.left.selectedDisk;
    },
    buttonDisabled() {
      return !this.selectedFiles.length;
    },
    filesInClipboard() {
      return this.$store.state.fm.clipboard.files
    }
  },

  watch: {
    fileSelected() {
      this.selectedFiles = this.fileSelected;
    },
    folderSelected() {
      if (this.folderSelected.length) {
        this.selectedFiles = [];
      }
    },
    diskSelected() {
      this.selectedFiles = [];
      this.selectedDisk = this.diskSelected;
    },
    filesInClipboard() {
      if (this.filesInClipboard.length) {
        this.selectedFiles = [];
      }
    }
  },

  beforeMount() {
    this.getFileUploadRules();
  },

  methods: {
    bytesToHuman(bytes) {
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

      if (bytes === 0) return '0 Bytes';

      const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10);

      if (i === 0) return `${bytes} ${sizes[i]}`;

      return `${(bytes / (1024 ** i)).toFixed(1)} ${sizes[i]}`;
    },

    showModalDialog() {
      this.displayModal = !this.displayModal;
    },

    onSubmit() {
      let inlineFilesAreNotPdf = false;

      this.selectedFiles.forEach((file) => {
        if (file.split('.').pop() !== 'pdf') inlineFilesAreNotPdf = true
      });

      if (this.selectedFiles.length) {
        let disk = this.$store.state.fm.left.selectedDisk;
        if (['page','kb'].includes(this.page)) {
          this.$emit('fileChosenInlinePdf', {files: this.selectedFiles, disk: disk});
        } else {
          this.$emit('filesChosen', {files: this.selectedFiles, disk: disk});
        }
        this.onClose();
      } else {
        this.$store.dispatch('setAlert', {
          type: "danger",
          message: this.lang('file-manager-select-atleast-one-file'),
          component_name: 'fileManagerModal'
        })
      }
    },

    onClose() {
      this.displayModal = false;
    },

    async getFileUploadRules() {
      try {
        let response = await axios.get('file-manager/upload-info');
        let data = response.data.data;
        this.settings.maxSingleFileSize = data.maxSingleFileSize;
        this.settings.maxFileUploadCount = data.maxFileUploadCount;
        this.settings.maxPostSize = data.maxPostSize;
        this.toolTipMessage = 'Maximum File Upload Size : ' + this.bytesToHuman(data.maxPostSize) +', Single File Size : ' + this.bytesToHuman(data.maxSingleFileSize) + ' and Max Number Of Files : '+ data.maxFileUploadCount;
      } catch(e) {
        this.settings.maxSingleFileSize = null;
        this.settings.maxFileUploadCount = null;
        this.settings.maxPostSize = null;
        this.toolTipMessage = null;
      }
    },

    onChange(value,name) {
      this[name] = value;
    }
  }
}
</script>

<style>
#add-media-button-with-margin {
  margin-bottom: 0.8rem;
}

.fm_modal_body {
  max-height: 500px;
  overflow-y: auto;
  height: 500px;
  padding: 0 1rem !important;
}

</style>
