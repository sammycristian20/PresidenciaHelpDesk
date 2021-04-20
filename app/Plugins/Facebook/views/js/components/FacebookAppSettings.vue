<template>

  <div>

    <!--loader-->
    <div class="row" v-if="loading === true">
      <custom-loader :duration="loadingSpeed"></custom-loader>
    </div>

    <!-- ALERT COMPONENT-->
    <alert componentName="facebookAppSettings" />
    <!-- ALERT COMPONENT -->

    <faveo-box :title="lang('facebook_app_settings')">
      <tool-tip :message="lang('facebook_security_tooltip')" size="large" slot="headerTooltip"/>

      <div slot="headerMenu" class="card-tools">
        <button @click.prevent="gotoPageSettingsPage" class="btn btn-tool btn-default">
          <i class="fab fa-facebook-square"></i>
          {{ lang('facebook_go_to_page_settings') }}
        </button>
      </div>

      <div>
        <div class="row">

          <text-field
              :label="lang('facebook_verify_token')" :onChange="onChange"
              :value="hub_verify_token" type="text" name="hub_verify_token"
              :required="true"  classname="col-sm-6"
              :hint="lang('facebook_verify_token_hint')" id="facebook_verify_token"
              :disabled="true"
          />

          <text-field
              :label="lang('facebook_app_secret')" :onChange="onChange"
              :value="fb_secret" type="password" name="fb_secret"
              :required="true"  classname="col-sm-6"
              :hint="lang('facebook_app_secret_hint')" id="facebook_app_secret"
          />

        </div> <!--row-->

      </div>

      <div class="card-footer" slot="actions">

        <button class="btn btn-primary" @click.prevent="submitter">
          <i class="fas fa-save"> </i> {{ lang('facebook_save') }}
        </button>

        <button class="btn btn-primary" @click="toggleModal" :disabled="!appExists">
          <i class="glyphicon glyphicon-repeat"></i>&nbsp;
          {{ lang('facebook_reset') }}
        </button>

      </div> <!--row-->

    </faveo-box>

    <transition name="modal">
      <!-- reset pop-up -->
      <modal v-if="showModal" :showModal="true" :onClose="()=> showModal = false" containerStyle="width: 500px">

        <!-- if mode is reset, we only show the confirmation message that if they really want to reset -->
        <div slot="title">
          <h4>{{lang('facebook_reset')}}</h4>
        </div>

        <!-- if mode is reset, we only show the confirmation message that if they really want to reset -->
        <div v-if="!isLoading" slot="fields">
          <h5 id="H5">
            {{lang('facebook_reset_confirm')}}
          </h5>
        </div>

        <div v-if="isLoading" class="row" slot="fields" >
          <loader :animation-duration="4000" color="#1d78ff" :size="size" :class="{spin: lang_locale === 'ar'}" />
        </div>

        <div slot="controls">
          <button type="button" @click="deleteFacebook" class="btn btn-primary">
            <i class="glyphicon glyphicon-repeat" aria-hidden="true"></i>
            &nbsp;{{lang('reset')}}
          </button>
        </div>

      </modal>

    </transition>


  </div>

</template>

<script>

import axios from "axios";
import {errorHandler, successHandler} from 'helpers/responseHandler';
import FaveoBox from "components/MiniComponent/FaveoBox";
import { mapGetters } from 'vuex'


export default {

  computed :{
    ...mapGetters(['formattedTime','formattedDate']),
  },

  data() {
    return {
      fb_secret: '',
      hub_verify_token: '',
      appExists: false,
      loadingSpeed: 4000,
      showModal: false,
      loading: false,
      settingsId: '',
    }
  },


  components:{
    "text-field": require("components/MiniComponent/FormField/TextField"),
    'alert' : require('components/MiniComponent/Alert'),
    "custom-loader": require("components/MiniComponent/Loader"),
    'dynamic-select': require("components/MiniComponent/FormField/DynamicSelect"),
    "modal": require('components/Common/Modal'),
    "faveo-box" : FaveoBox,
    "tool-tip": require("components/MiniComponent/ToolTip")
  },

  beforeMount() {

    this.getAppDetails();

  },

  methods: {

    redirectToPages() {
      this.redirect('/facebook/pages/list');
    },

    onChange(value, name){
      this[name]= value
    },

    gotoPageSettingsPage() {
      this.redirect('/facebook/settings');
    },

    getAppDetails() {
      this.loading = true;
      axios.get('facebook/api/security-settings/index')
          .then((res) => {
            let data = res.data.data;
            if(data) {
              this.appExists = true;
              this.fb_secret = data.fb_secret;
              this.hub_verify_token = data.hub_verify_token;
              this.settingsId = data.id;
              this.loading = false;
            }
          })
          .catch((err) => {
            Object.assign(this.$data, this.$options.data.apply(this));
            this.appExists = false;
            this.loading = false;
            this.hub_verify_token = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
          })
    },

    deleteFacebook() {
      //for reset
      this.isLoading = true
      this.isDisabled = true
      axios.delete(`facebook/api/security-settings/delete/${this.settingsId}`).then(res=>{
        successHandler(res,"facebookAppSettings");
      }).catch(err => {
        errorHandler(err,"facebookAppSettings");
      })
      .finally(() => {
        this.isLoading = false;
        this.showModal = false;
        this.getAppDetails();
      })
    },


    toggleModal() {
      this.showModal = !(this.showModal);
    },
    onClose(){
      this.showModal = false;
    },

    submitter() {

      let formDataObj = {
        fb_secret: this.fb_secret,
        hub_verify_token: this.hub_verify_token

      }

      this.loading = true;
      let url,method;

      if(this.appExists) {
        url = `facebook/api/security-settings/update/${this.settingsId}`;
        method = 'PUT';
      } else {
        url = 'facebook/api/security-settings/create';
        method = 'POST';
      }

      axios.request({
        method,
        url,
        data: formDataObj
      })
      .then(res=>{
        successHandler(res,'facebookAppSettings');
        this.$store.dispatch('unsetValidationError');
      }).catch(err => {
        errorHandler(err,'facebookAppSettings');
      })
      .finally(() => {
        this.loading = false;
        this.getAppDetails();
      })

    }

  }
}
</script>


<style>
.mt-5 {
  margin-top: 5rem;
}

.app-id{
  width:17% !important;
  word-break: break-all;
}

.app-secret {
  width: 25% !important;
  word-break: break-all;
}

.app-created {
  width: 18%;
  word-break: break-all;
}

.app-action {
  width: 10%;
  word-break : break-all;
}

.app-cron {
  width: 10% !important;
  word-break: break-all;
}

.app-new-ticket-interval{
  width: 20% !important;
  word-break: break-all;
}

.search {
  display: none !important;
}
#H5{
  margin-left:16px;
  /*margin-bottom:18px !important;*/
}

</style>