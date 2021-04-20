<template>

  <div>

      <div class="scrollable-ul">

        <div class="timeline timeline-inverse" v-if="inboxThreads.length > 0">

          <template v-for="(thread,index) in inboxThreads">

            <div class="time-label" v-if="checkDate(index)">

              <span class="bg-success">{{formattedDate(thread.created_at)}} </span>
            </div>

            <thread-body :thread="thread" :key="'thread'+index" :index="index"></thread-body>

            <div v-if="showThreadEnd(index)">

              <i class="fas fa-history bg-gray"></i>
            </div>
          </template>

          <infinite-loading @infinite="getThreads" ref="infiniteLoading">
            <div slot="spinner"></div>
            <div slot="no-results"></div>
            <div slot="no-more"></div>
          </infinite-loading>
        </div>
      </div>

      <custom-loader :duration="4000" v-if="showLoader" />
  </div>
</template>

<script>

import { mapGetters } from 'vuex';

import { successHandler, errorHandler } from 'helpers/responseHandler';

import axios from 'axios';

export default {

  name : 'inbox-threads',

  description : 'Inbox Threads Component',

  props : {

    ticketId : { type : String | Number, default : '' }
  },

  data() {

    return {

      inboxThreads : [],

      page : 1,

      showLoader: false
    }
  },

  computed : {

    ...mapGetters(['formattedTime','formattedDate'])
  },

  beforeMount() {

    this.getThreads()
  },

  methods : {

    updateLogs() {

      this.inboxThreads = [];

      this.page = 1;

      this.getThreads(undefined, true);
    },

    getThreads($state, isRefresh = false) {

      this.showLoader = true;

      axios.get('/api/agent/ticket-conversation/'+this.ticketId, { params: { page: this.page } }).then(res => {

        if(res.data.data.data.length) {

          if(isRefresh) {

            this.inboxThreads = res.data.data.data;

          } else {

            this.inboxThreads.push(...res.data.data.data);
          }

          this.page += 1;

        } else {

          $state && $state.complete();
        }
      }).catch(error => {

        errorHandler(error, 'inbox-threads');

        this.inboxThreads = [];

      }).finally(() => {

        $state && $state.loaded();

        this.showLoader = false;
      })
    },

    checkDate(x){

      if(x==0){

        return true;

      }else{

        var date1=this.formattedDate(this.inboxThreads[x-1].created_at);

        var date2=this.formattedDate(this.inboxThreads[x].created_at);

        if(date1!=date2){

          return true;
        }
      }
    },

    showThreadEnd(x){

      return x === this.inboxThreads.length-1
    }
  },

  components : {

    'thread-body' : require('./Mini/ThreadBody'),

    'faveo-box': require('components/MiniComponent/FaveoBox'),

    'custom-loader': require('components/MiniComponent/Loader')
  }
};
</script>

<style scoped>

.refresh_a { cursor: pointer;color: #777 !important; }
</style>