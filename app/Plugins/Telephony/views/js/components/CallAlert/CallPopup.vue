<template>
  <div class="telephone-alert-box">

    <div class="telephone-alert-box-header">
      <header>
        {{lang('call_from')}} <b>{{userNotificationData.data.call_from}}</b>
        <span v-if="userNotificationData.timer.showTimer">
          <span id="call-pop-countdown"><b>{{userNotificationData.timer.getRemaining()}} </b>
            <i class="fa fa-stop pointer-cursor" aria-hidden="true" v-if="userNotificationData.timer.canUserStopTimer" @click="onStopTimerClick" :title="lang('click_to_stop_counter_otherwise_popup_will_close')"></i>
          </span>
        </span>
      </header>
    </div>

    <div class="telephone-alert-box-body">
      <div class="caller-info">

        <faveo-image-element id="caller-profile-image" class="caller-profile-image-avatar" :source-url="getUserProfiePic"/>

        <div v-if="userNotificationData.data.is_registered_user" class="user-name-and-email">
          <a :href="getUserProfileUrl" target="_blank">
            {{userNotificationData.data.user.name}}
            <sup v-if="userNotificationData.status === 'started' && userNotificationData.timer.showTimer"><span class="spinner-grow text-warning spinner-grow-small"></span></sup>
          </a><br>
          <small>{{userNotificationData.data.user.email}}</small>
        </div>

        <div v-else class="unknown-user">
          {{lang('unknown_user')}}
          <sup v-if="userNotificationData.status === 'started' && userNotificationData.timer.showTimer"><span class="spinner-grow text-warning spinner-grow-small"></span></sup>
        </div>

        <div class="recent-ticket-list-btn">
          <recent-ticket-list v-if="isUserActive" :ticket-list="userNotificationData.data.user.recent_tickets" :on-ticket-click="onTicketClick"></recent-ticket-list>
        </div>

      </div>

      <div v-if="linkedTicketObj && isUserActive" class="linked-ticket-block padding-top-7">
        <small>{{lang('linked_ticket')}}: #<a :href="basePath() + '/thread/' + linkedTicketObj.id" target="_blank">{{linkedTicketObj.ticket_number}}</a>
        <i v-if="showUnlinkBtn" class="fa fa-times ticket-unlink-btn pointer-cursor" :title="('remove_linked_ticket')" @click="unLinkTicket()"></i>
        </small>
      </div>

      <div class="take-note padding-top-7" v-if="isUserActive">
        <textarea id="telephone-alert-internal-note" class="form-control" style="resize: none;" v-model="internalNote" placeholder="Add internal note"></textarea>
      </div>

      <div class="telephone-alert-submit padding-top-7" v-if="userNotificationData.status === 'ended' && isUserActive">
        <button class="btn btn-primary btn-sm" :disabled="loading" @click="onSubmit()">
          {{loading ? lang('submitting') : lang('submit')}}
        </button>
      </div>

    </div>
  </div>
</template>

<script>

import { Timer } from '../../utils';
import { errorHandler, successHandler } from "helpers/responseHandler";

export default {

  name: 'call-popup',

  props: {
    callData: { type: Object, required: true },
  },

  data: () => {
    return {
      userNotificationData: null,
      internalNote: '',
      linkedTicketObj: null,
      loading: false,
      showUnlinkBtn: false,
      isUserActive: false,
    }
  },

  beforeMount() {
    this.userNotificationData = this.callData;
    this.linkedTicketObj = this.callData.data.user.linked_ticket;
  },

  watch: {
    callData: {
      handler: function (newValue, oldValue) {
        this.userNotificationData = newValue;
        const LINKED_TICKET_OBJ = newValue.data.user.linked_ticket
        if (LINKED_TICKET_OBJ) {
          this.linkedTicketObj = LINKED_TICKET_OBJ;
        }
      },
      deep: true
    },
  },

  methods: {

    onStopTimerClick() {
      this.isUserActive = true;
      clearTimeout(this.userNotificationData.timer.timerId);
      if(this.userNotificationData.status === 'ended') {
        this.userNotificationData.timer = new Timer(() => this.onSubmit(), this.userNotificationData.data.conversion_waiting_time, false);
      } else {
        this.userNotificationData.timer.showTimer = false;
      }
      this.userNotificationData.timer.canPauseResume = false;
     },

    unLinkTicket() {
      this.linkedTicketObj = this.callData.data.user.linked_ticket;
      this.showUnlinkBtn = false;
    },

    onTicketClick(ticket) {
      if(this.callData.data.user.linked_ticket) {
        return;
      }
      this.linkedTicketObj = ticket;
      this.showUnlinkBtn = true;
    },

    onSubmit() {
      clearTimeout(this.userNotificationData.timer.timerId);

      if(!this.userNotificationData.data.allow_ticket_conversion) {
        this.$store.dispatch('removeElement', this.userNotificationData);
        return;
      };

      this.loading = true;
      const params = {
        link_ticket: this.linkedTicketObj ? this.linkedTicketObj.id : undefined,
        notes: this.internalNote ? this.internalNote : undefined
      }
      axios.post('telephony/api/convert-call-to-ticket/' + this.userNotificationData.id , params)
        .then((response) => {
          successHandler(response, 'root-alert-container');
        })
        .catch((error) => {
          errorHandler(error, 'root-alert-container');
        })
        .finally( () => {
          this.loading = false;
          this.$store.dispatch('removeElement', this.userNotificationData);
        })
    },
  },

  computed: {

    getUserProfileUrl() {
      return this.userNotificationData.data.user.id ? this.basePath() + '/user/' + this.userNotificationData.data.user.id : ''
    },

    getUserProfiePic() {
      return this.userNotificationData.data.user.profile_pic || '';
    }

  },

  components: {
    'faveo-image-element': require('components/Common/FaveoImageElement'),
    'recent-ticket-list': require('./RecentTickets')
  }

}
</script>

<style scoped>

.telephone-alert-box {
  min-width: 400px;
  font-size: 16px;
  border-radius: 2px;
  background-color: #222d32;
  color: #b8c7ce;
  box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
  transition: box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
}

.telephone-alert-box-header {
  border-bottom: 1px solid #393939;
  padding: 1rem 1rem;
}

.telephone-alert-box-body {
  padding: 0.7rem 0.7rem;
}

.caller-info {
  display: flex;
  flex-wrap: nowrap;
}

.caller-profile-image-avatar {
  vertical-align: middle;
  width: 50px;
  height: 50px;
  border-radius: 100%;
  border: 3px solid #CBCBDA;
  padding: 3px;
}

.user-name-and-email {
  padding-left: 0.7rem;
}

.unknown-user {
  line-height: 50px;
  padding-left: 0.7rem;
}

#telephone-alert-internal-note {
  font-size: 14px;
}

.pointer-cursor {
  cursor: pointer;
}

.padding-top-7 {
  padding-top: 0.7rem;
}

.ticket-unlink-btn {
  padding-left: 0.3rem;
}

#call-pop-countdown {
  float: right;
  color: red;
}

.recent-ticket-list-btn {
  position: fixed;
  right: 0.7rem;
}

.spinner-grow-small {
  width: .5rem;
  height: .5rem;
}
</style>