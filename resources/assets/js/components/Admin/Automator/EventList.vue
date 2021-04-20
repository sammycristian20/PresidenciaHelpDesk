<template>
  <faveo-box :title="trans('involves_any_of_these_events')">

    <faveo-automator-event v-for="(event, index) in eventList" :key="event._id" :event="event" :index="index"/>

    <div style = "margin-top: 1rem">
      <button type="button" class="btn btn-light btn-block" @click="addNew()"><span><i data-v-7868ea29="" aria-hidden="true" class="fas fa-plus"></i> {{trans('add_new_event')}}</span></button>
    </div>
  </faveo-box>
</template>

<script>
import FaveoBox from 'components/MiniComponent/FaveoBox'
import { Event } from 'helpers/AutomatorUtils'
import { boolean } from 'helpers/extraLogics'

export default {

  name: 'event-list',

  data: () => {
    return {
      eventList: [],
    }
  },

  beforeMount () {
    this.eventList = this.$store.getters.getAutomatorDataByKey('events')

    if (!boolean(this.eventList)) {
      this.addNew()
    }
  },

  methods: {
    addNew () {
      this.$store.dispatch('addNewElementToAutomatorProperty', { key: 'events', value: new Event() });
    },
  },

  components: {
    'faveo-box': FaveoBox,
    'faveo-automator-event': require('components/Admin/Automator/FaveoAutomatorEvent'),
  }

}
</script>

<style scoped>
.event-label {
  line-height: 2.5;
  text-align: right;
}

.faveo-trash {
  padding-right: 1rem;
  line-height: 1.5;
}
</style>