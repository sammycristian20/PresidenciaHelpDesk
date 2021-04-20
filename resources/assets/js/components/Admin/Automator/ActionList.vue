<template>
  <faveo-box :title="trans('perform_these_actions')">

    <faveo-automator-action v-for="(action, index) in actionList" :key="action._id" :action="action" :formFields="formFields" :index="index" />

    <div style = "margin-top: 1rem">
      <button type="button" class="btn btn-light btn-block" @click="addNew()"><span><i data-v-7868ea29="" aria-hidden="true" class="fas fa-plus"></i> {{trans('add_new_action')}}</span></button>
    </div>

  </faveo-box>
</template>

<script>
import FaveoBox from 'components/MiniComponent/FaveoBox'
import { Action } from 'helpers/AutomatorUtils'
import { boolean } from 'helpers/extraLogics'
import {mapGetters} from "vuex";

export default {

  name: 'action-list',

  props: {
    formFields: { type: Array, required: true },
  },

  beforeMount () {

    if (!boolean(this.actionList)) {
      this.addNew()
    }
  },

  computed: {
    ...mapGetters({actionList: 'getAutomatorActions'})
  },

  methods: {

    addNew () {
      this.$store.dispatch('addNewElementToAutomatorProperty', { key: 'actions', value: new Action() });
    }
  },

  components: {
    'faveo-box': FaveoBox,
    'faveo-automator-action': require('./FaveoAutomatorAction'),
  }

}
</script>

<style>

</style>