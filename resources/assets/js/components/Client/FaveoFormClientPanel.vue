<template>
  <div id="faveo-form-client-panel">
    <template v-if="formUniqueKey">
      <alert componentName="faveo-form" />

      <loader v-if="showLoader || submitFormInProgress" :duration="4000"/>


      <form-with-captcha btnClass='btn btn-primary pull-right' btnName="submit"
                         componentName="faveo-form" :loading="!showLoader"
                         btn_id="client-ticket-submit-button" :formSubmit="formSubmit" :page="category+'_create_client'"
                         recaptchaContainerClass="col offset-md-2 col-md-10" :disableSubmit="submitFormInProgress">

        <div slot="fields">

        <ValidationObserver ref="faveoFormObserver">
          <form-renderer
            :key="formUniqueKey"
            :fetchFormFieldApi="fetchFormFieldApi"
            :formUniqueKey="formUniqueKey"
            :scenario="scenario || mode"
            :category="category"
            :panel="panel"
            :afterSubmit="afterSubmit">
          </form-renderer>
        </ValidationObserver>
        </div>
      </form-with-captcha>

          <div class="form-group row" v-show="!showLoader">
            <div class="col-md-12">
              <button type="submit" class="btn btn-primary pull-right" :disabled="submitFormInProgress" @click="popUpSubmit()">
                <span><i class="fas fa-save"></i></span>
                {{ trans('submit') }}
              </button>
            </div>
          </div>

    </template>
  </div>
</template>

<script>

import { getIdFromUrl } from 'helpers/extraLogics';
import { getApiForFetchingFormFields, getFormUniqueKey, validateFormData } from 'helpers/formUtils';
import { mapGetters } from 'vuex';
import { faveoFormCommons } from 'mixins/faveoFormCommons';

export default {

  name: 'faveo-form-client-panel',

  props: {

    category: { type: String, required: true },

    panel: { type: String, default: 'agent' },

    scenario: { type: String, default: '' },

    updateCount : { type : Function, default : ()=>{}}

  },

  data () {
    return {
      fetchFormFieldApi: '',
      formUniqueKey: null,
      submitFormInProgress: false
    }
  },

  mixins: [faveoFormCommons],

  beforeMount() {

    this.fetchFormFieldApi = 'api/form/render/';

    const editId = getIdFromUrl(this.currentPath());

    if (this.mode === 'edit') {
      this.fetchFormFieldApi = getApiForFetchingFormFields(this.category, editId);
    }

  },

  computed: {

    mode () {
      return this.currentPath().indexOf('edit') !== -1 ? 'edit' : 'create';
    },

  },

  methods: {
    /**
     * it clicks the button, it clicks the button in FormWithCaptcha which in change triggers formSubmit.
     * Reason for this workaround is to hide the submit button coming from FormWithCaptcha without breaking
     * the functionality
     */
    popUpSubmit() {
      document.getElementById('client-ticket-submit-button').click();
    },

    formSubmit(key, value) {
      this.$store.dispatch('setRecaptchaKey', value)
      this.onSubmit();
    },

    afterSubmit (data) {
        this.$store.dispatch('destroyFormInsatnce', this.formUniqueKey);
        let formUniqueKey = getFormUniqueKey(this.category);
        this.$store.dispatch('createNewFormInstance', { formUniqueKey, scenario: this.mode });
        this.formUniqueKey = formUniqueKey;
        this.updateCount();
    },

  },

  components: {
    'alert': require('components/MiniComponent/Alert'),
    'loader': require('components/MiniComponent/Loader'),
    "form-with-captcha": require("components/Common/Form/FormWithCaptcha.vue"),
  }
}
</script>

<style scoped>
#faveo-form-client-panel {
  padding: 1rem;
  width: 100%;
}
.sdf {
  position: relative;
  flex: 1 1 auto;
  padding: 1rem;
}

</style>

<style>
  #client-ticket-submit-button {
    display: none;
  }
</style>