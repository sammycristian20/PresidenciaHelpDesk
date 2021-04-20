<template>
   <div id="telephone-alert-box__position">
     <transition-group name="slide-fade">
       <template v-if="getItems.length > 0">
         <div id="telephone-alert-box" v-for="item in getItems" :key="item.id" @mouseenter="onMouseEnter(item)" @mouseleave="onMouseLeave(item)">
          <call-popup :key="item.id" :call-data="item"/>
        </div>
       </template>
     </transition-group>
   </div>
</template>

<script>

import { mapGetters } from 'vuex';
import { Timer } from '../../utils';

export default {

  name: 'telephone-alert',

  data: () => {
    return {
    }
  },

  props: {
    /**
     * the id of user subscribing to the user-notifications channel
     * @type {Number}
     */
    user: { type: Number, required: true},
  },

  mounted() {
    window.Echo.private('user-notifications.' + this.user)
    .listen('.call-started',(response) => {
      console.debug('call-started', response);
      this.addUpdateItem(response, 'started');
    }).listen('.call-ended',(response) => {
      console.debug('call-ended', response);
      this.addUpdateItem(response, 'ended');
    })
  },

  computed: {
    ...mapGetters(['getItems'])
  },

  methods: {

    onMouseEnter(item) {
      item.timer.canPauseResume && item.timer.canUserStopTimer && item.timer.pause();
    },

    onMouseLeave(item) {
      item.timer.canPauseResume && item.timer.canUserStopTimer && item.timer.resume();
    },

    addUpdateItem(response, status) {

      if(this.isDuplicate(response.call_id, status)) return;

      let item = {
        id: response.call_id,
        data: response,
        status: status
      };

      item.timer = new Timer(()=> this.distoryItem(item), 15, true);

      this.$store.dispatch('addUpdateElement', item);
    },

    isDuplicate(id, status) {
      const item = this.getItems.find(v => v.id === id);

      if(typeof item === 'undefined') return false;

      if(item.status === status) return true;

      return false;
    },

    distoryItem(item) {
      this.$store.dispatch('removeElement', item);
    },

  },

  beforeDestroy() {
    this.$store.dispatch('clearAll');
  },

  components: {
    'call-popup': require('./CallPopup')
  }

}
</script>

<style scoped>
#telephone-alert-box__position {
  position: fixed;
  bottom: 0;
  right: 2px;
  z-index: 9999;
}

#telephone-alert-box {
  margin-bottom: 0.5rem;
}

.slide-fade-enter-active {
  transition: all .10s ease;
}
.slide-fade-leave-active {
  transition: all .5s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to {
  transform: translateX(100%);
}
</style>