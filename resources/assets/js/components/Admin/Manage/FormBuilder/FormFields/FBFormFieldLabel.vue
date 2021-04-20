<template>
  <div class="form-field-label">
    <img v-if="isChild" id="enter-arrow" width="16" height="16" :src="basePath() + imageUrl" alt="" />
    <ValidationProvider :name="getUniqueKey" rules="required">
      <template slot-scope="{ failed, errors, classes}">
        <input type="text" v-tooltip="lang('tap_to_edit')" :name="getUniqueKey" v-model.trim="fieldData.labels_for_form_field[0].label" :class="classes"/>
        <div>
          <small v-show="failed"><span class="error-block is-danger">{{errors[0]}}</span></small>
        </div>
      </template>
    </ValidationProvider>
  </div>
</template>

<script>

import { mapGetters } from 'vuex';

export default {

  props: {

    fieldData: {
      type: Object,
      required: true
    },

    // `true` if a node is not a root node
    isChild: {
      type: Boolean,
      default: () => false
    }

  },

  data: () => {
    return {
      imageUrl: '/themes/default/client/images/enterarrow.png'
    }
  },

  computed: {

    getUniqueKey: function() {
      return this.fieldData.title + '_' + this.fieldData.id;
    },
  },

}
</script>

<style scoped>
.form-field-label input {
  font-weight: 600;
  padding: 5px;
  width: 85%;
  outline: none;
  border: none;
  transition: 0.5s;
  -webkit-transition: 0.5s;
}
.form-field-label input:focus {
  border: 1px solid #555;
}
.field-danger {
  border: 1px solid #d73925 !important;
}
</style>