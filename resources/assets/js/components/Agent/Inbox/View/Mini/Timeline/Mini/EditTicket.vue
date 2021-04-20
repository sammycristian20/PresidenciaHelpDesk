<template>
  <div>
    <loader v-if="submitFormInProgress"/>
    <modal v-if="showModal" :showModal="showModal" :onClose="onClose">
      <div slot="title">
        <h4>{{ lang('edit_ticket') }}</h4>
      </div>

      <div slot="fields">
        <mini-loader v-if="showLoader"/>
      </div>

      <div slot="fields" id="ticket_modal_body">
        <alert componentName="faveo-form"/>

        <form-with-captcha :loading="!showLoader" btnClass='btn btn-primary pull-right' btnName="submit"
                           componentName="faveo-form"
                           btn_id="edit-ticket-submit" :formSubmit="formSubmit" page="ticket_edit_agent"
                           recaptchaContainerClass="col offset-md-2 col-md-10" :disableSubmit="submitFormInProgress">
          <div slot="fields" class="row">

            <ValidationObserver ref="faveoFormObserver">
              <form-renderer
                  category="ticket"
                  :fetchFormFieldApi="'api/agent/edit-mode-ticket-details/' + this.ticketId"
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
    onClose: {type: Function},
    ticketId: {type: String | Number, default: ''},
  },

  data() {
    return {
      formUniqueKey: '',
      submitFormInProgress: false,
      scenario: 'edit',
      panel: 'agent',
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
      document.getElementById('edit-ticket-submit').click();
    },

    formSubmit(key, value) {
      this.$store.dispatch('setRecaptchaKey', value)
      this.onSubmit();
    },

    afterSubmit(data) {
      window.eventHub.$emit('reset-captcha');
      setTimeout(() => {
        window.eventHub.$emit('refreshTableAndData', true);
        this.onClose();
      }, 1000);
    },
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
#ticket_modal_body {
  max-height: 300px;
  overflow-y: auto;
  overflow-x: hidden;
}
</style>

<style>

#edit-ticket-submit{
  /*for hiding submit button of FormWithCaptcha component*/
  display: none;
}
</style>