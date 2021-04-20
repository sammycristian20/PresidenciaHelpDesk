<template>
  <faveo-box :title="trans('when_ticket_satisfies_these_condtions')">

    <div class="row">
      <div class="col-sm-3">
        <label class="radio-inline">
          <input type="radio" name="matcher" @change="onchange('any')" value="any" :checked="matcher === 'any'"> {{lang('match_any_of_below')}}
        </label>
      </div>

      <div class="col-sm-3">
        <label class="radio-inline">
          <input type="radio" name="matcher" @change="onchange('all')" value="all" :checked="matcher === 'all'"> {{lang('match_all_of_below')}}
        </label>
      </div>
	  </div>

    <faveo-automator-rule v-for="(rule, index) in ruleList" :key="rule._id" :rule="rule" :formFields="formFields" :index="index"/>

    <div style = "margin-top: 1rem">
      <button type="button" class="btn btn-light btn-block" @click="addNew()"><span><i data-v-7868ea29="" aria-hidden="true" class="fas fa-plus"></i> {{trans('add_new_rule')}}</span></button>
    </div>
  </faveo-box>
</template>

<script>
import FaveoBox from 'components/MiniComponent/FaveoBox'
import { Rule } from 'helpers/AutomatorUtils'
import { boolean } from 'helpers/extraLogics'
import {mapGetters} from "vuex";

export default {

  name: 'rule-list',

  props: {
    formFields: { type: Object, required: true },
  },

  data: () => {
    return {
      matcher: '',
    }
  },

  beforeMount () {
    this.matcher = this.$store.getters.getAutomatorDataByKey('matcher')

    if (!boolean(this.ruleList)) {
      this.addNew()
    }
  },

  computed: {
    ...mapGetters({ruleList: 'getAutomatorRules'})
  },

  methods: {
    addNew () {
      this.$store.dispatch('addNewElementToAutomatorProperty', { key: 'rules', value: new Rule() });
    },

    onchange (value) {
      this.matcher = value
      this.$store.dispatch('setAutomatorDataByKey', { key: 'matcher', value: value })
    }
  },

  components: {
    'faveo-box': FaveoBox,
    'faveo-automator-rule': require('./FaveoAutomatorRule'),
  }

}
</script>

<style>

</style>