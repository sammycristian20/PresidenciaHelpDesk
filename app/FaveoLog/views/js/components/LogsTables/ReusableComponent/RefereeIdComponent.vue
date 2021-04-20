<template>

	<div class="log-mail-referee-id" id="referee_id" v-html="data.referee_id">
	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {

		name:"referee-id",

		description: "Referee id component",

		props: {

			data : { type : Object, required : true }

		},

		data(){

			return {

				showModal:false,
			}
		},

	};

</script>

<style type="text/css">

	.log-mail-referee-id {
		max-width: 300px;
	}

</style>
