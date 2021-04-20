<template>

	<div class="block">

		<a class="btn btn-primary btn-sm" @click="showEditModal = true" href="javascript:;"
			v-tooltip="lang('edit')" >

			<i class="fas fa-edit"></i>
		</a>

		<a class="btn btn-primary btn-sm" id="settings-modal-button" @click="showSettingsModal = true" 
			href="javascript:;" v-tooltip="lang('get_webhook_url')">

				<i class="fas fa-link"></i>
		</a>

		<transition name="modal">

		 	<telephony-settings-modal v-if="showSettingsModal" :onClose="onClose" :showModal="showSettingsModal" 
		 		:data="data">

		 	</telephony-settings-modal>
		</transition>

		<transition name="modal">

		 	<telephony-edit-modal v-if="showEditModal" :onClose="onClose" :showModal="showEditModal" 
		 		:data="data">

		 	</telephony-edit-modal>
		</transition>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';
	import {lang} from 'helpers/extraLogics';
	import {boolean} from 'helpers/extraLogics'

	export default {

		name:"data-table-actions",

		description: "Contains edit, delete and view buttons as group which can be used as a component as whole. It is built basically for displaying edit, delete and view button in a datable.",

		props: {

			data : { type : Object, required : true },
		},

		data(){

			return{

				showSettingsModal : false,

				showEditModal : false
			}
		},

		methods:{

			onClose(){

		    this.showSettingsModal = false;

		    this.showEditModal = false;

		    this.$store.dispatch('unsetValidationError');
		  },
		},

		components:{

			'telephony-settings-modal' : require('./TelephonySettingsModal'),

			'telephony-edit-modal' : require('./TelephonyEditModal')
		}
	};

</script>

<style type="text/css" scoped>

	.block{
		display: block !important;
	}
</style>
