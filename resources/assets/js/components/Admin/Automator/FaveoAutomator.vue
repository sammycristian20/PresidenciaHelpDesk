<template>
  <faveo-box :title="trans(title)">

    <alert componentName="faveo-automator" />

    <loader v-if="showLoader || submitFormInProgress" :duration="4000" />

    <template v-if="!showLoader">

      <status-switch slot="headerMenu" class="pull-right" name="status" :title="status ? trans('active') : trans('inactive')" :value="status" :onChange="onChange" />

      <ValidationObserver ref="faveoAutomatorForm">

        <text-field id="name" type="text" :label="trans('name')" name="name" classname="col-sm-6" :value="name" :onChange="onChange" :required="true" rules="required" />

        <action-performer v-if="category === 'listener'" />

        <event-list v-if="category === 'listener'" />

        <rule-list :formFields="formFields.rules" />

        <action-list :formFields="formFields.actions" />

        <text-field :label="trans('internal_note')" type="textarea" name="internal_notes" :value="internal_notes" :onChange="onChange" 
          :length="255" :show-word-limit="true" :rows="2" />

        <div class="form-group row" v-if="!showLoader">
          <div class="col-md-12">
            <button type="submit" class="btn btn-primary" @click="onSubmit()" :disabled="submitFormInProgress">
              <span v-if="!editId"><i class="fas fa-save"></i></span>
              <span v-else><i class="fas fa-sync"></i></span>
              {{ editId ? trans('update') : trans('submit') }}
            </button>
          </div>
        </div>

      </ValidationObserver>

    </template>

  </faveo-box>
</template>

<script>
import FaveoBox from 'components/MiniComponent/FaveoBox';
import { getIdFromUrl, boolean } from 'helpers/extraLogics';
import { validateFormData } from 'helpers/formUtils';
import { FaveoAutomator, getEnforcerInstanceList, getEvenInstancetList, getEnforcerDataByPropertyForSubmit, getEventDataForSubmit, isLocalElement } from 'helpers/AutomatorUtils';
import { errorHandler, successHandler } from "helpers/responseHandler";
import { mapGetters } from 'vuex';

export default {

  name: 'faveo-automator',

  data: () => {
    return {
      title: '',
      name: '',
      status: true,
      internal_notes: '',
      editId: 0,
      formFields: {},
      submitEndpoint: '',
      rules: [],
      actions: [],
      events: [],
      submitFormInProgress: false
    }
  },

  props: {
    category: { type: String, required: true },
  },

  beforeMount () {

    this.getAutomatorData();

    this.editId = getIdFromUrl(this.currentPath());

    boolean(this.editId) ? this.handleEditCase() : this.handleCreateCase();

  },

  created () {
    window.eventHub.$on('deleteItem', this.deleteItem);
  },

  computed: { ...mapGetters(['showLoader']) },

  methods: {

    handleCreateCase () {
      this.title = 'create_' + this.category;
      this.$store.dispatch('createNewAutomatorInstance');
    },

    handleEditCase () {
      this.title = 'edit_' + this.category;
      this.getAutomatorEditData();
    },

    onChange (value, name) {
      this[name] = value
    },

    getAutomatorData () {
      this.$store.dispatch('startLoader', 'getAutomatorData');

      axios.get('api/form/automator', { params: { category: this.category }})
      .then((response) => {
        this.formFields = response.data.data.form_fields
        this.submitEndpoint = response.data.data.submit_endpoint
      })
      .catch((error) => {
        errorHandler(error, 'faveo-automator')
      })
      .finally(() => {
        this.$store.dispatch('stopLoader', 'getAutomatorData');
      })
    },

    getAutomatorEditData () {
      this.$store.dispatch('startLoader', 'getAutomatorEditData');

      axios.get('api/get-enforcer/' + this.category + '/' + this.editId)
      .then((response) => {

        const DATA = response.data.data;

        this.name = DATA.name;
        this.status = DATA.status;
        this.internal_notes = DATA.internal_notes;

        const faveoAutomator = new FaveoAutomator(DATA.id, getEnforcerInstanceList(DATA.rules, 'rule'), getEnforcerInstanceList(DATA.actions, 'action'), getEvenInstancetList(DATA.events), DATA.matcher, DATA.triggered_by);

        this.$store.dispatch('createNewAutomatorInstance', faveoAutomator);
        this.$store.dispatch('stopLoader', 'getAutomatorEditData');

      })
      .catch((error) => {
        errorHandler(error, 'faveo-automator')
      })
    },

    async onSubmit () {

      const isValid = await validateFormData(this.$refs.faveoAutomatorForm);

      if (isValid) {

        this.submitFormInProgress = true;
  
        const automatorData = this.$store.getters.getAutomatorData;

        let params = {
          type: this.category,
          data: {
            id: automatorData.id,
            matcher: automatorData.matcher,
            triggered_by: automatorData.actionsPerformer,
            name: this.name,
            status: this.status,
            internal_notes: this.internal_notes,
            rules: getEnforcerDataByPropertyForSubmit(automatorData.rules, 'rules'),
            actions: getEnforcerDataByPropertyForSubmit(automatorData.actions, 'actions'),
            events: getEventDataForSubmit(automatorData.events)
          }
        };

        axios.post(this.submitEndpoint, params )
          .then((response) => {
            successHandler(response, 'faveo-automator');

            if (!boolean(this.editId)) {
              setTimeout(() => {
                this.redirect('/' + this.category);
              }, 700);
            } else {
              this.getAutomatorEditData();
            }
          })
          .catch((error) => {
            errorHandler(error, 'faveo-automator');
          })
          .finally(() => {
            this.submitFormInProgress = false;
          })
      }
    },

    deleteItem (type, index, id) {

      const isConfirm = confirm('Are you sure you want to delete?');

      if (!isConfirm) return;

      if (isLocalElement(id)) { this.deleteItemFromStore(type, index); return };

      axios.delete(`api/delete-enforcer/${type}/${id}`)
        .then((response) => {
          this.deleteItemFromStore(type, index);
          successHandler(response, 'faveo-automator')
        })
        .catch((error) => {
          errorHandler(error, 'faveo-automator')
        })
    },

    deleteItemFromStore (type, index) {
      this.$store.dispatch('deleteElementFromAutomator', { key: type, index: index })
    },

  },

  beforeDestroy() {
    this.$store.dispatch('forceStopLoader')
    this.$store.dispatch('destroyAutomatorInstance')
  },

  components: {
    'faveo-box': FaveoBox,
    'loader': require('components/MiniComponent/Loader'),
    'rule-list': require('components/Admin/Automator/RuleList'),
    'action-list': require('components/Admin/Automator/ActionList'),
    'event-list': require('components/Admin/Automator/EventList'),
    'text-field': require('components/MiniComponent/FormField/TextField'),
    'status-switch': require('components/MiniComponent/FormField/Switch'),
    'action-performer': require('components/Admin/Automator/ActionPerformer'),
  }
}
</script>

<style>

</style>