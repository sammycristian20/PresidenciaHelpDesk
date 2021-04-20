<template>
<div>
  <alert componentName="file-system-settings" />

  <faveo-box :title="lang('settings_file_system_page_header')">
    <loader v-if="isLoading"></loader>

    <div class="row">
      <text-field
          classname="col-sm-6" :onChange="onChange" rows="1"
          :label="lang('settings_file_system_allowed_files')"
          :value="allowedFiles" type="textarea" name="allowedFiles"
          :hint="lang('settings_file_system_allowed_files_hint')"
          :placehold="lang('settings_file_system_allowed_files_placeholder')"
          :required="true"
      />

    </div> <!--row-->

    <div class="row">

      <div class="col-sm-6">

        <label class="label_align">
          <input class="checkbox_align" type="checkbox" name="showPublicDiskWithDefaultDisk" v-model="showPublicDiskWithDefaultDisk">&nbsp;{{lang('file-manager-show-public-folder')}}
        </label>

      </div>

    </div>

    <div slot="actions" class="card-footer">
      <button id="agent-submit" class="btn btn-primary" type="button" :disabled="isLoading" @click="onSubmit">
        <span class="fas fa-save" id="submit-btn"></span>&nbsp; {{ lang('submit')}}
      </button>
    </div>

  </faveo-box>
</div>
</template>

<script>

import axios from 'axios';
import FaveoBox from 'components/MiniComponent/FaveoBox';
import StaticSelect from "components/MiniComponent/FormField/StaticSelect";
import {errorHandler, successHandler} from "helpers/responseHandler";
import Loader from "components/MiniComponent/Loader";
import TextField from "components/MiniComponent/FormField/TextField";
import Alert from 'components/MiniComponent/Alert';

  export default {

    name: "FileSystemSettings",

    components: {
      'faveo-box': FaveoBox, 'loader': Loader, 'text-field': TextField, 'static-select' : StaticSelect, alert: Alert
    },

    props: {
      disks: {
        type: String,
        required: true,
      }
    },

    data() {
      return {
        isLoading: false,
        allowedFiles: null,
        showPublicDiskWithDefaultDisk: 0,
      }
    },

    computed: {
      selectOptions() {
        return JSON.parse(this.disks);
      }
    },

    beforeMount() {
      this.getDefaultSettingsValues()
    },

    methods: {
      onChange(value, name){
        this[name]= value
      },

      async onSubmit() {
        this.isLoading = true

        try {
          let response = await axios.put('file-system-settings', {
            disk: this.disk,
            allowedFiles: this.allowedFiles,
            showPublicDiskWithDefaultDisk: this.showPublicDiskWithDefaultDisk ? 1 : 0,
          })
          successHandler(response, 'file-system-settings');
          this.$store.dispatch('unsetValidationError');
        } catch (e) {
          errorHandler(e, 'file-system-settings');
        } finally {
          this.isLoading = false
        }
      },

      async getDefaultSettingsValues() {

        try {
          let response = await axios.get('file-system-settings-values')

          let data = response.data.data;

          this.allowedFiles = data.allowed_files;

          this.showPublicDiskWithDefaultDisk = Boolean(data.show_public_folder_with_default_disk);

        } catch (e) {
          Object.assign(this.$data, this.$options.data.apply(this))
        }

      }
    }

}
</script>

<style scoped>
.checkbox_align {
  width: 13px; height: 13px; padding: 0; vertical-align: bottom; position: relative; top: -3px; overflow: hidden;
}

.label_align {
  display: block; padding-left: 15px; text-indent: -15px; font-weight: 500 !important; padding-top: 6px;
}
</style>
