<template>

	<div class="btn-group">
		
		
		<a id="status">
			<span title="Status" :class="{'btn btn-success btn-xs':data.status !== 0,'btn btn-danger btn-xs':data.status === 0}" id="edit_btn">{{data.order.status ? lang('active') : lang('pending')}}</span>
		</a>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {
		
		name:"data-table-status",

		description: "Contains active and inactive status",		
		
		props: {
			
			/**
			 * component will display buttons based on the indexes present in the `data` object
			 * For eg : if you want this component to display delete button, `data` should have 
			 * an index `delete` which should be the API endpoint which can be used to delete 
			 * the data.
			 * @type {Object}
			 */
			data : { type : Object, required : true }
		
		},
		
		methods:{

		}
	};

</script>

<style type="text/css">

</style>