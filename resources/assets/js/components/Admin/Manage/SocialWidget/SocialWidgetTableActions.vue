<template>
 
	<div class="btn-group block">

		<a id="edit-button" @click="onClickEvent('edit')" href="javascript:;" class="btn btn-primary btn-sm" 
			v-tooltip="lang('edit')"><i class="fas fa-edit"></i> {{lang('edit')}}
		</a>

		
		<transition  name="modal">
			<social-modal v-if="showModal" :onClose="onClose" :modal-mode="modalMode" :showModal="showModal" :assetId="data.id" :data="data"></social-modal>
		</transition>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	import {boolean} from 'helpers/extraLogics'

	export default {

		name:"social-widget-table-actions",

		description: "Widget table edit componnet",

		data: () => {
			return {
				
				showModal: false
			}
		},
 
		props: {

			data : { type : Object, required : true },
		},

		methods: {
			onClickEvent(mode) {
				this.showModal = true; 
				this.modalMode = mode;
			},

			onClose() {
				this.showModal = false;
			},
		
		},

		components: {
			'social-modal': require('./SocialModal')
		}
	};

</script>