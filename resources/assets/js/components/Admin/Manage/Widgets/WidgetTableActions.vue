<template>
 
	<div class="btn-group block">

		<a id="edit-button" @click="onClickEvent('edit')" href="javascript:;">
			<span title="Edit" class="btn btn-primary btn-sm" id="edit_btn"><i class="fa fa-edit"></i> {{lang('edit')}}</span>
		</a>

		<a id="edit-button" @click="onClickEvent('view')" href="javascript:;">
			<span title="Edit" class="btn btn-primary btn-sm" id="edit_btn"><i class="fa fa-eye"></i> {{lang('view')}}</span>
		</a>

		<transition  name="modal">
			<Widget-Modal v-if="showModal" :onClose="onClose" :modal-mode="modalMode" :showModal="showModal" :assetId="data.id" :data="data"></Widget-Modal>
		</transition>
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	import {boolean} from 'helpers/extraLogics'

	export default {

		name:"widget-table-actions",

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
			'Widget-Modal': require('./WidgetModal')
		}
	};

</script>

<style type="text/css" scoped>

	.block{
		display: block;
	}

	#edit-button , #view-button{
		margin-left: 2px;
		margin-right: 2px;
	}

	#down{
		display: inline;
	}

	#action-menu{

		right: 0;
		left: unset;
		top: auto;
	}
</style>
