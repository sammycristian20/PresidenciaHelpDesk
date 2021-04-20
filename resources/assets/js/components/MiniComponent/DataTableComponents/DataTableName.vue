<template>

	<div class="btn-group">
		<p :title="data.name">{{data.name | subStringFilter}}</p>
	</div>

</template>

<script type="text/javascript">

	export default {
		
		name:"datatable-name",

		description: "Datatable name component",		
		
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
			}
		},
		filters : {
			subStringFilter(value) {
				if(value){
					if(value.length>25){ 
						return value.substring(0,25) + '...';
					} 
					else { return value; }
				}
			}
		},
	};
</script>

<style type="text/css">
</style>