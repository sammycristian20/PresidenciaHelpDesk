<template>

	<div class="btn-group">
		
		
		<a id="user" :href="base+'/user/'+data.user.id" target="_blank">
			{{data.user.first_name}} {{data.user.last_name}}
		</a>

	</div>

</template>

<script type="text/javascript">

	import axios from 'axios';

	export default {
		
		name:"data-table-user",

		description: "Contains user name",		
		
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
		data(){
			return {
				base : window.axios.defaults.baseURL
			}
		}
	};

</script>

<style type="text/css">

</style>