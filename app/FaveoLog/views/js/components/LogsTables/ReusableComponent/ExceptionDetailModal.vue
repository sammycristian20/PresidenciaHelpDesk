<template>
	<div>
		<modal
      v-if="showModal"
      :showModal="showModal"
      :onClose="onClose"
      @close="showModal = false"
      :containerStyle="containerStyle">

			<div slot="title">
				<h4 class="modal-title">{{lang('exception_details')}}</h4>
			</div>

			<div slot="fields">
				<samp style="font-size: small">
					<strong>{{lang('message')}}:</strong> <span>{{data.message}}</span><br>
					<br/>
					<strong>{{lang('file')}}:</strong> <span>{{data.file}}({{data.line}})</span><br>
					<br/>
					<strong>{{lang('trace')}}:</strong>
					<p v-html="data.trace" class="trace"></p>
				</samp>
			</div>

		</modal>
	</div>
</template>

<script>

	import Modal from 'components/Common/Modal';

	export default {

		name: 'exception-detail-modal',

		description: 'Logs exception detail component',

		props: {

			showModal: {
        type: Boolean,
        default: false
			},
			
			// On modal close action
			onClose: {
        type: Function,
        required: true
			},
			
			// Data to show in the modal
			data: {
        type: Object,
        required: true
      },
		},

		data: () => ({
			containerStyle:{
				width:'800px'
			},
		}),

	components:{
		'modal': Modal,
	}

};
</script>

<style type="text/css" scoped>

 .trace {
	 padding: 1rem;
	 color: #ffffff;
	 background-color: black;
	 height: 35vh;
	 overflow: auto !important;
	 line-height: 1.5 !important;
	 white-space: nowrap !important;
 }
</style>
