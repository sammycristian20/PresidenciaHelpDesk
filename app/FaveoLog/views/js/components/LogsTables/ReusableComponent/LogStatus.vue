<template>
    <div>
      <span :id="'log-status-' + data.id" :class="['badge', badgeClass, 'log-status', 'text-uppercase']" @click="showModal = Boolean(data.exception)" v-tooltip="getStatusTitle" :style="data.exception ? {cursor: 'pointer'} : {} ">
        {{lang(data.status)}}
      </span>
      <transition name="modal">
        <exception-detail-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :data="data.exception"/>
      </transition>
    </div>
</template>

<script type="text/javascript">

	import axios from "axios";

	export default {

		name: "log-status",

    description: "status of the log",
    
    components: {
      'exception-detail-modal': require('./ExceptionDetailModal')
    },

		props: {
			data : { type : Object, required : true }
		},

		data(){
			return {
				showModal: false,
			}
    },

    computed : {
      badgeClass(){
        switch(this.data.status){

          case "sent":
          case "accepted":
          case "completed":
            return "btn-success";

          case "queued":
          case "running":
          case "pending":
            return "btn-warning";

          case "rejected":
          case "failed":
          case "blocked":
            return "btn-danger";

          default:
            return "btn-default";
        }
      },

      getStatusTitle: function() {
        return Boolean(this.data.exception) ? this.data.status + ': Click to view exception details.' : this.data.status;
      }
    },

    methods: {
      onClose() {
				this.showModal = false;
			},
    },

	};

</script>

<style type="text/css">
  .log-status{
    min-width : 65px;
  }
</style>
