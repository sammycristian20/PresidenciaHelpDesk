<template>
  <faveo-box :title="trans('when_an_action_performed_by')">

    <div class="row">
      <div class="col-sm-4">
        <label class="radio-inline">
          <input type="radio" name="actionsPerformer" @change="onchange('agent')" value="agent" :checked="actionsPerformer === 'agent'"> {{lang('agent')}}
        </label>
      </div>

      <div class="col-sm-4">
        <label class="radio-inline">
          <input type="radio" name="actionsPerformer" @change="onchange('requester')" value="requester" :checked="actionsPerformer === 'requester'"> {{lang('requester')}}
        </label>
      </div>

      <div class="col-sm-4">
        <label class="radio-inline">
          <input type="radio" name="actionsPerformer" @change="onchange('agent_requester')" value="agent_requester" :checked="actionsPerformer === 'agent_requester'"> {{lang('agent_or_requester')}}
        </label>
      </div>
	  </div>

  </faveo-box>
</template>

<script>
import FaveoBox from 'components/MiniComponent/FaveoBox'

export default {

  name: 'action-performer',

  data: () => {
    return {
      actionsPerformer: 'agent',
    }
  },

  beforeMount () {
    this.actionsPerformer = this.$store.getters.getAutomatorDataByKey('actionsPerformer')
  },

  methods: {
    onchange (value) {
      this.$store.dispatch('setAutomatorDataByKey', { key: 'actionsPerformer', value: value })
    }
  },

  components: { 'faveo-box': FaveoBox }

}
</script>

<style>

</style>