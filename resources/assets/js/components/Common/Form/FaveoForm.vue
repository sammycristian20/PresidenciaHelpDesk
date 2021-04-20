<template>
  <div id="faveo-form">
    <faveo-box :title="trans(mode)">

      <alert componentName="faveo-form"/>

      <loader v-if="showLoader || submitFormInProgress" :duration="4000"/>

      <template v-if="formUniqueKey">

        <span slot="headerMenu" v-if="showViewButton" class="pull-right">
          <a :href="basePath() + '/' + category + '/' +  editId" class="btn btn-tool has-tooltip" v-tooltip="trans('view')">
            <span><i class="fas fa-eye"></i></span>
          </a>
        </span>

        <form-with-captcha :loading="!showLoader" btnClass='btn btn-primary pull-right' btnName="submit"
                           componentName="faveo-form"
                           btn_id="form-submit" :formSubmit="formSubmit" :page="page"
                           recaptchaContainerClass="col offset-md-2 col-md-10" :disableSubmit="submitFormInProgress">

          <div slot="fields">
            <ValidationObserver ref="faveoFormObserver">
              <form-renderer
                  :key="formUniqueKey"
                  :fetchFormFieldApi="fetchFormFieldApi"
                  :formUniqueKey="formUniqueKey"
                  :scenario="scenario || mode"
                  :category="category"
                  :panel="panel">
              </form-renderer>
            </ValidationObserver>
          </div>
        </form-with-captcha>
      </template>

    </faveo-box>
  </div>
</template>

<script>

import FaveoBox from 'components/MiniComponent/FaveoBox';
import {getIdFromUrl} from 'helpers/extraLogics';
import {getApiForFetchingFormFields, getFormUniqueKey} from 'helpers/formUtils';
import {mapGetters} from 'vuex';
import {faveoFormCommons} from 'mixins/faveoFormCommons';

export default {

  name: 'faveo-form',

  props: {

    // category is for categorizing the FaveoForm; may be ticket, user, organization, etc
    category: {type: String, required: true},

    // One category may be present in differnet panels(admin/client/agent)
    panel: {type: String, default: 'agent'},

    // scenario will be the mode of the FaveoForm Category; may be create/edt/recur etc
    scenario: {type: String, default: ''},

    // if true one button with link("basePath() + '/' + category + '/' +  editId") will be shown on the card header
    showViewButton: {type: Boolean, default: false}

  },

  data() {
    return {
      fetchFormFieldApi: '',
      formUniqueKey: null,
      submitFormInProgress: false,
      editId: ''
    }
  },

  mixins: [faveoFormCommons],

  beforeMount() {

    this.fetchFormFieldApi = 'api/form/render/';

    this.editId = getIdFromUrl(this.currentPath());

    if (this.mode === 'edit') {
      // In case of edit, get API Endpoint to fetch the form field on basis of category/scenario
      let key = this.scenario || this.category;
      this.fetchFormFieldApi = getApiForFetchingFormFields(key, this.editId);
    }
  },

  computed: {

    mode() {
      return this.currentPath().indexOf('edit') !== -1 ? 'edit' : 'create';
    },

    page() {

      if(this.scenario){
        return this.category + '_' + this.scenario + '_' + this.panel;
      }

      return this.category + '_' + this.mode + '_' + this.panel;
    },

  },

  methods: {

    // After form submited to the server perform these operations
    afterSubmit(data) {

      if(this.scenario === 'recur' && this.mode === "create"){
        // recur is present both in admin and in agent panel
        // the one contains "agent" in the url will be redirected to agent panel
        if(this.panel === 'agent'){
          this.redirect('/agent/recur/list');
        } else {
          this.redirect('/recur/list');
        }
      } else {
        window.eventHub.$emit('update-sidebar');
        this.$store.dispatch('destroyFormInsatnce', this.formUniqueKey);
        let formUniqueKey = getFormUniqueKey(this.category);
        this.$store.dispatch('createNewFormInstance', {formUniqueKey, scenario: this.scenario || this.mode});
        this.formUniqueKey = formUniqueKey;
        window.eventHub.$emit('reset-captcha');
      }
    },

    formSubmit(key, value) {

      this.$store.dispatch('setRecaptchaKey', value)

      this.onSubmit();
    }

  },

  components: {
    'faveo-box': FaveoBox,
    'alert': require('components/MiniComponent/Alert'),
    'loader': require('components/MiniComponent/Loader'),
    "form-with-captcha": require("components/Common/Form/FormWithCaptcha.vue"),
  },
}
</script>
