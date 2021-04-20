<template>

	<div>
	
		<approval-progress :ticketId="id" :actions="actions" alertName="inboxActions" :key="'approval'+counter"/>
	</div>
</template>

<script>

	export default {

		props: {
			
			id: { type: String | Number, required: true },

			actions : { type : Object, default : ()=>{} },
		},

		data() {

			return {

				counter : 0,
			}
		},

		created() {

			window.eventHub.$on('approvalWorkflowApplied',this.updateData)
		},

		methods : { 

			updateData() {

				this.counter += 1;
			}
		},

		components: {
			
			'approval-progress': require('components/Admin/ApprovalWorkflow/ApprovalProgress'),
		}
	};
</script>