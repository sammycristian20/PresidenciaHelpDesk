<template>

	<div id="associated-task-list">
		<TicketsTaskView :ticketProp="ticketNumber" />
	</div>

</template>

<script>

import TicketsTaskView from './TicketsTaskView';
export default {

	props: {
		data : {type: String|Object}
	},

	components : {
		TicketsTaskView
	},

	computed: {
		ticketNumber() {
			return JSON.parse(this.data);
		}
	}
}

</script>