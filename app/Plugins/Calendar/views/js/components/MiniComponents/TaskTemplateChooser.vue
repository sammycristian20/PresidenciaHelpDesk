<template>

  <div class="container-fluid">
    <loader v-if="loading" animation-duration="4000" color="#1d78ff" size="60" />
    <div class="row">

      <dynamic-select
          name="template" classname="col-sm-12"
          apiEndpoint="tasks/api/template/dropdown" :multiple="false"
          :label="lang('task-plugin-task-template')" :clearble="true"
          :value="template" :onChange="onChange"
          :searchable="true" :required="true"
      />

    </div>

  </div>

</template>

<script>

import axios from 'axios';

import {errorHandler, successHandler} from 'helpers/responseHandler'

export default {

  name: "TaskTemplateChooser",

  components: {
    'alert'          : require('components/MiniComponent/Alert'),
    "loader"         : require("components/MiniComponent/Loader"),
    'dynamic-select' : require("components/MiniComponent/FormField/DynamicSelect"),
  },

  props: {
    ticketId : { type : String | Number, default : '' },
    onComplete : { type : Function }
  },

  data() {
    return {
      template: null,
      loading: false
    }

  },

  methods: {
    onChange(value, name){
      this[name]= value;
    },

    apply() {

      this.loading = true

      axios.post('tasks/api/template/apply', {
        template : (this.template) ? this.template.id : null,
        ticketId : this.ticketId
      })
      .then((res) => {
        successHandler(res, 'timeline');
        this.onComplete();
        window.eventHub.$emit('refreshData');
      })
      .catch((err) => {
        errorHandler(err, 'timeline')
        if (!(err.response.status === 412))
          this.onComplete();
      })
      .finally(() => {
        this.loading = false;
      })

    }

  }
}

</script>
