<template>
    <div id="create-org" class="float-right">
    <loader v-if="submitFormInProgress"/>
      <a data-toggle="modal" @click="showModalMethod" href="javascript:;" type="button" data-target="#modal_body">
        <i class="fas fa-edit"> </i> {{ trans('create') }} |&nbsp;
      </a>

      <modal v-if="showForm" :showModal="showForm" :onClose="onClose">
        <div slot="title">
          <h4>{{ lang('create_organization') }}</h4>
        </div>

        <div slot="fields">
          <mini-loader v-if="showLoader"/>
        </div>

        <div slot="fields" id="modal_body">
          <alert componentName="faveo-form"/>

          <form-with-captcha :loading="!showLoader" btnClass='btn btn-primary pull-right' btnName="submit"
                             componentName="faveo-form"
                             btn_id="organization-submit-button" :formSubmit="formSubmit" page="organisation_create_agent"
                             recaptchaContainerClass="col offset-md-2 col-md-10" :disableSubmit="submitFormInProgress">
            <div slot="fields" class="row">

              <ValidationObserver ref="faveoFormObserver">
                <form-renderer
                    category="organisation"
                    fetchFormFieldApi="api/form/render"
                    :scenario="scenario"
                    :panel="panel"
                    :formUniqueKey="formUniqueKey">
                </form-renderer>
              </ValidationObserver>
            </div>
          </form-with-captcha>
        </div>

        <div slot="controls">
          <button v-if="!showLoader" type="submit" class="btn btn-primary pull-right" :disabled="submitFormInProgress"
                  @click="popUpSubmit()">
            <i class="fas fa-sync"></i>
            {{ trans('update') }}
          </button>
        </div>

      </modal>
    </div>
</template>

<script>

import {faveoFormCommons} from 'mixins/faveoFormCommons';
import Modal from 'components/Common/Modal';

export default {
  name: 'edit-ticket-modal',
  description: 'Edit Ticket modal Component',

  props: {
    showModal: {type: Boolean, default: false},
    userId: {type: String | Number, default: ''},
  },

  data() {
    return {
      formUniqueKey: '',
      submitFormInProgress: false,
      scenario: 'create',
      panel: 'agent',
      showForm: false,
    }
  },

  mixins: [faveoFormCommons],

  methods: {

    /**
     * it clicks the button, it clicks the button in FormWithCaptcha which in change triggers formSubmit.
     * Reason for this workaround is to hide the submit button coming from FormWithCaptcha without breaking
     * the functionality
     */
    popUpSubmit() {
      document.getElementById('organization-submit-button').click();
    },

    formSubmit(key, value) {
      this.$store.dispatch('setRecaptchaKey', value)
      this.onSubmit({user_id: this.userId});
    },

    afterSubmit(data) {
      window.eventHub.$emit('reset-captcha');
      setTimeout(() => {
        window.eventHub.$emit('refreshUserData');
        this.onClose();
      }, 1000);
    },

    showModalMethod() {

      this.showForm = true;
    },
    onClose(){

      this.showForm = false;
    }
  },

  beforeDestroy() {
    this.$store.dispatch('unsetAlert');
  },

  components: {
    'modal': Modal,
    'alert': require('components/MiniComponent/Alert'),
    'mini-loader': require('components/Extra/Loader'),
    'loader': require('components/MiniComponent/Loader'),
    "form-with-captcha": require("components/Common/Form/FormWithCaptcha.vue"),
    "faveo-form": require('components/Common/Form/FormWithCaptcha.vue')
  }
};
</script>

<style scoped>
#modal_body {
  max-height: 300px;
  overflow-y: auto;
  overflow-x: hidden;
}


#create-org {
  display: inline-block;
}

</style>

<style>

#organization-submit-button{
/* hiding main button because there already is a button exists in the popup */
  display: none;
}
</style>