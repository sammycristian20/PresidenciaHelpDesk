<template>
  <div>
    <loader v-if="submitFormInProgress || isChildFormLoading"/>

    <modal :showModal="show" :onClose="closeModal">
      
      <div slot="title"><h4>{{ panel === 'client' ? lang('register') : lang('create_user') }}</h4></div>
      
      <template v-show="!isChildFormLoading">
        
        <div slot="fields" id="user-form-modal-body">
          
          <alert componentName="user-form-modal"/>

          <form-with-captcha :loading="!isChildFormLoading" btnClass='btn btn-primary pull-right' btnName="submit"
                           componentName="user-form-modal"
                           btn_id="user-submit" :formSubmit="formSubmit" page="user_create_agent"
                           recaptchaContainerClass="col offset-md-2 col-md-10" :disableSubmit="submitFormInProgress">

          <div slot="fields" class="row">

            <ValidationObserver ref="faveoFormObserver">
              <form-renderer
                  category="user"
                  fetchFormFieldApi="api/form/render"
                  scenario="create"
                  :panel="panel"
                  :formUniqueKey="formUniqueKey"
                  :isChildForm="true"
              >
              </form-renderer>
            </ValidationObserver>
          </div>
        </form-with-captcha>
      </div>

      <div slot="controls">
        <button type="submit" class="btn btn-primary pull-right" :disabled="submitFormInProgress"
                @click="popUpSubmit()">
          <span><i class="fas fa-save"></i></span>
          {{ trans('submit') }}
        </button>
      </div>
    </template>

    </modal>
  </div>
</template>

<script>
import {faveoFormCommons} from 'mixins/faveoFormCommons';
import Modal from 'components/Common/Modal';

import {mapGetters} from 'vuex';

export default {
  name: 'user-form-modal',

  props: {
    show: {type: Boolean, default: false},
    closeModal: {type: Function, required: true},
    updateNewRequester: {type: Function, required: true},
    panel: {type: String, default: 'agent'},
  },

  data: () => {
    return {
      formUniqueKey: '',
      submitFormInProgress: false
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
      document.getElementById('user-submit').click();
    },

    formSubmit(key, value) {
      this.$store.dispatch('setRecaptchaKey', value)
      this.onSubmit();
    },

    afterSubmit(data) {
      
      window.eventHub.$emit('reset-captcha');
      
      let requesterData = data.data.data;

      if(!requesterData.email){

        requesterData['name'] = requesterData.full_name;
      }

      this.updateNewRequester(requesterData);

      this.closeModal();
    },

    afterError() {
      this.closeModal();
    }
  },

  components: {
    'modal': Modal,
    'alert': require('components/MiniComponent/Alert'),
    'loader': require('components/MiniComponent/Loader'),
    "form-with-captcha": require("components/Common/Form/FormWithCaptcha.vue"),
  }
}
</script>

<style scoped>
#user-form-modal-body {
  max-height: 300px;
  overflow-y: auto;
  overflow-x: hidden;
}
</style>

<style>

#user-submit{
  display: none;
}
</style>