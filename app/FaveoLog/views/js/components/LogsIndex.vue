<template>

	<div>
		<alert componentName="dataTableModal"/>

			<faveo-box :title="lang('logs')">

				<div slot="headerMenu" class="card-tools">
					<button type="button" class="btn btn-tool" @click="showModal = true" v-tooltip="lang('delete_logs')">		
						<i class="fas fa-trash"></i> 
					</button>
				</div>

				<cron-logs/>

				<mail-logs/>

				<exception-logs/>

			</faveo-box>

		<transition name="modal">
			<logs-modal v-if="showModal" title="delete_logs" :onClose="onClose" :showModal="showModal"/>
		</transition>

	</div>

</template>

<script>

	import axios from 'axios';

	import moment from 'moment';
	import Vue from 'vue';
	import FaveoBox from 'components/MiniComponent/FaveoBox';

	Vue.component('logs-trace', require('./LogsTables/ReusableComponent/LogsTrace.vue'));

	export default {

		name : 'system-logs',

		description : 'System logs component',

		data(){

			return {

				showModal : false,

			}

		},

		methods : {

			onChange(value,name){
				this[name] = value;
			},

			onClose(){

		        this.showModal = false;

		        this.$store.dispatch('unsetValidationError');
		    },

		},

		components : {

			'exception-logs': require('./LogsTables/ExceptionLogs'),

			'cron-logs': require('./LogsTables/CronLogs'),

			'mail-logs': require('./LogsTables/MailLogs'),

			'logs-modal': require('./LogsTables/ReusableComponent/LogsModal'),

			"alert": require("components/MiniComponent/Alert"),
			'faveo-box' : FaveoBox
		}
	};

</script>