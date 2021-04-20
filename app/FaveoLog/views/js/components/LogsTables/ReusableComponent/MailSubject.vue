<template>

	<div>
		<a href="javascript:;" id="log-mail-subject" @click="getMailBody">{{data.subject}}</a>
		<transition name="modal">
			<logs-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :data="data" title="logs_content"></logs-modal>
		</transition>
	</div>

</template>

<script type="text/javascript">

  import LogsModal from "./LogsModal";

	export default {

		name:"mail-subject",

		description: "shows mail subject as a clickable link which gives mail body on click",

		props: {

			data : { type : Object, required : true }

		},

		data(){
			return {
				showModal:false,
			}
		},

    methods:{

			onClose(){
				this.showModal = false;
        this.$store.dispatch("unsetValidationError");
			},

      getMailBody(){
        this.showModal = true;
      }
		},

		components:{
			"logs-modal": LogsModal
		}
	};

</script>

<style type="text/css">

</style>
