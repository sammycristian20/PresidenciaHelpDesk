<template>
  <div class="row p-2">
    <div class="col-md-8" style="padding-top: 10px;">
      <label for="apiendpoint">{{lang('api_endpoint')}}</label>
      <tool-tip :message="lang('api_endpoint')" size="small"></tool-tip>
      <div class="input-group" style="width: 100%">
        <input type="text" class="form-control" id="apiendpoint" v-model.trim="apiInfoObj.url" @input="onChange()" placeholder="https://testapi.com/my/api/path">
      </div>
    </div>

    <div class="col-md-4" style="padding-top: 10px;">
      <label for="apiendpoint">{{lang('property_to_map')}}</label>
      <tool-tip :message="lang('this_property_will_get_saved_when_user_submit_the_form')" size="small"></tool-tip>
      <div class="input-group" style="width: 100%">
        <input type="text" class="form-control" id="apiendpoint" v-model.trim="apiInfoObj.key" @input="onChange()" placeholder="id">
      </div>
    </div>
  </div>
</template>

<script>
import ToolTip from 'components/MiniComponent/ToolTip';
import { MULTIPLE_PROPERTY_HELPER } from "helpers/extraLogics";
import _ from 'lodash-core';

export default {
  
  name: 'api-info-block',

  props: {
    formFieldData: { type: Object, required: true },
    updateApiInfo: { type: Function, required: true }
  },

  data: () => {
    return {
      apiInfoObj: null
    }
  },

  beforeMount() {
    this.apiInfoObj = MULTIPLE_PROPERTY_HELPER.convertStringOfPropertiesToObject(this.formFieldData.api_info);
  },

  methods: {

    onChange: _.debounce(function () {
      this.updateApiInfo(MULTIPLE_PROPERTY_HELPER.convertObjectOfPropertiesToString(this.apiInfoObj))
		}, 350),
    
  },

  components: {
    'tool-tip': ToolTip
  }
}
</script>

<style>

</style>