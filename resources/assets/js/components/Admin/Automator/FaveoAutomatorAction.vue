<template>
  <div class="row faveo-automator-action">

    <span class="col-sm-auto"><i class="fas fa-trash faveo-trash" @click="deleteAction()"></i></span>

    <dynamic-select
      classname="col-sm-5"
      :labelStyle="{display: 'none'}"
      :multiple="false"
      :id="'action-' + action._id"
      :name="'action-' + action._id"
      :elements="selectableFormFields"
      optionLabel="label"
      :value="field"
      :onChange="onFieldChange"
      :clearable="false">
    </dynamic-select>

    <template v-if="field !== null">
      <div v-if="field.type === 'custom'" class="col-sm-12" :key="JSON.stringify(field)">
        <automator-custom-type-field :field="field" :action-email="action.action_email" :index="index" />
      </div>
      <div v-else class="col-sm-6" :key="JSON.stringify(field)">
        <automator-form-field
          :key="action._id"
          enforcerType="action"
          :form-field="field"
          :enforcer-object="action"
          :labelStyle="{display: 'none'}">
        </automator-form-field>
      </div>
    </template>
  </div>
</template>

<script>
import { Action } from 'helpers/AutomatorUtils'
import {mapGetters} from "vuex";

export default {

  name: 'faveo-automator-action',

  props: {
    action: { type: Action, required: true },
    formFields: { type: Array, required: true },
    index: { type: Number, required: true },
  },

  data: () => {
    return {
      field: null
    }
  },

  beforeMount () {
    this.field = this.action.field
  },

  methods: {

    onFieldChange (value) {
      this.field = value;
      this.$store.dispatch('onFieldChange', { key: 'actions', index: this.index, field: this.field })
    },

    deleteAction () {
      window.eventHub.$emit('deleteItem', 'action', this.index, this.action._id);
    }
  },

  computed : {
    ...mapGetters({selectedActionKeys: 'getSelectedActionKeys'}),

    /**
     * Gives form fields which can be selected. Form fields which are already selected cannot be selected again
     */
    selectableFormFields(){
      // check what all fields has already been selected and return the rest of it
      return this.formFields.filter((formField)=>{
        return !this.selectedActionKeys.includes(formField.unique);
      });
    }
  },

  components: {
    'dynamic-select': require('components/MiniComponent/FormField/DynamicSelect'),
    'automator-form-field': require('./AutomatorFormField'),
    'automator-custom-type-field': require('./AutomatorCustomTypeField')
  }

}
</script>

<style scoped>

.faveo-trash {
  padding-right: 1rem;
  line-height: 1.5;
}

.faveo-automator-action {
  padding-top: 1.5rem;
}

.faveo-automator-action:hover {
  box-shadow: 0 -1px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
}

</style>