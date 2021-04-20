<template>
  <div>
    <time-period v-if="scenario === 'recur'"></time-period>

    <div class="row">
      <div v-for="formField in formFields" :key="formField.unique" :id="formField.unique" class="col-md-12">
        <form-field-renderer
          :form-field="formField"
          :formUniqueKey="formUniqueKey"
          :panel="panel"
          :scenario="scenario">
        </form-field-renderer>
      </div>
    </div>

  </div>
</template>

<script>
import {errorHandler} from 'helpers/responseHandler'

export default {
  name: 'form-renderer',

  props: {

    // API Endpoint to fetch the form fields JSON
    fetchFormFieldApi: { type: String, required: true },

    // Form unique key to pick data from the Store
    formUniqueKey: { type: String, required: true },

    // category is for categorizing the FaveoForm; may be ticket, user, organization, etc
    category: { type: String, required: true },

    // scenario will be the mode of the FaveoForm Category; may be create/edt/recur etc
    scenario: { type: String, required: true },

    // One category may be present in differnet panels(admin/client/agent)
    panel: { type: String, required: true },

    // if it is a child form of a parent form (for eg. requester form in ticket form)
    isChildForm: { type: Boolean, default: false },
  },

  data: () => {
    return {
      // Array of form field data 
      formFields: [],
    }
  },

  beforeMount() {
    // get form field json data from server
    this.getFormFields();
  },

  computed: {

    getApiParams () {
      return {category: this.category, scenario: this.scenario, panel: this.panel}
    }
  },

  methods: {

    getFormFields () {

      if(this.isChildForm){
        this.$store.dispatch('startChildFormLoader');
      } else {
        this.$store.dispatch('startLoader', 'getFormFields');
      }

      axios.get(this.fetchFormFieldApi, { params: this.getApiParams })
      .then(response => {
        this.formFields = response.data.data.form_fields;

        // Update the submitApiENdpoint for corresponding form insatnce to the store
        this.$store.dispatch('updateSubmitApiEndpoint', { formUniqueKey: this.formUniqueKey, submitApiEndpoint: response.data.data.submit_endpoint });

        // Update recur data if any
        const recurData = response.data.data.recur;
        if (recurData) {
          for (const [key, value] of Object.entries(recurData)) {
            this.$store.dispatch('updateRecurProperties', { key: key, value: value });
          }
        }

        // Update batch ticket mode to the store
        this.$store.dispatch('setBatchTicketMode', response.data.data.batch_ticket_status);

      })
      .catch(err => {
        errorHandler(err, 'form-renderer');
      })
      .finally(() => {
        if (this.isChildForm){
          this.$store.dispatch('stopChildFormLoader');
        } else {
          this.$store.dispatch('stopLoader', 'getFormFields');
        }
      })
    },

  },

  components: {
    'form-field-renderer': require('./FormFieldRenderer'),
    'loader': require('components/MiniComponent/Loader'),
    'time-period': require('components/Admin/Recurring/TimePeriod')
  }
}
</script>

<style scoped>
.form-field-wrapper {
  min-height: 91px;
}
</style>